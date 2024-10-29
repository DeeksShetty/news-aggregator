<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Article;

class FetchNewsAPIArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news-a-p-i-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the News API and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fromTime = Carbon::now()->subHours(3.5)->toISOString();
        $apiKey = env('NEWS_API_KEY','562dad6f4a0742c3b1ce91f147b5a960');
        $url = "https://newsapi.org/v2/top-headlines?sources=bbc-news&from=$fromTime&apiKey=$apiKey";

        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['articles'];

            foreach ($articles as $articleData) {
                // Check for existing article by title
                if (!Article::where('title', $articleData['title'])->exists()) {

                    $publishedAt = $articleData['publishedAt']?Helper::formatDate($articleData['publishedAt']):NULL;
                    // Create new article record
                    Article::create([
                        'source_id' => $articleData['source']['id'] ?? null,
                        'source_name' => $articleData['source']['name'] ?? null,
                        'author' => $articleData['author'] ?? null,
                        'title' => $articleData['title'],
                        'description' => $articleData['description'] ?? null,
                        'url' => $articleData['url'] ?? null,
                        'urlToImage' => $articleData['urlToImage'] ?? null,
                        'publishedAt' => $publishedAt,
                        'content' => $articleData['content'] ?? null,
                    ]);
                }
            }
            $this->info('Articles fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch articles: ' . $response->status());
        }
    }
}
