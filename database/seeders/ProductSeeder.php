<?php

// filepath: c:\Users\lenovo\Desktop\Api laravel\API_Administrateur_E-commerce_(GameXpress)\database\seeders\ProductSeeder.php
namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            // PlayStation Products
            [
                'name' => 'PlayStation 5',
                'description' => 'Experience lightning-fast loading with an ultra-high-speed SSD, deeper immersion with support for haptic feedback, adaptive triggers, and 3D Audio.',
                'price' => 499.99,
                'stock' => 15,
                'status' => 'available',
                'category_id' => 5, // PlayStation category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/ps5.jpg',
                        'is_primary' => true
                    ],
                    [
                        'image_url' => 'https://example.com/images/ps5-side.jpg',
                        'is_primary' => false
                    ]
                ]
            ],
            [
                'name' => 'PlayStation 4 Pro',
                'description' => 'The PS4 Pro enables 4K gaming and entertainment with enhanced power to deliver faster, smoother frame rates in select games.',
                'price' => 399.99,
                'stock' => 25,
                'status' => 'available',
                'category_id' => 5, // PlayStation category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/ps4-pro.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            
            // Xbox Products
            [
                'name' => 'Xbox Series X',
                'description' => 'The most powerful Xbox ever. Explore rich new worlds with 12 teraflops of raw graphic processing power, DirectX ray tracing, and 4K gaming.',
                'price' => 499.99,
                'stock' => 10,
                'status' => 'available',
                'category_id' => 6, // Xbox category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/xbox-series-x.jpg',
                        'is_primary' => true
                    ],
                    [
                        'image_url' => 'https://example.com/images/xbox-series-x-controller.jpg',
                        'is_primary' => false
                    ]
                ]
            ],
            [
                'name' => 'Xbox Series S',
                'description' => 'Experience next-gen speed and performance with the Xbox Series S, the smallest Xbox ever.',
                'price' => 299.99,
                'stock' => 30,
                'status' => 'available',
                'category_id' => 6, // Xbox category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/xbox-series-s.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            
            // Nintendo Products
            [
                'name' => 'Nintendo Switch OLED',
                'description' => 'The Nintendo Switch OLED model features a vibrant 7-inch OLED screen, enhanced audio, and a wide adjustable stand.',
                'price' => 349.99,
                'stock' => 20,
                'status' => 'available',
                'category_id' => 7, // Nintendo category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/switch-oled.jpg',
                        'is_primary' => true
                    ],
                    [
                        'image_url' => 'https://example.com/images/switch-oled-dock.jpg',
                        'is_primary' => false
                    ]
                ]
            ],
            
            // Gaming Accessories
            [
                'name' => 'Logitech G Pro X Gaming Headset',
                'description' => 'Professional-grade gaming headset with Blue VO!CE microphone technology and DTS Headphone:X 2.0 surround sound.',
                'price' => 129.99,
                'stock' => 45,
                'status' => 'available',
                'category_id' => 12, // Headsets category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/logitech-headset.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'name' => 'Razer DeathAdder V2 Gaming Mouse',
                'description' => 'The Razer DeathAdder V2 features the Focus+ 20K DPI Optical Sensor and Optical Mouse Switches for elite gaming performance.',
                'price' => 69.99,
                'stock' => 50,
                'status' => 'available',
                'category_id' => 13, // Gaming Mice category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/razer-mouse.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'name' => 'SteelSeries Apex Pro Mechanical Keyboard',
                'description' => 'The world\'s fastest mechanical keyboard with adjustable mechanical switches for customized key sensitivity.',
                'price' => 199.99,
                'stock' => 35,
                'status' => 'available',
                'category_id' => 14, // Gaming Keyboards category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/steelseries-keyboard.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            
            // Games
            [
                'name' => 'The Last of Us Part II - PS4',
                'description' => 'Five years after the events of The Last of Us, Ellie embarks on a journey of revenge in this action-adventure game.',
                'price' => 39.99,
                'stock' => 60,
                'status' => 'available',
                'category_id' => 15, // Action Games category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/tlou2.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
            [
                'name' => 'FIFA 23 - PS5',
                'description' => 'Experience the world\'s game with HyperMotion2 Technology, men\'s and women\'s FIFA World Cup tournaments, and more.',
                'price' => 69.99,
                'stock' => 40,
                'status' => 'available',
                'category_id' => 18, // Sports Games category
                'images' => [
                    [
                        'image_url' => 'https://example.com/images/fifa23.jpg',
                        'is_primary' => true
                    ]
                ]
            ],
        ];

        foreach ($products as $productData) {
            $images = $productData['images'];
            unset($productData['images']);
            
            // Create the slug from the name
            $productData['slug'] = Str::slug($productData['name']);
            
            // Create the product
            $product = Product::create($productData);
            
            // Create associated images
            foreach ($images as $imageData) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_url' => $imageData['image_url'],
                    'is_primary' => $imageData['is_primary'],
                ]);
            }
        }
    }
}