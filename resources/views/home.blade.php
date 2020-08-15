@extends('layouts.app')

@section('content')
<div id="blocPage">
    <div id="coinsTab">
        <table class="table table-inverse">
            <thead>
            <tr>
                <th>Coin</th>
                <th>Price</th>
                <th>24h</th>
            </tr>
            </thead>
            <tbody id="bodyData">
            @foreach($coinsName as $coin)
                    <tr>
                        <td>{{$coin->coin_name}}</td>
                        <td>{{$coinsPrice[$loop->index][0]}} €</td>

                        @if($coinsPrice[$loop->index][1] < 0)
                            <td id="negatif">
                        @else
                            <td id="positif">
                        @endif
                                {{$coinsPrice[$loop->index][1]}} %
                            </td>
                    </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <div id="line_top_x"></div>
    <div id="blocButtons">
    @foreach($coinsName as $coin)
        @if($coin->id !== 1)
            <input class="buttonCoin" type='button' value='{{$coin->coin_name}}'
                   id='fetch_{{$coin->id}}'>
        @endif
    @endforeach
    </div>
</div>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        var data;
        var chart;
        var options;
        google.charts.load('current', {'packages':['line']});
        google.charts.setOnLoadCallback(drawChart);
        var coinsGraphDisplay = [0, 1]; // 0 == date 1 == btc
        function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Bitcoin');
        data.addRows([
            @foreach($bitcoin as $btc)
                @foreach($btc->prices as $price)
                    ['{{date('j-F', strtotime($price->date_at))}}',
                    {{$price->price}}],
                @endforeach
            @endforeach
        ]);
        options = {
            width: 900,
            height: 500,
            axes: {
                x: {
                    0: {side: 'top'}
                }
            }
        };
        chart = new google.charts.Line(document.getElementById('line_top_x'));
        chart.draw(data, google.charts.Line.convertOptions(options));
        }

        $(document).ready(function() {
            @foreach($coinsName as $coin)
            $('#fetch_{{$coin->id}}').click(function () {
                fetchRecords({{$coin->id}});
            });
            @endforeach

            function fetchRecords(id){
                // id == bitcoin, stay in graph
                if(id === 1) {
                    return;
                }
                // if the coin is already in the graph, remove it
                if($.inArray(id, coinsGraphDisplay) !== -1) {
                    data.removeColumn($.inArray(id, coinsGraphDisplay));
                    chart.draw(data, google.charts.Line.convertOptions(options));
                    coinsGraphDisplay.splice(
                        $.inArray(id, coinsGraphDisplay), 1);
                } // if the coin is not in the graph, add it
                else {
                    $.ajax({
                        url: "/coinData/getCoinData/" + id,
                        type: "POST",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        cache: false,
                        dataType: 'json',
                        success: function (dataResult) {
                            var resultData = dataResult.data;
                            data.addColumn('number',
                                resultData[0]['coin_name']);
                            for (let i = 0; i < resultData[0]['prices']
                                .length; i++) {
                                data.setCell(i, coinsGraphDisplay.length,
                                    resultData[0]['prices'][i]['price']);
                            }
                            chart.draw(data, google.charts.Line.convertOptions(options));
                            coinsGraphDisplay.push(resultData[0]['id']);
                        }
                    });
                }
            }
        });
    </script>
@endsection
