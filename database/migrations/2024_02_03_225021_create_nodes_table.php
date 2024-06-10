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
        Schema::create('nodes', function (Blueprint $table) {
            $table->id();
            $table->integer('project_id')->nullable();
            $table->integer('node_id')->nullable();
            $table->integer('tier')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->integer('type')->nullable();
            $table->json('distribution_shape')->nullable();
            $table->string('unite')->nullable();
            $table->json('formula')->nullable();
            
            $table->integer('hidden_table')->nullable();
            $table->integer('hidden_node')->nullable();

            $table->integer('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nodes');
    }
};
