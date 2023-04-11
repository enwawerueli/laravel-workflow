<?php

use EmzD\Workflow\Http\Controllers\PlaceController;
use EmzD\Workflow\Http\Controllers\TransitionController;
use EmzD\Workflow\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::resource('workflows', WorkflowController::class)->except('show')->whereNumber('workflow');
Route::group(['prefix' => 'workflows'], function () {
    Route::post('/{workflow}/events', [WorkflowController::class, 'updateEvents'])->name('workflows.events.update');
    Route::resource('places', PlaceController::class)->except(['index', 'show']);
    Route::resource('transitions', TransitionController::class)->except(['index', 'show']);
    Route::post('/transitions/{transition}/places', [TransitionController::class, 'updatePlaces'])->name('transitions.places.update');
});