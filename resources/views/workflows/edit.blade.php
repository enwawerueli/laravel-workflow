@extends('emzd_workflow::layout')

@section('main')
<div class="lg:w-2/4 mx-auto p-5">
    <div class="flex justify-between items-center">
        <h2 class="text-gray-900 text-lg my-3">
            @if ($id = $workflow->id)
            Edit Workflow
            @else
            New Workflow
            @endif
        </h2>
        <a href="{{route('workflows.index')}}" class='border rounded-lg py-2 px-4 text-gray-800 bg-gray-100 hover:bg-blue-400'>Workflows</a>
    </div>
    @if (Session::has('success'))
    <div class="border border-green-100 rounded-lg text-green-300 text-sm font-medium bg-green-50 mb-2 p-2">
        <p>{{Session::get('success')}}</p>
    </div>
    @endif
    @if (Session::has('warning'))
    <div class="border border-orange-100 rounded-lg text-orange-300 text-sm font-medium bg-orange-50 mb-2 p-2">
        <p>{{Session::get('warning')}}</p>
    </div>
    @endif
    <div class="bg-white">
        <form id="workflow" action="{{$id ? route('workflows.update', ['workflow' => $id]) : route('workflows.store')}}" method="post">
            @if ($id)
            @method('patch')
            @endif
            @csrf
            <div class="grid gap-4 mb-4 lg:grid-cols-2">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-600">Name</label>
                    <input type="text" name="name" value="{{old('name') ?: $workflow->name}}" id="name" class="block w-full bg-gray-50 border border-{{$errors->has('name') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                    @error('name')
                    <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </div> 
                <div>
                    <label for="type" class="block mb-2 text-sm font-medium text-gray-600">Type</label>
                    <select name="type" id="type" class="block w-full bg-gray-50 border border-{{$errors->has('type') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                        <option value=""></option>
                        <option value="workflow" @selected($workflow->type === 'workflow')>Workflow</option>
                        <option value="state_machine" @selected($workflow->type === 'state_machine')>State Machine</option>
                    </select>
                    @error('type')
                    <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </div>
            </div>
            <div class="grid gap-4 mb-4 lg:grid-cols-2">
                <div>
                    <label for="supports" class="block mb-2 text-sm font-medium text-gray-600">Supported Class</label>
                    <input type="text" name="supports" value="{{old('supports') ?: $workflow->supports}}" id="supports" class="block w-full bg-gray-50 border border-{{$errors->has('supports') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2" />
                    @error('supports')
                    <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </div>
                <div>
                    <label for="marking_property" class="block mb-2 text-sm font-medium text-gray-600">Marking Property</label>
                    <input type="text" name="marking_property" value="{{old('marking_property') ?: $workflow->marking_property}}" id="marking_property" class="block w-full bg-gray-50 border border-{{$errors->has('marking_property') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2" />
                    @error('marking_property')
                    <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </div>
            </div>
            <div class="mb-4">
                <label for="metadata" class="block mb-2 text-sm font-medium text-gray-600">Metadata</label>
                <textarea name="metadata" id="metadata" class="block w-full bg-gray-50 border border-{{$errors->has('metadata') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">{{old('metadata') ?: ($workflow->metadata ? json_encode($workflow->metadata) : '')}}</textarea>
                @error('metadata')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div>
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-600">Is Active?</label>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center py-1">
                        <input type="radio" name="active" value="1" @checked(!$id || $workflow->active === 1) id="active_1" />
                        <label for="active_1" class="text-sm font-medium text-gray-600 mx-2">Yes</label>
                    </div>
                    <div class="flex items-center py-1">
                        <input type="radio" name="active" value="0" @checked($workflow->active === 0) id="active_0" />
                        <label for="active_0" class="text-sm font-medium text-gray-600 mx-2">No</label>
                    </div>
                </div>
            </div>
        </form>
        @if ($id)
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-600">Places</label>
            <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                @foreach ($workflow->places as $place)
                <div class="inline-flex items-center max-w-min mb-2 mr-1 p-1 border rounded-full text-sm bg-white">
                    <a href="{{route('places.edit', ['place'=> $place->id])}}" rel="noopener noreferrer" class="mx-2">
                        {{$place->name}}
                    </a>
                    <form action="{{route('places.destroy', ['place'=> $place->id])}}" method="post">
                        @method('delete')
                        @csrf
                        <button type="submit" class="bg-gray-100 border rounded-full p-1 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-red-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-300" onclick="return window.confirm('Are you sure?');">
                            <span class="sr-only">Delete</span>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
                <form action="{{route('places.store')}}" method="post">
                    @csrf
                    <input type="hidden" name="workflow_id" value="{{$workflow->id}}" />
                    <input type="text" name="name" value="" placeholder="Name" class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2 mr-2" />
                    <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Add</button>
                    @error('name')
                    <small class="block p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </form>
            </div>
        </div>
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-600">Initial Places</label>
            <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                @foreach ($workflow->initialPlaces as $place)
                <div class="inline-flex items-center max-w-min mb-2 mr-1 p-1 border rounded-full text-sm bg-white">
                    <a href="{{route('places.edit', ['place'=> $place->id])}}" rel="noopener noreferrer" class="mx-2">
                        {{$place->name}}
                    </a>
                    <form action="{{route('places.update', ['place'=> $place->id])}}" method="post">
                        @method('patch')
                        @csrf
                        <input type="hidden" name="initial" value="0" />
                        <button type="submit" class="bg-gray-100 border rounded-full p-1 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-red-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-300">
                            <span class="sr-only">Remove</span>
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </form>
                </div>
                @endforeach
                @if (!($s = $workflow->singleState()) || ($s && $workflow->initialPlaces->isEmpty()))
                <form id="addInitialPlace" action="{{route('places.update', ['place'=> '#id'])}}" method="post">
                    @method('patch')
                    @csrf
                    <input type="hidden" name="initial" value="1" />
                    <select name="place" id="initialPlace" class="bg-gray-50 border border-{{$errors->has('initial') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                        <option value=""></option>
                        @foreach ($workflow->places as $place)
                        <option value="{{$place->id}}">{{$place->name}}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Add</button>
                    @error('initial')
                    <small class="block p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </form>
                @endif
            </div>
        </div>
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-600">Transitions</label>
            <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                @foreach ($workflow->transitions as $transition)
                    <div class="inline-flex items-center max-w-min mb-2 mr-1 p-1 border rounded-full text-sm bg-white">
                        <a href="{{route('transitions.edit', ['transition'=> $transition->id])}}" rel="noopener noreferrer" class="mx-2">
                            {{$transition->name}}
                        </a>
                        <form action="{{route('transitions.destroy', ['transition'=> $transition->id])}}" method="post">
                            @method('delete')
                            @csrf
                            <button type="submit" class="bg-gray-100 border rounded-full p-1 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-red-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-300" onclick="return window.confirm('Are you sure?');">
                                <span class="sr-only">Delete</span>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                @endforeach
                <form action="{{route('transitions.store')}}" method="post">
                    @csrf
                    <input type="hidden" name="workflow_id" value="{{$workflow->id}}" />
                    <input type="text" name="name" value="" placeholder="Name" class="bg-gray-50 border border-gray-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2 mr-2" />
                    <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Add</button>
                    @error('name')
                    <small class="block p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                    @enderror
                </form>
            </div>
        </div>
        <div class="mb-4">
            <label class="block mb-2 text-sm font-medium text-gray-600">Events</label>
            <div class="border rounded-lg p-4 bg-gray-50">
                <form action="{{route('workflows.events.update', ['workflow'=> $id])}}" method="post">
                    @csrf
                    <div class="flex justify-between">
                        <div>
                            @foreach ($events as $event)
                            @if ($event->configurable)
                            <div class="flex items-center py-1">
                                <input type="checkbox" name="events[]" value="{{$event->id}}" @checked($workflow->events->contains($event)) id="event_{{$event->name}}" class="bg-gray-50 border-gray-300 focus:ring-3 focus:ring-blue-300 h-4 w-4 rounded">
                                <label for="event_{{$event->name}}" class="text-sm font-medium text-gray-600 mx-2">{{$event->name}}</label>
                            </div>
                            @else
                            <input type="hidden" name="events[]" value="{{$event->id}}" checked >
                            @endif
                            @endforeach
                        </div>
                        <div>
                            <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 my-1 text-center">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
        <button form="workflow" type="submit" class="text-white bg-blue-400 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Save</button>
    </div>
</div>
@endsection

@pushOnce('scripts')
    <script type="text/javascript">
        var addInitialPlace = document.forms['addInitialPlace'];
        addInitialPlace && addInitialPlace.addEventListener('submit', function(e) {
            var place = this.elements['place'];
            if (!place.value) {
                e.preventDefault();
                return;
            }
            this.setAttribute('action', this.getAttribute('action').replace('#id', place.value));
            place.remove();
        });
    </script>
@endPushOnce