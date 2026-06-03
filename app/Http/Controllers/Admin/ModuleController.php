<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function index(Request $request): View
    {
        $q = Module::query()->with(['semester', 'teacher'])->withCount(['students', 'chapters', 'evaluations']);

        if ($search = $request->get('q')) {
            $q->where(fn ($w) => $w->where('name', 'like', "%$search%")->orWhere('code', 'like', "%$search%"));
        }
        if ($semester = $request->get('semester')) {
            $q->where('semester_id', $semester);
        }

        $modules   = $q->latest()->paginate(15)->withQueryString();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('admin.modules.index', compact('modules', 'semesters'));
    }

    public function create(): View
    {
        return view('admin.modules.create', [
            'module'    => new Module(),
            'semesters' => Semester::orderByDesc('start_date')->get(),
            'teachers'  => User::where('role', 'teacher')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Module::create($data);
        return redirect()->route('admin.modules.index')->with('status', 'Module créé.');
    }

    public function show(Module $module): View
    {
        $module->load(['semester', 'teacher', 'students', 'chapters', 'evaluations']);
        $allStudents = User::where('role', 'student')
                        ->whereNotIn('id', $module->students->pluck('id'))
                        ->orderBy('name')->get();

        return view('admin.modules.show', compact('module', 'allStudents'));
    }

    public function edit(Module $module): View
    {
        return view('admin.modules.edit', [
            'module'    => $module,
            'semesters' => Semester::orderByDesc('start_date')->get(),
            'teachers'  => User::where('role', 'teacher')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Module $module): RedirectResponse
    {
        $module->update($this->validated($request, $module->id));
        return redirect()->route('admin.modules.show', $module)->with('status', 'Module mis à jour.');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $module->delete();
        return redirect()->route('admin.modules.index')->with('status', 'Module supprimé.');
    }

    public function enroll(Request $request, Module $module): RedirectResponse
    {
        $data = $request->validate([
            'student_ids'   => ['required', 'array'],
            'student_ids.*' => ['integer', 'exists:users,id'],
        ]);
        $module->students()->syncWithoutDetaching($data['student_ids']);
        return back()->with('status', 'Étudiants inscrits au module.');
    }

    public function unenroll(Request $request, Module $module): RedirectResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $module->students()->detach($data['student_id']);
        return back()->with('status', 'Étudiant retiré du module.');
    }

    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'code'        => ['required', 'string', 'max:32', Rule::unique('modules', 'code')->ignore($id)],
            'name'        => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'semester_id' => ['required', 'exists:semesters,id'],
            'teacher_id'  => ['nullable', 'exists:users,id'],
        ]);
    }
}
