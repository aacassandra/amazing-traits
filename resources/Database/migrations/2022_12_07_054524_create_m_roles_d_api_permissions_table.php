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
        Schema::create(getTablePrefix().'m_roles_d_api_permissions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('ihm_m_roles_id');
            $table->bigInteger('model_id');
            $table->boolean('get')->default(true);
            $table->boolean('find')->default(true);
            $table->boolean('store')->default(true);
            $table->boolean('update')->default(true);
            $table->boolean('destroy')->default(true);
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
        Schema::dropIfExists(getTablePrefix().'m_roles_d_api_permissions');
    }
};
