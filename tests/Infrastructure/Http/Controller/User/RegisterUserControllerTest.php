<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use MyShoppingCart\Domain\Entity\User;
use MyShoppingCart\Domain\ValueObject\Email;
use MyShoppingCart\Domain\ValueObject\Password;
use MyShoppingCart\Infrastructure\Http\Controller\User\RegisterUserController;
use PHPUnit\Framework\TestCase;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\StreamFactory;

class RegisterUserControllerTest extends TestCase {

    public function testInvokeShouldReturnCreatedUser(): void {
        // Arrange
        $useCase = $this->createMock(RegisterUserUseCase::class);
        $user = new User(
            'user-123',
            'Anderson',
            new Email('anderson@example.com'),
            Password::hash('password123')
        );

        $useCase->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(RegisterUserInput::class))
            ->willReturn($user);

        $controller = new RegisterUserController($useCase);

        $requestFactory = new ServerRequestFactory();
        $streamFactory = new StreamFactory();
        
        $body = json_encode([
            'name' => 'Anderson',
            'email' => 'anderson@example.com',
            'password' => 'password123'
        ]);
        
        $request = $requestFactory->createServerRequest('POST', '/users/register')
            ->withParsedBody(json_decode($body, true))
            ->withBody($streamFactory->createStream($body));
            
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        // Act
        $response = $controller($request, $response);

        // Assert
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);
        $data = json_decode($responseBody, true);
        
        $this->assertEquals('user-123', $data['id']);
        $this->assertEquals('Anderson', $data['name']);
        $this->assertEquals('anderson@example.com', $data['email']);
    }

    public function testInvokeShouldReturnErrorWhenUseCaseFails(): void {
        // Arrange
        $useCase = $this->createMock(RegisterUserUseCase::class);
        $useCase->expects($this->once())
            ->method('execute')
            ->willThrowException(new \DomainException('Invalid email'));

        $controller = new RegisterUserController($useCase);

        $requestFactory = new ServerRequestFactory();
        $streamFactory = new StreamFactory();
        
        $body = json_encode([
            'name' => 'Anderson',
            'email' => 'invalid-email',
            'password' => 'password123'
        ]);
        
        $request = $requestFactory->createServerRequest('POST', '/users/register')
            ->withParsedBody(json_decode($body, true))
            ->withBody($streamFactory->createStream($body));
            
        $responseFactory = new ResponseFactory();
        $response = $responseFactory->createResponse();

        // Act
        $response = $controller($request, $response);

        // Assert
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('application/json', $response->getHeaderLine('Content-Type'));
        
        $responseBody = (string) $response->getBody();
        $this->assertJson($responseBody);
        $data = json_decode($responseBody, true);
        
        $this->assertEquals('Invalid email', $data['error']);
    }
}
