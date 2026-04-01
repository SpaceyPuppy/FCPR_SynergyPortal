<x-app-layout title="Register Domain">
    <div class="max-w-2xl bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('domains.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Domain Name *</label>
                    <input type="text" name="domain_name" value="{{ old('domain_name', request('domain')) }}" required
                           placeholder="example.com.au"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Years *</label>
                    <select name="years" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('years', 1) == $i ? 'selected' : '' }}>{{ $i }} year(s)</option>
                        @endfor
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Customer *</label>
                <select name="customer_id" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">— Select customer —</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}"
                                {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} ({{ $customer->email }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nameservers * (min 2)</label>
                @for($n = 0; $n < 4; $n++)
                    <input type="text" name="nameservers[]"
                           value="{{ old('nameservers.' . $n) }}"
                           placeholder="ns{{ $n + 1 }}.example.com"
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm {{ $n >= 2 ? 'mt-2' : '' }}">
                @endfor
            </div>

            <div class="flex items-center gap-2">
                <input type="checkbox" name="id_protect" id="id_protect" value="1" {{ old('id_protect') ? 'checked' : '' }}>
                <label for="id_protect" class="text-sm text-gray-700">Enable ID Protection</label>
            </div>

            <fieldset class="border border-gray-200 rounded-md p-4">
                <legend class="text-sm font-medium text-gray-700 px-2">Registrant Contact</legend>
                <div class="grid grid-cols-2 gap-3 mt-2">
                    <div>
                        <label class="block text-xs text-gray-500">First Name *</label>
                        <input type="text" name="contact[firstName]" value="{{ old('contact.firstName') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Last Name *</label>
                        <input type="text" name="contact[lastName]" value="{{ old('contact.lastName') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Email *</label>
                        <input type="email" name="contact[email]" value="{{ old('contact.email') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Phone *</label>
                        <input type="text" name="contact[phone]" value="{{ old('contact.phone') }}" required
                               placeholder="+61.400000000"
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-xs text-gray-500">Address *</label>
                        <input type="text" name="contact[address1]" value="{{ old('contact.address1') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">City *</label>
                        <input type="text" name="contact[city]" value="{{ old('contact.city') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">State *</label>
                        <input type="text" name="contact[state]" value="{{ old('contact.state') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Postcode *</label>
                        <input type="text" name="contact[postcode]" value="{{ old('contact.postcode') }}" required
                               class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                    <div>
                        <label class="block text-xs text-gray-500">Country (2-letter) *</label>
                        <input type="text" name="contact[country]" value="{{ old('contact.country', 'AU') }}" required
                               maxlength="2" class="mt-1 block w-full border-gray-300 rounded text-sm">
                    </div>
                </div>
            </fieldset>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                    Register Domain
                </button>
                <a href="{{ route('domains.index') }}" class="px-4 py-2 rounded text-sm border border-gray-300 hover:bg-gray-50">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
