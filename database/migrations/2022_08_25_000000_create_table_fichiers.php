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
        Schema::create('nephelefiles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('path', 255)->nullable();
            $table->integer('size')->nullable();
            $table->foreignId('created_user_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nephelefiles');
    }
};
