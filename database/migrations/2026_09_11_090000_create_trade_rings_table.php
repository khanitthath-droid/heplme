<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_rings', function (Blueprint $table) {
            $table->id();

            // ผู้ริเริ่มสร้างวงจร 3 ฝ่าย
            $table->foreignId('initiator_id')->constrained('users')->onDelete('cascade');

            // ฝ่ายที่ 1
            $table->foreignId('user1_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book1_id')->constrained('books')->onDelete('cascade');
            $table->enum('user1_status', ['pending', 'accepted', 'rejected'])->default('pending');

            // ฝ่ายที่ 2
            $table->foreignId('user2_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book2_id')->constrained('books')->onDelete('cascade');
            $table->enum('user2_status', ['pending', 'accepted', 'rejected'])->default('pending');

            // ฝ่ายที่ 3
            $table->foreignId('user3_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('book3_id')->constrained('books')->onDelete('cascade');
            $table->enum('user3_status', ['pending', 'accepted', 'rejected'])->default('pending');

            // สถานะรวมของวงจร ('pending' -> 'accepted' เมื่อครบ 3/3 หรือ 'rejected' เมื่อมีใครปฏิเสธ)
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_rings');
    }
};
