<?php

use Illuminate\Database\Seeder;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        \App\Models\Category::create([
            'name'	        => 'Concert'
        ]);

        factory(\App\Models\Article::class, 10)->create()->each(function($post) use ($faker) {
            $post->category_id = 1;
            $post->user_id = 1;
        });
    }
}
