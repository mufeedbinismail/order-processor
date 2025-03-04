<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemType extends Model
{
    use HasFactory;

    const USER = 1;
    const CUSTOMER = 2;
    const ORDER = 3;
    const ITEM = 4;
    const ITEM_CATEGORY = 5;
}
