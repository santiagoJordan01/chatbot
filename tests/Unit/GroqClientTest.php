<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\GroqClient;

class GroqClientTest extends TestCase
{
    public function test_chat_returns_json()
    {
        Http::fake([
            '*' => Http::response(['result' => 'hello'], 200),
        ]);

        $client = new GroqClient();
        $resp = $client->chat('hi');

        $this->assertIsArray($resp);
        $this->assertEquals('hello', $resp['result']);
    }
}
