<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'marketing_id', 'tanggal_transfer', 'tanggal_lead_masuk', 'periode_lead_bulan',
        'nama_lengkap_mitra', 'no_wa', 'umur', 'pekerjaan', 'product_id', 'lead_awal',
        'sumber', 'kabupaten', 'provinsi', 'status', 'nominal_masuk', 'harga_paket'
    ];

    protected $casts = [
        'tanggal_transfer' => 'date',
        'tanggal_lead_masuk' => 'date',
    ];

    public function marketing()
    {
        return $this->belongsTo(User::class, 'marketing_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
