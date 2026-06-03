@if(session('status'))
    <div x-data="{show:true}" x-show="show" x-transition.duration.300ms
         class="mb-6 flex items-start gap-3 rounded-md border border-saffron-200 bg-saffron-50 px-4 py-3 text-sm text-ink-800">
        <span class="text-saffron-500 mt-0.5">●</span>
        <p class="flex-1">{{ session('status') }}</p>
        <button @click="show=false" class="text-ink-400 hover:text-ink-700 text-xs">×</button>
    </div>
@endif

@if($errors->any())
    <div class="mb-6 rounded-md border border-clay-400/40 bg-clay-400/10 px-4 py-3 text-sm text-clay-600">
        <p class="font-semibold mb-1">Veuillez corriger les erreurs suivantes :</p>
        <ul class="list-disc ml-5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
