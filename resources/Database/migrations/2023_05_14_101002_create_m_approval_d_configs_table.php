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
        Schema::create(env("TABLE_PREFIX").'m_approval_d_configs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(env("TABLE_PREFIX").'m_approval_id');
            $table->integer('level');
            $table->boolean('is_full_approve')->default(false);
            $table->boolean('is_skippable')->default(false);
            $table->boolean('is_able_to_skip')->default(false);
            $table->boolean('send_wa_notif')->default(false);
            $table->boolean('send_email_notif')->default(false);
            $table->integer('min_value')->nullable();
            $table->integer('max_value')->nullable();
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
        Schema::dropIfExists(env("TABLE_PREFIX").'m_approval_d_configs');
    }
};
