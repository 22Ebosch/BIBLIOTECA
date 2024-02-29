<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table -> unsignedBigInteger('user_id');
            $table -> foreign('user_id')->references('id')->on('users');
            $table -> unsignedBigInteger('libro_id');
            $table -> foreign('libro_id')->references('id')->on('libros');
            $table->string('resena', 5000);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};