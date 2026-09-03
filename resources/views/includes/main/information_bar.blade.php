<!-- Sidebar -->
<aside class="w-full hidden md:block sticky top-40">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-24">
        <h3 class="font-heading text-lg font-bold text-[#0B1F3B] mb-6 flex items-center">
            <div class="w-8 h-8 bg-[#0B1F3B] rounded-lg flex items-center justify-center mr-3">
                <i class="fas fa-info-circle text-[#D4AF5A] text-sm"></i>
            </div>
            Інформація
        </h3>
        
        <nav class="space-y-1">
            @foreach ([
                ['route' => 'pro_kompaniiu', 'icon' => 'fa-building', 'label' => 'Про нас'],
                ['route' => 'garantiya', 'icon' => 'fa-shield-alt', 'label' => 'Гарантія'],
                ['route' => 'oplata', 'icon' => 'fa-credit-card', 'label' => 'Оплата'],
                ['route' => 'dostavka_ta_povernennia', 'icon' => 'fa-truck', 'label' => 'Доставка та повернення'],
                ['route' => 'dohovir_oferty', 'icon' => 'fa-file-contract', 'label' => 'Договір публічної оферти'],
                ['route' => 'kontaktna_informatsiia', 'icon' => 'fa-phone', 'label' => 'Контакти'],
                ['route' => 'uhoda_korystuvacha', 'icon' => 'fa-user-shield', 'label' => 'Угода користувача'],
                ['route' => 'privacy_policy', 'icon' => 'fa-lock', 'label' => 'Політика конфіденційності'],
            ] as $link)
            <a href="{{ route($link['route']) }}" 
               class="flex items-center px-4 py-2.5 text-gray-600 hover:text-[#0B1F3B] hover:bg-[#F5F6FB] rounded-lg transition-all duration-200 group {{ request()->routeIs($link['route']) ? 'bg-[#F5F6FB] text-[#0B1F3B] font-semibold' : '' }}">
                <i class="fas {{ $link['icon'] }} text-gray-400 group-hover:text-[#D4AF5A] mr-3 w-5"></i>
                {{ $link['label'] }}
            </a>
            @endforeach
        </nav>
    </div>
</aside>
