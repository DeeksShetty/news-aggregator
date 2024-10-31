<?php

namespace Database\Seeders;

use App\Models\UserPreference;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'deekshith@gmail.com')->first();

        if ($user) {
            UserPreference::create([
                'user_id' => $user->id,
                'prefer_source' => 'BBC News,The Guardian',
                'prefer_categories' => 'Society,Education',
                'prefer_author' => 'John Doe,Jane Smith'
            ]);
        }
    }
}