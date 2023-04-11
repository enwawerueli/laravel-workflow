<?php

namespace EmzD\Workflow\Http\Controllers;

use EmzD\Workflow\Http\Requests\TransitionRequest;
use EmzD\Workflow\Models\Place;
use EmzD\Workflow\Models\Transition;
use EmzD\Workflow\Models\Workflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransitionController extends Controller {
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // $this->authorize('create', Transition::class);
        return view('emzd_workflow::transitions.edit', [
            'transition' => new Transition(),
            'places' => [],
            'workflows' => Workflow::with('places')->whereActive(true)->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TransitionRequest $request): RedirectResponse
    {
        // $this->authorize('create', Transition::class);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Transition::class)->getFillable()));
        $transition = Transition::create($fillable);
        return redirect(route('transitions.edit', ['transition' => $transition->id]))->with('success', 'Created successfuly!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transition $transition): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($transition): View
    {
        $transition = Transition::with(['workflow', 'from', 'to'])->find($transition);
        // $this->authorize('update', $transition);
        return view('emzd_workflow::transitions.edit', [
            'transition' => $transition,
            'places' => Place::whereWorkflowId($transition->workflow_id)->get(),
            'workflows' => Workflow::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TransitionRequest $request, Transition $transition): RedirectResponse
    {
        // $this->authorize('update', $transition);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Transition::class)->getFillable()));
        $transition->update($fillable);
        return back()->with('success', 'Saved successfuly!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($transition): RedirectResponse
    {
        $transition = Transition::with('workflow')->find($transition);
        // $this->authorize('delete', $transition);
        $transition->delete();
        return redirect(route('workflows.edit', ['workflow' => $transition->workflow->id]));
    }

    public function updatePlaces(Request $request, Transition $transition): RedirectResponse
    {
        // $this->authorize('update', $transition);
        if ($request->has($k = 'from') || $request->has($k = 'to')) {
            $transition->{$k}()->{$request->action}($request->{$k});
        }
        return back();
    }
}