<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreVisibilityApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_regular_store_list_excludes_suspended_stores(): void
    {
        $visibleStore = Store::factory()->create([
            'name' => 'Visible Store',
            'is_suspended' => false,
        ]);

        $suspendedStore = Store::factory()->suspended()->create([
            'name' => 'Suspended Store',
        ]);

        $response = $this->getJson('/api/stores');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $visibleStore->name,
            ])
            ->assertJsonMissing([
                'name' => $suspendedStore->name,
            ]);
    }

    public function test_admin_store_list_includes_suspended_stores(): void
    {
        $suspendedStore = Store::factory()->suspended()->create([
            'name' => 'Suspended Store',
        ]);

        $response = $this->getJson('/api/admin/stores');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $suspendedStore->name,
                'is_suspended' => true,
            ]);
    }
}
