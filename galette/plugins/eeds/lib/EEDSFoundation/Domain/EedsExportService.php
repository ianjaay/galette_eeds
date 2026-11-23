<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Domain;

use Analog\Analog;
use Galette\Core\Db;
use Galette\Core\Login;
use Galette\Core\Logs;
use Galette\Entity\FieldsConfig;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Safe\DateTimeImmutable;
use Throwable;

use function Safe\tempnam;
use function Safe\unlink;
use function Safe\json_encode;

/**
 * Scoped XLSX export for the R1 EEDS member register.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
final readonly class EedsExportService
{
    /**
     * @param list<int> $structure_ids
     * @return array{path:string,fields:list<string>,row_count:int}
     */
    public function export(Db $zdb, Login $login, FieldsConfig $fields_config, array $structure_ids): array
    {
        if ($structure_ids === []) {
            throw new \InvalidArgumentException('An export scope is required.');
        }

        $path = null;
        $spreadsheet = null;
        $export_created = false;
        try {
            $member_fields = [
                'id_adh', 'id_statut', 'nom_adh', 'prenom_adh', 'societe_adh', 'pseudo_adh',
                'titre_adh', 'ddn_adh', 'sexe_adh', 'adresse_adh', 'cp_adh', 'ville_adh',
                'region_adh', 'pays_adh', 'tel_adh', 'gsm_adh', 'email_adh', 'info_adh',
                'info_public_adh', 'prof_adh', 'login_adh', 'date_crea_adh', 'date_modif_adh',
                'activite_adh', 'bool_admin_adh', 'bool_exempt_adh', 'bool_display_info',
                'date_echeance', 'pref_lang', 'lieu_naissance', 'parent_id', 'num_adh',
                'gpgid',
            ];
            $access_level = $login->getAccessLevel();
            $member_fields = array_values(array_filter(
                $member_fields,
                static fn(string $field): bool => $fields_config->isVisibleFor($field, $access_level)
            ));

            $select = $zdb->select('adherents', 'a');
            $select->columns($member_fields);
            $select->join(
                name: ['aff' => PREFIX_DB . 'eeds_member_affiliations'],
                on: 'a.id_adh=aff.id_adh',
                columns: [
                    'id_affiliation', 'id_structure', 'branch', 'unit', 'subgroup',
                    'affiliation_status', 'effective_start', 'effective_end',
                ],
                type: $select::JOIN_INNER
            )->join(
                name: ['n' => PREFIX_DB . 'eeds_member_numbers'],
                on: 'a.id_adh=n.id_adh',
                columns: ['national_number', 'issued_at', 'withdrawn_at'],
                type: $select::JOIN_LEFT
            )->join(
                name: ['life' => PREFIX_DB . 'eeds_member_lifecycle'],
                on: 'a.id_adh=life.id_adh',
                columns: ['membership_status', 'effective_date'],
                type: $select::JOIN_LEFT
            )->join(
                name: ['st' => PREFIX_DB . 'eeds_structures'],
                on: 'aff.id_structure=st.id_structure',
                columns: ['structure_code', 'structure_name', 'structure_type'],
                type: $select::JOIN_INNER
            );
            $select->where([
                'aff.id_structure' => $structure_ids,
                'aff.affiliation_status' => 'active',
            ]);
            $date = (new DateTimeImmutable())->format('Y-m-d');
            $select->where('aff.effective_start <= ' . $zdb->platform->quoteValue($date));
            $select->where('(aff.effective_end IS NULL OR aff.effective_end >= ' . $zdb->platform->quoteValue($date) . ')');

            $fields = [
                ...$member_fields,
                'id_affiliation', 'id_structure', 'branch', 'unit', 'subgroup',
                'affiliation_status', 'effective_start', 'effective_end', 'national_number',
                'issued_at', 'withdrawn_at', 'membership_status', 'effective_date',
                'structure_code', 'structure_name', 'structure_type',
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('EEDS');
            foreach ($fields as $column => $field) {
                $sheet->setCellValueExplicit(
                    [$column + 1, 1],
                    $field,
                    DataType::TYPE_STRING
                );
            }
            $sheet->getStyle('1:1')->getFont()->setBold(true);

            $row_number = 2;
            foreach ($zdb->execute($select) as $row) {
                foreach ($fields as $column => $field) {
                    $value = $row->$field ?? null;
                    $sheet->setCellValueExplicit(
                        [$column + 1, $row_number],
                        $value === null ? null : (string)$value,
                        $value === null ? DataType::TYPE_NULL : DataType::TYPE_STRING
                    );
                }
                $row_number++;
            }

            foreach (range(1, count($fields)) as $column) {
                $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
            }

            $path = tempnam(sys_get_temp_dir(), 'eeds-');
            (new Xlsx($spreadsheet))->save($path);
            $spreadsheet->disconnectWorksheets();
            $spreadsheet = null;

            $insert = $zdb->insert('eeds_export_audit');
            $insert->values([
                'actor_id' => (int)$login->id,
                'export_fields' => json_encode($fields, JSON_UNESCAPED_UNICODE),
                'structure_ids' => json_encode($structure_ids, JSON_UNESCAPED_UNICODE),
                'row_count' => $row_number - 2,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $zdb->execute($insert);
            $export_created = true;

            return [
                'path' => $path,
                'fields' => $fields,
                'row_count' => $row_number - 2,
            ];
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to generate the scoped EEDS XLSX export', Analog::WARNING);
            throw $e;
        } finally {
            $spreadsheet?->disconnectWorksheets();
            if (!$export_created && $path !== null && file_exists($path)) {
                try {
                    unlink($path);
                } catch (Throwable $cleanup_exception) {
                    Logs::exception(
                        $cleanup_exception,
                        'Unable to remove a failed EEDS XLSX export file',
                        Analog::WARNING
                    );
                }
            }
        }
    }
}
