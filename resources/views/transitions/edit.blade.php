@extends('emzd_workflow::layout')

@section('main')
<div class="lg:w-2/4 mx-auto p-5">
    <h2 class="text-gray-900 text-lg my-3">
        @if ($id = $transition->id)
        Edit Transition
        @else
        New Transition
        @endif
    </h2>
    @if (Session::has('success'))
    <div class="border border-green-100 rounded-lg text-green-300 text-sm font-medium bg-green-50 mb-2 p-2">
        <p>{{Session::get('success')}} <a class="underline text-blue-400" href="{{route('workflows.edit', ['workflow'=>$transition->workflow->id])}}">Go to workflow</a></p>
    </div>
    @endif
    <div class="bg-white">
        <form id="transition" action="{{$id ? route('transitions.update', ['transition' => $id]) : route('transitions.store')}}" method="post">
            @if ($id)
            @method('patch')
            @endif
            @csrf
            <div class="mb-4">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-600">Name</label>
                <input type="text" name="name" value="{{old('name') ?: $transition->name}}" id="name" class="block w-full bg-gray-50 border border-{{$errors->has('name') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                @error('name')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div> 
            @if (!$id)
            <div class="mb-4">
                <label for="workflow" class="block mb-2 text-sm font-medium text-gray-600">Workflow</label>
                <select name="workflow_id" id="workflow" class="block w-full bg-gray-50 border border-{{$errors->has('workflow_id') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                    <option value=""></option>
                    @foreach ($workflows as $workflow)
                    <option value="{{$workflow->id}}">{{$workflow->name}}</option>
                    @endforeach
                </select>
                @error('type')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div>
            @endif
            <div class="mb-4">
                <label for="guard" class="block mb-2 text-sm font-medium text-gray-600">Guard</label>
                <input type="text" name="guard" value="{{old('guard') ?: $transition->guard}}" id="guard" class="block w-full bg-gray-50 border border-{{$errors->has('guard') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
                @error('guard')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div> 
            <div class="mb-4">
                <label for="metadata" class="block mb-2 text-sm font-medium text-gray-600">Metadata</label>
                <textarea name="metadata" id="metadata" class="block w-full bg-gray-50 border border-{{$errors->has('metadata') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">{{old('metadata') ?: ($transition->metadata ? json_encode($transition->metadata) : '')}}</textarea>
                @error('metadata')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div>
        </form>
        <div class="grid gap-4 mb-4 lg:grid-cols-2">
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-600">From</label>
                <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                    @foreach ($transition->from as $place)
                    <div class="inline-flex items-center max-w-min mb-2 mr-1 p-1 border rounded-full text-sm bg-white">
                        <a href="{{route('places.edit', ['place'=> $place->id])}}" rel="noopener noreferrer" class="mx-2">
                            {{$place->name}}
                        </a>
                        <form action="{{route('transitions.places.update', ['transition'=> $transition->id])}}" method="post">
                            @csrf
                            <input type="hidden" name="action" value="detach" />
                            <input type="hidden" name="from" value="{{$place->id}}" />
                            <button type="submit" class="bg-gray-100 border rounded-full p-1 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-red-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-300">
                                <span class="sr-only">Remove</span>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                    <form action="{{route('transitions.places.update', ['transition'=> $transition->id])}}" method="post">
                        @csrf
                        <input type="hidden" name="action" value="attach" />
                        <select name="from" id="from" class="bg-gray-50 border border-{{$errors->has('from') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2 mr-2">
                            <option value=""></option>
                            @foreach ($places as $place)
                            @if (!$transition->from->contains($place))
                            <option value="{{$place->id}}">{{$place->name}}</option>
                            @endif
                            @endforeach
                        </select>
                        <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Add</button>
                        @error('name')
                        <small class="block p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                        @enderror
                    </form>
                </div>
            </div>
            <div>
                <label class="block mb-2 text-sm font-medium text-gray-600">To</label>
                <div class="border rounded-lg p-4 bg-gray-50 text-gray-800">
                    @foreach ($transition->to as $place)
                    <div class="inline-flex items-center max-w-min mb-2 mr-1 p-1 border rounded-full text-sm bg-white">
                        <a href="{{route('places.edit', ['place'=> $place->id])}}" rel="noopener noreferrer" class="mx-2">
                            {{$place->name}}
                        </a>
                        <form action="{{route('transitions.places.update', ['transition'=> $transition->id])}}" method="post">
                            @csrf
                            <input type="hidden" name="action" value="detach" />
                            <input type="hidden" name="to" value="{{$place->id}}" />
                            <button type="submit" class="bg-gray-100 border rounded-full p-1 inline-flex items-center justify-center text-gray-400 hover:text-gray-500 hover:bg-red-300 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-300">
                                <span class="sr-only">Remove</span>
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                    @if (!$transition->workflow->singleState() || $transition->to->isEmpty())
                    <form action="{{route('transitions.places.update', ['transition'=> $transition->id])}}" method="post">
                        @csrf
                        <input type="hidden" name="action" value="attach" />
                        <select name="to" id="to" class="bg-gray-50 border border-{{$errors->has('from') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2 mr-2">
                            <option value=""></option>
                            @foreach ($places as $place)
                            @if (!$transition->to->contains($place))
                            <option value="{{$place->id}}">{{$place->name}}</option>
                            @endif
                            @endforeach
                        </select>
                        <button type="submit" class="text-white bg-gray-300 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Add</button>
                        @error('name')
                        <small class="block p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                        @enderror
                    </form>
                    @endif
                </div>
            </div>
        </div>
        <button form="transition" type="submit" class="text-white bg-blue-400 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Save</button>
    </div>
</div>
@endsection