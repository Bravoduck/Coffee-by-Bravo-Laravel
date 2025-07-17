<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    /**
     * Relasi ke Category: Satu produk adalah milik satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi ke OptionGroup: Satu produk bisa punya banyak grup opsi.
     */
    public function optionGroups(): BelongsToMany
    {
        return $this->belongsToMany(OptionGroup::class, 'option_product');
    }

    /**
     * Relasi ke Varian (Anak): Satu produk induk bisa punya banyak varian.
     */
    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    /**
     * Relasi ke Induk: Satu varian adalah milik satu produk induk.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }
}