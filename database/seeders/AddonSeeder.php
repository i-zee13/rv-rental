<?php

namespace Database\Seeders;

use App\Models\Addon;
use Illuminate\Database\Seeder;

class AddonSeeder extends Seeder
{
    public function run(): void
    {
        $addons = [
            [
                'code' => 'gps',
                'price' => 12.00,
                'title' => 'GPS Navigation',
                'description' => 'Turn-by-turn navigation for your Miami trip.',
            ],
            [
                'code' => 'child_seat',
                'price' => 15.00,
                'title' => 'Child Safety Seat',
                'description' => 'Rear-facing or booster seat on request.',
            ],
            [
                'code' => 'extra_driver',
                'price' => 10.00,
                'title' => 'Additional Driver',
                'description' => 'Add a second authorized driver to your rental.',
            ],
            [
                'code' => 'premium_insurance',
                'price' => 35.00,
                'title' => 'Premium Insurance',
                'description' => 'Reduced deductible and extended coverage.',
            ],
            [
                'code' => 'airport_delivery',
                'price' => 45.00,
                'title' => 'Airport Delivery',
                'description' => 'We deliver your vehicle to MIA or FLL.',
            ],
            [
                'code' => 'wifi_hotspot',
                'price' => 8.00,
                'title' => 'Wi-Fi Hotspot',
                'description' => 'Stay connected on the road.',
            ],
        ];

        foreach ($addons as $data) {
            $addon = Addon::updateOrCreate(
                ['code' => $data['code']],
                ['price' => $data['price'], 'is_taxable' => true, 'is_active' => true]
            );

            $addon->translations()->updateOrCreate(
                ['locale' => 'en'],
                ['title' => $data['title'], 'description' => $data['description']]
            );
        }
    }
}
