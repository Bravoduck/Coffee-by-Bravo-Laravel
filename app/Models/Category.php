<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // <-- Tambahkan ini

class Category extends Model
{
    use HasFactory;

    // Lewati baris ini jika sudah ada
    protected $guarded = [];

    /**
     * Mendefinisikan bahwa satu Category memiliki banyak Product.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}