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
        Schema::create(env("TABLE_PREFIX") . 'm_users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();

            $table->string('gender')->nullable();
            $table->date('date_birth')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('province_id')->nullable();
            $table->bigInteger('regency_id')->nullable();

            $table->string('role')->nullable();
            $table->json('roles')->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('otp_code', 6)->nullable();
            $table->string('password');

            $table->json('device')->nullable();
            $table->boolean('active_flag')->default(true);

            $table->string('status', 12)->default("PENDING")->nullable();

            $table->tinyInteger('suspend')->default(0);
            $table->timestamp('suspend_expired_at')->nullable();
            $table->boolean('blocked')->default(false);

            $table->bigInteger('blocked_id')->nullable();
            $table->bigInteger('created_id');
            $table->bigInteger('updated_id')->nullable();
            $table->bigInteger('deleted_id')->nullable();
            $table->softDeletes();

            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(env("TABLE_PREFIX") . 'm_users');
    }
};
