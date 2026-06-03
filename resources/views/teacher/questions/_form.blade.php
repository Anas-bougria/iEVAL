@php $editing = isset($question) && $question->exists; @endphp
<form action="{{ $editing ? route('teacher.questions.update', $question) : route('teacher.evaluations.questions.store', $evaluation) }}"
      method="POST" class="space-y-6"
      x-data="questionForm({{ Js::from([
          'type' => old('type', $question->type ?? 'single'),
          'options' => old('options', $options),
      ]) }})">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="grid sm:grid-cols-3 gap-5">
        <div class="sm:col-span-3">
            <label class="label">Énoncé de la question</label>
            <textarea name="statement" rows="3" required class="textarea">{{ old('statement', $question->statement ?? '') }}</textarea>
        </div>

        <div>
            <label class="label">Type</label>
            <select name="type" x-model="type" class="select">
                <option value="single">QCU (une seule bonne réponse)</option>
                <option value="multiple">QCM (plusieurs bonnes réponses)</option>
            </select>
        </div>

        <div>
            <label class="label">Points</label>
            <input type="number" step="0.25" min="0.25" max="100" name="points" required
                   value="{{ old('points', $question->points ?? 1) }}" class="input">
        </div>

        <div>
            <label class="label">Chapitre</label>
            <select name="chapter_id" class="select">
                <option value="">— Aucun —</option>
                @foreach($chapters as $c)
                    <option value="{{ $c->id }}" @selected(old('chapter_id', $question->chapter_id ?? null) == $c->id)>{{ $c->title }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-2">
            <label class="label mb-0">Options de réponse</label>
            <button type="button" @click="addOption()" class="text-sm text-saffron-500 hover:text-saffron-700">+ Ajouter une option</button>
        </div>

        <p class="text-xs text-ink-400 mb-3" x-show="type === 'single'">Cochez exactement une bonne réponse.</p>
        <p class="text-xs text-ink-400 mb-3" x-show="type === 'multiple'">Cochez toutes les bonnes réponses (1 ou plus).</p>

        <template x-for="(opt, i) in options" :key="i">
            <div class="flex items-start gap-3 mb-2">
                <label class="flex items-center pt-2 shrink-0">
                    <template x-if="type === 'single'">
                        <input type="checkbox" :name="`options[${i}][is_correct]`" value="1"
                               :checked="opt.is_correct"
                               @change="setSingleCorrect(i)"
                               class="w-5 h-5 rounded border-ink-300 text-saffron-500 focus:ring-saffron-300">
                    </template>
                    <template x-if="type === 'multiple'">
                        <input type="checkbox" :name="`options[${i}][is_correct]`" value="1"
                               x-model="opt.is_correct"
                               class="w-5 h-5 rounded border-ink-300 text-saffron-500 focus:ring-saffron-300">
                    </template>
                </label>
                <input type="text" :name="`options[${i}][text]`" x-model="opt.text" required
                       class="input flex-1" :placeholder="`Option ${i + 1}`">
                <button type="button" @click="removeOption(i)" x-show="options.length > 2"
                        class="text-clay-500 hover:text-clay-600 pt-2">×</button>
            </div>
        </template>
    </div>

    <div class="flex justify-end gap-3 pt-2 border-t border-ink-100">
        <a href="{{ route('teacher.evaluations.show', $evaluation) }}" class="btn-ghost">Annuler</a>
        <button class="btn-primary">{{ $editing ? 'Enregistrer' : 'Ajouter la question' }}</button>
    </div>
</form>

@push('scripts')
<script>
function questionForm(init) {
    return {
        type:    init.type,
        options: init.options.map(o => ({
            text: o.text || '',
            is_correct: !!o.is_correct,
        })),
        addOption() {
            if (this.options.length < 8) this.options.push({ text: '', is_correct: false });
        },
        removeOption(i) {
            if (this.options.length > 2) this.options.splice(i, 1);
        },
        setSingleCorrect(i) {
            this.options.forEach((o, idx) => o.is_correct = (idx === i));
        },
    };
}
</script>
@endpush
