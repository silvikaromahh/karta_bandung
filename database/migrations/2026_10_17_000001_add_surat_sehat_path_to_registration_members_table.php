<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->string('surat_sehat_path')->nullable()->after('pasfoto_path');
        });
    }

    public function down(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $table->dropColumn('surat_sehat_path');
        });
    }
};
