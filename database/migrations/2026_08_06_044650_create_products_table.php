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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->string('product_code')->unique();
            $table->string('product_name');
            $table->string('hs_code')->nullable();
            $table->decimal('import_duty', 5, 2)->nullable(); // percentage
            $table->string('facility')->nullable(); // country of origin
            $table->string('description')->nullable();
            $table->string('brand')->nullable();
            $table->string('category')->nullable();
            $table->string('unit')->nullable();
            $table->string('country_of_origin')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
