<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_belongs_to_sender(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $chat = Chat::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello, world!'
        ]);

        $this->assertInstanceOf(User::class, $chat->sender);
        $this->assertEquals($sender->id, $chat->sender->id);
    }

    public function test_chat_belongs_to_receiver(): void
    {
        $sender = User::factory()->create();
        $receiver = User::factory()->create();
        
        $chat = Chat::factory()->create([
            'sender_id' => $sender->id,
            'receiver_id' => $receiver->id,
            'message' => 'Hello, world!'
        ]);

        $this->assertInstanceOf(User::class, $chat->receiver);
        $this->assertEquals($receiver->id, $chat->receiver->id);
    }

    public function test_chat_casts(): void
    {
        $chat = Chat::factory()->create([
            'is_read' => '1',
            'is_archived' => '0',
            'metadata' => ['type' => 'image', 'size' => 'large']
        ]);

        $this->assertIsBool($chat->is_read);
        $this->assertIsBool($chat->is_archived);
        $this->assertIsArray($chat->metadata);

        $this->assertTrue($chat->is_read);
        $this->assertFalse($chat->is_archived);
        $this->assertEquals(['type' => 'image', 'size' => 'large'], $chat->metadata);
    }

    public function test_scope_between_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Create chats between user1 and user2
        Chat::factory()->create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Message 1'
        ]);

        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Message 2'
        ]);

        // Create chat between user1 and user3 (should not appear in result)
        Chat::factory()->create([
            'sender_id' => $user1->id,
            'receiver_id' => $user3->id,
            'message' => 'Message 3'
        ]);

        $chats = Chat::betweenUsers($user1->id, $user2->id)->get();

        $this->assertCount(2, $chats);
        $this->assertTrue($chats->contains('message', 'Message 1'));
        $this->assertTrue($chats->contains('message', 'Message 2'));
        $this->assertFalse($chats->contains('message', 'Message 3'));
    }

    public function test_scope_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Create chats involving user1
        Chat::factory()->create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'From user1 to user2'
        ]);

        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'From user2 to user1'
        ]);

        // Create chat not involving user1
        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user3->id,
            'message' => 'From user2 to user3'
        ]);

        $chats = Chat::byUser($user1->id)->get();

        $this->assertCount(2, $chats);
        $this->assertTrue($chats->contains('message', 'From user1 to user2'));
        $this->assertTrue($chats->contains('message', 'From user2 to user1'));
        $this->assertFalse($chats->contains('message', 'From user2 to user3'));
    }

    public function test_scope_unread_by_user(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create unread message for user1
        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Unread message',
            'is_read' => false
        ]);

        // Create read message for user1
        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Read message',
            'is_read' => true
        ]);

        // Create unread message for user2 (should not appear in result)
        Chat::factory()->create([
            'sender_id' => $user1->id,
            'receiver_id' => $user2->id,
            'message' => 'Unread message for user2',
            'is_read' => false
        ]);

        $unreadChats = Chat::unreadByUser($user1->id)->get();

        $this->assertCount(1, $unreadChats);
        $this->assertEquals('Unread message', $unreadChats->first()->message);
        $this->assertFalse($unreadChats->first()->is_read);
    }

    public function test_mark_as_read_static_method(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        // Create unread messages
        $chat1 = Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Message 1',
            'is_read' => false
        ]);

        $chat2 = Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Message 2',
            'is_read' => false
        ]);

        // Mark all messages from user2 as read for user1
        $updatedCount = Chat::markAsRead($user1->id, $user2->id);

        $this->assertEquals(2, $updatedCount);

        // Refresh from database to check status
        $chat1->refresh();
        $chat2->refresh();

        $this->assertTrue($chat1->is_read);
        $this->assertTrue($chat2->is_read);
    }

    public function test_mark_as_read_all_messages(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $user3 = User::factory()->create();

        // Create unread messages from multiple users to user1
        Chat::factory()->create([
            'sender_id' => $user2->id,
            'receiver_id' => $user1->id,
            'message' => 'Message from user2',
            'is_read' => false
        ]);

        Chat::factory()->create([
            'sender_id' => $user3->id,
            'receiver_id' => $user1->id,
            'message' => 'Message from user3',
            'is_read' => false
        ]);

        // Mark all messages to user1 as read
        $updatedCount = Chat::markAsRead($user1->id);

        $this->assertEquals(2, $updatedCount);

        // Check that all messages to user1 are now read
        $unreadChats = Chat::unreadByUser($user1->id)->get();
        $this->assertCount(0, $unreadChats);
    }

    public function test_chat_fillable_attributes(): void
    {
        $fillable = [
            'sender_id',
            'receiver_id',
            'message',
            'is_read',
            'is_archived',
            'messageable_type',
            'messageable_id',
            'metadata',
        ];

        $chat = new Chat();
        $this->assertEquals($fillable, $chat->getFillable());
    }

    public function test_chat_default_values(): void
    {
        $chat = Chat::factory()->make();

        $this->assertNull($chat->id);
        $this->assertNull($chat->sender_id);
        $this->assertNull($chat->receiver_id);
        $this->assertNull($chat->message);
        $this->assertFalse($chat->is_read);
        $this->assertFalse($chat->is_archived);
        $this->assertNull($chat->messageable_type);
        $this->assertNull($chat->messageable_id);
        $this->assertNull($chat->metadata);
    }
}