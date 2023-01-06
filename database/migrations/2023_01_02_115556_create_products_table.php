<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->string('ProductCode')->primary()->unique();
            $table->string('ProductDescription');
            $table->string('ProductNumberCode')->unique();
            $table->string('ProductCategory')->default('M');
            $table->string('UnitOfMeasure')->default('Unidade');
            $table->decimal('PriceCost', 10,4);
            // $table->decimal('PriceSale', 10,4);
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
}
