<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(env("TABLE_PREFIX").'e_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(env("TABLE_PREFIX").'e_approval_id');
            $table->bigInteger('trx_id');
            $table->string('trx_name');
            $table->string('trx_no');
            $table->string('trx_table');
            $table->date('trx_date');
            $table->bigInteger('trx_creator_id');
            $table->string('action');
            $table->bigInteger('action_user_id');
            $table->text('action_note')->nullable();
            $table->datetime('action_at');
            $table->bigInteger('created_id');
            $table->bigInteger('updated_id')->nullable();
            $table->bigInteger('deleted_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(env("TABLE_PREFIX").'e_approval_logs');
    }
};
