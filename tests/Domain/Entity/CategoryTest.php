<?php declare(strict_types=1);

namespace MyShoppingCart\Tests\Domain\Entity;
use MyShoppingCart\Domain\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase {

    public function testCreateCategoryWithoutNameShould(): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category name cannot be empty');

        new \MyShoppingCart\Domain\Entity\Category(null, '');
    }

    public function testCreateCategoryWithValidNameShouldSucceed(): void {
        $categoryName = 'Cleaning';
        $category = new Category(null, $categoryName);

        $this->assertNull($category->id());
        $this->assertEquals($categoryName, $category->name());
    }
}