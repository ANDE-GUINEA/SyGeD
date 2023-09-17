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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('departement_id')
                // ->constrained()
                // ->cascadeOnDelete()
                ->nullable();
            $table->foreignId('worker_id')
                // ->constrained()
                // ->cascadeOnDelete()
                ->nullable();
            // $table->foreignId('worker_id')
            //     // ->constrained()
            //     // ->cascadeOnDelete()
            //     ->nullable();
            $table->boolean('IsAdmin')->default(false);
            $table->boolean('IsWorker')->default(false);
            $table->string('name');
            $table->string('fonction')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};