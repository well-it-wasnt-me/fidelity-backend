<?php
/*
 * Copyright (c) 2022. Moebius Integrated System.
 */


namespace App\Database;

interface TransactionInterface
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
}
