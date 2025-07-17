<section class="option-group" id="option-group-{{ $group->id }}">
    <div class="option-group-header">
        <h2>{{ $group->name }}</h2>
        @if($group->name == 'Topping')
            <p>Opsional, Maks 2</p>
        @elseif($group->name == 'Syrup')
            <p>Opsional, Maks 1</p>
        @else
            <p>Wajib, Pilih 1</p>
        @endif
    </div>

    {{-- ▼▼▼ KITA GUNAKAN LOGIKA PENANDA BARU YANG LEBIH PINTAR ▼▼▼ --}}
    @php
        $isFirstVisibleOption = true;
    @endphp

    @foreach ($group->options as $option)
        @php
            // Logika untuk hanya menampilkan opsi yang relevan (Iced/Hot)
            $isIcedOption = Str::contains($option->name, 'Ice');
            $isHotOption = Str::contains($option->name, 'Hot');
            $isGeneralOption = !$isIcedOption && !$isHotOption;

            // Tentukan apakah opsi ini harus ditampilkan berdasarkan varian yang aktif
            $shouldShow = ($variantName == 'Iced' && ($isIcedOption || $isGeneralOption)) ||
                          ($variantName == 'Hot' && ($isHotOption || $isGeneralOption));
        @endphp

        @if($shouldShow)
            <div class="option-item">
                <label>
                    <span class="option-name">{{ $option->name }}</span>
                    @if($option->price > 0)
                        <span class="option-price">+Rp {{ number_format($option->price, 0, ',', '.') }}</span>
                    @endif
                    <input 
                        type="{{ $group->type }}"
                        name="customizations[{{ $group->id }}]{{ $group->type === 'checkbox' ? '[]' : '' }}"
                        value="{{ $option->name }}"
                        data-price="{{ $option->price }}"
                        {{-- Logika baru: Jika ini opsi pertama YANG TERLIHAT & grupnya wajib, maka 'checked' --}}
                        @if($isFirstVisibleOption && $group->is_required)
                            checked
                            @php $isFirstVisibleOption = false; @endphp
                        @endif
                    >
                    <span class="custom-{{ $group->type }}"></span>
                </label>
            </div>
        @endif
    @endforeach
</section>