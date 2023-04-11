@extends('emzd_workflow::layout')

@section('main')
<h2>List of transitions</h2>
<table class="table-auto">
    <thead>
      <tr>
        <th>Name</th>
        <th>Guard</th>
        <th>Metadata</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($transitions as $transition)
        <tr>
          <td><a href="{{route('transitions.edit', ['transition'=> $transition->id])}}">{{$transition->name}}</a></td>
          <td>{{$transition->guard}}</td>
          <td>{{$transition->metadata ? json_encode($transition->metadata) : '{}'}}</td>
        </tr>
        @endforeach
    </tbody>
  </table>
@endsection