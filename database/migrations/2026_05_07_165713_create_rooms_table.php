<?php

use App\Enums\RoomStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('type', 30);
            $table->decimal('price_per_night', 10, 2);
            $table->unsignedSmallInteger('capacity');
            $table->unsignedSmallInteger('floor');
            $table->string('status', 20)->default(RoomStatus::Available->value);
            $table->string('thumbnail')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('floor');
            $table->index('price_per_night');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
