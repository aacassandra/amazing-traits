<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(env("TABLE_PREFIX").'m_general', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->nullable();
            $table->string('group', 255)->nullable();
            $table->string('key', 255)->nullable();
            $table->text('value_1')->nullable();
            $table->text('value_2')->nullable();
            $table->text('value_3')->nullable();
            $table->boolean('active_flag');
            $table->bigInteger('created_id');
            $table->bigInteger('updated_id')->nullable();
            $table->bigInteger('deleted_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(env("TABLE_PREFIX").'m_general');
    }
};
