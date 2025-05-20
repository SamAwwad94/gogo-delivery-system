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
        'delivery_man_id',
        'start_location',
        'start_latitude',
        'start_longitude',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_latitude' => 'float',
        'start_longitude' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the delivery man associated with the route.
     */
    public function deliveryMan()
    {
        return $this->belongsTo(User::class, 'delivery_man_id');
    }

    /**
     * Get the user who created the route.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the orders associated with the route.
     */
    public function orders()
    {
        return $this->belongsToMany(Order::class, 'delivery_route_orders')
            ->withPivot('order_sequence')
            ->withTimestamps();
    }
    
    /**
     * Get the route status badge HTML.
     *
     * @return string
     */
    public function getStatusBadgeAttribute()
    {
        $statusClasses = [
            'pending' => 'bg-warning/10 text-warning ring-warning/20',
            'in_progress' => 'bg-info/10 text-info ring-info/20',
            'completed' => 'bg-success/10 text-success ring-success/20',
            'cancelled' => 'bg-destructive/10 text-destructive ring-destructive/20',
        ];
        
        $class = $statusClasses[$this->status] ?? 'bg-primary/10 text-primary ring-primary/20';
        
        return '<span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset ' . $class . '">' . 
            ucfirst(str_replace('_', ' ', $this->status)) . 
        '</span>';
    }
}
