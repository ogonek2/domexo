<!-- Footer -->
<footer class="bg-[#0B1F3B] text-white">
    <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- About -->
            <div class="lg:col-span-1">
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('storage/src/logo.svg') }}" alt="DOMEXO" class="h-10 w-10 brightness-0 invert">
                    <div>
                        <h3 class="font-heading text-xl font-bold">
                            DOM<span class="text-[#D4AF5A]">EXO</span>
                        </h3>
                        <p class="text-[10px] tracking-brand-wide text-white/50 uppercase">Home &bull; Kitchen &bull; Bath</p>
                    </div>
                </div>
                <p class="text-white/60 mb-6 leading-relaxed text-sm">
                    Якісні товари для дому, кухні та ванної оптом і в роздріб. Надійний партнер з широким асортиментом та вигідними умовами.
                </p>
                <div class="flex gap-3">
                    <a href="#" class="w-10 h-10 bg-white/10 hover:bg-[#D4AF5A] hover:text-[#0B1F3B] flex items-center justify-center transition-all duration-200">
                        <iconify-icon icon="mdi:facebook" width="20"></iconify-icon>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 hover:bg-[#D4AF5A] hover:text-[#0B1F3B] flex items-center justify-center transition-all duration-200">
                        <iconify-icon icon="mdi:instagram" width="20"></iconify-icon>
                    </a>
                    <a href="#" class="w-10 h-10 bg-white/10 hover:bg-[#D4AF5A] hover:text-[#0B1F3B] flex items-center justify-center transition-all duration-200">
                        <iconify-icon icon="mdi:telegram" width="20"></iconify-icon>
                    </a>
                </div>
            </div>

            <!-- Information -->
            <div>
                <h4 class="font-heading text-base font-bold mb-4 text-[#D4AF5A]">Інформація</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ route('pro_kompaniiu') }}" class="text-white/60 hover:text-white transition-colors text-sm">Про компанію</a></li>
                    <li><a href="{{ route('garantiya') }}" class="text-white/60 hover:text-white transition-colors text-sm">Гарантія</a></li>
                    <li><a href="{{ route('oplata') }}" class="text-white/60 hover:text-white transition-colors text-sm">Оплата</a></li>
                    <li><a href="{{ route('dostavka_ta_povernennia') }}" class="text-white/60 hover:text-white transition-colors text-sm">Доставка та повернення</a></li>
                    <li><a href="{{ route('dohovir_oferty') }}" class="text-white/60 hover:text-white transition-colors text-sm">Договір публічної оферти</a></li>
                    <li><a href="{{ route('privacy_policy') }}" class="text-white/60 hover:text-white transition-colors text-sm">Політика конфіденційності</a></li>
                </ul>
            </div>

            <!-- Contacts -->
            <div>
                <h4 class="font-heading text-base font-bold mb-4 text-[#D4AF5A]">Контакти</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <x-lucide-icon name="phone" width="16" class="text-[#D4AF5A] mt-0.5" />
                        <div>
                            <a href="tel:0931874889" class="text-white/80 hover:text-white transition-colors">+380 93 187 48 89</a>
                        </div>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-lucide-icon name="mail" width="16" class="text-[#D4AF5A] mt-0.5" />
                        <a href="mailto:shershah169@gmail.com" class="text-white/80 hover:text-white transition-colors">shershah169@gmail.com</a>
                    </li>
                    <li class="flex items-start gap-3">
                        <x-lucide-icon name="map-pin" width="16" class="text-[#D4AF5A] mt-0.5" />
                        <span class="text-white/60">Одеса, Україна</span>
                    </li>
                </ul>
            </div>

            <!-- Working Hours -->
            <div>
                <h4 class="font-heading text-base font-bold mb-4 text-[#D4AF5A]">Графік роботи</h4>
                <ul class="space-y-2 text-sm mb-6">
                    <li class="flex justify-between">
                        <span class="text-white/50">Пн – Чт</span>
                        <span class="text-white/80">05:00 – 14:00</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-white/50">П'ятниця</span>
                        <span class="text-red-400">Вихідний</span>
                    </li>
                    <li class="flex justify-between">
                        <span class="text-white/50">Сб – Нд</span>
                        <span class="text-white/80">05:00 – 14:00</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-site mx-auto px-4 sm:px-6 lg:px-8 py-5">
            <div class="flex flex-col md:flex-row justify-between items-center gap-3 text-sm text-white/50">
                <div>&copy; {{ date('Y') }} DOMEXO. Всі права захищені.</div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('kontaktna_informatsiia') }}" class="hover:text-[#D4AF5A] transition-colors">Контакти</a>
                    <a href="{{ route('privacy_policy') }}" class="hover:text-[#D4AF5A] transition-colors">Конфіденційність</a>
                    <a href="{{ route('dohovir_oferty') }}" class="hover:text-[#D4AF5A] transition-colors">Оферта</a>
                </div>
            </div>
        </div>
    </div>
</footer>
