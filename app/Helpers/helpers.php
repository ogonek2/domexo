<?php

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function get_all_category() {
    return Category::where('is_active', true)
        ->orderBy('sort_order', 'asc')
        ->orderBy('name', 'asc')
        ->get();
}

if (!function_exists('get_category_total_products')) {
    /**
     * Подсчитывает количество товаров для категории с учетом всех дочерних категорий.
     *
     * Использует кеширование в рамках одного запроса, чтобы избежать лишних SQL-запросов.
     */
    function get_category_total_products(Category $category): int
    {
        static $categoryChildrenMap = null;
        static $categoryDirectCounts = null;
        static $calculatedTotals = [];

        if ($categoryChildrenMap === null || $categoryDirectCounts === null) {
            $categories = Category::where('is_active', true)
                ->select(['id', 'parent_id'])
                ->withCount('products')
                ->get();

            $categoryChildrenMap = $categories->groupBy('parent_id');
            $categoryDirectCounts = $categories->pluck('products_count', 'id')->toArray();
            $calculatedTotals = [];
        }

        return calculate_total_products_for_category($category->id, $categoryChildrenMap, $categoryDirectCounts, $calculatedTotals);
    }
}

if (!function_exists('calculate_total_products_for_category')) {
    /**
     * Рекурсивно подсчитывает количество товаров для категории и всех ее потомков.
     *
     * @param int $categoryId
     * @param \Illuminate\Support\Collection $childrenMap
     * @param array<int,int> $directCounts
     * @param array<int,int> $cache
     */
    function calculate_total_products_for_category(
        int $categoryId,
        $childrenMap,
        array $directCounts,
        array &$cache
    ): int {
        if (isset($cache[$categoryId])) {
            return $cache[$categoryId];
        }

        $total = $directCounts[$categoryId] ?? 0;

        if ($childrenMap->has($categoryId)) {
            foreach ($childrenMap->get($categoryId) as $childCategory) {
                $total += calculate_total_products_for_category(
                    $childCategory->id,
                    $childrenMap,
                    $directCounts,
                    $cache
                );
            }
        }

        return $cache[$categoryId] = $total;
    }
}

if (!function_exists('get_category_card_data')) {
    function get_category_card_data(Category $category): array
    {
        $categoryImage = null;
        $latestProduct = $category->products()->latest()->first();
        if ($latestProduct && $latestProduct->image_path) {
            $categoryImage = $latestProduct->image_path;
        }
        if (!$categoryImage) {
            foreach ($category->childCategories()->where('is_active', true)->get() as $childCategory) {
                $childProduct = $childCategory->products()->latest()->first();
                if ($childProduct && $childProduct->image_path) {
                    $categoryImage = $childProduct->image_path;
                    break;
                }
            }
        }

        $count = get_category_total_products($category);

        return [
            'name' => $category->name,
            'url' => $category->url,
            'href' => route('catalog_category_page', $category->url),
            'image' => $categoryImage,
            'count' => $count,
            'description' => $category->description
                ? \Illuminate\Support\Str::limit(strip_tags($category->description), 70)
                : null,
        ];
    }
}

if (!function_exists('get_category_filter_tree')) {
    /**
     * Дерево категорій для фільтрів (корінь → діти → онуки).
     *
     * @param  \Illuminate\Support\Collection<int, Category>|null  $roots
     */
    function get_category_filter_tree($roots = null): array
    {
        $roots = $roots ?? Category::query()
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('name')
            ->with(['childCategories' => function ($q) {
                $q->where('is_active', true)
                    ->orderBy('name')
                    ->with(['childCategories' => function ($q2) {
                        $q2->where('is_active', true)->orderBy('name');
                    }]);
            }])
            ->get();

        $mapNode = function (Category $category) use (&$mapNode) {
            $children = $category->relationLoaded('childCategories')
                ? $category->childCategories
                : $category->childCategories()->where('is_active', true)->orderBy('name')->get();

            return [
                'name' => $category->name,
                'url' => $category->url,
                'href' => route('catalog_category_page', $category->url),
                'count' => get_category_total_products($category),
                'children' => $children
                    ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                    ->values()
                    ->map(fn (Category $child) => $mapNode($child))
                    ->all(),
            ];
        };

        return $roots
            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
            ->values()
            ->map(fn (Category $c) => $mapNode($c))
            ->all();
    }
}

if (!function_exists('build_category_breadcrumbs')) {
    /**
     * Повний ланцюжок крихт: Каталог → батьки → поточна категорія.
     */
    function build_category_breadcrumbs(Category $category, bool $currentAsLink = false): array
    {
        $crumbs = [
            ['label' => 'Каталог', 'url' => route('catalog')],
        ];

        foreach ($category->getParents() as $parent) {
            $crumbs[] = [
                'label' => $parent->name,
                'url' => route('catalog_category_page', $parent->url),
            ];
        }

        $crumbs[] = $currentAsLink
            ? ['label' => $category->name, 'url' => route('catalog_category_page', $category->url)]
            : ['label' => $category->name];

        return $crumbs;
    }
}

if (!function_exists('get_mega_menu_data')) {
    /**
     * Дані для мега-меню каталогу: кореневі → підкатегорії → під-підкатегорії + товари (A–Я).
     */
    function get_mega_menu_data(): array
    {
        return Cache::remember('site.mega_menu', now()->addHour(), function () {
            $availabilityExclude = [0, 2, '0', '2', 'out_of_stock', false];

            $roots = Category::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('name')
                ->with(['childCategories' => function ($q) {
                    $q->where('is_active', true)
                        ->orderBy('name')
                        ->with(['childCategories' => function ($q2) {
                            $q2->where('is_active', true)->orderBy('name');
                        }]);
                }])
                ->get();

            $loadProducts = function (int $categoryId, string $categoryUrl, int $limit = 5) use ($availabilityExclude) {
                $products = Product::query()
                    ->whereHas('categories', fn ($q) => $q->where('categories.id', $categoryId))
                    ->whereNotIn('availability', $availabilityExclude)
                    ->select(['id', 'name', 'url', 'image_path', 'price', 'discount'])
                    ->orderBy('name')
                    ->limit($limit)
                    ->get();

                $products->each(fn ($p) => $p->category_url = $categoryUrl);

                return $products;
            };

            $items = [];

            foreach ($roots as $root) {
                $children = $root->childCategories->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)->values();
                $childBlocks = [];
                $rootProducts = collect();

                if ($children->isNotEmpty()) {
                    foreach ($children as $child) {
                        $grandchildren = $child->childCategories
                            ->sortBy('name', SORT_NATURAL | SORT_FLAG_CASE)
                            ->values();

                        $subBlocks = [];

                        if ($grandchildren->isNotEmpty()) {
                            foreach ($grandchildren as $grand) {
                                $products = $loadProducts($grand->id, $grand->url, 5);
                                $subBlocks[] = [
                                    'category' => $grand,
                                    'count' => get_category_total_products($grand),
                                    'products' => $products,
                                ];
                            }
                        }

                        $childProducts = $grandchildren->isEmpty()
                            ? $loadProducts($child->id, $child->url, 6)
                            : collect();

                        $childBlocks[] = [
                            'category' => $child,
                            'count' => get_category_total_products($child),
                            'products' => $childProducts,
                            'children' => $subBlocks,
                        ];
                    }
                } else {
                    $rootProducts = $loadProducts($root->id, $root->url, 12);
                }

                $items[] = [
                    'category' => $root,
                    'count' => get_category_total_products($root),
                    'children' => $childBlocks,
                    'products' => $rootProducts,
                ];
            }

            // Alphabetical roots already ordered by name
            return $items;
        });
    }
}

function uploadToBunnyCDN($localFilePath, $destinationPath)
{
    $storageName = env('BUNNY_STORAGE_NAME');
    $password = env('BUNNY_STORAGE_PASSWORD');
    $region = env('BUNNY_STORAGE_REGION', 'de');

    // Проверяем существование файла
    if (!file_exists($localFilePath)) {
        throw new \Exception("File not found: " . $localFilePath);
    }

    $url = "https://storage.bunnycdn.com/{$storageName}/{$destinationPath}";

    // Используем cURL для более надежной загрузки бинарных файлов
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PUT, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'AccessKey: ' . $password,
        'Content-Type: application/octet-stream',
    ]);
    
    // Отключаем проверку SSL в среде разработки
    if (env('APP_ENV') !== 'production') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    }
    
    // Открываем файл для чтения
    $fileHandle = fopen($localFilePath, 'rb');
    if (!$fileHandle) {
        curl_close($ch);
        throw new \Exception("Cannot open file: " . $localFilePath);
    }
    
    curl_setopt($ch, CURLOPT_INFILE, $fileHandle);
    curl_setopt($ch, CURLOPT_INFILESIZE, filesize($localFilePath));
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    
    fclose($fileHandle);
    curl_close($ch);
    
    if ($error) {
        throw new \Exception("cURL error: " . $error);
    }
    
    if ($httpCode >= 200 && $httpCode < 300) {
        return env('BUNNY_CDN_URL') . '/' . $destinationPath;
    } else {
        throw new \Exception("Upload failed with HTTP code: " . $httpCode . " Response: " . $response);
    }
}

/**
 * Генерирует уникальное название файла для изображения
 */
function generateUniqueImageName($originalName = null, $prefix = 'img') {
    $extension = 'jpg'; // по умолчанию
    
    if ($originalName) {
        $pathInfo = pathinfo($originalName);
        $extension = $pathInfo['extension'] ?? 'jpg';
    }
    
    // Генерируем уникальное название: префикс + uniqid + случайное число + расширение
    $fileName = $prefix . '_' . uniqid('', true) . '_' . random_int(10000, 99999) . '.' . $extension;
    
    return $fileName;
}