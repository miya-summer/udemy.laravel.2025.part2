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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('information');
            $table->unsignedInteger('price');
            $table->boolean('is_selling');
            $table->integer('sort_order')->nullable();
            // 親(shop)が削除されたら削除するので、onUpdate、onDeleteを書く
            $table->foreignId('shop_id')
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreignId('secondary_category_id')
                ->constrained();
            $table->foreignId('image1')
                ->nullable()
                ->constrained('images');    // image1というカラムがimagesのモデルに属していることを明示する
            $table->foreignId('image2')
                ->nullable()
                ->constrained('images');
            $table->foreignId('image3')
                ->nullable()
                ->constrained('images');
            $table->foreignId('image4')
                ->nullable()
                ->constrained('images');
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
        Schema::dropIfExists('products');
    }
};
