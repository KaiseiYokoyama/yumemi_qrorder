<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                ->comment('お客さんの一団を受け入れる店舗')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->integer('state')
                ->comment('お客さんの一団の状態 0: pending 初期状態, 1: waiting 会計待ち, 2: accounted 会計済み');
            $table->uuid('uuid')
                ->comment('お客さんの一団に割り当てられたUUID QRコード/cookieの元になる')
                ->unique();
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
        Schema::dropIfExists('parties');
    }
}
