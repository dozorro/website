@extends('layouts.app')

@section('content')

<div class="c-b">
    <div class="container">
        <div class="filter_go">
            <h4 class="js-filter-go">{{t('ngo.reviews.stats_title')}}</h4>
            <h4 class="js-filter-go mobile">{{t('ngo.reviews.stats_title')}}</h4>
            <div style="max-width: 700px;margin: 0 auto;">
            <canvas id="myChart" width="400" height="400"></canvas>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script type="text/javascript">

    var data = {
        datasets: [{
            data: [{{ $reviews[\App\Models\NgoReview::REVIEW_STATUS1] }}, {{ $reviews[\App\Models\NgoReview::REVIEW_STATUS2] }}, {{ $reviews[\App\Models\NgoReview::REVIEW_STATUS3] }}],
            backgroundColor: [
                '#ff6384',
                '#36a2eb',
                '#cc65fe',
            ]
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: [
            '{{ $statuses[1] }}',
            '{{ $statuses[2] }}',
            '{{ $statuses[3] }}',
        ],
    };

    $(function () {
        var ctx = $("#myChart");
        var myPieChart = new Chart(ctx,{
            type: 'pie',
            data: data,
            options: {cutoutPercentage:0}
        });
    });

</script>
@endpush