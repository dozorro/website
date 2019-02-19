@if(!$block->data->ngos->isEmpty() || !$block->data->customers->isEmpty() || !$block->data->monitoring->isEmpty())
    <div class="c-b">
        <div class="container">

            <div class="filter_go">
                <h4 class="js-filter-go">{{t('tenders.ngo_customers_title')}}</h4>
                <h4 class="js-filter-go mobile">{{t('tenders.ngo.title')}}</h4>
                <form>
                    <div class="list_radio inline-layout">
                        <div class="form-holder radio">
                            <input checked type="radio" value="" name="radio" id="radio1" data-form="ngos">
                            <label for="radio1">{{t('tenders.ngo_tab')}}</label>
                        </div>
                        <div class="form-holder radio">
                            <input type="radio" value="" name="radio" id="radio5" data-form="customers">
                            <label for="radio5">{{ t('tenders.customers_tab') }}</label>
                        </div>
                        @if(!$block->data->monitoring->isEmpty())
                        <div class="form-holder radio">
                            <input type="radio" value="" name="radio" id="monitoring-tab" data-form="monitoring">
                            <label for="monitoring-tab">{{ t('tenders.monitoring_tab') }}</label>
                        </div>
                        @endif
                    </div>
                    <br>
                </form>
            </div>

            <br>

            @if(!$block->data->ngos->isEmpty())
            <div class="block_header_go_short none" data-ngos>
                @foreach($block->data->ngos as $ngo)
                    @include('partials.ngo_info', ['profile_link' => true])
                @endforeach
            </div>
            @endif
            @if(!$block->data->customers->isEmpty())
                <div class="block_header_go_short none" data-customers>
                    @foreach($block->data->customers as $customer)
                        @include('partials.customer_info')
                    @endforeach
                </div>
            @endif
            @if(!$block->data->monitoring->isEmpty())
                <div class="block_header_go_short none" data-monitoring>
                    @foreach($block->data->monitoring as $monitor)
                        @include('partials.monitoring.monitoring_info', ['profile_link' => true])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

@push('scripts')
<script>
    $(document).ready(function(){

        $('[data-ngos]').show();

        $('[data-form]').on('click', function() {
            $('[data-ngos]').hide();
            $('[data-customers]').hide();
            $('[data-monitoring]').hide();
            $('[data-'+$(this).data('form')+']').show();
        });
    });
</script>
@endpush