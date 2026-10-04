<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = [
        'company_id', 'category_id', 'name', 'description', 'price', 'image_path', 'is_available',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'is_available' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
