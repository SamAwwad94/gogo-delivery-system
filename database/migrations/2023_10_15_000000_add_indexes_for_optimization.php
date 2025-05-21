<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexesForOptimization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add indexes to countries table
        Schema::table('countries', function (Blueprint $table) {
            if (!Schema::hasIndex('countries', 'countries_status_index')) {
                $table->index('status', 'countries_status_index');
            }
            if (!Schema::hasIndex('countries', 'countries_distance_type_index')) {
                $table->index('distance_type', 'countries_distance_type_index');
            }
        });

        // Add indexes to cities table
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasIndex('cities', 'cities_country_id_index')) {
                $table->index('country_id', 'cities_country_id_index');
            }
            if (!Schema::hasIndex('cities', 'cities_status_index')) {
                $table->index('status', 'cities_status_index');
            }
        });

        // Add indexes to users table
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasIndex('users', 'users_user_type_index')) {
                $table->index('user_type', 'users_user_type_index');
            }
            if (!Schema::hasIndex('users', 'users_status_index')) {
                $table->index('status', 'users_status_index');
            }
            if (!Schema::hasIndex('users', 'users_country_id_index')) {
                $table->index('country_id', 'users_country_id_index');
            }
            if (!Schema::hasIndex('users', 'users_city_id_index')) {
                $table->index('city_id', 'users_city_id_index');
            }
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasIndex('orders', 'orders_client_id_index')) {
                $table->index('client_id', 'orders_client_id_index');
            }
            if (!Schema::hasIndex('orders', 'orders_delivery_man_id_index')) {
                $table->index('delivery_man_id', 'orders_delivery_man_id_index');
            }
            if (!Schema::hasIndex('orders', 'orders_country_id_index')) {
                $table->index('country_id', 'orders_country_id_index');
            }
            if (!Schema::hasIndex('orders', 'orders_city_id_index')) {
                $table->index('city_id', 'orders_city_id_index');
            }
            if (!Schema::hasIndex('orders', 'orders_status_index')) {
                $table->index('status', 'orders_status_index');
            }
            if (!Schema::hasIndex('orders', 'orders_payment_id_index')) {
                $table->index('payment_id', 'orders_payment_id_index');
            }
        });

        // Add indexes to payments table
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasIndex('payments', 'payments_order_id_index')) {
                $table->index('order_id', 'payments_order_id_index');
            }
            if (!Schema::hasIndex('payments', 'payments_client_id_index')) {
                $table->index('client_id', 'payments_client_id_index');
            }
            if (!Schema::hasIndex('payments', 'payments_payment_type_index')) {
                $table->index('payment_type', 'payments_payment_type_index');
            }
            if (!Schema::hasIndex('payments', 'payments_payment_status_index')) {
                $table->index('payment_status', 'payments_payment_status_index');
            }
            if (!Schema::hasIndex('payments', 'payments_datetime_index')) {
                $table->index('datetime', 'payments_datetime_index');
            }
        });

        // Add indexes to wallets table
        Schema::table('wallets', function (Blueprint $table) {
            if (!Schema::hasIndex('wallets', 'wallets_user_id_index')) {
                $table->index('user_id', 'wallets_user_id_index');
            }
        });

        // Add indexes to wallet_histories table
        Schema::table('wallet_histories', function (Blueprint $table) {
            if (!Schema::hasIndex('wallet_histories', 'wallet_histories_user_id_index')) {
                $table->index('user_id', 'wallet_histories_user_id_index');
            }
            if (!Schema::hasIndex('wallet_histories', 'wallet_histories_type_index')) {
                $table->index('type', 'wallet_histories_type_index');
            }
            if (!Schema::hasIndex('wallet_histories', 'wallet_histories_transaction_type_index')) {
                $table->index('transaction_type', 'wallet_histories_transaction_type_index');
            }
            if (!Schema::hasIndex('wallet_histories', 'wallet_histories_order_id_index')) {
                $table->index('order_id', 'wallet_histories_order_id_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove indexes from countries table
        Schema::table('countries', function (Blueprint $table) {
            if (Schema::hasIndex('countries', 'countries_status_index')) {
                $table->dropIndex('countries_status_index');
            }
            if (Schema::hasIndex('countries', 'countries_distance_type_index')) {
                $table->dropIndex('countries_distance_type_index');
            }
        });

        // Remove indexes from cities table
        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasIndex('cities', 'cities_country_id_index')) {
                $table->dropIndex('cities_country_id_index');
            }
            if (Schema::hasIndex('cities', 'cities_status_index')) {
                $table->dropIndex('cities_status_index');
            }
        });

        // Remove indexes from users table
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_user_type_index')) {
                $table->dropIndex('users_user_type_index');
            }
            if (Schema::hasIndex('users', 'users_status_index')) {
                $table->dropIndex('users_status_index');
            }
            if (Schema::hasIndex('users', 'users_country_id_index')) {
                $table->dropIndex('users_country_id_index');
            }
            if (Schema::hasIndex('users', 'users_city_id_index')) {
                $table->dropIndex('users_city_id_index');
            }
        });

        // Remove indexes from orders table
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasIndex('orders', 'orders_client_id_index')) {
                $table->dropIndex('orders_client_id_index');
            }
            if (Schema::hasIndex('orders', 'orders_delivery_man_id_index')) {
                $table->dropIndex('orders_delivery_man_id_index');
            }
            if (Schema::hasIndex('orders', 'orders_country_id_index')) {
                $table->dropIndex('orders_country_id_index');
            }
            if (Schema::hasIndex('orders', 'orders_city_id_index')) {
                $table->dropIndex('orders_city_id_index');
            }
            if (Schema::hasIndex('orders', 'orders_status_index')) {
                $table->dropIndex('orders_status_index');
            }
            if (Schema::hasIndex('orders', 'orders_payment_id_index')) {
                $table->dropIndex('orders_payment_id_index');
            }
        });

        // Remove indexes from payments table
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasIndex('payments', 'payments_order_id_index')) {
                $table->dropIndex('payments_order_id_index');
            }
            if (Schema::hasIndex('payments', 'payments_client_id_index')) {
                $table->dropIndex('payments_client_id_index');
            }
            if (Schema::hasIndex('payments', 'payments_payment_type_index')) {
                $table->dropIndex('payments_payment_type_index');
            }
            if (Schema::hasIndex('payments', 'payments_payment_status_index')) {
                $table->dropIndex('payments_payment_status_index');
            }
            if (Schema::hasIndex('payments', 'payments_datetime_index')) {
                $table->dropIndex('payments_datetime_index');
            }
        });

        // Remove indexes from wallets table
        Schema::table('wallets', function (Blueprint $table) {
            if (Schema::hasIndex('wallets', 'wallets_user_id_index')) {
                $table->dropIndex('wallets_user_id_index');
            }
        });

        // Remove indexes from wallet_histories table
        Schema::table('wallet_histories', function (Blueprint $table) {
            if (Schema::hasIndex('wallet_histories', 'wallet_histories_user_id_index')) {
                $table->dropIndex('wallet_histories_user_id_index');
            }
            if (Schema::hasIndex('wallet_histories', 'wallet_histories_type_index')) {
                $table->dropIndex('wallet_histories_type_index');
            }
            if (Schema::hasIndex('wallet_histories', 'wallet_histories_transaction_type_index')) {
                $table->dropIndex('wallet_histories_transaction_type_index');
            }
            if (Schema::hasIndex('wallet_histories', 'wallet_histories_order_id_index')) {
                $table->dropIndex('wallet_histories_order_id_index');
            }
        });
    }
}
