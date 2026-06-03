<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SemesterController extends Controller
{
    public function index(): View
    {
        $semesters = Semester::withCount('modules')->orderByDesc('start_date')->paginate(15);
        return view('admin.semesters.index', compact('semesters'));
    }

    public function create(): View
    {
        return view('admin.semesters.create', ['semester' => new Semester()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        if (!empty($data['is_current'])) Semester::query()->update(['is_current' => false]);
        Semester::create($data);
        return redirect()->route('admin.semesters.index')->with('status', 'Semestre créé.');
    }

    public function show(Semester $semester): View
    {
        return view('admin.semesters.edit', compact('semester'));
    }

    public function edit(Semester $semester): View
    {
        return view('admin.semesters.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester): RedirectResponse
    {
        $data = $this->validated($request, $semester->id);
        if (!empty($data['is_current'])) Semester::where('id', '!=', $semester->id)->update(['is_current' => false]);
        $semester->update($data);
        return redirect()->route('admin.semesters.index')->with('status', 'Semestre mis à jour.');
    }

    public function destroy(Semester $semester): RedirectResponse
    {
        $semester->delete();
        return back()->with('status', 'Semestre supprimé.');
    }

    protected function validated(Request $request, ?int $id = null): array
    {
        return $request->validate([
            'name'       => ['required', 'string', 'max:120'],
            'code'       => ['required', 'string', 'max:32', Rule::unique('semesters', 'code')->ignore($id)],
            'start_date' => ['required', 'date'],
            'end_date'   => ['required', 'date', 'after_or_equal:start_date'],
            'is_current' => ['sometimes', 'boolean'],
        ]);
    }
}
