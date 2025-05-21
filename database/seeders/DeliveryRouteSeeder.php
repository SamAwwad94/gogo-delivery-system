<?php

namespace Database\Seeders;

use App\Models\DeliveryRoute;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DeliveryRouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get delivery men IDs
        $deliveryMen = User::whereHas('roles', function ($q) {
            $q->where('name', 'deliveryman');
        })->pluck('id')->toArray();

        // If no delivery men found, create a sample one
        if (empty($deliveryMen)) {
            echo "No delivery men found. Creating a sample delivery man...\n";
            $email = 'delivery' . rand(1000, 9999) . '@example.com';
            $user = User::create([
                'name' => 'Sample Delivery Man',
                'email' => $email,
                'username' => 'delivery_man_' . rand(1000, 9999),
                'password' => bcrypt('password'),
                'user_type' => 'delivery_man',
                'status' => 1,
                'contact_number' => '+1234567890',
            ]);

            // Create deliveryman role if it doesn't exist
            if (!\Spatie\Permission\Models\Role::where('name', 'deliveryman')->exists()) {
                \Spatie\Permission\Models\Role::create(['name' => 'deliveryman']);
                echo "Created 'deliveryman' role\n";
            }

            // Assign deliveryman role
            $user->assignRole('deliveryman');

            $deliveryMen[] = $user->id;
        }

        // Sample locations in Lebanon
        $locations = [
            'Beirut Downtown',
            'Hamra, Beirut',
            'Achrafieh, Beirut',
            'Tripoli City Center',
            'Sidon Waterfront',
            'Tyre Archaeological Site',
            'Jounieh Bay',
            'Byblos Old Port',
            'Baalbek',
            'Zahle',
            'Batroun',
            'Beiteddine',
            'Faraya',
            'Jbeil',
            'Anjar',
            'Bcharre',
            'Ehden',
            'Harissa',
            'Jeita',
            'Raouche'
        ];

        // Sample statuses - using only 'active' and 'inactive' as these are the only ones allowed in the database
        $statuses = ['active', 'inactive'];

        // Create 30 sample delivery routes
        for ($i = 1; $i <= 30; $i++) {
            $startLocation = $locations[array_rand($locations)];
            $endLocation = $locations[array_rand($locations)];

            // Ensure start and end locations are different
            while ($endLocation === $startLocation) {
                $endLocation = $locations[array_rand($locations)];
            }

            // Random date within the last 30 days
            $createdAt = Carbon::now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            DeliveryRoute::create([
                'name' => 'Route #' . ($i + 1000),
                'description' => 'Delivery route from ' . $startLocation . ' to ' . $endLocation,
                'start_location' => $startLocation,
                'end_location' => $endLocation,
                'waypoints' => json_encode([
                    ['lat' => 33.8 + (rand(-100, 100) / 1000), 'lng' => 35.5 + (rand(-100, 100) / 1000)],
                    ['lat' => 33.9 + (rand(-100, 100) / 1000), 'lng' => 35.6 + (rand(-100, 100) / 1000)],
                ]),
                'deliveryman_id' => $deliveryMen[array_rand($deliveryMen)],
                'status' => $statuses[array_rand($statuses)],
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            echo "Created delivery route #" . ($i + 1000) . "\n";
        }

        echo "Completed seeding delivery routes.\n";
    }
}
