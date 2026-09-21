<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\ExchangeRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExchangeDuplicatePreventionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_send_duplicate_pending_request(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $bookA = Book::create(['user_id' => $userA->id, 'title' => 'Book A', 'status' => 'available']);
        $bookB = Book::create(['user_id' => $userB->id, 'title' => 'Book B', 'status' => 'available']);

        // First request: Should succeed
        $response1 = $this->actingAs($userA)->post(route('exchange-requests.store'), [
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookA->id,
            'requested_book_id' => $bookB->id,
        ]);
        $response1->assertSessionHas('success');
        $this->assertDatabaseCount('exchange_requests', 1);

        // Second duplicate request: Should fail and give error
        $response2 = $this->actingAs($userA)->post(route('exchange-requests.store'), [
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookA->id,
            'requested_book_id' => $bookB->id,
        ]);
        $response2->assertSessionHas('error', 'คุณได้ส่งคำขอแลกเปลี่ยนคู่นี้ไปแล้ว และกำลังรอการตอบรับ');
        $this->assertDatabaseCount('exchange_requests', 1);
    }

    public function test_user_cannot_send_cross_reverse_request_when_partner_already_sent_pending(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $bookA = Book::create(['user_id' => $userA->id, 'title' => 'Book A', 'status' => 'available']);
        $bookB = Book::create(['user_id' => $userB->id, 'title' => 'Book B', 'status' => 'available']);

        // User B sends request to User A (offering Book B, requesting Book A)
        ExchangeRequest::create([
            'requester_id' => $userB->id,
            'receiver_id' => $userA->id,
            'offered_book_id' => $bookB->id,
            'requested_book_id' => $bookA->id,
            'status' => 'pending',
        ]);

        // User A tries to send reverse request to User B (offering Book A, requesting Book B)
        $response = $this->actingAs($userA)->post(route('exchange-requests.store'), [
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookA->id,
            'requested_book_id' => $bookB->id,
        ]);

        $response->assertSessionHas('error', 'อีกฝ่ายได้ส่งคำขอแลกเปลี่ยนคู่นี้มาถึงคุณแล้ว คุณสามารถไปกดยอมรับได้ในหน้ารายการคำขอ');
        $this->assertDatabaseCount('exchange_requests', 1);
    }

    public function test_user_cannot_offer_books_not_owned(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();

        $bookC = Book::create(['user_id' => $userC->id, 'title' => 'Book C', 'status' => 'available']);
        $bookB = Book::create(['user_id' => $userB->id, 'title' => 'Book B', 'status' => 'available']);

        // User A tries to offer Book C (which belongs to User C)
        $response = $this->actingAs($userA)->post(route('exchange-requests.store'), [
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookC->id,
            'requested_book_id' => $bookB->id,
        ]);

        $response->assertSessionHas('error', 'คุณไม่ใช่เจ้าของหนังสือที่นำมาเสนอแลกเปลี่ยน หรือไม่พบข้อมูลหนังสือ');
        $this->assertDatabaseCount('exchange_requests', 0);
    }

    public function test_accepting_request_updates_both_books_and_auto_rejects_competing_requests(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        $userC = User::factory()->create();

        $bookA = Book::create(['user_id' => $userA->id, 'title' => 'Book A', 'status' => 'available']);
        $bookB = Book::create(['user_id' => $userB->id, 'title' => 'Book B', 'status' => 'available']);
        $bookC = Book::create(['user_id' => $userC->id, 'title' => 'Book C', 'status' => 'available']);

        // Request 1: User A wants Book B (offering Book A)
        $req1 = ExchangeRequest::create([
            'requester_id' => $userA->id,
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookA->id,
            'requested_book_id' => $bookB->id,
            'status' => 'pending',
        ]);

        // Request 2: User C ALSO wants Book B (offering Book C)
        $req2 = ExchangeRequest::create([
            'requester_id' => $userC->id,
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookC->id,
            'requested_book_id' => $bookB->id,
            'status' => 'pending',
        ]);

        // User B accepts Request 1 from User A
        $response = $this->actingAs($userB)->patch(route('exchange-requests.accept', $req1));
        $response->assertSessionHas('success');

        // Verify status updates
        $this->assertEquals('accepted', $req1->fresh()->status);
        $this->assertEquals('exchanged', $bookA->fresh()->status);
        $this->assertEquals('exchanged', $bookB->fresh()->status);

        // Verify competing request 2 was automatically rejected
        $this->assertEquals('rejected', $req2->fresh()->status);

        // Verify User C cannot accept or make new requests for Book B
        $response3 = $this->actingAs($userC)->post(route('exchange-requests.store'), [
            'receiver_id' => $userB->id,
            'offered_book_id' => $bookC->id,
            'requested_book_id' => $bookB->id,
        ]);
        $response3->assertSessionHas('error', 'หนังสือที่คุณต้องการได้รับการแลกเปลี่ยนไปแล้ว');
    }
}
