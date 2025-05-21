<?php

namespace Tests\Feature\Controllers;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RefactoredCountryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    protected $admin;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin role with permissions
        $role = Role::create(['name' => 'admin']);
        $permissions = [
            'country-list',
            'country-add',
            'country-edit',
            'country-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        $role->syncPermissions($permissions);

        // Create admin user
        $this->admin = User::factory()->create([
            'user_type' => 'admin',
        ]);
        $this->admin->assignRole('admin');
    }

    /**
     * Test index method
     */
    public function testIndex()
    {
        $this->actingAs($this->admin);

        Country::factory()->count(3)->create();

        $response = $this->get(route('refactored-country.index'));

        $response->assertStatus(200);
        $response->assertViewIs('country.shadcn-countries');
        $response->assertViewHas('countries');
    }

    /**
     * Test index method with filters
     */
    public function testIndexWithFilters()
    {
        $this->actingAs($this->admin);

        Country::factory()->create(['status' => 1, 'name' => 'Country A']);
        Country::factory()->create(['status' => 0, 'name' => 'Country B']);
        Country::factory()->create(['status' => 1, 'name' => 'Country C']);

        $response = $this->get(route('refactored-country.index', ['status' => 1]));

        $response->assertStatus(200);
        $response->assertViewIs('country.shadcn-countries');
        $response->assertViewHas('countries');
        
        $countries = $response->viewData('countries');
        $this->assertEquals(2, $countries->total());
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('refactored-country.create'));

        $response->assertStatus(200);
        $response->assertViewIs('country.form');
    }

    /**
     * Test store method
     */
    public function testStore()
    {
        $this->actingAs($this->admin);

        $data = [
            'name' => 'Test Country',
            'code' => 'TC',
            'status' => 1,
            'distance_type' => 'km',
        ];

        $response = $this->post(route('refactored-country.store'), $data);

        $response->assertRedirect(route('refactored-country.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('countries', [
            'name' => 'Test Country',
            'code' => 'TC',
        ]);
    }

    /**
     * Test edit method
     */
    public function testEdit()
    {
        $this->actingAs($this->admin);

        $country = Country::factory()->create();

        $response = $this->get(route('refactored-country.edit', $country->id));

        $response->assertStatus(200);
        $response->assertViewIs('country.form');
        $response->assertViewHas('data');
        
        $data = $response->viewData('data');
        $this->assertEquals($country->id, $data->id);
    }

    /**
     * Test update method
     */
    public function testUpdate()
    {
        $this->actingAs($this->admin);

        $country = Country::factory()->create([
            'name' => 'Original Country',
            'code' => 'OC',
        ]);

        $data = [
            'name' => 'Updated Country',
            'code' => 'UC',
            'status' => 1,
            'distance_type' => 'km',
        ];

        $response = $this->put(route('refactored-country.update', $country->id), $data);

        $response->assertRedirect(route('refactored-country.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'name' => 'Updated Country',
            'code' => 'UC',
        ]);
    }

    /**
     * Test destroy method
     */
    public function testDestroy()
    {
        $this->actingAs($this->admin);

        $country = Country::factory()->create();

        $response = $this->delete(route('refactored-country.destroy', $country->id));

        $response->assertRedirect(route('refactored-country.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted('countries', [
            'id' => $country->id,
        ]);
    }

    /**
     * Test action method for restore
     */
    public function testActionRestore()
    {
        $this->actingAs($this->admin);

        $country = Country::factory()->create();
        $country->delete();

        $response = $this->post(route('refactored-country.action', $country->id), [
            'type' => 'restore',
        ]);

        $response->assertRedirect(route('refactored-country.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('countries', [
            'id' => $country->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * Test action method for force delete
     */
    public function testActionForceDelete()
    {
        $this->actingAs($this->admin);

        $country = Country::factory()->create();
        $country->delete();

        $response = $this->post(route('refactored-country.action', $country->id), [
            'type' => 'forcedelete',
        ]);

        $response->assertRedirect(route('refactored-country.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('countries', [
            'id' => $country->id,
        ]);
    }
}
