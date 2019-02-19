<div class="item-container">
    <div class="item @if(!empty($metricsData[$index])){{'item-dropdown'}}@endif" data-column="{{ $index }}">
        <!-- <span class="icon settings">
            <img src="/assets/images/item-dropdown.svg">
        </span> -->
        <div class="form-holder" style="top: 10px; position: absolute; z-index: 999; left: 0; right: 0px;">
            @if(!$object->metrics->isEmpty())
            <select size="1" name="metrics" class="metrics">
                <option></option>
                @foreach($object->metrics as $metric)
                    @if(!empty($metric->mvp_data))
                        <option
                            data-third-label="{{ t(@$metric->third_metric->label) }}"
                            data-second-label="{{ t(@$metric->second_metric->label) }}"
                            data-third="@if(!empty($metric->mvp_data_third) && !empty($metric->third_metric)){{ $metric->mvp_data_third->showMetricValue($metric->third_metric) }}@endif"
                            data-second="@if(!empty($metric->mvp_data_second) && !empty($metric->second_metric)){{ $metric->mvp_data_second->showMetricValue($metric->second_metric) }}@endif"
                            data-value="@if(!empty($metric->mvp_data)){{ $metric->mvp_data->showMetricValue($metric) }}@endif"
                            value="{{ $metric->code }}"
                            @if(!empty($metricsData[$index]->code) && $metricsData[$index]->code == $metric->code){{'selected'}}@endif
                        >
                            {{ t($metric->label) }}
                        </option>
                    @endif
                @endforeach
            </select>
            @endif
        </div>
        @if(!empty($metricsData[$index]) && !empty($metricsData[$index]->mvp_data))
            <div class="number text-center" data-code="{{ $metricsData[$index]->code }}">
                {!! $metricsData[$index]->mvp_data->showMetricValue($metricsData[$index]) !!}
                @if(!empty($metricsData[$index]->suffic))
                <span class="metric-value-preffix">{{ $metricsData[$index]->suffic }}</span>
                @endif
            </div>
            <div class="title">{{ t($metricsData[$index]->label) }}</div>
            <div class="additional-metrics">
                <div class="metric second-metric">
                    <span class="metric-title">{{ t(@$metricsData[$index]->second_metric->label) }}</span>
                    <span class="metric-value">@if(!empty($metricsData[$index]->mvp_data_second) && !empty($metricsData[$index]->second_metric)){!! $metricsData[$index]->mvp_data_second->showMetricValue($metricsData[$index]->second_metric) !!}@endif</span>
                </div>
                <div class="metric third-metric">
                    <span class="metric-title">{{ t(@$metricsData[$index]->third_metric->label) }}</span>
                    <span class="metric-value">@if(!empty($metricsData[$index]->mvp_data_third) && !empty($metricsData[$index]->third_metric)){!! $metricsData[$index]->mvp_data_third->showMetricValue($metricsData[$index]->third_metric) !!}@endif</span>
                </div>
            </div>
        @else
            <div class="number text-center">-</div>
            @if(!empty($metricsData[$index]->label))
            <div class="title">{{ t($metricsData[$index]->label) }}</div>
            @else
            <div class="title"></div>
            @endif
            <div class="additional-metrics">
                <div class="metric second-metric">
                    <span class="metric-title"></span>
                    <span class="metric-value"></span>
                </div>
                <div class="metric third-metric">
                    <span class="metric-title"></span>
                    <span class="metric-value"></span>
                </div>
            </div>
        @endif

    </div>
</div>