<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'created_date',
    ];

    // Mengonfigurasi agar 'created_date' sebagai objek Carbon untuk format tanggal
    protected $dates = ['created_date'];
}