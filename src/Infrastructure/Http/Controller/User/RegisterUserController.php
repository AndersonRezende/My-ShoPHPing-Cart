<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\User;

use MyShoppingCart\Application\DTO\RegisterUserInput;
use MyShoppingCart\Application\UseCase\User\RegisterUserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class RegisterUserController {
    public function __construct(private RegisterUserUseCase $registerUserUseCase) {}

    public function __invoke(Request $request, Response $response): Response {
        $data = json_decode((string) $request->getBody(), true);
        
        try {
            $input = new RegisterUserInput(
                $data['name'],
                $data['email'],
                $data['password']
            );

            $user = $this->registerUserUseCase->execute($input);

            $payload = json_encode([
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email()->value()
            ]);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }
}
