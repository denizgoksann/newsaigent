<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'user_id',
        'news_title',
        'news_draft',
        'news',
        'uniq_words',
        'file',
        'editor',
        'location',
        'spot',
    ];
}
