<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $evaluation->title }} — iEVAL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">

<div x-data="examRunner({
        secondsRemaining: {{ $attempt->secondsRemaining() }},
        submitUrl: @js(route('student.attempts.submit', $attempt)),
        totalQuestions: {{ $evaluation->questions->count() }},
        csrf: @js(csrf_token()),
     })"
     @keydown.window.left.prevent="prev()"
     @keydown.window.right.prevent="next()"
     class="min-h-screen flex flex-col">

    {{-- Top bar with timer --}}
    <header class="bg-ink-900 text-paper-50 px-6 py-4 sticky top-0 z-30 border-b border-ink-700">
        <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="small-caps text-saffron-300">{{ $evaluation->module->name }}</p>
                <h1 class="font-display text-lg truncate">{{ $evaluation->title }}</h1>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="small-caps text-ink-300">Temps restant</p>
                    <p class="font-mono text-2xl font-semibold"
                       :class="secondsRemaining <= 60 ? 'text-clay-400 animate-pulse' : (secondsRemaining <= 300 ? 'text-saffron-300' : 'text-paper-50')"
                       x-text="formatTime(secondsRemaining)"></p>
                </div>
                <button @click="confirmSubmit()" class="btn-accent">Soumettre</button>
            </div>
        </div>
    </header>

    <main class="flex-1 p-4 sm:p-8">
        <div class="max-w-6xl mx-auto grid lg:grid-cols-4 gap-6">

            {{-- Question navigator --}}
            <aside class="lg:col-span-1 lg:sticky lg:top-24 lg:self-start">
                <div class="card card-pad">
                    <p class="small-caps text-ink-500 mb-3">Navigation</p>
                    <div class="grid grid-cols-5 lg:grid-cols-4 gap-2 mb-4">
                        @foreach($evaluation->questions as $i => $q)
                            <button type="button"
                                    @click="goTo({{ $i }})"
                                    :class="current === {{ $i }} ? 'bg-ink-900 text-paper-50' : (answered[{{ $q->id }}] ? 'bg-saffron-100 text-ink-900 border border-saffron-300' : 'bg-paper-100 text-ink-700 hover:bg-paper-200 border border-ink-100')"
                                    class="aspect-square rounded-md text-sm font-medium transition-colors">
                                {{ $i + 1 }}
                            </button>
                        @endforeach
                    </div>
                    <div class="text-xs text-ink-500 space-y-1">
                        <p><span class="inline-block w-3 h-3 rounded bg-saffron-100 border border-saffron-300 mr-1"></span> Répondue</p>
                        <p><span class="inline-block w-3 h-3 rounded bg-paper-100 border border-ink-100 mr-1"></span> Non répondue</p>
                        <p><span class="inline-block w-3 h-3 rounded bg-ink-900 mr-1"></span> En cours</p>
                    </div>
                    <div class="rule"></div>
                    <p class="text-xs text-ink-500"><span x-text="answeredCount"></span> / {{ $evaluation->questions->count() }} questions répondues</p>
                </div>
            </aside>

            {{-- Question form --}}
            <form id="exam-form" method="POST" action="{{ route('student.attempts.submit', $attempt) }}"
                  class="lg:col-span-3 space-y-4"
                  @submit.prevent="onSubmit($event)">
                @csrf

                @foreach($evaluation->questions as $i => $q)
                    <div class="card card-pad" x-show="current === {{ $i }}" x-transition.opacity>
                        <div class="flex items-baseline justify-between mb-3">
                            <p class="small-caps text-ink-500">Question {{ $i + 1 }} / {{ $evaluation->questions->count() }}</p>
                            <div class="flex items-center gap-2">
                                <span class="badge-ink">{{ $q->type === 'single' ? 'QCU' : 'QCM' }}</span>
                                <span class="text-xs text-ink-500">{{ number_format($q->points, 2) }} pt</span>
                            </div>
                        </div>

                        <h2 class="font-display text-xl text-ink-900 leading-snug mb-5">{{ $q->statement }}</h2>

                        <div class="space-y-2">
                            @foreach($q->options as $o)
                                <label class="flex items-start gap-3 rounded-md border border-ink-200 hover:border-saffron-300 hover:bg-saffron-50/50 px-4 py-3 cursor-pointer transition-colors">
                                    @if($q->type === 'single')
                                        <input type="radio"
                                               name="answers[{{ $q->id }}]"
                                               value="{{ $o->id }}"
                                               @change="markAnswered({{ $q->id }}, $event.target.value)"
                                               class="mt-1 w-5 h-5 border-ink-300 text-ink-900 focus:ring-ink-300">
                                    @else
                                        <input type="checkbox"
                                               name="answers[{{ $q->id }}][]"
                                               value="{{ $o->id }}"
                                               @change="markAnsweredMulti({{ $q->id }}, {{ $o->id }}, $event.target.checked)"
                                               class="mt-1 w-5 h-5 rounded border-ink-300 text-ink-900 focus:ring-ink-300">
                                    @endif
                                    <span class="text-ink-800">{{ $o->text }}</span>
                                </label>
                            @endforeach
                        </div>

                        <div class="rule"></div>

                        <div class="flex justify-between">
                            <button type="button" @click="prev()" class="btn-outline" :disabled="current === 0">← Précédent</button>
                            @if($i === $evaluation->questions->count() - 1)
                                <button type="button" @click="confirmSubmit()" class="btn-accent">Soumettre l'évaluation →</button>
                            @else
                                <button type="button" @click="next()" class="btn-primary">Suivant →</button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </form>
        </div>
    </main>
</div>

<script>
function examRunner(config) {
    return {
        current: 0,
        secondsRemaining: config.secondsRemaining,
        totalQuestions: config.totalQuestions,
        submitUrl: config.submitUrl,
        answered: {}, // { questionId: true }
        answeredOptions: {}, // for multiple: { questionId: Set }
        timer: null,
        submitting: false,

        init() {
            this.timer = setInterval(() => {
                this.secondsRemaining = Math.max(0, this.secondsRemaining - 1);
                if (this.secondsRemaining === 0) {
                    clearInterval(this.timer);
                    if (!this.submitting) this.forceSubmit();
                }
            }, 1000);

            window.addEventListener('beforeunload', (e) => {
                if (!this.submitting) {
                    e.preventDefault();
                    e.returnValue = '';
                }
            });
        },

        formatTime(s) {
            const m = Math.floor(s / 60);
            const sec = s % 60;
            return String(m).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
        },

        prev() { if (this.current > 0) this.current--; },
        next() { if (this.current < this.totalQuestions - 1) this.current++; },
        goTo(i) { this.current = i; },

        get answeredCount() {
            return Object.keys(this.answered).length;
        },

        markAnswered(qid, val) {
            if (val) this.answered[qid] = true;
            else delete this.answered[qid];
        },
        markAnsweredMulti(qid, optId, checked) {
            this.answeredOptions[qid] = this.answeredOptions[qid] || new Set();
            if (checked) this.answeredOptions[qid].add(optId);
            else this.answeredOptions[qid].delete(optId);

            if (this.answeredOptions[qid].size > 0) this.answered[qid] = true;
            else delete this.answered[qid];
        },

        confirmSubmit() {
            const unanswered = this.totalQuestions - this.answeredCount;
            const msg = unanswered > 0
                ? `Il reste ${unanswered} question(s) sans réponse. Soumettre quand même ?`
                : 'Soumettre votre évaluation ?';
            if (confirm(msg)) this.onSubmit();
        },

        onSubmit() {
            this.submitting = true;
            clearInterval(this.timer);
            document.getElementById('exam-form').submit();
        },

        forceSubmit() {
            this.submitting = true;
            document.getElementById('exam-form').submit();
        },
    };
}
</script>

</body>
</html>
