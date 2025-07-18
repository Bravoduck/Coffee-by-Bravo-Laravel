<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Cast 'customizations' dari JSON ke array PHP secara otomatis
    protected $casts = [
        'customizations' => 'array',
    ];
}