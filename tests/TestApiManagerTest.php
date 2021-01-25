<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Test\TestApi\Manager\TestApiManager;
use Test\TestApi\Model\User;
use Test\TestApi\Response\TestTokenResponse;
use Test\TestApi\Response\TestUserResponse;

class TestApiManagerTest extends TestCase
{
    protected const CONFIG = [
        'test' => [
            'api' => [
                'url' => 'http://testapi.ru',
                'login' => 'test',
                'pass' => '12345',
            ]
        ]
    ];

    public function testGetToken(): void
    {
        $testApi = new TestApiManager(self::CONFIG, $this->getMockTokenClient());

        //200:Ok
        $response = $testApi->getToken();
        self::assertInstanceOf(TestTokenResponse::class, $response);
        self::assertTrue($response->isSuccess());
        self::assertEquals('dsfd79843r32d1d3dx23d32d', $response->getToken());

        //200:Error
        $response = $testApi->getToken();
        self::assertInstanceOf(TestTokenResponse::class, $response);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getToken());

        //200:Not found
        $response = $testApi->getToken();
        self::assertInstanceOf(TestTokenResponse::class, $response);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getToken());

        //Exception
        $response = $testApi->getToken();
        self::assertInstanceOf(TestTokenResponse::class, $response);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getToken());
    }

    public function testGetUser(): void
    {
        $testApi = new TestApiManager(self::CONFIG, $this->getMockGetUserClient());

        $this->expectException(\RuntimeException::class);

        $response = $testApi->getUser('petrov');
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        $testApi->token = 'dsfd79843r32d1d3dx23d32d';

        //200:Ok
        $response = $testApi->getUser('ivanov');
        self::assertTrue($response->isSuccess());
        self::assertNotEmpty($response->getUserData());
        $data = $response->getUserData();
        self::assertEquals(23, $data->getId());
        self::assertEquals(1, $data->getActive());
        self::assertFalse($data->getBlocked());
        self::assertEquals('Ivanov Ivan', $data->getName());
        self::assertEquals(1587457590, $data->getCreatedAt());
        self::assertEquals(
            [
                [
                    "id" => 1,
                    "permission" => "comment"
                ],
                [
                    "id" => 2,
                    "permission" => "upload photo"
                ],
                [
                    "id" => 3,
                    "permission" => "add event"
                ]
            ], $data->getPermissions());
        self::assertInstanceOf(TestUserResponse::class, $response);

        //200:Error
        $response = $testApi->getUser('petrov');
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        //200:Not found
        $response = $testApi->getUser('ivvanov');
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        //exception
        $response = $testApi->getUser('ivvanov');
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);
    }

    public function testUpdateUser(): void
    {
        $testApi = new TestApiManager(self::CONFIG, $this->getMockGetUserClient());

        $this->expectException(\RuntimeException::class);

        $data = (new User())
            ->setName('Petr Petrovich')
            ->setBlocked(true)
            ->setActive(1)
            ->setPermissions(
                [
                    "id" => 1,
                    "permission" => "comment"
                ]
            );

        $response = $testApi->updateUser(12, $data);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        $testApi->token = 'dsfd79843r32d1d3dx23d32d';

        //200:Ok
        $response = $testApi->updateUser(12, $data);
        self::assertTrue($response->isSuccess());
        self::assertNotEmpty($response->getUserData());

        //200:Error
        $response = $testApi->updateUser(12, $data);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        //200:Not found
        $response = $testApi->updateUser(12, $data);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);

        //exception
        $response = $testApi->updateUser(12, $data);
        self::assertFalse($response->isSuccess());
        self::assertEmpty($response->getUserData());
        self::assertInstanceOf(TestUserResponse::class, $response);
    }

    public function testLogIn(): void
    {
        $testApi = new TestApiManager(self::CONFIG, $this->getMockTokenClient());

        //200:Ok
        $testApi->logIn();
        self::assertNotEmpty($testApi->token);

        $this->expectException(\RuntimeException::class);

        //200:Error
        $testApi->logIn();
        self::assertEmpty($testApi->token);

        //200:Not found
        $testApi->logIn();
        self::assertEmpty($testApi->token);

        //Exception
        $testApi->logIn();
        self::assertEmpty($testApi->token);

    }

    public function getMockTokenClient(): Client
    {
        $mock = new MockHandler([
            new Response(200, [], '{"status":"OK", "token":"dsfd79843r32d1d3dx23d32d"}'),
            new Response(200, [], '{"status":"Error"}'),
            new Response(200, [], '{"status":"Not found"}'),
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', 'http://testapi.ru/auth?login=test&pass=12345'),
                new Response(505, [], '')
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function getMockGetUserClient(): Client
    {
        $mock = new MockHandler([
                new Response(200, [], '
                {
                    "status": "OK",
                    "active": "1",
                    "blocked": false,
                    "created_at": 1587457590,
                    "id": 23,
                    "name": "Ivanov Ivan",
                    "permissions": [
                        {
                            "id": 1,
                            "permission": "comment"
                        },
                        {
                            "id": 2,
                            "permission": "upload photo"
                        },
                        {
                            "id": 3,
                            "permission": "add event"
                        }
                    ]
                }
            '),
            new Response(200, [], '{"status":"Error"}'),
            new Response(200, [], '{"status":"Not found"}'),
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', 'http://testapi.ru/get-user/ivanov?token=dsfd79843r32d1d3dx23d32d'),
                new Response(505, [], '')
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }

    public function getMockUpdateUserClient(): Client
    {
        $mock = new MockHandler([
            new Response(200, [], '{"status": "OK" }'),
            new Response(200, [], '{"status":"Error"}'),
            new Response(200, [], '{"status":"Not found"}'),
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', 'http://testapi.ru/user/12?token=dsfd79843r32d1d3dx23d32d'),
                new Response(505, [], '')
            ),
        ]);

        $handlerStack = HandlerStack::create($mock);

        return new Client(['handler' => $handlerStack]);
    }
}
