<x-app-layout :title="'SSL: ' . $certificate->domain">
    <div class="max-w-lg bg-white rounded-lg shadow p-5 space-y-3 text-sm">
        <p><span class="text-gray-500">Domain:</span> {{ $certificate->domain }}</p>
        <p><span class="text-gray-500">Customer:</span>
            <a href="{{ route('customers.show', $certificate->user_id) }}" class="text-blue-600 hover:underline">
                {{ $certificate->user->name }}
            </a>
        </p>
        <p><span class="text-gray-500">Type:</span> {{ $certificate->type }}</p>
        <p><span class="text-gray-500">Expires:</span> {{ $certificate->expiry_date?->format('d M Y') ?? '—' }}</p>
        <p><span class="text-gray-500">Status:</span> <x-badge :status="$certificate->status" /></p>

        <form method="POST" action="{{ route('ssl.update', $certificate->id) }}" class="flex gap-2 pt-3">
            @csrf @method('PATCH')
            <select name="status" class="border-gray-300 rounded text-sm">
                @foreach(['pending','active','expired','revoked'] as $s)
                    <option value="{{ $s }}" {{ $certificate->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <input type="date" name="expiry_date" value="{{ $certificate->expiry_date?->format('Y-m-d') }}"
                   class="border-gray-300 rounded text-sm">
            <button class="bg-blue-600 text-white px-3 py-1.5 rounded text-sm hover:bg-blue-700">Update</button>
        </form>
    </div>
</x-app-layout>
