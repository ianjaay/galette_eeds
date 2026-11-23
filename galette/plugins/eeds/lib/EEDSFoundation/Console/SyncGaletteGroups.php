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
 * Preview or apply the EEDS structure to Galette group projection.
 *
 * @author EEDS Engineering <engineering@eeds.sn>
 */
#[AsCommand(
    name: 'eeds:sync-groups',
    description: 'Preview or synchronize EEDS structures with nested Galette groups'
)]
final class SyncGaletteGroups extends AbstractCommand
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
     * Configure preview and apply options.
     */
    #[Override]
    protected function configure(): void
    {
        $this
            ->addOption('apply', null, InputOption::VALUE_NONE, 'Apply group and membership synchronization')
            ->addOption(
                'replace-memberships',
                null,
                InputOption::VALUE_NONE,
                'Remove stale memberships from mapped groups after review'
            );
    }

    /**
     * Preview by default; apply only when explicitly requested.
     */
    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $mapper = new StructureGroupMapper();
        $apply = (bool)$input->getOption('apply');
        $replace = (bool)$input->getOption('replace-memberships');
        if ($replace && !$apply) {
            $this->io->error('--replace-memberships requires --apply.');
            return self::INVALID;
        }

        try {
            if (!$apply) {
                foreach ($mapper->preview($this->zdb) as $message) {
                    $this->io->text($message);
                }
                $this->io->note('Dry run only. Existing groups require explicit ID-based mapping.');
                return self::SUCCESS;
            }

            $mapper->synchronize($this->zdb, $replace);
            $this->io->success('EEDS groups and current member affiliations synchronized.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Logs::exception($e, 'Unable to synchronize EEDS Galette groups from the console', Analog::WARNING);
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
