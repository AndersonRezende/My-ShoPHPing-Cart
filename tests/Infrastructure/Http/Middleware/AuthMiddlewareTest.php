<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Middleware;

use MyShoppingCart\Infrastructure\Http\Middleware\AuthMiddleware;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Response;
use Firebase\JWT\JWT;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthMiddlewareTest extends TestCase {
    private const string JWT_SECRET = 'test_secret_key_1234567890123456';

    protected function setUp(): void {
        $_ENV['JWT_SECRET'] = self::JWT_SECRET;
    }

    public function testReturns401WhenAuthorizationHeaderIsMissing(): void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/test');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new AuthMiddleware();
        $response = $middleware($request, $handler);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertJsonStringEqualsJsonString(
            json_encode(['error' => 'Unauthorized']),
            (string) $response->getBody()
        );
    }

    public function testReturns401WhenTokenIsInvalid(): void {
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/test')
            ->withHeader('Authorization', 'Bearer invalid.token.here');
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->expects($this->never())->method('handle');

        $middleware = new AuthMiddleware();
        $response = $middleware($request, $handler);

        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testCallsHandlerAndInjectsUserIdWhenTokenIsValid(): void {
        $payload = [
            'sub' => 'user-123',
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $token = JWT::encode($payload, self::JWT_SECRET, 'HS256');
        $request = new ServerRequestFactory()
            ->createServerRequest('GET', '/test')
            ->withHeader('Authorization', 'Bearer ' . $token);
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler
            ->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (ServerRequestInterface $request) {
                return $request->getAttribute('userId') === 'user-123';
            }))
            ->willReturn(new Response());

        $middleware = new AuthMiddleware();
        $response = $middleware($request, $handler);

        $this->assertEquals(200, $response->getStatusCode());
    }
}