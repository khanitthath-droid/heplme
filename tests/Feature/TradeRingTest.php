<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\WantedBook;
use App\Models\TradeRing;
use App\Models\ExchangeRequest;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TradeRingTest extends TestCase
{
    use RefreshDatabase;

    private User $user1;
    private User $user2;
    private User $user3;
    private Book $book1;
    private Book $book2;
    private Book $book3;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = User::factory()->create(['name' => 'User1', 'phone' => '0811111111', 'line_id' => 'user1_line']);
        $this->user2 = User::factory()->create(['name' => 'User2', 'phone' => '0822222222', 'line_id' => 'user2_line']);
        $this->user3 = User::factory()->create(['name' => 'User3', 'phone' => '0833333333', 'line_id' => 'user3_line']);

        // User 1 has Book 1, wants Book 3
        $this->book1 = Book::create([
            'user_id' => $this->user1->id,
            'title' => 'Book 1 - Clean Code',
            'author' => 'Uncle Bob',
            'status' => 'available',
        ]);
        WantedBook::create([
            'user_id' => $this->user1->id,
            'title' => 'Book 3 - Design Patterns',
        ]);

        // User 2 has Book 2, wants Book 1
        $this->book2 = Book::create([
            'user_id' => $this->user2->id,
            'title' => 'Book 2 - Refactoring',
            'author' => 'Martin Fowler',
            'status' => 'available',
        ]);
        WantedBook::create([
            'user_id' => $this->user2->id,
            'title' => 'Book 1 - Clean Code',
        ]);

        // User 3 has Book 3, wants Book 2
        $this->book3 = Book::create([
            'user_id' => $this->user3->id,
            'title' => 'Book 3 - Design Patterns',
            'author' => 'GoF',
            'status' => 'available',
        ]);
        WantedBook::create([
            'user_id' => $this->user3->id,
            'title' => 'Book 2 - Refactoring',
        ]);
    }

    public function test_user_can_initiate_3way_trade_ring(): void
    {
        $response = $this->actingAs($this->user1)->post(route('trade-rings.store'), [
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
        ]);

        $response->assertRedirect(route('matching.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('trade_rings', [
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'user1_status' => 'accepted', // Initiator is automatically accepted
            'user2_id' => $this->user2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);
    }

    public function test_cannot_create_duplicate_3way_trade_ring(): void
    {
        TradeRing::create([
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user1_status' => 'accepted',
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user2)->post(route('trade-rings.store'), [
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, TradeRing::count());
    }

    public function test_3way_trade_ring_step_by_step_acceptance(): void
    {
        $ring = TradeRing::create([
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user1_status' => 'accepted',
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);

        $this->assertEquals(1, $ring->acceptedCount());
        $this->assertFalse($ring->allAccepted());

        // Step 2: User 2 accepts
        $response = $this->actingAs($this->user2)->post(route('trade-rings.accept', $ring->id));
        $response->assertSessionHas('success');

        $ring->refresh();
        $this->assertEquals('accepted', $ring->user2_status);
        $this->assertEquals('pending', $ring->status); // Still pending because User 3 hasn't accepted
        $this->assertEquals(2, $ring->acceptedCount());
        $this->assertFalse($ring->allAccepted());

        // Also create a competing pending 2-way request to verify auto-rejection
        $competingUser = User::factory()->create();
        $competingBook = Book::create([
            'user_id' => $competingUser->id,
            'title' => 'Competing Book',
            'status' => 'available',
        ]);
        $competingReq = ExchangeRequest::create([
            'requester_id' => $competingUser->id,
            'receiver_id' => $this->user1->id,
            'offered_book_id' => $competingBook->id,
            'requested_book_id' => $this->book1->id,
            'status' => 'pending',
        ]);

        // Step 3: User 3 accepts (Final 3/3 acceptance!)
        $response = $this->actingAs($this->user3)->post(route('trade-rings.accept', $ring->id));
        $response->assertSessionHas('success');

        $ring->refresh();
        $this->assertEquals('accepted', $ring->user3_status);
        $this->assertEquals('accepted', $ring->status);
        $this->assertEquals(3, $ring->acceptedCount());
        $this->assertTrue($ring->allAccepted());

        // Verify all 3 books are marked as 'exchanged'
        $this->assertEquals('exchanged', $this->book1->fresh()->status);
        $this->assertEquals('exchanged', $this->book2->fresh()->status);
        $this->assertEquals('exchanged', $this->book3->fresh()->status);

        // Verify competing request was auto-rejected
        $this->assertEquals('rejected', $competingReq->fresh()->status);
    }

    public function test_participant_can_reject_trade_ring(): void
    {
        $ring = TradeRing::create([
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user1_status' => 'accepted',
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->user2)->post(route('trade-rings.reject', $ring->id));
        $response->assertSessionHas('success');

        $ring->refresh();
        $this->assertEquals('rejected', $ring->status);
        $this->assertEquals('rejected', $ring->user2_status);
    }

    public function test_non_participant_cannot_accept_trade_ring(): void
    {
        $outsider = User::factory()->create();

        $ring = TradeRing::create([
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user1_status' => 'accepted',
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($outsider)->post(route('trade-rings.accept', $ring->id));
        $response->assertStatus(403);
    }

    public function test_matching_page_and_exchange_requests_page_render_correctly(): void
    {
        $ring = TradeRing::create([
            'initiator_id' => $this->user1->id,
            'user1_id' => $this->user1->id,
            'book1_id' => $this->book1->id,
            'user1_status' => 'accepted',
            'user2_id' => $this->user2->id,
            'book2_id' => $this->book2->id,
            'user2_status' => 'pending',
            'user3_id' => $this->user3->id,
            'book3_id' => $this->book3->id,
            'user3_status' => 'pending',
            'status' => 'pending',
        ]);

        // User 1 matching page
        $res1 = $this->actingAs($this->user1)->get(route('matching.index'));
        $res1->assertStatus(200);
        $res1->assertSee('วงจรแลกเปลี่ยน 3 ฝ่าย');
        $res1->assertSee('คุณ: ยืนยันแล้ว');

        // User 2 matching page
        $res2 = $this->actingAs($this->user2)->get(route('matching.index'));
        $res2->assertStatus(200);
        $res2->assertSee('กดยืนยันยอมรับการแลกเปลี่ยน 3 ฝ่าย');

        // User 2 exchange requests index page
        $res3 = $this->actingAs($this->user2)->get(route('exchange-requests.index'));
        $res3->assertStatus(200);
        $res3->assertSee('วงจร 3 ฝ่าย');
        $res3->assertSee('กดยืนยันยอมรับ');
    }
}
