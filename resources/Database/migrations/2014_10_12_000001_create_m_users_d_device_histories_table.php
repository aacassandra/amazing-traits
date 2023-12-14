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
        Schema::create(env("TABLE_PREFIX") . 'm_users_d_device_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger(env("TABLE_PREFIX") . 'm_users_id');
            $table->string('uuid', 100)->nullable();
            $table->string('brand', 20)->nullable();
            $table->string('os', 20)->nullable();
            $table->string('version', 20)->nullable();
            $table->string('app_version', 20)->nullable();
            $table->boolean('current')->default(false);
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
        Schema::dropIfExists(env("TABLE_PREFIX") . 'm_users_d_device_histories');
    }
};
