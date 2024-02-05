<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Laravel\Infrastructure\Migrations\BaseTableMigration;

class CreateSystemLogsTable extends BaseTableMigration
{
    protected string $tableName = "system_logs";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upTable(string $tableName): void
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("user_org_id")->nullable();
            $table->uuid("org_id")->nullable();
            $table->string("role_name")->nullable();
            $table->string("exception_type")->nullable();
            $table->string("file_name")->nullable();
            $table->string("line_number")->nullable();
            $table->string("error_message")->nullable();
            $table->string("event_type")->nullable();
            $table->longText("error_data")->nullable();
            $table->timestamps();
            // $table->foreign("user_org_id")->references("id")->on("user_organization_details");
            // $table->foreign("org_id")->references("id")->on("organizations");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    //    public function down()
    //    {
    //        Schema::dropIfExists('audits');
    //    }
}
