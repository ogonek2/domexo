@php
    /** @var \Illuminate\Support\Collection $rows */
    /** @var \Illuminate\Support\Collection $categories */
    /** @var array{count:int,recent:int,phones:int} $abandoned */
@endphp

<div class="fi-wi-widget space-y-6">
    <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <header class="fi-section-header px-6 py-4 border-b border-gray-100 dark:border-white/10">
            <h2 class="text-base font-semibold text-gray-950 dark:text-white">Популярные товары</h2>
            <p class="text-sm text-gray-500">Топ по количеству проданных единиц в заказах</p>
        </header>
        <div class="overflow-x-auto">
            @if ($rows->isEmpty())
                <p class="px-6 py-8 text-sm text-gray-500">Пока недостаточно данных по заказам.</p>
            @else
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 border-b border-gray-100 dark:border-white/10">
                            <th class="px-6 py-3 font-medium">#</th>
                            <th class="px-6 py-3 font-medium">Товар</th>
                            <th class="px-6 py-3 font-medium">Артикул</th>
                            <th class="px-6 py-3 font-medium text-center">Продано</th>
                            <th class="px-6 py-3 font-medium text-center">В заказах</th>
                            <th class="px-6 py-3 font-medium text-right">Выручка</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr class="border-b border-gray-50 dark:border-white/5">
                                <td class="px-6 py-3 text-gray-400">{{ $row['id'] }}</td>
                                <td class="px-6 py-3 font-medium text-gray-950 dark:text-white">{{ $row['name'] }}</td>
                                <td class="px-6 py-3 text-gray-500">{{ $row['articule'] }}</td>
                                <td class="px-6 py-3 text-center font-semibold">{{ $row['qty'] }}</td>
                                <td class="px-6 py-3 text-center">{{ $row['orders'] }}</td>
                                <td class="px-6 py-3 text-right font-semibold">{{ number_format($row['revenue'], 0, '.', ' ') }} ₴</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </section>

    <div class="grid gap-6 md:grid-cols-2">
        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="fi-section-header px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Рейтинг категорий</h2>
            </header>
            <div class="px-6 py-4">
                @if ($categories->isEmpty())
                    <p class="text-sm text-gray-500">Нет данных по категориям.</p>
                @else
                    <ul class="space-y-3">
                        @foreach ($categories as $i => $cat)
                            <li class="flex items-center justify-between gap-3 text-sm">
                                <span class="font-medium text-gray-950 dark:text-white">{{ $i + 1 }}. {{ $cat['name'] }}</span>
                                <span class="text-gray-500 shrink-0">{{ $cat['qty'] }} шт · {{ number_format($cat['revenue'], 0, '.', ' ') }} ₴</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </section>

        <section class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <header class="fi-section-header px-6 py-4 border-b border-gray-100 dark:border-white/10">
                <h2 class="text-base font-semibold text-gray-950 dark:text-white">Корзины / интерес</h2>
            </header>
            <div class="px-6 py-4 grid grid-cols-3 gap-4 text-center">
                <div>
                    <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ $abandoned['count'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Брошенных корзин</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ $abandoned['recent'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">За 7 дней</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-gray-950 dark:text-white">{{ $abandoned['phones'] }}</div>
                    <div class="text-xs text-gray-500 mt-1">Уникальных телефонов</div>
                </div>
            </div>
            <p class="px-6 pb-4 text-xs text-gray-400">Статистика «добавлений в корзину» строится по брошенным корзинам и составу заказов.</p>
        </section>
    </div>
</div>
