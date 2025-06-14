<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $table = 'notifications';

    protected $fillable = [ 'id', 'type', 'notifiable_type', 'notifiable_id', 'data' , 'read_at' ];

    protected static function boot()
    {
        parent::boot();
    
        static::deleted(function ($row) {
            \App\Models\Notification::whereJsonContains('data', ['id' => $row->id])->delete();
        });
    }
}
