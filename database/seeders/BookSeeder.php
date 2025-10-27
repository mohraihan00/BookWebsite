<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Book;
use App\Models\Category;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();

        // Get all category IDs
        $categoryIds = Category::pluck('id')->toArray();

        // Array of realistic publishers
        $publishers = [
            'Penguin Books', 'Harper Collins', 'Random House', 'Simon & Schuster',
            'Macmillan', 'Hachette', 'Scholastic', 'Wiley', 'McGraw-Hill',
            'Oxford University Press', 'Cambridge University Press', 'Pearson'
        ];

        // Create 100 books
        for ($i = 1; $i <= 100; $i++) {
            Book::create([
                'title' => $this->generateTitle($faker),
                'author' => $faker->name(),
                'isbn' => $this->generateUniqueISBN($faker),
                'category_id' => $faker->randomElement($categoryIds),
                'description' => rand(0, 1) ? $faker->paragraphs(rand(1, 3), true) : null,
                'publisher' => $faker->randomElement($publishers),
                'publication_year' => $faker->numberBetween(1990, 2024),
                'stock' => $faker->numberBetween(0, 50),
                'price' => $faker->randomFloat(2, 5.99, 49.99),
                'cover_image' => rand(0, 1) ? "https://picsum.photos/400/600?random={$i}" : null,
            ]);
        }
    }

    /**
     * Generate a book title
     */
    private function generateTitle($faker)
    {
        return ucwords($faker->words(rand(2, 5), true));
    }

    /**
     * Generate a unique ISBN
     */
    private function generateUniqueISBN($faker)
    {
        do {
            $isbn = '978-' . $faker->numberBetween(0, 9) . '-' .
                    $faker->numberBetween(1000, 9999) . '-' .
                    $faker->numberBetween(1000, 9999) . '-' .
                    $faker->numberBetween(0, 9);
        } while (Book::where('isbn', $isbn)->exists());

        return $isbn;
    }
}
