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

    @php
        $isFirstVisibleOption = true;
    @endphp

    @foreach ($group->options as $option)
        @php
            $isIcedOption = Str::contains($option->name, 'Ice');
            $isHotOption = Str::contains($option->name, 'Hot');
            $isGeneralOption = !$isIcedOption && !$isHotOption;

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