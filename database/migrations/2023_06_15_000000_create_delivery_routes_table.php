<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivery_routes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('delivery_man_id');
            $table->string('start_location');
            $table->decimal('start_latitude', 10, 7);
            $table->decimal('start_longitude', 10, 7);
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('delivery_man_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
        
        Schema::create('delivery_route_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('delivery_route_id');
            $table->unsignedBigInteger('order_id');
            $table->integer('order_sequence')->nullable();
            $table->timestamps();
            
            $table->foreign('delivery_route_id')->references('id')->on('delivery_routes')->onDelete('cascade');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            
            $table->unique(['delivery_route_id', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivery_route_orders');
        Schema::dropIfExists('delivery_routes');
    }
};
