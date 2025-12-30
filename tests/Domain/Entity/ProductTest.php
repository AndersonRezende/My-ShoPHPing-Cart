<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;

use MyShoppingCart\Domain\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase {

    public function testCannotCreateProductWithEmptyName(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Product name cannot be empty');

        new Product('prod-001', '');
    }

    public function testCanCreateProductWithValidName(): void {
        $product = new Product('prod-002', 'Sample Product');
        
        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('prod-002', $product->id());
        $this->assertEquals('Sample Product', $product->name());
    }
}