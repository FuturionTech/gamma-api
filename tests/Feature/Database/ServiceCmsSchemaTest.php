<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ServiceCmsSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_services_table_has_published_at_column(): void
    {
        $this->assertTrue(Schema::hasColumn('services', 'published_at'));
    }
}
