<?php

namespace Laravel\Infrastructure\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

abstract class BaseColumnMigration extends Migration
{
    protected string $tableName;

    protected string $columnName;

    public function up()
    {
        if (!Schema::hasColumn($this->tableName, $this->columnName)) {
            $this->upColumn($this->tableName, $this->columnName);
        }
    }

    public function down()
    {
        if (Schema::hasColumn($this->tableName, $this->columnName)) {
            $this->downColumn($this->tableName, $this->columnName);
        }
    }

    public abstract function upColumn(string $tableName, string $columnName): void;

    public abstract function downColumn(string $tableName, string $columnName): void;
}
