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
        Schema::create(env("TABLE_PREFIX").'m_colors', function (Blueprint $table) {
            $table->id();
            $table->string('group', 30);
            $table->text('code');
            $table->text('hex');
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(env("TABLE_PREFIX").'m_colors');
    }
};
