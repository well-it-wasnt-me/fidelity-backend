<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */

namespace App\Database;
use Cake\Database\Connection;

/**
 * final class
 */
final class Transaction implements TransactionInterface
{
    private Connection $connection;

    /**
     * Constructor
     * @param Connection $connection
     */
    public function __construct(Connection $connection){
        $this->connection = $connection;
    }

    /**
     * Begin
     * @return void
     */
    public function begin(): void
    {
        $this->connection->begin();
    }

    /**
     * Commit
     * @return void
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * Rollback
     * @return void
     */
    public function rollback(): void
    {
        $this->connection->rollback();
    }
}