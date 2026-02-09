<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Infrastructure\Http\Controller\Cart;

use DomainException;
use Slim\Psr7\Response;
use MyShoppingCart\Application\UseCase\Cart\AssociateUserToCartUseCase;
use MyShoppingCart\Infrastructure\Http\Controller\Cart\AssociateUserToCartController;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;

class AssociateUserToCartControllerTest extends TestCase {

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldAddItemSuccessfully(): void {
        $useCase = $this->createMock(AssociateUserToCartUseCase::class);
        $useCase->expects($this->once())->method('execute');
        $controller = new AssociateUserToCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['userId' => '1']));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => '1']);

        $this->assertEquals(204, $response->getStatusCode());
    }

    #[AllowMockObjectsWithoutExpectations]
    public function testShouldNotAddItemSuccessfully(): void {
        $useCase = $this->createMock(AssociateUserToCartUseCase::class);
        $useCase->expects($this->once())->method('execute')->willThrowException(new DomainException('Cart not found'));
        $controller = new AssociateUserToCartController($useCase);
        $stream = $this->createMock(StreamInterface::class);
        $stream->method('__toString')->willReturn(json_encode(['userId' => '10']));

        $request = $this->createMock(ServerRequestInterface::class);
        $request->method('getBody')->willReturn($stream);

        $response = $controller($request, new Response(), ['id' => '1']);

        $this->assertEquals(400, $response->getStatusCode());
    }
}
