@props(['game', 'label' => false])
@php
    $hex = fn (?string $c, string $fallback) => $c && preg_match('/^#[0-9a-f]{6}$/i', $c) ? $c : $fallback;
    $from = $hex($game->cover_from, '#4c3a7a');
    $to = $hex($game->cover_to, '#1f1830');
    $img = method_exists($game, 'coverSrc') ? $game->coverSrc() : null;
    $alts = method_exists($game, 'coverFallbacks') ? $game->coverFallbacks() : [];
@endphp
<span {{ $attributes->class('cover') }} style="--from: {{ $from }}; --to: {{ $to }}">
    @if ($img)
        <img src="{{ $img }}" alt="" loading="lazy" decoding="async" referrerpolicy="no-referrer"
             @if ($alts) data-alts="{{ json_encode($alts) }}" @endif
             onerror="window.lobbyCoverFail&&window.lobbyCoverFail(this)">
    @endif
    @if ($label)
        <span class="cover-title">{{ $game->title }}</span>
    @endif
</span>
