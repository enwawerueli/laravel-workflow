<?php

namespace EmzD\Workflow\Http\Controllers;

use EmzD\Workflow\Http\Requests\PlaceRequest;
use EmzD\Workflow\Models\Place;
use EmzD\Workflow\Models\Workflow;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class PlaceController extends Controller {
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
        // $this->authorize('create', Place::class);
        return view('emzd_workflow::places.edit', ['place' => new Place(), 'workflows' => Workflow::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PlaceRequest $request): RedirectResponse
    {
        // $this->authorize('create', Place::class);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Place::class)->getFillable()));
        $place = Place::create($fillable);
        return redirect(route('places.edit', ['place' => $place->id]))->with('success', 'Created successfuly!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Place $place): Response
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($place): View
    {
        $place = Place::with('workflow')->find($place);
        // $this->authorize('update', $place);
        return view('emzd_workflow::places.edit', [
            'place' => $place,
            'workflows' => Workflow::with('places')->whereActive(true)->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PlaceRequest $request, Place $place): RedirectResponse
    {
        // $this->authorize('update', $place);
        $validated = $request->validated();
        $fillable = array_intersect_key($validated, array_flip(app(Place::class)->getFillable()));
        $place->update($fillable);
        return back()->with('success', 'Saved successfuly!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($place): RedirectResponse
    {
        $place = Place::with('workflow')->find($place);
        // $this->authorize('delete', $place);
        $place->delete();
        return redirect(route('workflows.edit', ['workflow' => $place->workflow->id]));
    }
}