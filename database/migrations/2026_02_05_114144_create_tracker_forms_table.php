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
       Schema::create('tracker_forms', function (Blueprint $table) {
        $table->id();
        $table->string('client_name');
        $table->date('date');
        $table->string('payment_method');
        $table->text('description')->nullable();
        $table->decimal('amount_in', 10, 2)->nullable();
        $table->decimal('fees', 10, 2)->nullable()->default(0);
        $table->decimal('amount_out', 10, 2);
        $table->text('feedback')->nullable();
        $table->date('feedback_date')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracker_forms');
    }
};
