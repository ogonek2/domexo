<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MegaMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_empty_cache_is_rebuilt_from_categories(): void
    {
        Category::create([
            'name' => 'Ножи',
            'is_active' => true,
        ]);

        Cache::put('site.mega_menu', [], now()->addHour());

        $items = get_mega_menu_data();

        $this->assertCount(1, $items);
        $this->assertSame('Ножи', $items[0]['category']->name);
        $this->assertNotEmpty(Cache::get('site.mega_menu'));
    }

    public function test_category_save_clears_mega_menu_cache(): void
    {
        Cache::put('site.mega_menu', ['stale'], now()->addHour());

        Category::create([
            'name' => 'Ліхтарі',
            'is_active' => true,
        ]);

        $this->assertFalse(Cache::has('site.mega_menu'));
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
