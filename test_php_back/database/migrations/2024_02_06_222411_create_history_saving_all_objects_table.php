<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_saving_all_objects', function (Blueprint $table) {
            $table->id();

            $table->string('table_name');
            $table->json('old_object_data')->nullable();
            $table->json('new_object_data')->nullable();
            $table->unsignedBigInteger('original_instance_id')->nullable()->index();
            $table->unsignedBigInteger('history_change_id')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_saving_all_objects');
    }
};
