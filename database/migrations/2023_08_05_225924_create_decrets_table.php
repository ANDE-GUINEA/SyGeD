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
        Schema::create('decrets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inbox_id')
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->nullable();
            $table->foreignId('user_id')
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
            $table->enum('status', ['En Elaboration', 'Examen SGG', 'Examen Primature', 'Examen Presidence', 'Retour SGG', 'Retour Primature', 'Retour Presidence', 'Signé'])->default('En Elaboration');
            $table->boolean('okSGG')->default(false);
            $table->boolean('okPRIMATURE')->default(false);
            $table->boolean('okPRG')->default(false);
            $table->boolean('Signé')->default(false);
            $table->string('motif')->nullable();
            $table->string('references')->nullable();
            $table->string('visa')->nullable();
            $table->string('corps')->nullable();
            $table->string('confidential')->nullable();
            $table->string('autres')->nullable();
            $table->string('signe')->nullable();
            $table->string('publie')->nullable();
            $table->dateTime('submit_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('decrets');
    }
};
