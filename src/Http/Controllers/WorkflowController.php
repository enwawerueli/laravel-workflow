<?php

namespace EmzD\Workflow\Http\Controllers;

use EmzD\Workflow\Http\Requests\WorkflowRequest;
use EmzD\Workflow\Models\Event;
use EmzD\Workflow\Models\Workflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WorkflowController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // $this->authorize('viewAny', Workflow::class);
        return view('emzd_workflow::workflows.index', ['workflows' => Workflow::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // $this->authorize('create', Workflow::class);
        return view('emzd_workflow::workflows.edit', ['workflow' => new Workflow(), 'events' => Event::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkflowRequest $request): RedirectResponse
    {
        // $this->authorize('create', Workflow::class);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Workflow::class)->getFillable()));
        $workflow = Workflow::create($fillable);
        $redirect = redirect(route('workflows.edit', ['workflow' => $workflow->id]));
        try {
            $workflow->validate();
            return $redirect->with('success', 'Created successfuly!');
        } catch (\Throwable $e) {
            return $redirect->with('warning', 'WARNING! ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Workflow $workflow): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($workflow): View
    {
        // $this->authorize('update', $workflow)
        return view('emzd_workflow::workflows.edit', [
            'workflow' => Workflow::with(['places', 'initialPlaces', 'transitions', 'events'])->find($workflow),
            'events' => Event::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkflowRequest $request, Workflow $workflow): RedirectResponse
    {
        // $this->authorize('update', $workflow);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Workflow::class)->getFillable()));
        $workflow->update($fillable);
        try {
            $workflow->validate();
            return back()->with('success', 'Saved successfuly!');
        } catch (\Throwable $e) {
            return back()->with('warning', 'WARNING! ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workflow $workflow): RedirectResponse
    {
        // $this->authorize('delete', $workflow);
        $workflow->delete();
        return redirect(route('workflows.index'));
    }

    public function updateEvents(Request $request, Workflow $workflow): RedirectResponse
    {
        // $this->authorize('update', $workflow);
        $validated = $this->validate($request, ['events' => 'array']);
        $workflow->events()->sync($validated['events']);
        return back();
    }
}