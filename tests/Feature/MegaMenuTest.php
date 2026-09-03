<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MegaMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_mega_menu_lists_root_categories(): void
    {
        Category::create([
            'name' => 'Ножи',
            'is_active' => true,
        ]);

        $items = get_mega_menu_data();

        $this->assertCount(1, $items);
        $this->assertSame('Ножи', $items[0]['category']->name);
    }

    public function test_homepage_renders_root_categories_in_mega_menu(): void
    {
        Category::create([
            'name' => 'ОрганайзериДляМегаМеню',
            'is_active' => true,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('ОрганайзериДляМегаМеню', false)
            ->assertSee('mega-menu__cat', false);
    }
}
