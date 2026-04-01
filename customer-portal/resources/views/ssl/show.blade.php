<x-app-layout :title="'SSL: ' . $certificate->domain">
    <div class="max-w-lg bg-white rounded-lg shadow p-5 space-y-3 text-sm">
        <p><span class="text-gray-500">Domain:</span> {{ $certificate->domain }}</p>
        <p><span class="text-gray-500">Type:</span> {{ $certificate->type }}</p>
        <p><span class="text-gray-500">Expires:</span>
            <span class="{{ $certificate->isExpiringSoon() ? 'text-red-600 font-medium' : '' }}">
                {{ $certificate->expiry_date?->format('d M Y') ?? '—' }}
            </span>
        </p>
        <p><span class="text-gray-500">Status:</span> <x-badge :status="$certificate->status" /></p>
    </div>
</x-app-layout>
