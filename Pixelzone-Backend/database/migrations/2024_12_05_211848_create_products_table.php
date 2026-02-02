<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama produk
            $table->text('description'); // Deskripsi produk
            $table->integer('price'); // Harga produk
            $table->unsignedBigInteger('category_id'); // Relasi ke tabel kategori
            $table->string('image'); // Kolom untuk menyimpan URL gambar
            $table->timestamps();

            // Relasi Foreign Key
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}

