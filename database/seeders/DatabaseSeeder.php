<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@competition.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'university' => 'Admin University',
        ]);

        // Create Exam Manager User
        User::create([
            'name' => 'Exam Manager',
            'email' => 'exam@competition.com',
            'password' => bcrypt('password'),
            'role' => 'exam_manager',
            'university' => 'Manager University',
        ]);

        // Create Sample University Users
        $universities = [
            'Harvard University',
            'MIT',
            'Stanford University',
            'University of Cambridge',
            'Oxford University',
            'UC Berkeley',
            'Carnegie Mellon',
            'Princeton University',
        ];

        foreach ($universities as $university) {
            User::create([
                'name' => 'Student from ' . $university,
                'email' => strtolower(str_replace(' ', '.', $university)) . '@student.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'university' => $university,
            ]);
        }

        // Create Sample Questions
        $questions = [
            [
                'title' => 'What is the capital of France?',
                'option_a' => 'London',
                'option_b' => 'Berlin',
                'option_c' => 'Paris',
                'option_d' => 'Madrid',
                'correct_answer' => 'C',
                'category' => 'Geography',
            ],
            [
                'title' => 'What is 2 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'correct_answer' => 'B',
                'category' => 'Mathematics',
            ],
            [
                'title' => 'Which programming language is known as the "language of the web"?',
                'option_a' => 'Python',
                'option_b' => 'JavaScript',
                'option_c' => 'Java',
                'option_d' => 'C++',
                'correct_answer' => 'B',
                'category' => 'Computer Science',
            ],
            [
                'title' => 'What is the largest planet in our solar system?',
                'option_a' => 'Earth',
                'option_b' => 'Mars',
                'option_c' => 'Jupiter',
                'option_d' => 'Saturn',
                'correct_answer' => 'C',
                'category' => 'Science',
            ],
            [
                'title' => 'Who wrote "Romeo and Juliet"?',
                'option_a' => 'Charles Dickens',
                'option_b' => 'William Shakespeare',
                'option_c' => 'Jane Austen',
                'option_d' => 'Mark Twain',
                'correct_answer' => 'B',
                'category' => 'Literature',
            ],
            [
                'title' => 'What is the chemical symbol for gold?',
                'option_a' => 'G',
                'option_b' => 'Au',
                'option_c' => 'Ag',
                'option_d' => 'Go',
                'correct_answer' => 'B',
                'category' => 'Chemistry',
            ],
            [
                'title' => 'How many continents are there on Earth?',
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'correct_answer' => 'C',
                'category' => 'Geography',
            ],
            [
                'title' => 'What is the square root of 144?',
                'option_a' => '10',
                'option_b' => '11',
                'option_c' => '12',
                'option_d' => '13',
                'correct_answer' => 'C',
                'category' => 'Mathematics',
            ],
            [
                'title' => 'Which year did World War II end?',
                'option_a' => '1944',
                'option_b' => '1945',
                'option_c' => '1946',
                'option_d' => '1947',
                'correct_answer' => 'B',
                'category' => 'History',
            ],
            [
                'title' => 'What is the smallest prime number?',
                'option_a' => '0',
                'option_b' => '1',
                'option_c' => '2',
                'option_d' => '3',
                'correct_answer' => 'C',
                'category' => 'Mathematics',
            ],
        ];

        foreach ($questions as $questionData) {
            \App\Models\Question::create($questionData);
        }
    }
}
