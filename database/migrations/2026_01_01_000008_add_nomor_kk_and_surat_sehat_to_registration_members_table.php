<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            if (!Schema::hasColumn('registration_members', 'nomor_kk')) {
                $table->string('nomor_kk', 16)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('registration_members', 'surat_sehat_path')) {
                $table->string('surat_sehat_path')->nullable()->after('kk_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('registration_members', function (Blueprint $table) {
            $columns = array_filter(
                ['nomor_kk', 'surat_sehat_path'],
                fn (string $column) => Schema::hasColumn('registration_members', $column)
            );

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
