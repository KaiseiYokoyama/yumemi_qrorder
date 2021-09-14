<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')
                ->comment('メニューを提供する店舗')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('name')
                ->comment('メニューの名前');
            $table->unsignedInteger('price')
                ->comment('メニューの価格');
            $table->string('image_url')
                ->comment('メニューの画像のURL');
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
        Schema::dropIfExists('menus');
    }
}
