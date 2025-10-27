<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Fiction',
                'slug' => 'fiction',
                'description' => 'Novels and fictional literature'
            ],
            [
                'name' => 'Non-Fiction',
                'slug' => 'non-fiction',
                'description' => 'Factual books and real-life stories'
            ],
            [
                'name' => 'Science',
                'slug' => 'science',
                'description' => 'Scientific research and discoveries'
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Computing, programming, and tech trends'
            ],
            [
                'name' => 'History',
                'slug' => 'history',
                'description' => 'Historical events and biographies'
            ],
            [
                'name' => 'Biography',
                'slug' => 'biography',
                'description' => 'Life stories of notable people'
            ],
            [
                'name' => 'Children',
                'slug' => 'children',
                'description' => 'Books for young readers'
            ],
            [
                'name' => 'Fantasy',
                'slug' => 'fantasy',
                'description' => 'Fantasy and magical adventures'
            ],
            [
                'name' => 'Mystery',
                'slug' => 'mystery',
                'description' => 'Mystery, thriller, and crime novels'
            ],
            [
                'name' => 'Romance',
                'slug' => 'romance',
                'description' => 'Love stories and romantic fiction'
            ],
            [
                'name' => 'Self-Help',
                'slug' => 'self-help',
                'description' => 'Personal development and improvement'
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Business strategy and entrepreneurship'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
