@extends('layouts.app')

@section('seo')
    <title>Оформлення замовлення — DOMEXO</title>
    <meta name="description" content="Оформлення замовлення в інтернет-магазині DOMEXO. Доставка Новою Поштою по всій Україні.">
@endsection

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <style>
        .checkout-option { border: 1px solid #e5e7eb; background: #fff; cursor: pointer; transition: border-color .15s, background .15s; }
        .checkout-option:hover { border-color: #0B1F3B; }
        .checkout-option.active { border-color: #D4AF5A; background: #F5F0E6; }
        .checkout-field { width: 100%; padding: .75rem 1rem; border: 1px solid #e5e7eb; background: #fff; font-size: .875rem; color: #1E1E1E; }
        .checkout-field:focus { border-color: #D4AF5A; outline: none; box-shadow: 0 0 0 2px rgba(212,175,90,.18); }
        .checkout-field.is-invalid { border-color: #dc2626; }
        .invalid-feedback { margin-top: .35rem; font-size: .75rem; color: #dc2626; }
        .select2-container--default .select2-selection--single {
            height: 46px; border: 1px solid #e5e7eb; border-radius: 0; padding: 8px 12px;
        }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; color: #1E1E1E; padding-left: 0; }
        .select2-container--default .select2-selection--single .select2-selection__arrow { height: 44px; }
        .select2-dropdown { border-color: #e5e7eb; border-radius: 0; }
        .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable { background: #0B1F3B; }
    </style>
@endpush

@section('content')
    <div id="preloader" class="fixed inset-0 z-50 hidden items-center justify-center bg-white/90">
        <div class="flex flex-col items-center">
            <div class="h-11 w-11 animate-spin border-2 border-gray-200 border-t-[#D4AF5A]"></div>
            <p class="mt-4 text-sm font-medium text-[#0B1F3B]">Оформлюємо замовлення...</p>
        </div>
    </div>

    <section class="bg-white py-4 border-b border-gray-200">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex items-center space-x-2 text-sm">
                <a href="{{ url('/') }}" class="text-gray-600 hover:text-[#D4AF5A] transition-colors"><i class="fas fa-home"></i></a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <a href="{{ route('cart') }}" class="text-gray-600 hover:text-[#D4AF5A] transition-colors">Кошик</a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-[#0B1F3B] font-medium">Оформлення замовлення</span>
            </nav>
        </div>
    </section>

    <section class="bg-[#0B1F3B] py-10">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="font-heading text-xs font-semibold uppercase tracking-[0.25em] text-[#D4AF5A] mb-3">DOMEXO</p>
            <h1 class="font-heading text-3xl md:text-4xl font-bold text-white mb-3">Оформлення замовлення</h1>
            <p class="text-white/70">Заповніть дані доставки — ми відправимо замовлення в той самий день до 14:00</p>
        </div>
    </section>

    <div class="bg-[#F5F6FB] min-h-screen py-8">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2">
                    <form id="order-form" class="bg-white border border-gray-100 shadow-sm p-6 sm:p-8 space-y-10">
                        <section>
                            <h2 class="font-heading text-xl font-bold text-[#0B1F3B] mb-5 flex items-center gap-3">
                                <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                    <i class="fas fa-truck text-[#D4AF5A]"></i>
                                </span>
                                Спосіб доставки
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="checkout-option delivery-option relative p-5">
                                    <input type="radio" name="delivery_service" value="novaposhta" class="sr-only">
                                    <span class="absolute top-3 right-3 bg-[#D4AF5A] text-[#0B1F3B] text-[10px] font-bold uppercase tracking-wide px-2 py-0.5">Популярно</span>
                                    <div class="flex items-start gap-4 pr-16">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-truck text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Нова Пошта</span>
                                            <span class="block text-sm text-gray-600">Відділення або поштомат, 1–3 дні</span>
                                        </span>
                                    </div>
                                </label>

                                <label class="checkout-option delivery-option relative p-5">
                                    <input type="radio" name="delivery_service" value="courier" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-motorcycle text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Кур'єр</span>
                                            <span class="block text-sm text-gray-600">Доставка додому або в офіс</span>
                                        </span>
                                    </div>
                                </label>

                                <label class="checkout-option delivery-option relative p-5 md:col-span-2">
                                    <input type="radio" name="delivery_service" value="pickup" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-store text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Самовивіз</span>
                                            <span class="block text-sm text-gray-600">Забрати замовлення самостійно</span>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </section>

                        <section id="delivery-details" class="hidden">
                            <div id="novaposhta-details" class="hidden grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-semibold text-[#0B1F3B] mb-2">Місто <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select id="city-select" class="checkout-field">
                                            <option value="">Оберіть місто</option>
                                        </select>
                                        <div id="city-loader" class="absolute top-1/2 right-3 -translate-y-1/2 hidden">
                                            <div class="h-5 w-5 animate-spin border-2 border-gray-200 border-t-[#D4AF5A]"></div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-[#0B1F3B] mb-2">Відділення <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <select id="warehouse-select" class="checkout-field">
                                            <option value="">Оберіть відділення</option>
                                        </select>
                                        <div id="warehouse-loader" class="absolute top-1/2 right-3 -translate-y-1/2 hidden">
                                            <div class="h-5 w-5 animate-spin border-2 border-gray-200 border-t-[#D4AF5A]"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="manual-address-details" class="hidden">
                                <label for="manual-address" class="block text-sm font-semibold text-[#0B1F3B] mb-2">Адреса доставки <span class="text-red-500">*</span></label>
                                <textarea id="manual-address" class="checkout-field" rows="3" placeholder="Місто, вулиця, будинок, квартира"></textarea>
                            </div>
                        </section>

                        <section>
                            <h2 class="font-heading text-xl font-bold text-[#0B1F3B] mb-5 flex items-center gap-3">
                                <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                    <i class="fas fa-user text-[#D4AF5A]"></i>
                                </span>
                                Дані отримувача
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-[#0B1F3B] mb-2">Ім'я <span class="text-red-500">*</span></label>
                                    <input type="text" id="name" name="name" class="checkout-field" placeholder="Ваше ім'я">
                                </div>
                                <div>
                                    <label for="lastname" class="block text-sm font-semibold text-[#0B1F3B] mb-2">Прізвище <span class="text-red-500">*</span></label>
                                    <input type="text" id="lastname" name="lastname" class="checkout-field" placeholder="Ваше прізвище">
                                </div>
                                <div>
                                    <label for="fathername" class="block text-sm font-semibold text-[#0B1F3B] mb-2">По батькові</label>
                                    <input type="text" id="fathername" name="fathername" class="checkout-field" placeholder="Необов'язково">
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-semibold text-[#0B1F3B] mb-2">Телефон <span class="text-red-500">*</span></label>
                                    <input type="tel" id="phone" name="phone" class="checkout-field" placeholder="+380XXXXXXXXX">
                                </div>
                                <div class="md:col-span-2">
                                    <label for="comment" class="block text-sm font-semibold text-[#0B1F3B] mb-2">Коментар до замовлення</label>
                                    <textarea id="comment" name="comment" class="checkout-field" rows="3" placeholder="Додаткові побажання"></textarea>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h2 class="font-heading text-xl font-bold text-[#0B1F3B] mb-5 flex items-center gap-3">
                                <span class="w-10 h-10 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                    <i class="fas fa-credit-card text-[#D4AF5A]"></i>
                                </span>
                                Спосіб оплати
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="checkout-option payment-option relative p-5">
                                    <input type="radio" name="payment_method" value="cash" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-money-bill-wave text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Накладений платіж</span>
                                            <span class="block text-sm text-gray-600">Готівка при отриманні</span>
                                        </span>
                                    </div>
                                </label>
                                <label class="checkout-option payment-option relative p-5">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-university text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Банківський переказ</span>
                                            <span class="block text-sm text-gray-600">Оплата за рахунком</span>
                                        </span>
                                    </div>
                                </label>
                                <label class="checkout-option payment-option relative p-5">
                                    <input type="radio" name="payment_method" value="card_payment" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-credit-card text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Картка при отриманні</span>
                                            <span class="block text-sm text-gray-600">Термінал у кур'єра</span>
                                        </span>
                                    </div>
                                </label>
                                <label class="checkout-option payment-option relative p-5">
                                    <input type="radio" name="payment_method" value="pickup_payment" class="sr-only">
                                    <div class="flex items-start gap-4">
                                        <span class="w-11 h-11 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                            <i class="fas fa-store text-[#D4AF5A]"></i>
                                        </span>
                                        <span>
                                            <span class="block font-heading font-bold text-[#0B1F3B] mb-1">Оплата при самовивозі</span>
                                            <span class="block text-sm text-gray-600">Готівка або картка в магазині</span>
                                        </span>
                                    </div>
                                </label>
                            </div>
                        </section>

                        <div id="payment-info" class="hidden">
                            <div id="payment-info-cash" class="hidden border border-[#D4AF5A]/40 bg-[#F5F0E6] p-4 text-sm text-[#0B1F3B]">
                                <strong>Накладений платіж:</strong> оплата готівкою при отриманні. Кур'єр матиме чек і квитанцію.
                            </div>
                            <div id="payment-info-bank_transfer" class="hidden border border-[#D4AF5A]/40 bg-[#F5F0E6] p-4 text-sm text-[#0B1F3B]">
                                <strong>Банківський переказ:</strong> після підтвердження надішлемо реквізити на пошту або в месенджер.
                            </div>
                            <div id="payment-info-card_payment" class="hidden border border-[#D4AF5A]/40 bg-[#F5F0E6] p-4 text-sm text-[#0B1F3B]">
                                <strong>Оплата карткою при отриманні:</strong> у кур'єра буде термінал. Підтримуються основні платіжні системи.
                            </div>
                            <div id="payment-info-pickup_payment" class="hidden border border-[#D4AF5A]/40 bg-[#F5F0E6] p-4 text-sm text-[#0B1F3B]">
                                <strong>Оплата при самовивозі:</strong> готівкою або карткою в магазині.
                            </div>
                        </div>

                        <div class="pt-6 border-t border-gray-100">
                            <button type="submit" id="submit-order" class="w-full bg-[#D4AF5A] hover:bg-[#C5A059] text-[#0B1F3B] font-heading font-bold py-4 px-8 transition-colors">
                                Оформити замовлення
                            </button>
                            <p id="checkout-minimum-message" class="mt-3 text-sm text-center hidden"></p>
                            @if (!empty(shop_settings_public()['checkout_notice']))
                                <p class="mt-2 text-sm text-center text-slate-500">{{ shop_settings_public()['checkout_notice'] }}</p>
                            @endif
                        </div>

                        <meta name="csrf-token" content="{{ csrf_token() }}">
                    </form>
                </div>

                <aside class="lg:col-span-1">
                    <div class="bg-white border border-gray-100 shadow-sm p-6 sticky top-24">
                        <h3 class="font-heading text-lg font-bold text-[#0B1F3B] mb-5 flex items-center gap-3">
                            <span class="w-8 h-8 bg-[#0B1F3B] flex items-center justify-center shrink-0">
                                <i class="fas fa-shopping-cart text-[#D4AF5A] text-sm"></i>
                            </span>
                            Ваше замовлення
                        </h3>
                        <cart-list></cart-list>
                    </div>
                </aside>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const MIN_ORDER_TOTAL = (window.__SHOP_SETTINGS__ && window.__SHOP_SETTINGS__.min_order_enabled === false)
            ? 0
            : (Number(window.__SHOP_SETTINGS__?.min_order_total) || 1000);
        const CURRENCY_SYMBOL = window.__SHOP_SETTINGS__?.currency_symbol || '₴';

        $(document).ready(function() {
            let cities = [];
            let warehouses = [];
            let selectedCityRef = '';
            const submitButton = document.getElementById('submit-order');
            const minOrderMessage = document.getElementById('checkout-minimum-message');

            const updateSubmitState = () => {
                if (!submitButton) return;

                const totalInput = document.getElementById('total_price_stream');
                const rawValue = totalInput ? parseFloat(totalInput.value) : 0;
                const total = isNaN(rawValue) ? 0 : rawValue;

                if (minOrderMessage) {
                    minOrderMessage.classList.remove('text-red-600', 'text-[#0B1F3B]');
                }

                if (MIN_ORDER_TOTAL > 0 && total < MIN_ORDER_TOTAL) {
                    submitButton.disabled = true;
                    submitButton.classList.add('cursor-not-allowed', 'opacity-60', 'pointer-events-none');
                    if (minOrderMessage) {
                        const difference = Math.ceil(MIN_ORDER_TOTAL - total);
                        minOrderMessage.textContent = `Мінімальна сума замовлення — ${MIN_ORDER_TOTAL.toLocaleString('uk-UA')} ${CURRENCY_SYMBOL}. Додайте товарів ще на ${difference.toLocaleString('uk-UA')} ${CURRENCY_SYMBOL}.`;
                        minOrderMessage.classList.add('text-red-600');
                        minOrderMessage.classList.remove('hidden');
                    }
                } else {
                    submitButton.disabled = false;
                    submitButton.classList.remove('cursor-not-allowed', 'opacity-60', 'pointer-events-none');
                    if (minOrderMessage) {
                        if (MIN_ORDER_TOTAL > 0) {
                            minOrderMessage.textContent = 'Мінімальна сума замовлення виконана. Можна оформлювати.';
                            minOrderMessage.classList.add('text-[#0B1F3B]');
                            minOrderMessage.classList.remove('hidden');
                        } else {
                            minOrderMessage.classList.add('hidden');
                        }
                    }
                }
            };

            updateSubmitState();
            setTimeout(updateSubmitState, 400);
            window.addEventListener('cart-updated', updateSubmitState);

            $('input[name="delivery_service"]').on('change', function() {
                $('.delivery-option').removeClass('active');
                $(this).closest('.delivery-option').addClass('active');
                showDeliveryDetails($(this).val());
            });

            $('.delivery-option').on('click', function() {
                const radio = $(this).find('input[type="radio"]');
                radio.prop('checked', true).trigger('change');
            });

            $('input[name="payment_method"]').on('change', function() {
                $('.payment-option').removeClass('active');
                $(this).closest('.payment-option').addClass('active');
                showPaymentInfo($(this).val());
            });

            $('.payment-option').on('click', function() {
                const radio = $(this).find('input[type="radio"]');
                radio.prop('checked', true).trigger('change');
            });

            $('#city-select').select2({
                placeholder: 'Оберіть місто',
                allowClear: true,
                width: '100%',
                language: { noResults: () => 'Міста не знайдено', searching: () => 'Пошук...' },
            });

            $('#warehouse-select').select2({
                placeholder: 'Оберіть відділення',
                allowClear: true,
                width: '100%',
                language: { noResults: () => 'Відділення не знайдено', searching: () => 'Пошук...' },
            });

            function showDeliveryDetails(service) {
                $('#delivery-details').removeClass('hidden');
                $('#novaposhta-details').addClass('hidden');
                $('#manual-address-details').addClass('hidden');

                if (service === 'novaposhta') {
                    $('#novaposhta-details').removeClass('hidden');
                    loadCities();
                } else if (service === 'pickup') {
                    $('#delivery-details').addClass('hidden');
                } else {
                    $('#manual-address-details').removeClass('hidden');
                }
            }

            function showPaymentInfo(method) {
                $('#payment-info').removeClass('hidden');
                $('#payment-info > div').addClass('hidden');
                $(`#payment-info-${method}`).removeClass('hidden');
            }

            function loadCities() {
                $('#city-loader').removeClass('hidden');
                $.get('/cities')
                    .done(function(data) {
                        cities = data;
                        populateCitySelect(data);
                    })
                    .always(function() {
                        $('#city-loader').addClass('hidden');
                    });
            }

            function populateCitySelect(citiesData) {
                const $select = $('#city-select');
                $select.empty().append('<option value="">Оберіть місто</option>');
                citiesData.forEach((city) => {
                    $select.append(`<option value="${city.Ref}">${city.Description}</option>`);
                });
                $select.trigger('change.select2');
            }

            $('#city-select').on('change', function() {
                const cityRef = $(this).val();
                if (cityRef) {
                    selectedCityRef = cityRef;
                    loadWarehouses(cityRef);
                }
            });

            function loadWarehouses(cityRef) {
                $('#warehouse-loader').removeClass('hidden');
                $.ajax({
                    method: 'POST',
                    url: '/warehouses',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    contentType: 'application/json',
                    data: JSON.stringify({ cityRef }),
                    success: function(data) {
                        warehouses = data;
                        populateWarehouseSelect(data);
                    },
                    complete: function() {
                        $('#warehouse-loader').addClass('hidden');
                    }
                });
            }

            function populateWarehouseSelect(warehousesData) {
                const $select = $('#warehouse-select');
                $select.empty().append('<option value="">Оберіть відділення</option>');
                warehousesData.forEach((warehouse) => {
                    $select.append(`<option value="${warehouse.Ref}">${warehouse.Description}</option>`);
                });
                $select.trigger('change.select2');
            }

            function validateForm() {
                let isValid = true;
                $('input, textarea, select').removeClass('is-invalid');
                $('.invalid-feedback').remove();

                const deliveryService = $('input[name="delivery_service"]:checked').val();
                if (!deliveryService) {
                    showError('Оберіть спосіб доставки');
                    isValid = false;
                }

                if (deliveryService === 'novaposhta') {
                    if (!$('#city-select').val()) {
                        showFieldError('#city-select', 'Оберіть місто');
                        isValid = false;
                    }
                    if (!$('#warehouse-select').val()) {
                        showFieldError('#warehouse-select', 'Оберіть відділення');
                        isValid = false;
                    }
                } else if (deliveryService && deliveryService !== 'pickup') {
                    const manualAddress = $('#manual-address').val().trim();
                    if (!manualAddress || manualAddress.length < 2) {
                        showFieldError('#manual-address', 'Введіть адресу доставки');
                        isValid = false;
                    }
                }

                const name = $('#name').val().trim();
                const lastname = $('#lastname').val().trim();
                const phone = $('#phone').val().trim();

                if (!name || name.length < 2) {
                    showFieldError('#name', "Введіть коректне ім'я");
                    isValid = false;
                }
                if (!lastname || lastname.length < 2) {
                    showFieldError('#lastname', 'Введіть коректне прізвище');
                    isValid = false;
                }
                if (!phone || phone.length < 10) {
                    showFieldError('#phone', 'Введіть коректний номер телефону');
                    isValid = false;
                }

                if (!$('input[name="payment_method"]:checked').val()) {
                    showError('Оберіть спосіб оплати');
                    isValid = false;
                }

                return isValid;
            }

            function showFieldError(selector, message) {
                $(selector).addClass('is-invalid');
                $(selector).after(`<div class="invalid-feedback">${message}</div>`);
            }

            function showError(message) {
                alert(message);
            }

            $('#order-form').on('submit', function(e) {
                e.preventDefault();
                if (!validateForm()) return;

                const cart = localStorage.getItem('cart');
                if (!cart || cart === '[]' || cart === '{}' || cart === 'null') {
                    alert('Кошик порожній. Додайте товари до кошика.');
                    return;
                }

                $('#preloader').removeClass('hidden').addClass('flex');

                $.ajax({
                    type: 'POST',
                    url: '/order-submit',
                    data: {
                        delivery_service: $('input[name="delivery_service"]:checked').val(),
                        city: $('#city-select option:selected').text(),
                        warehouse: $('#warehouse-select option:selected').text(),
                        manual_address: $('#manual-address').val().trim(),
                        name: $('#name').val().trim(),
                        lastname: $('#lastname').val().trim(),
                        fathername: $('#fathername').val().trim(),
                        phone: $('#phone').val().trim(),
                        comment: $('#comment').val().trim(),
                        payment: $('input[name="payment_method"]:checked').val(),
                        cart: cart,
                        total_price: $('#total_price_stream').val(),
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        $('#preloader').addClass('hidden').removeClass('flex');
                        localStorage.removeItem('cart');
                        window.location.href = '/thank-you';
                    },
                    error: function(xhr) {
                        $('#preloader').addClass('hidden').removeClass('flex');
                        let errorMessage = 'Помилка під час надсилання замовлення. Спробуйте ще раз.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.error) errorMessage = response.error;
                        } catch (err) {}
                        alert(errorMessage);
                    }
                });
            });
        });
    </script>
@endpush
