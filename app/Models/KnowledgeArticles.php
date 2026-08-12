<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KnowledgeArticles extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'category',
        'author',
        'is_published',
    ];
}
