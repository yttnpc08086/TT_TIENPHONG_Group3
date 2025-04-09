<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();  // Auto-incrementing primary key for 'id'
            $table->string('customerName');  // Customer's name
            $table->integer('quantity');  // Quantity of the ordered product
            $table->foreignId('product_id')->constrained()->onDelete('cascade');  // Foreign key to the 'products' table
            $table->decimal('total', 10, 2);  // Total cost of the order
            $table->timestamps();  // Created at and updated at timestamps
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
