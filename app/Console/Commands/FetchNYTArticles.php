<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Console\Command;

use Illuminate\Support\Facades\Http;
use App\Models\Article;

class FetchNYTArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:fetch-n-y-t-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the Newyork times API and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $apiKey = env('NYT_API_KEY','CU9ZYufQS07XM65YuAV4hO3hcpUp5GJh');
        $url = 'https://api.nytimes.com/svc/search/v2/articlesearch.json?q=a&api-key=' . $apiKey;

        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['response']['docs'];

            foreach ($articles as $articleData) {
                // Check for existing article by title
                if (!Article::where('source_id', $articleData['_id'])->exists()) {
                    $publishedAt = $articleData['pub_date']?Helper::formatDate($articleData['pub_date']):NULL;
                    // Create new article record
                    Article::create([
                        'source_id' => $articleData['_id'] ?? null,
                        'source_name' => $articleData['source'] ?? null,
                        'author' => $articleData['author'] ?? $articleData['source'],
                        'title' => $articleData['headline']['print_headline'],
                        'description' => $articleData['abstract'] ?? null,
                        'url' => $articleData['web_url'] ?? null,
                        'urlToImage' => $articleData['urlToImage'] ?? null,
                        'publishedAt' => $publishedAt,
                        'content' => $articleData['headline']['main'] ?? null,
                    ]);
                }
            }
            $this->info('Articles fetched and saved successfully.');
        } else {
            $this->error('Failed to fetch articles: ' . $response->status());
        }
    }
}
