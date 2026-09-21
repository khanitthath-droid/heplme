<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. เพิ่มฟิลด์ last_seen_at ให้ตาราง users
        if (!Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('last_seen_at')->nullable()->after('exchange_area');
            });
        }

        // 2. ขยายฟิลด์ใน exchange_requests สำหรับการยืนยันส่งมอบ และสถานะ completed
        if (!Schema::hasColumn('exchange_requests', 'requester_confirmed_at')) {
            Schema::table('exchange_requests', function (Blueprint $table) {
                $table->timestamp('requester_confirmed_at')->nullable()->after('status');
                $table->timestamp('receiver_confirmed_at')->nullable()->after('requester_confirmed_at');
            });
        }

        // ปรับเปลี่ยน status ให้รองรับ 'completed'
        try {
            DB::statement("ALTER TABLE exchange_requests MODIFY COLUMN status ENUM('pending', 'accepted', 'completed', 'rejected') DEFAULT 'pending'");
        } catch (\Throwable $e) {
            // กรณี SQLite หรือ ไดรเวอร์อื่นที่ไม่รองรับ ALTER MODIFY
        }

        // 3. สร้างตาราง chat_rooms
        if (!Schema::hasTable('chat_rooms')) {
            Schema::create('chat_rooms', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exchange_request_id')
                    ->constrained('exchange_requests')
                    ->onDelete('cascade');
                $table->foreignId('user1_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->foreignId('user2_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->timestamp('last_message_at')->nullable();
                $table->timestamps();

                $table->unique('exchange_request_id');
            });
        }

        // 4. สร้างตาราง chat_messages
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('chat_room_id')
                    ->constrained('chat_rooms')
                    ->onDelete('cascade');
                $table->foreignId('sender_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->text('message')->nullable();
                $table->string('type', 30)->default('text'); // 'text', 'image', 'meetup', 'delivery', 'system'
                $table->string('image_path')->nullable();
                $table->json('metadata')->nullable(); // วันเวลานัดรับ, tracking number, etc.
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->boolean('deleted_by_sender')->default(false);
                $table->timestamps();

                $table->index(['chat_room_id', 'created_at']);
            });
        }

        // 5. สร้างตาราง user_blocks
        if (!Schema::hasTable('user_blocks')) {
            Schema::create('user_blocks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->foreignId('blocked_user_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->timestamps();

                $table->unique(['user_id', 'blocked_user_id']);
            });
        }

        // 6. สร้างตาราง reports
        if (!Schema::hasTable('reports')) {
            Schema::create('reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('reporter_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->foreignId('reported_user_id')
                    ->constrained('users')
                    ->onDelete('cascade');
                $table->foreignId('chat_room_id')
                    ->nullable()
                    ->constrained('chat_rooms')
                    ->nullOnDelete();
                $table->foreignId('chat_message_id')
                    ->nullable()
                    ->constrained('chat_messages')
                    ->nullOnDelete();
                $table->string('reason');
                $table->text('details')->nullable();
                $table->enum('status', ['pending', 'resolved', 'dismissed'])->default('pending');
                $table->text('admin_notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
        Schema::dropIfExists('user_blocks');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_rooms');

        if (Schema::hasColumn('exchange_requests', 'receiver_confirmed_at')) {
            Schema::table('exchange_requests', function (Blueprint $table) {
                $table->dropColumn(['requester_confirmed_at', 'receiver_confirmed_at']);
            });
        }

        if (Schema::hasColumn('users', 'last_seen_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('last_seen_at');
            });
        }
    }
};
