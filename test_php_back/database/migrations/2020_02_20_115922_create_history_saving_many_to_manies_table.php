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
        Schema::create('history_saving_many_to_manies', function (Blueprint $table) {
            $table->id();

            $table->string('first_table');
            $table->string('second_table');

            $table->unsignedBigInteger('first_id');
            $table->unsignedBigInteger('second_id');

            $table->index(['first_table', 'first_id']);
            $table->index(['second_table', 'second_id']);

            $table->json('first_data')->nullable();
            $table->json('second_data')->nullable();

            $table->timestamp('change_made_at', 6)->nullable();
            $table->timestamp('expired_at', 6)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_saving_many_to_manies');
    }
};
