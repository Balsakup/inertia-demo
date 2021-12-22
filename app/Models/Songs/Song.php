<?php

namespace App\Models\Songs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Song extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use HasEagerLimit;

    protected $fillable = [
        'name',
        'duration',
        'description',
        'slug',
    ];

    protected $appends = [
        'cover',
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

    public function types(): BelongsToMany
    {
        return $this->belongsToMany(SongType::class, 'song_type', 'song_id', 'type_id');
    }

    public function getCoverAttribute(): Collection
    {
        $cover = $this->getFirstMedia('cover');

        if (! $cover) {
            return collect();
        }

        return $cover
            ->getGeneratedConversions()
            ->map(static fn(bool $generated, string $conversionName) => $cover->getUrl($conversionName));
    }
}
