<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // Blog::factory(15)->create();

        User::factory(15)->create()->each(function ($user) {
            Blog::factory(random_int(2, 5))->seeding()->create(['user_id' => $user])->each(function ($blog) {
                Comment::factory(random_int(1, 3))->create(['blog_id' => $blog]);
            });
        });

        User::first()->update([
            'name' => '自分',
            'email' => 'aaa@bbb.net',
            'password' => bcrypt('hogehoge'),
        ]);

    }
}
