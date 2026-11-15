<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleConsistencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_roles_follow_the_project_business_names(): void
    {
        $this->seed();

        $roles = Role::query()->pluck('name')->sort()->values()->all();

        $this->assertSame([
            'administrateur',
            'artiste',
            'responsable_studio',
            'secretariat',
        ], $roles);
    }
}
