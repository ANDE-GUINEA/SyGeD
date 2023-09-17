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
        Schema::create('archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->nullable();
            $table->foreignId('decret_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->nullable();
            $table->foreignId('type_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->nullable();
            $table->string('code');
            $table->string('init')->nullable();
            $table->string('objet')->nullable();
            $table->longText('content')->nullable();
            $table->string('motif')->nullable();
            $table->string('references')->nullable();
            $table->string('visa')->nullable();
            $table->string('confidential')->nullable();
            $table->string('autres')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('archives');
    }
};
