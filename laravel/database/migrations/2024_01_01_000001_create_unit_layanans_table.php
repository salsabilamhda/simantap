<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_layanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->string('unit_induk')->default('UP3 Ponorogo');
            $table->string('color_hex')->default('#2BA8A2');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_layanans');
    }
};
