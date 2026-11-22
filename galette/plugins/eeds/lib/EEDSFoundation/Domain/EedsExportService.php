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
use Galette\Core\Logs;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Safe\DateTimeImmutable;
use Throwable;

use function Safe\tempnam;
use function Safe\file_get_contents;

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
    public function export(Db $zdb, int $actor_id, array $structure_ids): array
    {
        if ($structure_ids === []) {
            throw new \InvalidArgumentException('An export scope is required.');
        }

        try {
            $select = $zdb->select('adherents', 'a');
            $select->columns([
                'a.id_adh',
                'a.id_statut',
                'a.nom_adh',
                'a.prenom_adh',
                'a.societe_adh',
                'a.pseudo_adh',
                'a.titre_adh',
                'a.ddn_adh',
                'a.sexe_adh',
                'a.adresse_adh',
                'a.cp_adh',
                'a.ville_adh',
                'a.region_adh',
                'a.pays_adh',
                'a.tel_adh',
                'a.gsm_adh',
                'a.email_adh',
                'a.info_adh',
                'a.info_public_adh',
                'a.prof_adh',
                'a.login_adh',
                'a.date_crea_adh',
                'a.date_modif_adh',
                'a.activite_adh',
                'a.bool_admin_adh',
                'a.bool_exempt_adh',
                'a.bool_display_info',
                'a.date_echeance',
                'a.pref_lang',
                'a.lieu_naissance',
                'a.parent_id',
                'a.num_adh',
                'aff.id_affiliation',
                'aff.id_structure',
                'aff.branch',
                'aff.unit',
                'aff.subgroup',
                'aff.affiliation_status',
                'aff.effective_start',
                'aff.effective_end',
                'n.national_number',
                'n.issued_at',
                'n.withdrawn_at',
                'life.membership_status',
                'life.effective_date',
                'st.structure_code',
                'st.structure_name',
                'st.structure_type',
            ]);
            $select->join(
                name: ['aff' => PREFIX_DB . 'eeds_member_affiliations'],
                on: 'a.id_adh=aff.id_adh',
                columns: [],
                type: $select::JOIN_INNER
            )->join(
                name: ['n' => PREFIX_DB . 'eeds_member_numbers'],
                on: 'a.id_adh=n.id_adh',
                columns: [],
                type: $select::JOIN_LEFT
            )->join(
                name: ['life' => PREFIX_DB . 'eeds_member_lifecycle'],
                on: 'a.id_adh=life.id_adh',
                columns: [],
                type: $select::JOIN_LEFT
            )->join(
                name: ['st' => PREFIX_DB . 'eeds_structures'],
                on: 'aff.id_structure=st.id_structure',
                columns: [],
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
                'id_adh', 'id_statut', 'nom_adh', 'prenom_adh', 'societe_adh', 'pseudo_adh',
                'titre_adh', 'ddn_adh', 'sexe_adh', 'adresse_adh', 'cp_adh', 'ville_adh',
                'region_adh', 'pays_adh', 'tel_adh', 'gsm_adh', 'email_adh', 'info_adh',
                'info_public_adh', 'prof_adh', 'login_adh', 'date_crea_adh', 'date_modif_adh',
                'activite_adh', 'bool_admin_adh', 'bool_exempt_adh', 'bool_display_info',
                'date_echeance', 'pref_lang', 'lieu_naissance', 'parent_id', 'num_adh',
                'id_affiliation', 'id_structure', 'branch', 'unit', 'subgroup',
                'affiliation_status', 'effective_start', 'effective_end', 'national_number',
                'issued_at', 'withdrawn_at', 'membership_status', 'effective_date',
                'structure_code', 'structure_name', 'structure_type',
            ];

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('EEDS');
            foreach ($fields as $column => $field) {
                $sheet->setCellValue([$column + 1, 1], $field);
            }
            $sheet->getStyle('1:1')->getFont()->setBold(true);

            $row_number = 2;
            foreach ($zdb->execute($select) as $row) {
                foreach ($fields as $column => $field) {
                    $value = $row->$field ?? null;
                    $sheet->setCellValue([$column + 1, $row_number], $value);
                }
                $row_number++;
            }

            foreach (range(1, count($fields)) as $column) {
                $sheet->getColumnDimensionByColumn($column)->setAutoSize(true);
            }

            $path = tempnam(sys_get_temp_dir(), 'eeds-');
            if ($path === false) {
                throw new \RuntimeException('Unable to create a temporary XLSX export file.');
            }
            (new Xlsx($spreadsheet))->save($path);
            $spreadsheet->disconnectWorksheets();

            $insert = $zdb->insert('eeds_export_audit');
            $insert->values([
                'actor_id' => $actor_id,
                'export_fields' => json_encode($fields, JSON_UNESCAPED_UNICODE),
                'structure_ids' => json_encode($structure_ids, JSON_UNESCAPED_UNICODE),
                'row_count' => $row_number - 2,
                'created_at' => (new DateTimeImmutable())->format('Y-m-d H:i:s'),
            ]);
            $zdb->execute($insert);

            return [
                'path' => $path,
                'fields' => $fields,
                'row_count' => $row_number - 2,
            ];
        } catch (Throwable $e) {
            Logs::exception($e, 'Unable to generate the scoped EEDS XLSX export', Analog::WARNING);
            throw $e;
        }
    }
}
