<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('image_url')->nullable()->after('description');
            $table->json('gallery')->nullable()->after('image_url');
            $table->json('amenities')->nullable()->after('gallery');
            $table->unsignedTinyInteger('max_guests')->default(2)->after('amenities');
            $table->string('room_size')->nullable()->after('max_guests');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['image_url', 'gallery', 'amenities', 'max_guests', 'room_size']);
        });
    }
};
