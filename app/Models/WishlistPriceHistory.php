<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WishlistPriceHistory extends Model
{
    protected $fillable = ['wishlist_id', 'price', 'currency', 'source', 'recorded_at'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'recorded_at' => 'datetime'];
    }

    public function wishlist()
    {
        return $this->belongsTo(Wishlist::class);
    }
}
