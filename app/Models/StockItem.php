<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockItem extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    public $incrementing = false;

    public $fillable = ['category_id', 'name', 'price'];
}
