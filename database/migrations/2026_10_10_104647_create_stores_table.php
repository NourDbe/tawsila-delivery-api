<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('address')->nullable();

            /*
             * Suspended stores must normally be hidden
             * from customer-facing queries.
             *
             * We will handle this later using
             * a Global Scope.
             */
            $table->boolean('is_suspended')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
