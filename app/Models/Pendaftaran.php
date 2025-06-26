<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendaftaran extends Model
{
    use HasFactory;

    protected $table = 'pendaftarans';

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal_pendaftaran' => 'date',
        'status_changed' => 'boolean',
    ];

    public function child()
    {
        return $this->belongsTo(FamilyChildren::class, 'children_id');
    }

    public function parent()
    {
        return $this->belongsTo(FamilyParent::class, 'parent_id');
    }

    public function getNamaPendaftarAttribute()
    {
        if ($this->sasaran === 'balita' && $this->child) {
            return $this->child->fullname;
        } elseif ($this->sasaran === 'ibu_hamil' && $this->parent) {
            return $this->parent->mother_fullname;
        }
        return '-';
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'menunggu' ? 'Menunggu' : 'Sudah Dilayani';
    }

    public function getSasaranLabelAttribute()
    {
        return $this->sasaran === 'balita' ? 'Balita' : 'Ibu Hamil';
    }
}
