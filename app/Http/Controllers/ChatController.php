<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatMessage;
use App\Models\ExchangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Display chat inbox and active room.
     */
    public function index(Request $request, $roomId = null)
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        // ดึงรายการห้องแชตทั้งหมดของผู้ใช้
        $chatRooms = ChatRoom::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->with([
                'user1:id,name,avatar,last_seen_at',
                'user2:id,name,avatar,last_seen_at',
                'exchangeRequest.offeredBook:id,title,image,condition,author',
                'exchangeRequest.requestedBook:id,title,image,condition,author',
                'latestMessage',
            ])
            ->orderByDesc('last_message_at')
            ->orderByDesc('created_at')
            ->get();

        $activeRoom = null;
        if ($roomId) {
            $activeRoom = ChatRoom::with([
                'user1:id,name,avatar,last_seen_at',
                'user2:id,name,avatar,last_seen_at',
                'exchangeRequest.offeredBook:id,title,image,condition,author',
                'exchangeRequest.requestedBook:id,title,image,condition,author',
                'exchangeRequest.requester:id,name',
                'exchangeRequest.receiver:id,name',
            ])->findOrFail($roomId);

            // ตรวจสอบสิทธิ์
            if ($activeRoom->user1_id !== $user->id && $activeRoom->user2_id !== $user->id && $user->role !== 'admin') {
                abort(403, 'คุณไม่มีสิทธิ์เข้าถึงห้องแชตนี้');
            }
        } elseif ($chatRooms->isNotEmpty()) {
            $activeRoom = $chatRooms->first();
        }

        $messages = collect();
        $isBlocked = false;
        $counterpart = null;

        if ($activeRoom) {
            $counterpart = $activeRoom->otherUser($user->id);
            $isBlocked = $user->isBlockedWith($counterpart->id);

            // ทำเครื่องหมายว่าอ่านแล้วสำหรับข้อความของอีกฝ่าย
            ChatMessage::where('chat_room_id', $activeRoom->id)
                ->where('sender_id', '!=', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);

            // ดึงประวัติข้อความ
            $messages = ChatMessage::where('chat_room_id', $activeRoom->id)
                ->with('sender:id,name,avatar')
                ->orderBy('id', 'asc')
                ->get();
        }

        return view('chats.index', compact(
            'chatRooms',
            'activeRoom',
            'messages',
            'isBlocked',
            'counterpart'
        ));
    }

    /**
     * Start or open chat room for an exchange request.
     */
    public function start(ExchangeRequest $exchangeRequest)
    {
        $userId = Auth::id();

        // ตรวจสอบว่าผู้ใช้เป็นคู่แลกเปลี่ยนจริงหรือไม่
        if ($exchangeRequest->requester_id !== $userId && $exchangeRequest->receiver_id !== $userId && Auth::user()->role !== 'admin') {
            abort(403, 'คุณไม่มีสิทธิ์สนทนาในรายการแลกเปลี่ยนนี้');
        }

        $chatRoom = ChatRoom::firstOrCreate(
            ['exchange_request_id' => $exchangeRequest->id],
            [
                'user1_id' => $exchangeRequest->requester_id,
                'user2_id' => $exchangeRequest->receiver_id,
                'last_message_at' => now(),
            ]
        );

        // หากเป็นห้องใหม่ และยังไม่มีข้อความ ให้ใส่ข้อความระบบเริ่มต้น
        if ($chatRoom->wasRecentlyCreated) {
            ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $exchangeRequest->requester_id,
                'type' => 'system',
                'message' => '🎉 เริ่มต้นการพูดคุยสำหรับรายการแลกเปลี่ยนหนังสือ กรุณาประสานงาน นัดหมาย หรือส่งรูปภาพเพิ่มเติมผ่านห้องแชตนี้อย่างปลอดภัย',
                'is_read' => true,
            ]);
        }

        return redirect()->route('chats.show', $chatRoom->id);
    }

    /**
     * Fetch new messages for real-time polling.
     */
    public function fetchMessages(ChatRoom $chatRoom, Request $request)
    {
        $user = Auth::user();
        if ($chatRoom->user1_id !== $user->id && $chatRoom->user2_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // อัปเดตสถานะออนไลน์
        $user->update(['last_seen_at' => now()]);

        $afterId = $request->query('after_id');
        $query = ChatMessage::where('chat_room_id', $chatRoom->id)
            ->with('sender:id,name,avatar');

        if ($afterId) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->orderBy('id', 'asc')->get();

        // มาร์คข้อความของอีกฝ่ายว่าอ่านแล้ว
        ChatMessage::where('chat_room_id', $chatRoom->id)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        $counterpart = $chatRoom->otherUser($user->id);

        // ตรวจสอบ typing status
        $typingKey = "typing_room_{$chatRoom->id}_user_{$counterpart->id}";
        $isTyping = Cache::has($typingKey);

        return response()->json([
            'messages' => $messages->map(function ($msg) use ($user) {
                return [
                    'id' => $msg->id,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender ? $msg->sender->name : 'สมาชิก',
                    'sender_avatar' => $msg->sender && $msg->sender->avatar ? asset('storage/' . $msg->sender->avatar) : null,
                    'is_me' => $msg->sender_id === $user->id,
                    'message' => $msg->deleted_by_sender ? 'ข้อความนี้ถูกยกเลิกแล้ว' : $msg->message,
                    'type' => $msg->type,
                    'image_url' => (!$msg->deleted_by_sender && $msg->image_path) ? asset('storage/' . $msg->image_path) : null,
                    'metadata' => $msg->metadata,
                    'is_read' => $msg->is_read,
                    'deleted_by_sender' => $msg->deleted_by_sender,
                    'time' => $msg->created_at->locale('th')->translatedFormat('H:i น.'),
                    'date' => $msg->created_at->locale('th')->translatedFormat('j M Y'),
                ];
            }),
            'is_typing' => $isTyping,
            'counterpart_name' => $counterpart->name,
            'counterpart_online' => $counterpart->isOnline(),
            'counterpart_last_seen' => $counterpart->last_seen_at ? $counterpart->last_seen_at->locale('th')->diffForHumans() : 'ออฟไลน์',
            'exchange_status' => $chatRoom->exchangeRequest ? $chatRoom->exchangeRequest->status : null,
        ]);
    }

    /**
     * Send text or image message.
     */
    public function sendMessage(ChatRoom $chatRoom, Request $request)
    {
        $user = Auth::user();
        if ($chatRoom->user1_id !== $user->id && $chatRoom->user2_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $counterpart = $chatRoom->otherUser($user->id);
        if ($user->isBlockedWith($counterpart->id)) {
            return response()->json(['error' => 'ไม่สามารถส่งข้อความได้เนื่องจากมีการบล็อกผู้ใช้'], 403);
        }

        $request->validate([
            'message' => 'nullable|string|max:3000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if (empty($request->message) && !$request->hasFile('image')) {
            return response()->json(['error' => 'กรุณากรอกข้อความหรือแนบรูปภาพ'], 422);
        }

        $imagePath = null;
        $type = 'text';

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat_images', 'public');
            $type = 'image';
        }

        $chatMessage = ChatMessage::create([
            'chat_room_id' => $chatRoom->id,
            'sender_id' => $user->id,
            'message' => $request->message,
            'type' => $type,
            'image_path' => $imagePath,
            'is_read' => false,
        ]);

        $chatRoom->update(['last_message_at' => now()]);

        // เคลียร์ typing status ของผู้ส่ง
        Cache::forget("typing_room_{$chatRoom->id}_user_{$user->id}");

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $chatMessage->id,
                    'sender_id' => $chatMessage->sender_id,
                    'sender_name' => $user->name,
                    'is_me' => true,
                    'message' => $chatMessage->message,
                    'type' => $chatMessage->type,
                    'image_url' => $imagePath ? asset('storage/' . $imagePath) : null,
                    'metadata' => null,
                    'is_read' => false,
                    'time' => $chatMessage->created_at->locale('th')->translatedFormat('H:i น.'),
                ],
            ]);
        }

        return back();
    }

    /**
     * Send structured card (Meetup proposal or Shipping Tracking).
     */
    public function sendStructured(ChatRoom $chatRoom, Request $request)
    {
        $user = Auth::user();
        if ($chatRoom->user1_id !== $user->id && $chatRoom->user2_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $counterpart = $chatRoom->otherUser($user->id);
        if ($user->isBlockedWith($counterpart->id)) {
            return response()->json(['error' => 'ไม่สามารถส่งข้อความได้เนื่องจากมีการบล็อกผู้ใช้'], 403);
        }

        $request->validate([
            'card_type' => 'required|in:meetup,delivery',
        ]);

        if ($request->card_type === 'meetup') {
            $request->validate([
                'meetup_date' => 'required|date',
                'meetup_time' => 'required|string',
                'meetup_location' => 'required|string|max:255',
                'meetup_notes' => 'nullable|string|max:500',
            ]);

            $metadata = [
                'date' => $request->meetup_date,
                'time' => $request->meetup_time,
                'location' => $request->meetup_location,
                'notes' => $request->meetup_notes,
            ];

            $messageText = "📅 เสนอนัดรับหนังสือ: {$request->meetup_location} วันที่ {$request->meetup_date} เวลา {$request->meetup_time}";

            $chatMessage = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $user->id,
                'message' => $messageText,
                'type' => 'meetup',
                'metadata' => $metadata,
                'is_read' => false,
            ]);
        } else {
            $request->validate([
                'carrier' => 'required|string|max:100',
                'tracking_number' => 'required|string|max:100',
                'delivery_notes' => 'nullable|string|max:500',
            ]);

            $metadata = [
                'carrier' => $request->carrier,
                'tracking_number' => $request->tracking_number,
                'notes' => $request->delivery_notes,
            ];

            $messageText = "🚚 แจ้งเลขพัสดุจัดส่ง: {$request->carrier} เลขพัสดุ {$request->tracking_number}";

            $chatMessage = ChatMessage::create([
                'chat_room_id' => $chatRoom->id,
                'sender_id' => $user->id,
                'message' => $messageText,
                'type' => 'delivery',
                'metadata' => $metadata,
                'is_read' => false,
            ]);
        }

        $chatRoom->update(['last_message_at' => now()]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => $chatMessage]);
        }

        return back()->with('success', 'ส่งรายละเอียดเรียบร้อยแล้ว');
    }

    /**
     * Delete own message.
     */
    public function deleteMessage(ChatMessage $chatMessage)
    {
        $user = Auth::user();
        if ($chatMessage->sender_id !== $user->id && $user->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // ลบรูปภาพออกจาก storage ถ้ามี
        if ($chatMessage->image_path) {
            Storage::disk('public')->delete($chatMessage->image_path);
        }

        $chatMessage->update([
            'deleted_by_sender' => true,
            'message' => null,
            'image_path' => null,
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Typing heartbeat.
     */
    public function typing(ChatRoom $chatRoom)
    {
        $user = Auth::user();
        if ($chatRoom->user1_id !== $user->id && $chatRoom->user2_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Cache::put("typing_room_{$chatRoom->id}_user_{$user->id}", true, now()->addSeconds(4));

        return response()->json(['success' => true]);
    }

    /**
     * Global unread count for navbar badge.
     */
    public function unreadCount()
    {
        $user = Auth::user();
        $user->update(['last_seen_at' => now()]);

        $rooms = ChatRoom::where('user1_id', $user->id)
            ->orWhere('user2_id', $user->id)
            ->pluck('id');

        $unreadCount = ChatMessage::whereIn('chat_room_id', $rooms)
            ->where('sender_id', '!=', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'unread_count' => $unreadCount,
        ]);
    }
}
