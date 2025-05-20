<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryRoute extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'start_location',
        'end_location',
        'waypoints',
        'deliveryman_id',
        'status',
    ];

    /**
     * Get the deliveryman that owns the delivery route.
     */
    public function deliveryman()
    {
        return $this->belongsTo(User::class, 'deliveryman_id');
    }

    /**
     * Get the orders associated with this delivery route.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_route_id');
    }

    /**
     * Get the status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        $statusClasses = [
            'active' => 'bg-green-100 text-green-800 ring-green-200',
            'inactive' => 'bg-red-100 text-red-800 ring-red-200',
            'pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
            'completed' => 'bg-blue-100 text-blue-800 ring-blue-200',
            'cancelled' => 'bg-red-100 text-red-800 ring-red-200',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800 ring-gray-200';

        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $class . '">' .
            ucfirst(str_replace('_', ' ', $this->status)) .
            '</span>';
    }
}
