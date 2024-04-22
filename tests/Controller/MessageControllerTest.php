<?php
declare(strict_types=1);

namespace App\Tests\Controller;

use App\Enum\StatusMessage;
use App\Message\SendMessage;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Zenstruck\Messenger\Test\InteractsWithMessenger;

class MessageControllerTest extends WebTestCase
{
    use InteractsWithMessenger;
    
    function test_can_list_messages_with_valid_limit(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => '10']) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $content = $client->getResponse()->getContent() ?: '{}';
        $this->assertJson($content);

        /**
         * @var array{
         *     messages: list<array{
         *         uuid: string,
         *         text: string,
         *         status: string
         *     }>,
         *     page: int,
         *     limit: int,
         *     totalItems: int
         * } $responseContent
         */
        $responseContent = json_decode($content, true);

        $this->assertArrayHasKey('messages', $responseContent);
        $this->assertArrayHasKey('page', $responseContent);
        $this->assertArrayHasKey('limit', $responseContent);
        $this->assertArrayHasKey('totalItems', $responseContent);
        $this->assertIsArray($responseContent['messages']);
        foreach ($responseContent['messages'] as $message) {
            $this->assertArrayHasKey('uuid', $message);
            $this->assertArrayHasKey('text', $message);
            $this->assertArrayHasKey('status', $message);
        }
        $this->assertSame(1, $responseContent['page']);
        $this->assertSame(10, $responseContent['limit']);
        $this->assertCount(10, $responseContent['messages']);
    }

    function test_can_list_messages_with_limit_and_valid_status_of_message(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => 10, 'status' => StatusMessage::read->value]) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent() ?: '{}';

        /**
         * @var array{
         *     messages: list<array{
         *         uuid: string,
         *         text: string,
         *         status: string
         *     }>,
         *     page: int,
         *     limit: int,
         *     totalItems: int
         * } $responseContent
         */
        $responseContent = json_decode($content, true);
        $this->assertIsArray($responseContent['messages']);
        foreach ($responseContent['messages'] as $message) {
            $this->assertSame(StatusMessage::read->value, $message['status']);
        }
    }

    function test_can_list_messages_with_valid_page_and_limit_params(): void
    {
        $client = static::createClient();
        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => 3, 'page' => 2]) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());

        $content = $client->getResponse()->getContent() ?: '{}';

        /**
         * @var array{
         *     messages: list<array{
         *         uuid: string,
         *         text: string,
         *         status: string
         *     }>,
         *     page: int,
         *     limit: int,
         *     totalItems: int
         * } $responseContent
         */
        $responseContent = json_decode($content, true);

        $this->assertSame(2, $responseContent['page']);
        $this->assertSame(3, $responseContent['limit']);
        $this->assertCount(3, $responseContent['messages']);
    }

    function test_cannot_list_with_not_valid_limit_and_page_format(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => 'not-valid-format', 'page' => 'not-valid-format']) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    function test_cannot_list_with_empty_body(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([]) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    function test_cannot_list_with_invalid_limit(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => -25]) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    function test_cannot_list_with_invalid_page_number(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => 2, 'page' => -4]) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }

    function test_cannot_list_with_invalid_status(): void
    {
        $client = static::createClient();

        $client->request(
            'POST',
            'api/v1/messages',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['limit' => 4, 'status' => 'not-valid-status']) ?: '{}'
        );

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $client->getResponse()->getStatusCode());
    }


    function test_that_it_sends_a_message(): void
    {
        $client = static::createClient();
        $client->request('POST', 'api/v1/messages/send', ['text' => 'Hello, world!']);

        $this->assertResponseIsSuccessful();
        // This is using https://packagist.org/packages/zenstruck/messenger-test
        $this->transport('sync')
            ->queue()
            ->assertContains(SendMessage::class, 1);
    }
}