@props(['value'])
<span {{ $attributes->class('stars') }} role="img" aria-label="{{ __('Nota :n de 5', ['n' => \App\Support\Fmt::num((float) $value, 1)]) }}">
    @for ($i = 1; $i <= 5; $i++)
        @php($fill = max(0, min(100, ((float) $value - ($i - 1)) * 100)))
        <span class="star-fraction" aria-hidden="true">
            <x-icon name="star" />
            <span class="star-fraction-fill" style="width: {{ $fill }}%"><x-icon name="star" filled /></span>
        </span>
    @endfor
</span>
