@extends('emzd_workflow::layout')

@section('main')
<div class="lg:w-2/4 mx-auto p-5">
    <h2 class="text-gray-900 text-lg my-3">
        @if ($id = $place->id)
        Edit Place
        @else
        New Place
        @endif
    </h2>
    @if (Session::has('success'))
    <div class="border border-green-100 rounded-lg text-green-300 text-sm font-medium bg-green-50 mb-2 p-2">
        <p>{{Session::get('success')}} <a class="underline text-blue-400" href="{{route('workflows.edit', ['workflow'=>$place->workflow->id])}}">Go to workflow</a></p>
    </div>
    @endif
    <div class="bg-white">
        <form action="{{$id ? route('places.update', ['place' => $id]) : route('places.store')}}" method="post">
            @if ($id)
            @method('patch')
            @endif
            @csrf
            <div class="mb-4">
                <label for="name" class="block mb-2 text-sm font-medium text-gray-600">Name</label>
                <input type="text" name="name" value="{{old('name') ?: $place->name}}" id="name" class="block w-full bg-gray-50 border border-{{$errors->has('name') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">
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
                @error('workflow_id')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div>
            @endif
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-600">Is Initial?</label>
                <div class="border rounded-lg p-4 bg-gray-50">
                    <div class="">
                        <input type="radio" name="initial" value="1" @checked($place->initial === 1) id="initial_1" />
                        <label for="initial_1" class="text-sm font-medium text-gray-600">Yes</label>
                    </div>
                    <div class="">
                        <input type="radio" name="initial" value="0" @checked($place->initial === 0) id="initial_0" />
                        <label for="initial_0" class="text-sm font-medium text-gray-600">No</label>
                    </div>
                </div>
            </div>
            <div class="mb-4">
                <label for="metadata" class="block mb-2 text-sm font-medium text-gray-600">Metadata</label>
                <textarea name="metadata" id="metadata" class="block w-full bg-gray-50 border border-{{$errors->has('metadata') ? 'red' : 'gray'}}-300 text-gray-800 text-sm rounded-lg focus:outline-none focus:ring-blue-300 focus:border-blue-300 p-2">{{old('metadata') ?: ($place->metadata ? json_encode($place->metadata) : '')}}</textarea>
                @error('metadata')
                <small class="p-1 text-red-400 text-sm font-medium">{{$message}}</small>
                @enderror
            </div>
            <button type="submit" class="text-white bg-blue-400 hover:bg-blue-500 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2 text-center">Save</button>
        </form>
    </div>
</div>
@endsection