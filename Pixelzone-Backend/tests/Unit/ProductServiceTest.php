<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    /** @test */
    public function it_can_create_a_product()
    {
        $category = Category::factory()->create(['name' => 'horror']);

        $productData = [
            'name' => 'God Of War',
            'description' => 'Game seru',
            'price' => 569000,
            'category_id' => $category->id,
            'image' => 'https://example.com/gow.jpg'
        ];

        $product = $this->productService->createProduct($productData);

        $this->assertInstanceOf(Product::class, $product);
        $this->assertEquals('God Of War', $product->name);
        $this->assertEquals('Game seru', $product->description);
        $this->assertEquals(569000, $product->price);
        $this->assertEquals($category->id, $product->category_id);
        $this->assertEquals('https://example.com/gow.jpg', $product->image);

        $this->assertDatabaseHas('products', [
            'name' => 'God Of War',
            'price' => 569000
        ]);
    }

    /** @test */
    public function it_can_list_products()
    {
        Product::factory()->count(3)->create();

        $products = $this->productService->getAllProducts();

        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(3, $products);
    }

    /** @test */
    public function it_can_show_single_product()
    {
        $product = Product::factory()->create([
            'name' => 'Doom Eternal'
        ]);

        $foundProduct = $this->productService->getProductById($product->id);

        $this->assertInstanceOf(Product::class, $foundProduct);
        $this->assertEquals($product->id, $foundProduct->id);
        $this->assertEquals('Doom Eternal', $foundProduct->name);
    }

    /** @test */
    public function it_returns_null_when_product_not_found()
    {
        $foundProduct = $this->productService->getProductById(999);

        $this->assertNull($foundProduct);
    }

    /** @test */
    public function it_can_update_product()
    {
        $product = Product::factory()->create([
            'name' => 'Spiderman 2',
            'price' => 789000
        ]);

        $updateData = [
            'name' => 'Spiderman Updated',
            'description' => 'Game updated',
            'price' => 999000,
            'category_id' => $product->category_id,
            'image' => 'https://example.com/new.jpg'
        ];

        $updatedProduct = $this->productService->updateProduct($product->id, $updateData);

        $this->assertInstanceOf(Product::class, $updatedProduct);
        $this->assertEquals('Spiderman Updated', $updatedProduct->name);
        $this->assertEquals('Game updated', $updatedProduct->description);
        $this->assertEquals(999000, $updatedProduct->price);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Spiderman Updated',
            'price' => 999000
        ]);
    }

    /** @test */
    public function it_returns_null_when_updating_non_existent_product()
    {
        $updateData = [
            'name' => 'Updated Product',
            'price' => 100000
        ];

        $result = $this->productService->updateProduct(999, $updateData);

        $this->assertNull($result);
    }

    /** @test */
    public function it_can_delete_product()
    {
        $product = Product::factory()->create();

        $result = $this->productService->deleteProduct($product->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    /** @test */
    public function it_returns_false_when_deleting_non_existent_product()
    {
        $result = $this->productService->deleteProduct(999);

        $this->assertFalse($result);
    }
}
