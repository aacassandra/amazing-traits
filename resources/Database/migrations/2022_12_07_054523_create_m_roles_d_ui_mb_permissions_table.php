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
        Schema::create(getTablePrefix().'m_roles_d_ui_mb_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ihm_m_roles_id');
            $table->string('name', 60);
            $table->string('module', 60);
            $table->string('submodule', 60);
            $table->boolean('view')->default(true);
            $table->boolean('preview')->default(true);
            $table->boolean('create')->default(true);
            $table->boolean('edit')->default(true);
            $table->boolean('delete')->default(true);
            $table->json('customs')->nullable();
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
        Schema::dropIfExists(getTablePrefix().'m_roles_d_ui_mb_permissions');
    }
};
