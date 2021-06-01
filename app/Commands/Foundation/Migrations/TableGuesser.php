<?php

namespace App\Commands\Foundation\Migrations;

class TableGuesser
{
    /**
     * Attempt to guess the table name and "creation" status of the given migration.
     *
     * @param  string  $migration
     * @return array
     */
    public static function guess($migration)
    {
        if (preg_match('/^(create|new)_(\w+)_table$/', $migration, $matches)) {
            return [$matches[2], $create = true];
        }

        if (preg_match('/_(to|from|in)_(\w+)_table$/', $migration, $matches)) {
            return [$matches[2], $create = false];
        }
    }
}
