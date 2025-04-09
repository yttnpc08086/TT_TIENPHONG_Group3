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
            $table->id();
            $table->string('name'); // Tên sản phẩm
            $table->text('description'); // Mô tả sản phẩm
            $table->decimal('price', 8, 2); // Giá sản phẩm
            $table->integer('stock'); // Số lượng tồn kho
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Khóa ngoại đến bảng categories
            $table->timestamps(); // Created at và Updated at
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
