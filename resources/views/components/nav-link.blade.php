@props(['route', 'icon', 'label'])

@php
  // Nama route lengkap, misal 'home', 'user', dsb.
  $isActive = request()->routeIs($route.'*');
@endphp

<a href="{{ route($route) }}"
   class="nav-link d-flex align-items-center text-white{{ $isActive ? ' active bg-white-10' : '' }}">
  <i class="fa {{ $icon }} me-2"></i>
  <span>{{ $label }}</span>
</a>
