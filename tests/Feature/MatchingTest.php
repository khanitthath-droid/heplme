<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Book;
use App\Models\WantedBook;
use App\Services\MatchingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchingTest extends TestCase
{
    use RefreshDatabase;

    public function test_two_way_matching_works_correctly(): void
    {
        $userA = User::factory()->create(['name' => 'Alice']);
        $userB = User::factory()->create(['name' => 'Bob']);

        // Alice has Book A, wants Book B
        $bookA = Book::create([
            'user_id' => $userA->id,
            'title' => 'Harry Potter and the Philosopher Stone',
            'author' => 'J.K. Rowling',
            'status' => 'available',
        ]);
        WantedBook::create([
            'user_id' => $userA->id,
            'title' => 'The Lord of the Rings',
            'author' => 'J.R.R. Tolkien',
        ]);

        // Bob has Book B, wants Book A
        $bookB = Book::create([
            'user_id' => $userB->id,
            'title' => 'The Lord of the Rings: Fellowship of the Ring',
            'author' => 'J.R.R. Tolkien',
            'status' => 'available',
        ]);
        WantedBook::create([
            'user_id' => $userB->id,
            'title' => 'Harry Potter',
            'author' => null, // Test null author safety
        ]);

        $matchesA = MatchingService::getTwoWayMatches($userA);
        $this->assertCount(1, $matchesA);
        $this->assertEquals($bookA->id, $matchesA[0]['my_book']->id);
        $this->assertEquals($bookB->id, $matchesA[0]['other_book']->id);

        $matchesB = MatchingService::getTwoWayMatches($userB);
        $this->assertCount(1, $matchesB);
        $this->assertEquals($bookB->id, $matchesB[0]['my_book']->id);
        $this->assertEquals($bookA->id, $matchesB[0]['other_book']->id);
    }

    public function test_three_way_circular_matching_works_correctly(): void
    {
        $user1 = User::factory()->create(['name' => 'User1']);
        $user2 = User::factory()->create(['name' => 'User2']);
        $user3 = User::factory()->create(['name' => 'User3']);

        // User1 has Book 1, wants Book 2
        $book1 = Book::create(['user_id' => $user1->id, 'title' => 'Clean Code', 'status' => 'available']);
        WantedBook::create(['user_id' => $user1->id, 'title' => 'Refactoring']);

        // User2 has Book 2, wants Book 3
        $book2 = Book::create(['user_id' => $user2->id, 'title' => 'Refactoring', 'status' => 'available']);
        WantedBook::create(['user_id' => $user2->id, 'title' => 'Design Patterns']);

        // User3 has Book 3, wants Book 1
        $book3 = Book::create(['user_id' => $user3->id, 'title' => 'Design Patterns', 'status' => 'available']);
        WantedBook::create(['user_id' => $user3->id, 'title' => 'Clean Code']);

        // Check 2-Way Matches (Should be 0)
        $this->assertCount(0, MatchingService::getTwoWayMatches($user1));

        // Check 3-Way Matches for User1
        $rings1 = MatchingService::getThreeWayMatches($user1);
        $this->assertCount(1, $rings1);
        $this->assertEquals($book1->id, $rings1[0]['my_book']->id);
        $this->assertEquals($book2->id, $rings1[0]['my_receive_book']->id);
        $this->assertEquals($user3->id, $rings1[0]['partner1_user']->id);
        $this->assertEquals($user2->id, $rings1[0]['partner2_user']->id);

        // Check 3-Way Matches for User2
        $rings2 = MatchingService::getThreeWayMatches($user2);
        $this->assertCount(1, $rings2);
        $this->assertEquals($book2->id, $rings2[0]['my_book']->id);
        $this->assertEquals($book3->id, $rings2[0]['my_receive_book']->id);

        // Check Matching Page Response
        $response = $this->actingAs($user1)->get(route('matching.index'));
        $response->assertStatus(200);
        $response->assertSee('Clean Code');
        $response->assertSee('Design Patterns');
    }
}
