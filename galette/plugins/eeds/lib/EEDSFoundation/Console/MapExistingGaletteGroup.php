<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Console;

use EEDSFoundation\Domain\StructureGroupMapper;
use Galette\Console\Command\AbstractCommand;
use Galette\Core\Db;
use Galette\Core\Logs;
use Analog\Analog;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Explicitly link a reviewed existing Galette group to an EEDS structure.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
#[AsCommand(
    name: 'eeds:map-structure-group',
    description: 'Explicitly map a reviewed existing Galette group to an EEDS structure'
)]
final class MapExistingGaletteGroup extends AbstractCommand
{
    /**
     * Create the command.
     */
    public function __construct(
        string $basepath,
        private readonly Db $zdb
    ) {
        parent::__construct($basepath);
    }

    /**
     * Configure the structure, group and membership-replacement options.
     */
    #[Override]
    protected function configure(): void
    {
        $this
            ->addOption('structure-id', null, InputOption::VALUE_REQUIRED, 'EEDS structure ID')
            ->addOption('group-id', null, InputOption::VALUE_REQUIRED, 'Reviewed Galette group ID')
            ->addOption(
                'replace-memberships',
                null,
                InputOption::VALUE_NONE,
                'Replace this mapped group membership list from current EEDS affiliations'
            );
    }

    /**
     * Validate IDs and perform the explicit mapping.
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $structure_id = filter_var($input->getOption('structure-id'), FILTER_VALIDATE_INT);
        $group_id = filter_var($input->getOption('group-id'), FILTER_VALIDATE_INT);
        if (!is_int($structure_id) || $structure_id <= 0 || !is_int($group_id) || $group_id <= 0) {
            $this->io->error('Both --structure-id and --group-id must be positive integers.');
            return self::INVALID;
        }

        try {
            (new StructureGroupMapper())->mapExistingGroup(
                $this->zdb,
                $structure_id,
                $group_id,
                (bool)$input->getOption('replace-memberships')
            );
            $this->io->success(
                sprintf('EEDS structure #%1$d is mapped to Galette group #%2$d.', $structure_id, $group_id)
            );
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Logs::exception($e, 'Unable to map an existing Galette group from the console', Analog::WARNING);
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
