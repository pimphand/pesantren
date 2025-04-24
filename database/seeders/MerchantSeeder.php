<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $merchant = Merchant::firstOrCreate([
            'user_id' => 7,
        ], [
            'name' => 'Toko A',
            'phone' => '08123456789',
            'address' => 'Jl. Raya No. 1',
            'photo' => 'toko-a.jpg',
            'description' => 'Toko A adalah toko yang menjual makanan',
            'category' => 'kantin',
        ]);

        $category = ProductCategory::firstOrCreate([
            'merchant_id' => $merchant->id,
            'name' => 'Makanan',
        ]);

        $product = Product::firstOrCreate([
            'merchant_id' => $merchant->id,
            'name' => 'Nasi Goreng',
        ], [
            'price' => 15000,
            'description' => 'Nasi goreng spesial',
            'photo' => 'nasi-goreng.jpg',
            'stock' => 10,
            'category_id' => $category->id,
        ]);

        for ($i = 1; $i <= 5; $i++) {
            $order = Order::create([
                'user_id' => 6,
                'merchant_id' => $merchant->id,
                'status' => 'pending',
                'total' => $product->price,
                'invoice_number' => 'INV/2025/03/17/' . str_pad($i, 5, '0', STR_PAD_LEFT),
            ]);

            $order->orderItems()->create([
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => $product->price,
            ]);

            $order->payment()->create([
                'amount' => $product->price,
                'status' => 'paid',
                'payment_type' => 'Transaction',
                'user_id' => 6,
                'payment_method' => 'QRCODE',
            ]);

            $order->user->balanceHistories()->create([
                'type' => 'transaction',
                'balance' => $order->user->balance,
                'debit' => $product->price,
                'amount' => $order->user->balance - $product->price,
                'reference_id' => $order->id,
                'reference_type' => Order::class,
                'description' => 'Membeli Makanan di ' . $order->merchant->name,
            ]);

            $order->user->balance -= $product->price;
            $order->user->save();
        }

        $order->user->balanceHistories()->create([
            'type' => 'top up',
            'balance' => $order->user->balance,
            'debit' => $product->price,
            'amount' => $order->user->balance - $product->price,
            'reference_id' => $order->id,
            'reference_type' => Order::class,
            'description' => 'Membeli Makanan di ' . $order->merchant->name,
        ]);

        for ($i = 0; $i < 20; $i++) {
            $product = Product::firstOrCreate([
                'merchant_id' => $merchant->id,
                'name' => 'Nasi Goreng ' . $i,
            ], [
                'price' => 15000,
                'description' => 'Nasi goreng spesial ' . $i,
                'photo' => 'nasi-goreng.jpg',
                'stock' => 10,
                'category_id' => $category->id,
            ]);
        }
    }
}
