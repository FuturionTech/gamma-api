<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clients = [
            [
                'name' => 'National Bank of Canada',
                'logo_url' => 'https://ui-avatars.com/api/?name=NBC&size=200&background=000&color=fff&format=svg',
                'industry' => 'Financial Services',
                'website_url' => 'https://www.nbc.ca',
                'order' => 1,
            ],
            [
                'name' => 'Ontario Provincial Health Network',
                'logo_url' => 'https://ui-avatars.com/api/?name=OPHN&size=200&background=000&color=fff&format=svg',
                'industry' => 'Healthcare',
                'website_url' => null,
                'order' => 2,
            ],
            [
                'name' => 'MapleLeaf Retail Group',
                'logo_url' => 'https://ui-avatars.com/api/?name=MLR&size=200&background=000&color=fff&format=svg',
                'industry' => 'Retail',
                'website_url' => null,
                'order' => 3,
            ],
            [
                'name' => 'City of Toronto',
                'logo_url' => 'https://ui-avatars.com/api/?name=COT&size=200&background=000&color=fff&format=svg',
                'industry' => 'Government',
                'website_url' => 'https://www.toronto.ca',
                'order' => 4,
            ],
            [
                'name' => 'Canadian Auto Manufacturing Corp.',
                'logo_url' => 'https://ui-avatars.com/api/?name=CAMC&size=200&background=000&color=fff&format=svg',
                'industry' => 'Manufacturing',
                'website_url' => null,
                'order' => 5,
            ],
            [
                'name' => 'Northern Shield Insurance',
                'logo_url' => 'https://ui-avatars.com/api/?name=NSI&size=200&background=000&color=fff&format=svg',
                'industry' => 'Financial Services',
                'website_url' => null,
                'order' => 6,
            ],
            [
                'name' => 'Ontario University Consortium',
                'logo_url' => 'https://ui-avatars.com/api/?name=OUC&size=200&background=000&color=fff&format=svg',
                'industry' => 'Education',
                'website_url' => null,
                'order' => 7,
            ],
            [
                'name' => 'TransCanada Energy Solutions',
                'logo_url' => 'https://ui-avatars.com/api/?name=TCES&size=200&background=000&color=fff&format=svg',
                'industry' => 'Energy',
                'website_url' => null,
                'order' => 8,
            ],
            [
                'name' => 'Federal Department of Innovation',
                'logo_url' => 'https://ui-avatars.com/api/?name=FDI&size=200&background=000&color=fff&format=svg',
                'industry' => 'Government',
                'website_url' => null,
                'order' => 9,
            ],
            [
                'name' => 'Pacific Coast Logistics',
                'logo_url' => 'https://ui-avatars.com/api/?name=PCL&size=200&background=000&color=fff&format=svg',
                'industry' => 'Logistics',
                'website_url' => null,
                'order' => 10,
            ],
        ];

        foreach ($clients as $client) {
            Client::create(array_merge($client, ['is_active' => true]));
        }
    }
}
