@if(session('success'))
    <div class="mb-4 rounded-md bg-green-50 border border-green-200 p-4 text-green-800 text-sm">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="mb-4 rounded-md bg-red-50 border border-red-200 p-4 text-red-800 text-sm">
        <ul class="list-disc list-inside space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
