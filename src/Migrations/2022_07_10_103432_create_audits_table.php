<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Laravel\Infrastructure\Migrations\BaseTableMigration;

class CreateAuditsTable extends BaseTableMigration
{
    protected string $tableName = "audits";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function upTable(string $tableName): void
    {
        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_type')->nullable();
            $table->uuid('user_id')->nullable();
            $table->string('event');
            $table->uuid('auditable_id');
            $table->string('auditable_type');
            $table->text('old_values')->nullable();
            $table->text('new_values')->nullable();
            $table->text('url')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent', 1023)->nullable();
            $table->string('tags')->nullable();
            $table->timestamps();
            $table->index(['user_id', 'user_type']);
            $table->index([
                'auditable_id',
                'auditable_type',
            ]);
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
