<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->string('citizen_name');
            $table->string('citizen_phone')->nullable();
            $table->string('title');
            $table->string('category');
            $table->string('district');
            $table->text('description');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('urgency')->default('normal');
            $table->string('priority')->default('moyenne');
            $table->string('status')->default('en_attente');
            $table->string('assigned_to')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamp('taken_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
