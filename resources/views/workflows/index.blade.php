@extends('emzd_workflow::layout')

@section('main')
<div class="p-5">
<div class="flex justify-between items-center">
  <h2 class="text-gray-900 text-lg my-3">Workflows</h2>
  <a href="{{route('workflows.create')}}" class='border rounded-lg py-2 px-4 text-gray-800 bg-gray-100 hover:bg-blue-400'>New</a>
</div>
<div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
  <table
    class="w-full border-collapse bg-white text-left text-sm text-gray-600"
  >
    <thead class="bg-gray-100">
      <tr>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">#</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Name</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Type</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">
          Supported Class
        </th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">
          Marking Property
        </th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">
          Metadata
        </th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Active</th>
        <th scope="col" class="px-6 py-4 font-medium text-gray-900">Actions</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100 border-t border-gray-100">
      @foreach ($workflows as $workflow)
      <tr class="hover:bg-gray-50">
        <th class="px-6 py-4">{{$loop->index + 1}}</th>
        <td class="px-6 py-4">
          {{$workflow->name}}
        </td>
        <td class="px-6 py-4">{{$workflow->type}}</td>
        <td class="px-6 py-4">{{$workflow->supports}}</td>
        <td class="px-6 py-4">{{$workflow->marking_property}}</td>
        <td class="px-6 py-4">
          {{$workflow->metadata ? json_encode($workflow->metadata) : '{}'}}
        </td>
        <td class="px-6 py-4">
          <span
            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-semibold text-{{$color = $workflow->active ? 'green' : 'red'}}-600"
          >
            <span class="h-1.5 w-1.5 rounded-full bg-{{$color}}-600"></span>
            {{$workflow->active ? 'Yes' : 'No'}}
          </span>
        </td>
        <td class="px-6 py-4">
          <div class="flex justify-start gap-4">
            <a title="Edit" href="{{route('workflows.edit', ['workflow'=> $workflow->id])}}">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="h-5 w-5"
                x-tooltip="tooltip"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"
                />
              </svg>
            </a>
            <form action="{{route('workflows.destroy', ['workflow'=> $workflow->id])}}" method="post">
              @csrf
              @method('delete')
              <button type="submit" title="Delete" onclick="return window.confirm('Are you sure?');">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke-width="1.5"
                  stroke="currentColor"
                  class="h-5 w-5"
                  x-tooltip="tooltip"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"
                  />
                </svg>
              </button>
            </form>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
</div>
@endsection
