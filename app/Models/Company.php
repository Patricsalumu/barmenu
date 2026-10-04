<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'slogan', 'address', 'logo_path', 'theme_color',
        'currency', 'exchange_rate', 'owner_code',
    ];

    protected $hidden = ['owner_code'];

    protected function casts(): array
    {
        return ['exchange_rate' => 'decimal:2', 'owner_code' => 'hashed'];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('position');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
