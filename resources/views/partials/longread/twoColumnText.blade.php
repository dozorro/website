@if(!empty($data->two_column_title) || !empty($data->first_column) || !empty($data->second_column))
    <div class="c-twoc">
        <div class="container c-twoc__container">
            <div class="row">
                @if(!empty($data->text_title))
                    <div class="col-md-12">
                        <h2>{{ $data->two_column_title }}</h2>
                    </div>
                @endif
                <div class="col-md-6 c-twoc__border-right">
                    <div class="c-twoc__col c-twoc__col--left">
                        {!! $data->first_column !!}
                    </div>
                </div>
                <div class="col-md-6 c-twoc__border-left">
                    <div class="c-twoc__col c-twoc__col--right">
                        {!! $data->second_column !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif