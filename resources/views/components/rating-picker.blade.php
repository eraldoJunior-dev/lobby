@props(['id', 'value' => null])
<fieldset class="rating-input rating-halves">
    <legend class="sr-only">{{ __('Nota') }} — 0,5–5</legend>
    @for ($step = 10; $step >= 1; $step--)
        @php($score = $step / 2)
        <input type="radio" id="{{ $id }}-{{ $step }}" name="rating" value="{{ $score }}" @checked($value !== null && (float) $value === $score)>
        <label class="{{ $step % 2 === 0 ? 'half-right' : 'half-left' }}" for="{{ $id }}-{{ $step }}" title="{{ __('Nota :n de 5', ['n' => \App\Support\Fmt::num($score, 1)]) }}">
            <x-icon name="star" filled /><span class="sr-only">{{ __('Nota :n de 5', ['n' => \App\Support\Fmt::num($score, 1)]) }}</span>
        </label>
    @endfor
</fieldset>
