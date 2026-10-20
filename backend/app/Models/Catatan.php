<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Catatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nama_catatan',
        'pemateri',
        'tempat',
        'tanggal',
        'isi_catatan',
        'referensi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'referensi' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
