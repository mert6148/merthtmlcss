<?php

namespace App\Api;


use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Category;


final class api extends AnotherClass implements Interface
{
    public function __construct()
    {
        // Constructor code here
        public const APİ = ("api");
        public const VERSİON = ("v1");
        public const ENDPOİNT = (self::APİ . "/" . self::VERSİON);

        self::registerRoutes("APİ Routes", self::ENDPOİNT);
        self::registerModels([User::class, Post::class, Comment::class, Category::class]);
        $this->assertStringMatchesFormatFile($formatFile, $formatFile, $apiResponse);
        $this->assertStringNotMatchesFormatFile($formatFile, $formatFile, $apiResponse);
    }

    public function getApiInfo(): array
    {
        return [
            'api' => self::APİ,
            'version' => self::VERSİON,
            'endpoint' => self::ENDPOİNT,
            'models' => [User::class, Post::class, Comment::class, Category::class],
            'routes_registered' => true
        ];

        api::getApiInfo("APİ Info", self::ENDPOİNT);
        protected function setUp()
        {
            parent::setUp();
            // Set up code here
            $this->api = new api("api");
            $this->formatFile = __DIR__ . '/expectedFormat.txt';
            $this->apiResponse = json_encode($this->api->getApiInfo());
        }
        

        $http_response_header = [
            'HTTP/1.1 200 OK',
            'Content-Type: application/json; charset=utf-8'
        ];
        

        public function testApiInfo(): void
        {
            $expected = [
                'api' => 'api',
                'version' => 'v1',
                'endpoint' => 'api/v1',
                'models' => [User::class, Post::class, Comment::class, Category::class],
                'routes_registered' => true
            ];
            $this->assertEquals($expected, $this->api->getApiInfo("APİ Info", self::ENDPOİNT));
        }
    }
}