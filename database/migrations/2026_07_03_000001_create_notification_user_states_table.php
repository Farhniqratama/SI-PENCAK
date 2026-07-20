<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_user_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('notification_id')->constrained('notifications')->cascadeOnDelete();
            $table->string('owner_type');
            $table->unsignedBigInteger('owner_id');
            $table->boolean('is_read')->default(false);
            $table->timestamp('deleted_at')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'owner_type', 'owner_id'], 'notification_owner_unique');
            $table->index(['owner_type', 'owner_id', 'deleted_at'], 'notification_owner_state_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_user_states');
    }
};
