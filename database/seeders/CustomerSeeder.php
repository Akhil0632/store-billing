<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = [
            ['name' => 'Rahul Sharma',     'email' => 'rahul.sharma@example.com'],
            ['name' => 'Priya Nair',       'email' => 'priya.nair@example.com'],
            ['name' => 'Arjun Mehta',      'email' => 'arjun.mehta@example.com'],
            ['name' => 'Sneha Reddy',      'email' => 'sneha.reddy@example.com'],
            ['name' => 'Vikram Singh',     'email' => 'vikram.singh@example.com'],
            ['name' => 'Ananya Iyer',      'email' => 'ananya.iyer@example.com'],
            ['name' => 'Karthik Subramanian', 'email' => 'karthik.subramanian@example.com'],
            ['name' => 'Meera Joshi',      'email' => 'meera.joshi@example.com'],
            ['name' => 'Rohit Verma',      'email' => 'rohit.verma@example.com'],
            ['name' => 'Divya Pillai',     'email' => 'divya.pillai@example.com'],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['email' => $customer['email']],
                ['name'  => $customer['name']]
            );
        }
    } 
}
