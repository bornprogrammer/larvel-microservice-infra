<?php

namespace Laravel\Infrastructure\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Schema;

abstract class BaseTableMigration extends Migration
{
    protected string $tableName;

    public function up()
    {
        if (!Schema::hasTable($this->tableName)) {
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            $this->upTable($this->tableName);
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
        }
    }

    public function down()
    {
        if (Schema::hasTable($this->tableName)) {
            $this->downTable($this->tableName);
        }
    }

    public abstract function upTable(string $tableName): void;

    public function downTable(string $tableName): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::drop($tableName);
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}
