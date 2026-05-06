<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('department')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('commune_id')->nullable()->after('role')->constrained()->nullOnDelete();
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignId('commune_id')->nullable()->after('user_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commune_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('commune_id');
        });

        Schema::dropIfExists('communes');
    }
};
