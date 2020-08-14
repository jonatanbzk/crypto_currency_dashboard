@extends('layouts.app')

@section('content')
    <div>
        <table class="table table-inverse">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
            </tr>
            </thead>
            <tbody id="coins-list" name="coins-list">
            @foreach ($coins as $data)
                <tr id="todo{{$data->id}}">
                    <td>{{$data->id}}</td>
                    <td>{{$data->name}} [{{$data->abbreviation}}]</td>
                    <td>
                        @foreach ($data->prices as $price)
                            {{$price->date}} :
                            {{$price->price}} <br>
                        @endforeach
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <br>
        {{--
        {{var_dump($rep)}}
        <br>
        {{var_dump($rep['grin'])}}
        <br>
        {{var_dump($rep['grin']['eur'])}}
        --}}
    </div>

@endsection
