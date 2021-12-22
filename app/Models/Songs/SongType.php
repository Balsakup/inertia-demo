<?php

namespace App\Models\Songs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class SongType extends Model
{
    use HasFactory;
    use HasEagerLimit;

    protected $fillable = [
        'name',
        'slug',
    ];

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'song_type', 'type_id', 'song_id');
    }

    public function latestSongs(): BelongsToMany
    {
        return $this
            ->belongsToMany(Song::class, 'song_type', 'type_id', 'song_id')
            ->limit(8);
    }
}
