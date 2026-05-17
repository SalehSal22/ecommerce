<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(10)->create();
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
        ])->map(fn (array $admin) => Admin::create([
            'name' => $admin['name'],
            'email' => $admin['email'],
            'password' => Hash::make('password'),
        ]));
        $products = Product::factory(10)->create();

        $carts = $users->map(fn (User $user) => Cart::create([
            'user_id' => $user->id,
        ]));

        $productPool = $products->values();

        foreach ($carts as $index => $cart) {
            $product = $productPool[$index % $productPool->count()];
            $quantity = random_int(1, 3);

            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
            ]);
        }

        foreach ($users as $index => $user) {
            $product = $productPool[$index % $productPool->count()];
            $quantity = random_int(1, 3);
            $price = (float) $product->price;
            $subtotal = $price * $quantity;

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $subtotal,
                'status' => 'placed',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
            ]);
        }
    }
}
