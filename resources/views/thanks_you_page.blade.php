@extends('layouts.app')

@section('seo')
    <title>Дякуємо за замовлення — DOMEXO</title>
    <meta name="description" content="Ваше замовлення прийнято. Ми зв'яжемося з вами найближчим часом.">
@endsection

@section('content')
    <section class="bg-white py-4 border-b border-gray-200">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-[#D4AF5A] transition-colors"><i class="fas fa-home"></i></a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-[#0B1F3B] font-medium">Дякуємо</span>
            </nav>
        </div>
    </section>

    <section class="bg-[#0B1F3B] py-10">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="font-heading text-xs font-semibold uppercase tracking-[0.25em] text-[#D4AF5A] mb-3">DOMEXO</p>
            <h1 class="font-heading text-3xl md:text-4xl font-bold text-white">Замовлення прийнято</h1>
        </div>
    </section>

    <div class="bg-[#F5F6FB] py-16">
        <div class="max-w-xl mx-auto px-4 sm:px-6 text-center">
            <div class="bg-white border border-gray-100 shadow-sm px-8 py-12">
                <div class="mx-auto mb-6 flex h-16 w-16 items-center justify-center bg-[#0B1F3B]">
                    <i class="fas fa-check text-[#D4AF5A] text-2xl"></i>
                </div>
                <h2 class="font-heading text-2xl font-bold text-[#0B1F3B] mb-3">Дякуємо!</h2>
                <p class="text-gray-600 mb-8">
                    Ми отримали ваше замовлення і зв’яжемося з вами найближчим часом, щоб підтвердити деталі доставки.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('welcome') }}" class="inline-flex items-center justify-center bg-[#D4AF5A] hover:bg-[#C5A059] text-[#0B1F3B] font-heading font-bold px-6 py-3 transition-colors">
                        На головну
                    </a>
                    <a href="{{ route('catalog') }}" class="inline-flex items-center justify-center border border-[#0B1F3B] text-[#0B1F3B] font-heading font-semibold px-6 py-3 hover:bg-[#0B1F3B] hover:text-white transition-colors">
                        Продовжити покупки
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(session('purchase_js'))
        <script>
            window.dataLayer = window.dataLayer || [];
            window.dataLayer.push({!! json_encode(session('purchase_js'), JSON_UNESCAPED_UNICODE) !!});
        </script>
    @endif
@endpush
