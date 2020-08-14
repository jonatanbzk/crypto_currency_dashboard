@extends('layouts.app')

@section('content')

    <div id="line_top_x"></div>

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
    </div>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {

            /*
                ['Product Id', 'Sales', 'Quantity'],

                @php
            /*
                    foreach($products as $product) {
                        echo "['".$product->id."', ".$product->sales.", ".$product->quantity."],";
                    }
 */
                @endphp
            */


            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'BTC');

            data.addRows([
                    @php
                        foreach ($coins as $data) {
                            foreach($data->prices as $price) {
                                //$day = date('', strtotime($price->date));
                                echo "['".$price->date."', ".$price->price."],";
                            }
                        }
                    @endphp

            ]);
            var options = {
                chart: {
                    title: 'Box Office Earnings in First Two Weeks of Opening',
                    subtitle: 'in millions of dollars (USD)'
                },
                width: 900,
                height: 500,
                axes: {
                    x: {
                        0: {side: 'top'}
                    }
                }
            };
            var chart = new google.charts.Line(document.getElementById('line_top_x'));

            chart.draw(data, google.charts.Line.convertOptions(options));
        }
    </script>


@endsection
