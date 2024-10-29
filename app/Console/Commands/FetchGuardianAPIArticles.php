<?php

namespace App\Console\Commands;

use App\Helpers\Helper;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Article;

class FetchGuardianAPIArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-guardian-a-p-i-articles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch articles from the Guardian API and save them to the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // $fromTime = Carbon::now()->subHours(3.5)->toISOString();
        $apiKey = env('GUARDIAN_API_KEY','e97b49ab-9e4b-4b17-8471-0957962a1af1');
        $url = "https://content.guardianapis.com/search?api-key=$apiKey";

        $response = Http::get($url);

        if ($response->successful()) {
            $articles = $response->json()['response']['results'];

            foreach ($articles as $articleData) {
                // Check for existing article by title
                if (!Article::where('source_id', $articleData['id'])->exists()) {

                    $publishedAt = $articleData['webPublicationDate']?Helper::formatDate($articleData['webPublicationDate']):NULL;
                    // Create new article record
                    Article::create([
                        'source_id' => $articleData['id'] ?? null,
                        'source_name' => 'Guardian api' ?? null,
                        'author' => $articleData['author'] ?? null,
                        'title' => $articleData['webTitle'],
                        'description' => $articleData['description'] ?? $articleData['webTitle'],
                        'url' => $articleData['webUrl'] ?? null,
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
