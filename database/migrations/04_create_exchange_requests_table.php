<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_requests', function (Blueprint $table) {
            $table->id();

            // คนที่ส่งคำขอแลกเปลี่ยน
            $table->foreignId('requester_id')
                ->constrained('users')
                ->onDelete('cascade');

            // คนที่ได้รับคำขอ
            $table->foreignId('receiver_id')
                ->constrained('users')
                ->onDelete('cascade');

            // หนังสือที่ผู้ส่งเสนอ
            $table->foreignId('offered_book_id')
                ->constrained('books')
                ->onDelete('cascade');

            // หนังสือที่ผู้ส่งต้องการ
            $table->foreignId('requested_book_id')
                ->constrained('books')
                ->onDelete('cascade');

            // สถานะคำขอ
            $table->enum('status', [
                'pending',
                'accepted',
                'rejected'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_requests');
    }
};