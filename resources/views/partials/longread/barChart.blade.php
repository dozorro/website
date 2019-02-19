@if(!empty($data->is_enabled))
    @if($data->position == 'left' || $data->position == 'right')
    <div class="col-md-6">
    @endif

    <div class="block_table">
        @if(!empty($data->title))
            <div class="title-container">
                <h3>{{ $data->title }}</h3>
            </div>
        @endif
        <canvas id="barChartProfile-{{ $data->code }}" data-js="barChart" data-datasets='{!! json_encode($data->datasets) !!}' data-labels='{!! json_encode($data->labels) !!}' data-code="{{ $data->code }}"></canvas>

        @if($data->position == 'full')
            <div class="clearfix"></div>
        @endif
    </div>

    @if($data->position == 'left' || $data->position == 'right')
    </div>
    @endif
@endif