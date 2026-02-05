<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title'); 
            $table->string('client_name');
            $table->date('date');
            $table->string('payment_method');
            $table->text('description')->nullable();
            
            // Financial fields
            $table->decimal('amount_in', 15, 2)->default(0);
            $table->decimal('fees', 15, 2)->default(0);
            $table->decimal('amount_out', 15, 2)->default(0);

            // Feedback fields
            $table->text('feedback')->nullable();
            $table->date('feedback_date')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};