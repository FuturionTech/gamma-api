<?php

namespace Database\Seeders;

use App\Models\Partner;
use Illuminate\Database\Seeder;

class PartnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $partners = [
            [
                'name' => 'Amazon Web Services',
                'logo_url' => 'https://ui-avatars.com/api/?name=AWS&size=200&background=232f3e&color=ff9900&format=svg',
                'website_url' => 'https://aws.amazon.com',
                'order' => 1,
            ],
            [
                'name' => 'Google Cloud',
                'logo_url' => 'https://ui-avatars.com/api/?name=GCP&size=200&background=4285f4&color=fff&format=svg',
                'website_url' => 'https://cloud.google.com',
                'order' => 2,
            ],
            [
                'name' => 'Microsoft Azure',
                'logo_url' => 'https://ui-avatars.com/api/?name=Azure&size=200&background=0078d4&color=fff&format=svg',
                'website_url' => 'https://azure.microsoft.com',
                'order' => 3,
            ],
            [
                'name' => 'Snowflake',
                'logo_url' => 'https://ui-avatars.com/api/?name=SF&size=200&background=29b5e8&color=fff&format=svg',
                'website_url' => 'https://www.snowflake.com',
                'order' => 4,
            ],
            [
                'name' => 'Databricks',
                'logo_url' => 'https://ui-avatars.com/api/?name=DB&size=200&background=ff3621&color=fff&format=svg',
                'website_url' => 'https://www.databricks.com',
                'order' => 5,
            ],
            [
                'name' => 'ServiceNow',
                'logo_url' => 'https://ui-avatars.com/api/?name=SN&size=200&background=81b5a1&color=fff&format=svg',
                'website_url' => 'https://www.servicenow.com',
                'order' => 6,
            ],
        ];

        foreach ($partners as $partner) {
            Partner::create(array_merge($partner, ['is_active' => true]));
        }
    }
}
