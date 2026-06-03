<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChapterController extends Controller
{
    public function index(Module $module): View
    {
        $chapters = $module->chapters()->paginate(20);
        return view('admin.chapters.index', compact('module', 'chapters'));
    }

    public function create(Module $module): View
    {
        return view('admin.chapters.create', [
            'module'  => $module,
            'chapter' => new Chapter(['module_id' => $module->id, 'position' => ($module->chapters()->max('position') ?? 0) + 1]),
        ]);
    }

    public function store(Request $request, Module $module): RedirectResponse
    {
        $data = $this->validated($request);
        $data['module_id'] = $module->id;
        Chapter::create($data);
        return redirect()->route('admin.modules.chapters.index', $module)->with('status', 'Chapitre créé.');
    }

    public function edit(Chapter $chapter): View
    {
        $module = $chapter->module;
        return view('admin.chapters.edit', compact('chapter', 'module'));
    }

    public function update(Request $request, Chapter $chapter): RedirectResponse
    {
        $chapter->update($this->validated($request));
        return redirect()->route('admin.modules.chapters.index', $chapter->module)->with('status', 'Chapitre mis à jour.');
    }

    public function destroy(Chapter $chapter): RedirectResponse
    {
        $module = $chapter->module;
        $chapter->delete();
        return redirect()->route('admin.modules.chapters.index', $module)->with('status', 'Chapitre supprimé.');
    }

    public function show(Chapter $chapter): View
    {
        return $this->edit($chapter);
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'title'       => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string'],
            'position'    => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
