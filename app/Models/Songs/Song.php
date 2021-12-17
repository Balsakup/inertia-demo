<?php

namespace App\Models\Songs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

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
        $this
            ->addMediaCollection('cover')
            ->useDisk('cover')
            ->acceptsMimeTypes(['image/png', 'image/jpg', 'image/jpeg', 'image/webp'])
            ->singleFile();
    }

    /**
     * @param \Spatie\MediaLibrary\MediaCollections\Models\Media|null $media
     *
     * @return void
     * @throws \Spatie\Image\Exceptions\InvalidManipulation
     */
    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('normal')
            ->fit(Manipulations::FIT_CROP, 250, 250)
            ->shouldBePerformedOn('cover');
        $this
            ->addMediaConversion('small')
            ->fit(Manipulations::FIT_CROP, 64, 64)
            ->shouldBePerformedOn('cover');
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
