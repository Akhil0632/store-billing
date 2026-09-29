<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            ['name' => 'Colgate Strong Teeth Toothpaste 100g', 'code' => 'COL-100', 'price' => 55.00,  'tax_percentage' => 18, 'stock' => 40],
            ['name' => 'Lifebuoy Soap 125g',                   'code' => 'LBW-125', 'price' => 35.00,  'tax_percentage' => 18, 'stock' => 60],
            ['name' => 'Dove Shampoo 340ml',                   'code' => 'DOV-340', 'price' => 299.00, 'tax_percentage' => 18, 'stock' => 15],
            ['name' => 'Ponds Face Wash 100g',                 'code' => 'PND-100', 'price' => 199.00, 'tax_percentage' => 18, 'stock' => 22],
            ['name' => 'Nivea Body Lotion 400ml',              'code' => 'NIV-400', 'price' => 425.00, 'tax_percentage' => 18, 'stock' => 14],
            ['name' => 'Parle-G Biscuit 250g',                 'code' => 'PAR-250', 'price' => 25.00,  'tax_percentage' => 18, 'stock' => 120],
            ['name' => 'Lays Classic Salted 52g',              'code' => 'LAY-052', 'price' => 20.00,  'tax_percentage' => 18, 'stock' => 3],
            ['name' => 'Haldiram Aloo Bhujia 200g',            'code' => 'HAL-200', 'price' => 55.00,  'tax_percentage' => 18, 'stock' => 30],
            ['name' => 'Dairy Milk Silk 60g',                  'code' => 'CDM-SLK', 'price' => 90.00,  'tax_percentage' => 18, 'stock' => 50],
            ['name' => 'Haldiram Soan Papdi 250g',             'code' => 'HAL-SPN', 'price' => 95.00,  'tax_percentage' => 18, 'stock' => 4],       
            ['name' => 'Modern Bread 400g',                    'code' => 'BRD-400', 'price' => 45.00,  'tax_percentage' => 5,  'stock' => 4],
            ['name' => 'Amul Milk 1L',                         'code' => 'AML-1L',  'price' => 68.00,  'tax_percentage' => 0,  'stock' => 9],
            ['name' => 'Eggs (Pack of 12)',                    'code' => 'EGG-012', 'price' => 84.00,  'tax_percentage' => 0,  'stock' => 2],
            ['name' => 'Tata Salt 1kg',                        'code' => 'TTL-1K',  'price' => 28.00,  'tax_percentage' => 5,  'stock' => 80],
            ['name' => 'Aashirvaad Atta 5kg',                  'code' => 'AAS-5K',  'price' => 275.00, 'tax_percentage' => 5,  'stock' => 25],
            ['name' => 'Tata Tea Gold 500g',                   'code' => 'TEA-500', 'price' => 260.00, 'tax_percentage' => 5,  'stock' => 20],
            ['name' => 'Nescafe Classic 100g',                 'code' => 'NES-100', 'price' => 340.00, 'tax_percentage' => 18, 'stock' => 12],
            ['name' => 'Real Mixed Fruit Juice 1L',            'code' => 'REA-1L',  'price' => 110.00, 'tax_percentage' => 12, 'stock' => 18],
            ['name' => 'Horlicks Classic 500g',                'code' => 'HOR-500', 'price' => 265.00, 'tax_percentage' => 18, 'stock' => 16],
            ['name' => 'Bisleri Water 1L',                     'code' => 'BIS-1L',  'price' => 20.00,  'tax_percentage' => 18, 'stock' => 100],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
