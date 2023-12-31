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
        Schema::create(getTablePrefix().'m_roles_d_users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(env("TABLE_PREFIX") . 'm_roles_id');
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists(getTablePrefix().'m_roles_d_users');
    }
};
