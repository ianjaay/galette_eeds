<?php

/**
 * This file is part of Galette (https://galette.eu).
 * SPDX-FileCopyrightText: Copyright © 2003-2026 The Galette Team
 * SPDX-License-Identifier: GPL-3.0-or-later
 */

declare(strict_types=1);

namespace EEDSFoundation\Console;

use EEDSFoundation\Domain\StructureService;
use Galette\Console\Command\AbstractCommand;
use Galette\Core\Db;
use Galette\Core\Logs;
use Analog\Analog;
use Override;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Safe\date;

#[AsCommand(
    name: 'eeds:initialize-national',
    description: 'Initialize the unique EEDS national root structure'
)]
final class InitializeNationalStructure extends AbstractCommand
{
    public function __construct(
        string $basepath,
        private readonly Db $zdb
    ) {
        parent::__construct($basepath);
    }

    #[Override]
    protected function configure(): void
    {
        $this
            ->addOption('name', null, InputOption::VALUE_REQUIRED, 'National structure name', 'Nation')
            ->addOption('code', null, InputOption::VALUE_REQUIRED, 'National structure code', 'NATION')
            ->addOption('effective-date', null, InputOption::VALUE_REQUIRED, 'Effective date', date('Y-m-d'))
            ->addOption('actor-id', null, InputOption::VALUE_REQUIRED, 'Optional technical actor member ID');
    }

    #[Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $actor_option = $input->getOption('actor-id');
        $actor_id = $actor_option === null || $actor_option === '' ? null : (int)$actor_option;
        if ($actor_id !== null && $actor_id <= 0) {
            $this->io->error('The actor ID must be a positive member ID.');
            return self::FAILURE;
        }

        try {
            $id = (new StructureService())->ensureNationalRoot(
                $this->zdb,
                (string)$input->getOption('name'),
                (string)$input->getOption('code'),
                (string)$input->getOption('effective-date'),
                $actor_id
            );
            $this->io->success('EEDS national root initialized (ID ' . $id . ').');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            Logs::exception($e, 'Unable to initialize the EEDS national root from the console', Analog::WARNING);
            $this->io->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
