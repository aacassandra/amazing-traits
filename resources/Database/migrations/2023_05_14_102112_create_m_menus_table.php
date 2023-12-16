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
        Schema::create(env("TABLE_PREFIX").'m_menu', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->string('name');
            $table->string('module')->nullable();
            $table->string('submodule')->nullable();
            $table->integer('sequence')->nullable();
            $table->string('path_url')->nullable();

            // 2023_08_16_114604_add_column_into_m_menus_table.php
            // $table->string('callback_approval_url')->default('/form');

            $table->boolean('active_flag')->default(true);
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
        Schema::dropIfExists(env("TABLE_PREFIX").'m_menu');
    }
};
