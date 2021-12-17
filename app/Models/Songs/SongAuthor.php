<?php

namespace App\Models\Songs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SongAuthor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'website',
        'slug',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class, 'author_id');
    }
}
