<x-app-layout title="Check Domain Availability">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('domains.check-availability') }}" class="flex gap-3">
            @csrf
            <input type="text" name="domain_name" value="{{ $domain_name ?? '' }}"
                   placeholder="e.g. example.com.au" required
                   class="flex-1 border-gray-300 rounded-md shadow-sm text-sm">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                Check
            </button>
        </form>

        @isset($available)
            <div class="mt-4 p-4 rounded-md {{ $available ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }}">
                @if($available)
                    <strong>{{ $domain_name }}</strong> is available for registration.
                    <a href="{{ route('domains.register') }}?domain={{ $domain_name }}"
                       class="ml-2 underline font-medium">Register it &rarr;</a>
                @else
                    <strong>{{ $domain_name }}</strong> is not available.
                @endif
            </div>
        @endisset

        @if(isset($available) && $available === null)
            <div class="mt-4 p-4 rounded-md bg-yellow-50 text-yellow-800">
                Could not check availability. Verify your Synergy API credentials.
            </div>
        @endif
    </div>
</x-app-layout>
