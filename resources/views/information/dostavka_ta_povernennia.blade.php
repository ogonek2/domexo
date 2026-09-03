@extends('layouts.app')

@section('seo')
    <title>Доставка та повернення — DOMEXO</title>
    <meta name="description" content="Умови доставки та повернення товарів інтернет-магазину DOMEXO. Нова Пошта, самовивіз, обмін і повернення протягом 14 днів.">
@endsection

@section('content')
    <section class="bg-[#F5F6FB] py-4">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-[#D4AF5A] transition-colors">
                    <i class="fas fa-home"></i>
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-[#0B1F3B] font-medium">Доставка та повернення</span>
            </nav>
        </div>
    </section>

    <section class="bg-[#0B1F3B] py-12">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="font-heading text-xs font-semibold uppercase tracking-[0.25em] text-[#D4AF5A] mb-3">DOMEXO</p>
            <h1 class="font-heading text-4xl md:text-5xl font-bold text-white mb-4">Доставка та повернення</h1>
            <p class="text-xl text-white/70">Умови доставки товару</p>
        </div>
    </section>

    <div class="bg-[#F5F6FB] min-h-screen py-8">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-4 gap-8">
                <div class="lg:col-span-1">
                    @include('includes.main.information_bar')
                </div>

                <div class="lg:col-span-3">
                    <div class="bg-white border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-8 space-y-10 text-gray-700 leading-relaxed text-sm">

                            <section>
                                <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] mb-4 flex items-center gap-3">
                                    <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                        <i class="fas fa-clock text-[#D4AF5A]"></i>
                                    </span>
                                    Час відправки
                                </h2>
                                <p class="text-base leading-relaxed">
                                    Замовлення, оформлені до <strong>14:00</strong>, відправляються в той же день
                                    (після 14:00 переносяться на наступний день). Замовлення, оформлені в п’ятницю,
                                    відправляються наступного дня.
                                </p>
                            </section>

                            <section>
                                <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] mb-4 flex items-center gap-3">
                                    <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                        <i class="fas fa-truck text-[#D4AF5A]"></i>
                                    </span>
                                    Доставка Новою Поштою
                                </h2>
                                <p class="text-base leading-relaxed">
                                    При оформленні замовлення клієнт може обрати доставку до будь-якого діючого відділення
                                    або поштомату Нової Пошти. Доставка тарифікується згідно з тарифами пошти.
                                    Строк доставки становить <strong>1–2 дні</strong> відповідно до стандартів компанії-перевізника.
                                </p>
                            </section>

                            <section>
                                <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] mb-4 flex items-center gap-3">
                                    <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                        <i class="fas fa-store text-[#D4AF5A]"></i>
                                    </span>
                                    Самовивіз
                                </h2>
                                <p class="text-base leading-relaxed">
                                    Самовивіз з фізичного магазину можливий уже в день замовлення товару;
                                    покупець отримає повідомлення про готовність видачі товару.
                                </p>
                            </section>

                            <section>
                                <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] mb-4 flex items-center gap-3">
                                    <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                        <i class="fas fa-undo text-[#D4AF5A]"></i>
                                    </span>
                                    Умови повернення товару
                                </h2>
                                <p class="text-base leading-relaxed mb-4">
                                    Будь-який товар, придбаний у нас на сайті, можна обміняти або повернути протягом
                                    <strong>14 днів</strong> після отримання посилки без слідів використання
                                    (зазначений товар не був у вжитку, збережено його товарний вигляд, споживчі властивості,
                                    фабричні ярлики, етикетки та оригінальну упаковку).
                                </p>
                                <p class="text-base leading-relaxed mb-4">
                                    Вартість доставки або комісії за накладений платіж не компенсується, за винятком обміну
                                    виробничого браку з нашого боку. Обмін товару здійснюється протягом <strong>3–5 днів</strong>
                                    після отримання посилки від клієнта. У разі повернення кошти за товар відшкодовуються
                                    протягом <strong>5 днів</strong> після отримання посилки на банківську картку клієнта.
                                </p>
                                <p class="text-base leading-relaxed">
                                    Щоб обміняти товар або оформити повернення, напишіть нам у Viber або Telegram
                                    <a href="tel:+380931874889" class="text-[#0B1F3B] font-semibold hover:text-[#D4AF5A] transition-colors">+380 93 187 48 89</a>
                                    з описом проблеми. Вашу заявку буде опрацьовано протягом одного робочого дня.
                                </p>
                            </section>

                            <div class="bg-[#0B1F3B] p-8 text-center">
                                <h3 class="font-heading text-xl font-bold text-white mb-3">Потрібна допомога?</h3>
                                <p class="text-white/70 mb-6 text-sm">Питання щодо доставки або повернення — звертайтеся до менеджерів</p>
                                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                    <a href="tel:+380931874889" class="btn-domiko-primary px-6 py-3 inline-flex items-center justify-center gap-2">
                                        <i class="fab fa-telegram-plane"></i> +380 93 187 48 89
                                    </a>
                                    <a href="{{ route('garantiya') }}" class="border border-[#D4AF5A] text-[#D4AF5A] hover:bg-[#D4AF5A] hover:text-[#0B1F3B] px-6 py-3 inline-flex items-center justify-center gap-2 transition-colors font-heading font-semibold">
                                        <i class="fas fa-shield-alt"></i> Умови гарантії
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
