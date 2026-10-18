<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistrationMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'tipe',
        'full_name',
        'nik',
        'nomor_kk',
        'tanggal_lahir',
        'posisi',
        'nomor_punggung',
        'jabatan',
        'ktp_path',
        'kk_path',
        'pasfoto_path',
        'surat_sehat_path',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
