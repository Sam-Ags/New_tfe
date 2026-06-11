<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->string('completion_photo_path')->nullable()->after('photo_path');
            $table->text('completion_note')->nullable()->after('completion_photo_path');
            $table->timestamp('completion_submitted_at')->nullable()->after('completion_note');
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropColumn(['completion_photo_path', 'completion_note', 'completion_submitted_at']);
        });
    }
};
