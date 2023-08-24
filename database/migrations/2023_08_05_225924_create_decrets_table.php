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
            $table->string('code');
            $table->string('objet')->nullable();
            $table->longText('content')->nullable();
            $table->enum('status', ['En attente', 'En cours', 'Approuvé', 'Publié'])->default('En attente');
            $table->boolean('okSGG')->default(false);
            $table->boolean('okPRIMATURE')->default(false);
            $table->boolean('okPRG')->default(false);
            $table->string('documents')->nullable();
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
