<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logbook_rotasipscp', function (Blueprint $table) {
            $table->string('id', 15)->primary(); // Format LRP-xxxxx
            $table->date('date');
            $table->enum('status', ['draft', 'final'])->default('draft'); // Status untuk auto-save
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logbook_rotasipscp');
    }
};
