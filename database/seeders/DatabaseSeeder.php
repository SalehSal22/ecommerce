<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed 100 Users with a known password
        $password = 'password123';
        $users = User::factory(100)->create([
            'password' => Hash::make($password),
        ]);

        // Export user credentials to JSON for the k6 stress test
        $k6Users = $users->map(fn(User $user) => [
            'email' => $user->email,
            'password' => $password,
        ]);
        file_put_contents(base_path('users.json'), $k6Users->toJson(JSON_PRETTY_PRINT));

        // 2. Seed Admins
        $admins = collect([
            ['name' => 'Admin 1', 'email' => 'admin1@example.com'],
            ['name' => 'Admin 2', 'email' => 'admin2@example.com'],
            ['name' => 'Admin 3', 'email' => 'admin3@example.com'],
            ['name' => 'Admin 4', 'email' => 'admin4@example.com'],
            ['name' => 'Admin 5', 'email' => 'admin5@example.com'],
            ['name' => 'Admin 6', 'email' => 'admin6@example.com'],
            ['name' => 'Admin 7', 'email' => 'admin7@example.com'],
            ['name' => 'Admin 8', 'email' => 'admin8@example.com'],
            ['name' => 'Admin 9', 'email' => 'admin9@example.com'],
            ['name' => 'Admin 10', 'email' => 'admin10@example.com'],
        ])->map(fn(array $admin) => Admin::create([
            'name' => $admin['name'],
            'email' => $admin['email'],
            'password' => Hash::make('password'),
        ]));

        // 3. Seed 20 Products
        // Note: Make sure your ProductFactory sets a limited stock (e.g., 'stock' => 50)
        // to properly test the overselling database locks.
        $products = Product::factory(20)->create();

        // 4. Seed 10,000 Orders dated now (batched to avoid memory issues)
        $now = now()->toDateTimeString();
        $batchSize = 500; // insert in batches to keep memory usage low
        $totalOrders = 10000;

        for ($i = 0; $i < $totalOrders; $i += $batchSize) {
            $batch = [];
            $limit = min($batchSize, $totalOrders - $i);

            for ($j = 0; $j < $limit; $j++) {
                $batch[] = [
                    'user_id' => $users->random()->id,
                    'total' => mt_rand(1000, 50000) / 100, // random total between 10.00 and 500.00
                    'status' => 'placed',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('orders')->insert($batch);
        }
    }
}
