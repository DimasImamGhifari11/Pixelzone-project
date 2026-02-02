<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'horror']);

        $payload = [
            'name' => 'God Of War',
            'description' => 'Game seru',
            'price' => 569000,
            'category_id' => $category->id,
            'image' => 'https://example.com/gow.jpg'
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/products', $payload);

        $response->assertStatus(201)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'name' => 'God Of War',
            'price' => 569000
        ]);
    }

    /** @test */
    public function it_can_list_products()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        Product::factory()->count(3)->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJson(['success' => true])
                 ->assertJsonCount(3, 'data');
    }

    /** @test */
    public function it_can_show_single_product()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Doom Eternal'
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $product->id,
                         'name' => 'Doom Eternal'
                     ]
                 ]);
    }

    /** @test */
    public function it_can_update_product()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Spiderman 2',
            'price' => 789000
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/products/' . $product->id, [
                'name' => 'Spiderman Updated',
                'description' => 'Game updated',
                'price' => 999000,
                'category_id' => $product->category_id,
                'image' => 'https://example.com/new.jpg'
            ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Spiderman Updated',
            'price' => 999000
        ]);
    }

    /** @test */
    public function it_can_delete_product()
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/products/' . $product->id);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }
}
