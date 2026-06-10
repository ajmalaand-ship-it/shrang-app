{{-- Shared flash notice. Renders success / error / warning / info from session.
     Optional: <x-notice class="dashboard-notice" /> to add page-specific styling. --}}
@props(['class' => ''])
@if(session('success'))
    <div class="sh-notice sh-notice--success {{ $class }}">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="sh-notice sh-notice--danger {{ $class }}">{{ session('error') }}</div>
@endif
@if(session('warning'))
    <div class="sh-notice sh-notice--warning {{ $class }}">{{ session('warning') }}</div>
@endif
@if(session('info'))
    <div class="sh-notice sh-notice--info {{ $class }}">{{ session('info') }}</div>
@endif
