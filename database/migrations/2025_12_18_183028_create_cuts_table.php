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
        Schema::create('cuts', function (Blueprint $table) {
            $table->id();

            // Tipo de corte
            $table->enum('type', ['x', 'z']);

            // Quién realiza el corte
            $table->string('clerk');

            // Totales según el sistema
            $table->decimal('cash_system', 10, 2);
            $table->decimal('card_system', 10, 2);
            $table->decimal('total_system', 10, 2);

            // Totales contados físicamente
            $table->decimal('cash_counted', 10, 2);
            $table->decimal('card_counted', 10, 2);
            $table->decimal('total_counted', 10, 2);

            // Diferencia
            $table->decimal('difference', 10, 2);
            // Relacion con los lotes
            $table->foreignId('lot_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuts');
    }
};
