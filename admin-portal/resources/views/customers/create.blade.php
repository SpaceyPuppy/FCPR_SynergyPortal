<x-app-layout title="New Customer">
    <div class="max-w-lg bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('customers.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Company</label>
                <input type="text" name="company" value="{{ old('company') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Phone</label>
                <input type="text" name="phone" value="{{ old('phone') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password *</label>
                <input type="password" name="password" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                <input type="password" name="password_confirmation" required
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Create Customer
                </button>
                <a href="{{ route('customers.index') }}"
                   class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
