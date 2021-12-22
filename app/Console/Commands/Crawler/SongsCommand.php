<?php

namespace App\Console\Commands\Crawler;

use App\Models\Songs\Song;
use App\Models\Songs\SongAuthor;
use App\Models\Songs\SongTag;
use App\Models\Songs\SongType;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use wapmorgan\Mp3Info\Mp3Info;

class SongsCommand extends Command
{
    protected $signature = 'crawler:songs';

    protected $description = 'Crawl bensound.com to seed database';

    /**
     * @return void
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Exception
     */
    public function handle(): void
    {
        $songAuthor = SongAuthor::firstOrCreate([
            'slug' => Str::slug('Benjamin Tissot'),
        ], [
            'name' => 'Benjamin Tissot',
            'website' => 'https://www.bensound.com/',
        ]);

        $this->getSongsTypes()->each(function (array $songTypeData) use ($songAuthor) {
            $songType = SongType::firstOrCreate([
                'slug' => Str::slug($songTypeData['name']),
            ], [
                'name' => $songTypeData['name'],
            ]);
            $songs = $this->getSongs($songTypeData['url']);
            $progress = $this->output->createProgressBar($songs->count());

            $progress->setFormat(' [%message%] %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%');
            $progress->setMessage($songTypeData['name']);
            $songs->each(function (array $songDataUrl) use ($songType, $songAuthor, $progress) {
                $songData = $this->getSong($songDataUrl['url']);

                if (str_ends_with($songData['audio'], 'mp3')) {
                    $song = Song::firstOrNew([
                        'slug' => Str::slug($songData['name']),
                    ], [
                        'name' => $songData['name'],
                        'description' => $songData['description'],
                    ]);

                    $song->author()->associate($songAuthor);
                    $song->save();
                    $song->types()->attach($songType);
                    $song->addMediaFromUrl($songData['cover'])->toMediaCollection('cover');

                    $audio = $song->addMediaFromUrl($songData['audio'])->toMediaCollection('audio');
                    $song->duration = (new Mp3Info($audio->getPath()))->duration;

                    $song->save();
                }

                $progress->advance();
            });

            $progress->finish();
            $progress->clear();
        });
    }

    protected function getSongsTypes(): Collection
    {
        return collect((new Crawler(file_get_contents('https://www.bensound.com/')))
            ->filter('#menu > ul > li > a')
            ->each(static fn(Crawler $songTypeNode) => [
                'name' => $songTypeNode->text(),
                'url' => $songTypeNode->attr('href'),
            ]))
            ->skip(1);
    }

    protected function getSongs(string $typeUrl): Collection
    {
        return collect((new Crawler(file_get_contents($typeUrl)))
            ->filter('#products_grid > .bloc_cat .player > a')
            ->each(static fn(Crawler $songNode) => [
                'url' => $songNode->attr('href'),
            ]));
    }

    protected function getSong(string $songUrl): array
    {
        $songCrawler = new Crawler(file_get_contents($songUrl));
        $songName = (string) Str::of($songCrawler
            ->filter('#pr_style_two > .focus_music > #titre_focus > h2 > a')
            ->text())
            ->before('|')
            ->trim(' ');
        $songCover = $songCrawler
            ->filter('#pr_style_two > .focus_music > #info_focus #img_focus > img')
            ->attr('src');
        $songDescription = $songCrawler
            ->filter('#pr_style_two > .focus_music > #info_focus .product_info > .acc > p:nth-child(4)')
            ->text();
        $songAudio = $songCrawler
            ->filter('#pr_style_two > .focus_music > #info_focus #player_focus > .player > audio')
            ->attr('src');

        return [
            'name' => $songName,
            'description' => $songDescription,
            'cover' => "https://www.bensound.com/$songCover",
            'audio' => "https://www.bensound.com$songAudio",
        ];
    }
}
