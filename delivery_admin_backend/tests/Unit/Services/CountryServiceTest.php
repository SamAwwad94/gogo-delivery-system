<?php

namespace Tests\Unit\Services;

use App\Models\Country;
use App\Repositories\CountryRepository;
use App\Services\CacheService;
use App\Services\CountryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery;
use Tests\TestCase;

class CountryServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CountryRepository|Mockery\MockInterface
     */
    protected $countryRepository;

    /**
     * @var CacheService|Mockery\MockInterface
     */
    protected $cacheService;

    /**
     * @var CountryService
     */
    protected $countryService;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->countryRepository = Mockery::mock(CountryRepository::class);
        $this->cacheService = Mockery::mock(CacheService::class);
        $this->countryService = new CountryService($this->countryRepository, $this->cacheService);
    }

    /**
     * Tear down the test
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test getAllCountries method
     */
    public function testGetAllCountries()
    {
        $perPage = 15;
        $filters = ['status' => 1];
        $paginator = new LengthAwarePaginator(
            [new Country(['name' => 'Test Country'])],
            1,
            $perPage
        );

        $this->cacheService->shouldReceive('getCollectionKey')
            ->once()
            ->with('countries', Mockery::any())
            ->andReturn('countries_key');

        $this->cacheService->shouldReceive('remember')
            ->once()
            ->with('countries_key', 60 * 15, Mockery::any())
            ->andReturnUsing(function ($key, $ttl, $callback) use ($paginator) {
                return $callback();
            });

        $this->countryRepository->shouldReceive('getAllWithFilters')
            ->once()
            ->with($perPage, $filters)
            ->andReturn($paginator);

        $result = $this->countryService->getAllCountries($perPage, $filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Test Country', $result->items()[0]->name);
    }

    /**
     * Test createCountry method
     */
    public function testCreateCountry()
    {
        $data = [
            'name' => 'Test Country',
            'code' => 'TC',
            'status' => 1,
            'distance_type' => 'km',
        ];

        $country = new Country($data);
        $country->id = 1;

        $this->countryRepository->shouldReceive('create')
            ->once()
            ->with($data)
            ->andReturn($country);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('countries')
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->twice()
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->twice()
            ->andReturn('key');

        $result = $this->countryService->createCountry($data);

        $this->assertInstanceOf(Country::class, $result);
        $this->assertEquals('Test Country', $result->name);
        $this->assertEquals('TC', $result->code);
    }

    /**
     * Test updateCountry method
     */
    public function testUpdateCountry()
    {
        $countryId = 1;
        $data = [
            'name' => 'Updated Country',
            'code' => 'UC',
        ];

        $country = new Country($data);
        $country->id = $countryId;

        $this->countryRepository->shouldReceive('update')
            ->once()
            ->with($countryId, $data)
            ->andReturn($country);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('countries')
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearModelCache')
            ->once()
            ->with('country', $countryId)
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->twice()
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->twice()
            ->andReturn('key');

        $result = $this->countryService->updateCountry($countryId, $data);

        $this->assertInstanceOf(Country::class, $result);
        $this->assertEquals('Updated Country', $result->name);
        $this->assertEquals('UC', $result->code);
    }

    /**
     * Test deleteCountry method
     */
    public function testDeleteCountry()
    {
        $countryId = 1;

        $this->countryRepository->shouldReceive('deleteById')
            ->once()
            ->with($countryId)
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('countries')
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearModelCache')
            ->once()
            ->with('country', $countryId)
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->twice()
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->twice()
            ->andReturn('key');

        $result = $this->countryService->deleteCountry($countryId);

        $this->assertTrue($result);
    }

    /**
     * Test getActiveCountries method
     */
    public function testGetActiveCountries()
    {
        $countries = collect([
            new Country(['name' => 'Country A', 'status' => 1]),
            new Country(['name' => 'Country B', 'status' => 1]),
        ]);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->once()
            ->with('countries_active')
            ->andReturn('countries_active_key');

        $this->cacheService->shouldReceive('remember')
            ->once()
            ->with('countries_active_key', 60 * 30, Mockery::any())
            ->andReturnUsing(function ($key, $ttl, $callback) use ($countries) {
                return $callback();
            });

        $this->countryRepository->shouldReceive('getActive')
            ->once()
            ->andReturn($countries);

        $result = $this->countryService->getActiveCountries();

        $this->assertCount(2, $result);
        $this->assertEquals('Country A', $result[0]->name);
        $this->assertEquals('Country B', $result[1]->name);
    }
}
