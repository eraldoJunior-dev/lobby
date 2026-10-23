@props(['name', 'filled' => false])
<svg {{ $attributes->class(['icon', 'icon-filled' => $filled]) }} viewBox="0 0 24 24" aria-hidden="true" focusable="false"><use href="#i-{{ $name }}"/></svg>
