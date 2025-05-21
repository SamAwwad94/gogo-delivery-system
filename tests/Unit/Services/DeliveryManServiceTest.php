<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\DeliveryManRepository;
use App\Services\CacheService;
use App\Services\DeliveryManService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Mockery\MockInterface;
use Mockery;
use Tests\TestCase;

class DeliveryManServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var DeliveryManRepository|Mockery\MockInterface
     */
    protected $deliveryManRepository;

    /**
     * @var CacheService|Mockery\MockInterface
     */
    protected $cacheService;

    /**
     * @var DeliveryManService
     */
    protected $deliveryManService;

    /**
     * Set up the test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->deliveryManRepository = Mockery::mock(DeliveryManRepository::class);
        $this->cacheService = Mockery::mock(CacheService::class);
        $this->deliveryManService = new DeliveryManService($this->deliveryManRepository, $this->cacheService);
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
     * Test getAllDeliveryMen method
     */
    public function testGetAllDeliveryMen()
    {
        $perPage = 15;
        $filters = ['status' => 'active'];
        $paginator = new LengthAwarePaginator(
            [new User(['name' => 'Test Delivery Man'])],
            1,
            $perPage
        );

        $this->cacheService->shouldReceive('getCollectionKey')
            ->once()
            ->with('delivery_men', Mockery::any())
            ->andReturn('delivery_men_key');

        $this->cacheService->shouldReceive('remember')
            ->once()
            ->with('delivery_men_key', 60 * 5, Mockery::any())
            ->andReturnUsing(function ($key, $ttl, $callback) use ($paginator) {
                return $callback();
            });

        $this->deliveryManRepository->shouldReceive('getAllWithFilters')
            ->once()
            ->with($perPage, $filters, ['country', 'city'])
            ->andReturn($paginator);

        $result = $this->deliveryManService->getAllDeliveryMen($perPage, $filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $this->assertEquals('Test Delivery Man', $result->items()[0]->name);
    }

    /**
     * Test getById method
     */
    public function testGetById()
    {
        $id = 1;
        $user = new User();
        $user->id = $id;
        $user->name = 'Test Delivery Man';

        $this->cacheService->shouldReceive('getModelKey')
            ->once()
            ->with('delivery_man', $id)
            ->andReturn('delivery_man_key');

        $this->cacheService->shouldReceive('remember')
            ->once()
            ->with('delivery_man_key', 60 * 15, Mockery::any())
            ->andReturnUsing(function ($key, $ttl, $callback) use ($user) {
                return $callback();
            });

        $this->deliveryManRepository->shouldReceive('findById')
            ->once()
            ->with($id, ['country', 'city', 'userWallet', 'userBankAccount'])
            ->andReturn($user);

        $result = $this->deliveryManService->getById($id);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals($id, $result->id);
        $this->assertEquals('Test Delivery Man', $result->name);
    }

    /**
     * Test createDeliveryMan method
     */
    public function testCreateDeliveryMan()
    {
        $data = [
            'name' => 'Test Delivery Man',
            'email' => 'test@example.com',
            'password' => 'password',
            'contact_number' => '1234567890',
            'status' => 1,
        ];

        $user = new User($data);
        $user->id = 1;

        $this->deliveryManRepository->shouldReceive('create')
            ->once()
            ->andReturn($user);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('delivery_men')
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->times(3)
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->times(3)
            ->andReturn('key');

        $result = $this->deliveryManService->createDeliveryMan($data);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Test Delivery Man', $result->name);
        $this->assertEquals('test@example.com', $result->email);
    }

    /**
     * Test updateDeliveryMan method
     */
    public function testUpdateDeliveryMan()
    {
        $id = 1;
        $data = [
            'name' => 'Updated Delivery Man',
            'email' => 'updated@example.com',
        ];

        $user = new User($data);
        $user->id = $id;
        $user->user_type = 'delivery_man';

        $this->deliveryManRepository->shouldReceive('findById')
            ->once()
            ->with($id)
            ->andReturn($user);

        $this->deliveryManRepository->shouldReceive('update')
            ->once()
            ->with($id, $data)
            ->andReturn($user);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('delivery_men')
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearModelCache')
            ->once()
            ->with('delivery_man', $id)
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->times(3)
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->times(3)
            ->andReturn('key');

        $result = $this->deliveryManService->updateDeliveryMan($id, $data);

        $this->assertInstanceOf(User::class, $result);
        $this->assertEquals('Updated Delivery Man', $result->name);
        $this->assertEquals('updated@example.com', $result->email);
    }

    /**
     * Test deleteDeliveryMan method
     */
    public function testDeleteDeliveryMan()
    {
        $id = 1;

        $this->deliveryManRepository->shouldReceive('deleteById')
            ->once()
            ->with($id)
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearCollectionCache')
            ->once()
            ->with('delivery_men')
            ->andReturn(true);

        $this->cacheService->shouldReceive('clearModelCache')
            ->once()
            ->with('delivery_man', $id)
            ->andReturn(true);

        $this->cacheService->shouldReceive('forget')
            ->times(3)
            ->andReturn(true);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->times(3)
            ->andReturn('key');

        $result = $this->deliveryManService->deleteDeliveryMan($id);

        $this->assertTrue($result);
    }

    /**
     * Test getActiveDeliveryMen method
     */
    public function testGetActiveDeliveryMen()
    {
        $user1 = new User();
        $user1->name = 'Delivery Man A';
        $user1->status = 1;

        $user2 = new User();
        $user2->name = 'Delivery Man B';
        $user2->status = 1;

        $deliveryMen = new Collection([$user1, $user2]);

        $this->cacheService->shouldReceive('getCollectionKey')
            ->once()
            ->with('delivery_men_active')
            ->andReturn('delivery_men_active_key');

        $this->cacheService->shouldReceive('remember')
            ->once()
            ->with('delivery_men_active_key', 60 * 15, Mockery::any())
            ->andReturnUsing(function ($key, $ttl, $callback) use ($deliveryMen) {
                return $callback();
            });

        $this->deliveryManRepository->shouldReceive('getActive')
            ->once()
            ->andReturn($deliveryMen);

        $result = $this->deliveryManService->getActiveDeliveryMen();

        $this->assertCount(2, $result);
        $this->assertEquals('Delivery Man A', $result[0]->name);
        $this->assertEquals('Delivery Man B', $result[1]->name);
    }
}
