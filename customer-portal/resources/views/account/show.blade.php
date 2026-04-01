<x-app-layout title="My Account">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Profile --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Profile Information</h2>
            <form method="POST" action="{{ route('account.update-profile') }}" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Company</label>
                    <input type="text" name="company" value="{{ old('company', $user->company) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Save Profile
                </button>
            </form>
        </div>

        {{-- Password --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Change Password</h2>
            <form method="POST" action="{{ route('account.update-password') }}" class="space-y-4">
                @csrf @method('PATCH')
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password *</label>
                    <input type="password" name="current_password" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password *</label>
                    <input type="password" name="password" required minlength="8"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password *</label>
                    <input type="password" name="password_confirmation" required
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <button type="submit" class="bg-gray-700 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">
                    Change Password
                </button>
            </form>
        </div>

    </div>
</x-app-layout>
