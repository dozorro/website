<div class="col-md-6">
    @if(!empty($data->title))
        <h3>{{ $data->title }}</h3>
    @endif
    <canvas id="myChartProfileRadar"></canvas>
</div>

@push('scripts')
<script type="text/javascript">

    var data = {
        datasets: [{
            label: "Student A",
            backgroundColor: "rgba(200,0,0,0.2)",
            data: [65, 75, 70, 80, 60, 80]
        }, {
            label: "Student B",
            backgroundColor: "rgba(0,0,200,0.2)",
            data: [54, 65, 60, 70, 70, 75]
        }],

        // These labels appear in the legend and in the tooltips when hovering different arcs
        labels: ["English", "Maths", "Physics", "Chemistry", "Biology", "History"],
    };

    $(function () {
        var ctx = $("#myChartProfileRadar");
        var myPieChart = new Chart(ctx,{
            type: 'radar',
            data: data,
            options: {cutoutPercentage:0}
        });
    });

</script>
@endpush
