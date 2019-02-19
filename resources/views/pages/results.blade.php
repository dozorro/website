@if($error)
    {{$error}}
@else
    @if ($start<=Config::get('prozorro.page_limit'))

        <div class="result-all">{{t('form.resuts_found')}}: <span header-totals>{{number_format($total, 0, '', ' ')}}</span></div>
        {{--<div class="search-form-wr"><a class="search-form--save" href=""><i class="sprite-arrow-right"></i>Зберегти пошуковий запит</a></div>--}}

        {{--<div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="search-form--filter mob-hide">
                        <div class="result-all">{{t('form.resuts_found')}}: <span header-totals>{{number_format($total, 0, '', ' ')}}</span></div>

                        <ul class="nav navbar-nav inline-navbar search-form--filter--filter-records">
                            <li><a href="">Додані сьогодні</a> (?)</li>
                            <li><a href="">Уточнення</a> (?)</li>
                            <li><a href="">Подання пропозицій</a> (?)</li>
                            <li><a href="">Аукціон</a> (?)</li>
                            <li><a href="">Кваліфікація</a> (?)</li>
                            <li><a href="">Завершені</a> (?)</li>
                        </ul>

                    </div>
                </div>
                <div class="col-md-4">

                        <ul class="nav navbar-nav inline-navbar search-form--filter--show-type">
                            <li>Показати:</li>
                            <li><a href="" class="active">Детально</a></li>
                            <li><a href="">Списком</a></li>
                        </ul>
                        <div class="clearfix"></div>
                        <select>
                            <option>Спочатку новіші</option>
                            <option>Спочатку новіші</option>
                        </select>

                </div>
            </div>
        </div>--}}
    @endif

    @include('partials.result-'.$search_type.'-header')

    @foreach ($items as $item)
        @include('partials.result-'.$search_type)
    @endforeach
    
    @include('partials.result-'.$search_type.'-footer')

    @if($start<$total)
        <button class="show-more" data-start="{{$start}}">{{t('form.show_more')}}</button>
    @endif

@endif