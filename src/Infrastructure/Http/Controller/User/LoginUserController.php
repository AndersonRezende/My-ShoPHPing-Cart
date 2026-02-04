<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Controller\User;

use MyShoppingCart\Application\DTO\LoginUserInput;
use MyShoppingCart\Application\UseCase\User\LoginUserUseCase;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class LoginUserController {
    public function __construct(private LoginUserUseCase $loginUserUseCase) {}

    public function __invoke(Request $request, Response $response): Response {
        $data = $request->getParsedBody();
        
        try {
            $input = new LoginUserInput($data['email'], $data['password']);
            $user = $this->loginUserUseCase->execute($input);

            $payload = json_encode([
                'id' => $user->id(),
                'name' => $user->name(),
                'email' => $user->email()->value()
            ]);

            $response->getBody()->write($payload);
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
    }
}
