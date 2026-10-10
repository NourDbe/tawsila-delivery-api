<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            /*
             * Customer who created the order.
             */
            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            /*
             * Store responsible for preparing the order.
             */
            $table->foreignId('store_id')
                ->constrained()
                ->restrictOnDelete();

            /*
             * The order may exist before a driver
             * is assigned, so driver_id is nullable.
             *
             * The composite index below starts with
             * driver_id and can also support this FK.
             */
            $table->foreignId('driver_id')
                ->nullable();

            /*
             * The application will later cast this value
             * to OrderStatus Enum.
             */
            $table->string('status', 30)
                ->default('pending');

            /*
             * Delivery address is stored on the order
             * because it belongs to this specific delivery.
             */
            $table->string('delivery_address');

            /*
             * Final delivery fee stored at order time.
             */
            $table->decimal('delivery_fee', 10, 2)
                ->default('0.00');

            $table->timestamps();

            /*
             * Used by "current orders":
             *
             * WHERE status IN (...)
             * ORDER BY created_at
             */
            $table->index(
                ['status', 'created_at'],
                'orders_status_created_at_idx'
            );

            /*
             * Used by queries such as:
             *
             * active orders for a specific driver
             * active unassigned orders
             * ordered from oldest to newest
             */
            $table->index(
                ['driver_id', 'status', 'created_at'],
                'orders_driver_status_created_at_idx'
            );

            /*
             * Used by date-based queries such as
             * today's orders without filtering by status.
             */
            $table->index(
                'created_at',
                'orders_created_at_idx'
            );

            /*
             * Foreign key added after the composite index.
             */
            $table->foreign('driver_id')
                ->references('id')
                ->on('drivers')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
