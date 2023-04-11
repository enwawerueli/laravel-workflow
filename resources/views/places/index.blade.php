@extends('emzd_workflow::layout')

@section('main')
<h2>List of places</h2>
<table class="table-auto">
    <thead>
      <tr>
        <th>Name</th>
        <th>Initial Place</th>
        <th>Metadata</th>
      </tr>
    </thead>
    <tbody>
        @foreach ($places as $place)
        <tr>
          <td><a href="{{route('places.edit', ['place'=> $place->id])}}">{{$place->name}}</a></td>
          <td>{{$place->initial ? 'Yes' : 'No'}}</td>
          <td>{{$place->metadata ? json_encode($place->metadata) : '{}'}}</td>
        </tr>
        @endforeach
    </tbody>
  </table>
@endsection