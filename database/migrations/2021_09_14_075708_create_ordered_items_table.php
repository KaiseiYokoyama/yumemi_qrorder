<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderedItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ordered_items', function (Blueprint $table) {
            $table->id();
            // 注文された料理の種類（メニュー）
            $table->foreignId('menu_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // この料理を頼んだお客さんの一団
            $table->foreignId('party_id')
                ->constrained()
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // 注文確定状態かどうか
            // falseの場合、まだカゴに入っている
            $table->boolean('is_draft');
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
        Schema::dropIfExists('ordered_items');
    }
}
