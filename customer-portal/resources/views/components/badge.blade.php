@props(['status'])

@php
$classes = match(strtolower($status ?? '')) {
    'active', 'paid'       => 'bg-green-100 text-green-800',
    'pending', 'draft'     => 'bg-yellow-100 text-yellow-800',
    'suspended', 'unpaid'  => 'bg-orange-100 text-orange-800',
    'expired', 'overdue'   => 'bg-red-100 text-red-800',
    'cancelled','revoked','terminated' => 'bg-gray-100 text-gray-600',
    default                => 'bg-blue-100 text-blue-800',
};
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ ucfirst($status ?? 'unknown') }}
</span>
