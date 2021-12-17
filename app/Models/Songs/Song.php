<?php

namespace App\Models\Songs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Song extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $fillable = [
        'name',
        'duration',
        'description',
        'slug',
    ];

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('audio')
            ->useDisk('audio')
            ->acceptsMimeTypes(['audio/mp3', 'audio/mpeg'])
            ->singleFile();
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(SongAuthor::class, 'author_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(SongTag::class, 'song_tag', 'song_id', 'tag_id');
    }
}
