<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desifre extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'desifre_draft',
        'desifre_title',
        'desifre',
        'active',
    ];
}
