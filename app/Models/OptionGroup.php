<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    // ▼▼▼ TAMBAHKAN FUNGSI INI ▼▼▼
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}