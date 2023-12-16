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
        Schema::create(env("TABLE_PREFIX").'m_approval_d_excludes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(env("TABLE_PREFIX").'m_approval_id');
            $table->integer('level');
            $table->bigInteger('user_id');
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
        Schema::dropIfExists(env("TABLE_PREFIX").'m_approval_d_excludes');
    }
};
