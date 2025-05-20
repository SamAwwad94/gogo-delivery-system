<?php

namespace Tests\Unit\Repositories;

use App\Models\Country;
use App\Repositories\CountryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CountryRepository
     */
    protected $repository;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CountryRepository(new Country());
    }

    /**
     * Test create method
     */
    public function testCreate()
    {
        $data = [
            'name' => 'Test Country',
            'code' => 'TC',
            'status' => 1,
            'distance_type' => 'km',
        ];

        $country = $this->repository->create($data);

        $this->assertInstanceOf(Country::class, $country);
        $this->assertEquals('Test Country', $country->name);
        $this->assertEquals('TC', $country->code);
        $this->assertEquals(1, $country->status);
        $this->assertEquals('km', $country->distance_type);
    }

    /**
     * Test findById method
     */
    public function testFindById()
    {
        $country = Country::factory()->create([
            'name' => 'Test Country',
            'code' => 'TC',
        ]);

        $foundCountry = $this->repository->findById($country->id);

        $this->assertInstanceOf(Country::class, $foundCountry);
        $this->assertEquals($country->id, $foundCountry->id);
        $this->assertEquals('Test Country', $foundCountry->name);
    }

    /**
     * Test update method
     */
    public function testUpdate()
    {
        $country = Country::factory()->create([
            'name' => 'Test Country',
            'code' => 'TC',
        ]);

        $updatedCountry = $this->repository->update($country->id, [
            'name' => 'Updated Country',
            'code' => 'UC',
        ]);

        $this->assertInstanceOf(Country::class, $updatedCountry);
        $this->assertEquals($country->id, $updatedCountry->id);
        $this->assertEquals('Updated Country', $updatedCountry->name);
        $this->assertEquals('UC', $updatedCountry->code);
    }

    /**
     * Test deleteById method
     */
    public function testDeleteById()
    {
        $country = Country::factory()->create();

        $result = $this->repository->deleteById($country->id);

        $this->assertTrue($result);
        $this->assertSoftDeleted('countries', ['id' => $country->id]);
    }

    /**
     * Test getActive method
     */
    public function testGetActive()
    {
        Country::factory()->create(['status' => 1]);
        Country::factory()->create(['status' => 1]);
        Country::factory()->create(['status' => 0]);

        $activeCountries = $this->repository->getActive();

        $this->assertCount(2, $activeCountries);
        $this->assertEquals(1, $activeCountries->first()->status);
    }

    /**
     * Test getWithCities method
     */
    public function testGetWithCities()
    {
        $country = Country::factory()->create(['status' => 1]);
        $city = $country->cities()->create([
            'name' => 'Test City',
            'status' => 1,
        ]);

        $countriesWithCities = $this->repository->getWithCities();

        $this->assertCount(1, $countriesWithCities);
        $this->assertEquals($country->id, $countriesWithCities->first()->id);
        $this->assertTrue($countriesWithCities->first()->relationLoaded('cities'));
    }

    /**
     * Test searchByName method
     */
    public function testSearchByName()
    {
        Country::factory()->create(['name' => 'United States']);
        Country::factory()->create(['name' => 'United Kingdom']);
        Country::factory()->create(['name' => 'Canada']);

        $results = $this->repository->searchByName('United');

        $this->assertCount(2, $results);
        $this->assertEquals('United States', $results[0]->name);
        $this->assertEquals('United Kingdom', $results[1]->name);
    }

    /**
     * Test getAllWithFilters method with status filter
     */
    public function testGetAllWithFiltersStatus()
    {
        Country::factory()->create(['status' => 1, 'name' => 'Country A']);
        Country::factory()->create(['status' => 0, 'name' => 'Country B']);
        Country::factory()->create(['status' => 1, 'name' => 'Country C']);

        $filters = ['status' => 1];
        $results = $this->repository->getAllWithFilters(15, $filters);

        $this->assertEquals(2, $results->total());
        $this->assertEquals('Country A', $results->items()[0]->name);
        $this->assertEquals('Country C', $results->items()[1]->name);
    }

    /**
     * Test getAllWithFilters method with search filter
     */
    public function testGetAllWithFiltersSearch()
    {
        Country::factory()->create(['name' => 'United States']);
        Country::factory()->create(['name' => 'United Kingdom']);
        Country::factory()->create(['name' => 'Canada']);

        $filters = ['search' => 'United'];
        $results = $this->repository->getAllWithFilters(15, $filters);

        $this->assertEquals(2, $results->total());
    }

    /**
     * Test getAllWithFilters method with distance_type filter
     */
    public function testGetAllWithFiltersDistanceType()
    {
        Country::factory()->create(['distance_type' => 'km', 'name' => 'Country A']);
        Country::factory()->create(['distance_type' => 'mile', 'name' => 'Country B']);
        Country::factory()->create(['distance_type' => 'km', 'name' => 'Country C']);

        $filters = ['distance_type' => 'km'];
        $results = $this->repository->getAllWithFilters(15, $filters);

        $this->assertEquals(2, $results->total());
        $this->assertEquals('Country A', $results->items()[0]->name);
        $this->assertEquals('Country C', $results->items()[1]->name);
    }
}
