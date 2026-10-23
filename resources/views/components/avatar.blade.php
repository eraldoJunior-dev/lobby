@props(['user', 'size' => 44])
<span {{ $attributes->class('avatar') }}
      style="--size: {{ (int) $size }}px; --hue: {{ ($user->id * 67) % 360 }}"
      aria-hidden="true">@if ($url = $user->avatarUrl())<img src="{{ $url }}" alt="" loading="lazy" onerror="this.remove()">@else{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}@endif</span>
