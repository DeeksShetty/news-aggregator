<?php

namespace Database\Seeders;

use App\Models\Article;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ArticleSeeder extends Seeder
{
    public function run()
    {
        Article::insert([
            [
                'source_id' => 'education/2024/oct/30/how-to-pay-less-for-better-special-educational-needs-provision',
                'source_name' => 'Guardian api',
                'author' => 'Jane Smith',
                'title' => 'How to pay less for better special educational needs provision',
                'description' => 'A detailed article about improving education quality.',
                'url' => 'https://www.theguardian.com/education/2024/oct/30/how-to-pay-less-for-better-special-educational-needs-provision',
                'url_to_image' => null,
                'published_at' => Carbon::now()->subDays(1),
                'content' => 'Content of the article goes here..',
                'category' => 'Education',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'source_id' => 'society/2024/oct/30/counsel-against-statutory-regulation-psychotherapists',
                'source_name' => 'Guardian api',
                'author' => 'John Doe',
                'title' => 'Why I would counsel against statutory regulation of psychotherapists',
                'description' => 'Discussion on psychotherapist regulation.',
                'url' => 'https://www.theguardian.com/society/2024/oct/30/counsel-against-statutory-regulation-psychotherapists',
                'url_to_image' => null,
                'published_at' => Carbon::now()->subDays(2),
                'content' => 'Content of the article goes here..',
                'category' => 'Society',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}