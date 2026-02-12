<?php declare(strict_types=1);

namespace MyShoppingCart\Infrastructure\Http\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    public function __invoke(Request $request, RequestHandler $handler): Response {
        // 1. Pega o header Authorization: Bearer <token>
        $authHeader = $request->getHeaderLine('Authorization');
        $token = str_replace('Bearer ', '', $authHeader);

        if (!$token) {
            return $this->unauthorizedResponse();
        }

        try {
            // 2. Tenta decodificar. Se a assinatura for inválida ou expirada, lança Exception
            $secretKey = $_ENV['JWT_SECRET'] ?? 'default_secret_key';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // 3. SUCESSO! Injetamos o ID do usuário na requisição
            // O atributo 'userId' ficará disponível para os Controllers
            $request = $request->withAttribute('userId', $decoded->sub);

            // 4. Passa a bola para o próximo (o Controller)
            return $handler->handle($request);

        } catch (\Exception $e) {
            return $this->unauthorizedResponse();
        }
    }

    private function unauthorizedResponse(): Response {
        $response = new SlimResponse();
        $response->getBody()->write(json_encode(['error' => 'Unauthorized']));
        return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
}
