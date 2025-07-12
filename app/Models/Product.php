<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Beritahu Laravel kolom mana saja yang boleh diisi
    protected $guarded = [];
}