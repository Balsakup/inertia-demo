<?php

namespace App\Console\Commands\Crawler;

use App\Models\Songs\Song;
use App\Models\Songs\SongAuthor;
use App\Models\Songs\SongTag;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\DomCrawler\Crawler;
use wapmorgan\Mp3Info\Mp3Info;

class SongsCommand extends Command
{
    protected $signature = 'crawler:songs';

    protected $description = 'Crawl bensound.com to seed database';

    protected SongAuthor $songAuthor;

    /**
     * @return void
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist
     * @throws \Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->songAuthor = SongAuthor::firstOrCreate([
            'slug' => Str::slug('Benjamin Tissot'),
        ], [
            'name' => 'Benjamin Tissot',
            'website' => 'https://www.bensound.com/',
        ]);

        $songNodes = (new Crawler(file_get_contents('https://www.bensound.com/')))
            ->filter('#products_grid .bloc_cat .player');
        $progress = $this->output->createProgressBar($songNodes->count());

        $progress->setFormat(ProgressBar::FORMAT_DEBUG);
        $songNodes->each(function (Crawler $songNode) use ($progress) {
            $songUrl = (string) Str::of($songNode
                ->filter('.bouton_play > audio')->attr('src'))
                ->prepend('https://www.bensound.com');
            $songCrawler = new Crawler(file_get_contents($songNode->filter('.bouton_play + a')->attr('href')));
            $name = (string) Str::of($songCrawler->filter('#titre_focus > h2 > a')->text())->before('|')->trim(' ');
            $description = $songCrawler->filter('#info_focus .product_info .acc p:nth-child(4)')->text();
            $imageUrl = (string) Str::of($songCrawler
                ->filter('#info_focus #img_focus > img')->attr('src'))
                ->prepend('https://www.bensound.com/');
            $tags = $songCrawler
                ->filter('#info_focus .taglist .tag')
                ->each(static fn(Crawler $tagNode) => $tagNode->text());
            $song = $this->createSong($name, $description, $tags);
            $audio = $song->addMediaFromUrl($songUrl)->toMediaCollection('audio');
            $song->duration = (new Mp3Info($audio->getPath()))->duration;

            $song->addMediaFromUrl($imageUrl)->toMediaCollection('cover');
            $song->save();
            $progress->advance();
        });
        $progress->finish();
        $progress->clear();
    }

    protected function createSong(string $name, string $description, array $tags): Song
    {
        /** @var \App\Models\Songs\Song $song */
        $song = $this
            ->songAuthor
            ->songs()
            ->firstOrCreate([
                'slug' => Str::slug($name),
            ], [
                'name' => $name,
                'description' => $description,
            ]);
        $song->tags()->sync($this->createTags($song, $tags));

        return $song;
    }

    protected function createTags(Song $song, array $tags): array
    {
        $tagsId = [];

        foreach ($tags as $tag) {
            $tagsId[] = SongTag::firstOrCreate([
                'slug' => Str::slug($tag),
            ], [
                'name' => $tag,
            ])
                ->id;
        }

        return $tagsId;
    }
}
