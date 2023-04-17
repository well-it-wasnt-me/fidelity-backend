<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Console;

use PDO;
use PDOStatement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use UnexpectedValueException;

/**
 * Command.
 */
final class SchemaImportCommand extends Command
{
    private PDO $pdo;

    /**
     * The constructor.
     *
     * @param PDO $pdo The database connection
     * @param string|null $name The name
     */
    public function __construct(PDO $pdo, ?string $name = null)
    {
        parent::__construct($name);
        $this->pdo = $pdo;
    }

    /**
     * Configure.
     *
     * @return void
     */
    protected function configure(): void
    {
        parent::configure();

        $this->setName('schema-import');
        $this->setDescription('Import schema data source.');
    }

    /**
     * Execute command.
     *
     * @param InputInterface $input The input
     * @param OutputInterface $output The output
     *
     * @return int The error code, 0 on success
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Lazy loading, because the database may not exists
        $output->writeln(sprintf('Starting import...'));

        $tempLine = '';
        // Read in the full file
        $lines = file(__DIR__ . '/../../resources/schema/schema.sql');
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Add this line to the current segment
            $tempLine .= $line;
            // If its semicolon at the end, so that is the end of one query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                $this->query($tempLine);
                // Reset temp variable to empty
                $tempLine = '';
            }
        }

        $output->writeln(sprintf('<info>Done</info>'));

        return 0;
    }

    /**
     * Create query statement.
     *
     * @param string $sql The sql
     *
     * @throws UnexpectedValueException
     *
     * @return PDOStatement The statement
     */
    private function query(string $sql): PDOStatement
    {
        $statement = $this->pdo->query($sql);

        if (!$statement) {
            throw new UnexpectedValueException('Query failed');
        }

        return $statement;
    }
}
