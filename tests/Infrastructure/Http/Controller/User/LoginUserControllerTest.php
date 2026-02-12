<?php declare(strict_types=1);

namespace Infrastructure\Http\Controller\User;

use Dotenv\Dotenv;
use MyShoppingCart\Application\DTO\LoginUserInput;
use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\LoginUserUseCase;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\Service\TokenGeneratorInterface;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Http\Controller\User\LoginUserController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class LoginUserControllerTest extends TestCase {

    public function testInvokeShouldReturnLoggedUser(): void {
        $expectedResponse = json_encode(['token' => $generatedToken = 'token-123']);
        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $tokenGenerator->expects($this->once())->method('generate')->willReturn($generatedToken);
        $useCase = $this->createMock(LoginUserUseCase::class);
        $user = new User(
            'user-123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );
        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(LoginUserInput::class))
            ->willReturn($user);
        $controller = new LoginUserController($useCase, $tokenGenerator);

        $requestFactory = new ServerRequestFactory();
        $streamFactory = new StreamFactory();
        
        $body = json_encode([
            'email' => 'anderson@example.com',
            'password' => 'password123'
        ]);
        
        $request = $requestFactory->createServerRequest('POST', '/users/login')
            ->withParsedBody(json_decode($body, true))
            ->withBody($streamFactory->createStream($body));
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        $response = $controller($request, $response);
        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals($expectedResponse, $responseBody);
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testInvokeShouldReturnErrorWhenUseCaseFails(): void {
        $tokenGenerator = $this->createMock(TokenGeneratorInterface::class);
        $useCase = $this->createMock(LoginUserUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new \DomainException('Invalid email or password'));
        $controller = new LoginUserController($useCase, $tokenGenerator);
        $requestFactory = new ServerRequestFactory();
        $streamFactory = new StreamFactory();
        $body = json_encode([
            'email' => 'invalid-email@example.com',
            'password' => 'password123'
        ]);
        $request = $requestFactory->createServerRequest('POST', '/users/register')
            ->withParsedBody(json_decode($body, true))
            ->withBody($streamFactory->createStream($body));
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        $response = $controller($request, $response);
        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);
        $data = json_decode($responseBody, true);

        $this->assertEquals(401, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        $this->assertEquals('Invalid email or password', $data['error']);
    }
}
