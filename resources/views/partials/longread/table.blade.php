@if(!empty($data))
@if(isset($single) && !$single && ($data->position == 'left' || $data->position == 'right'))
    <div class="col-md-6">
        <div class="block_table">
            <div class="title-container">
                @if(!empty($data->title))
                <h3>{{ $data->title }}</h3>
                @endif
                @if(!empty($data->table_subtitles))
                <div class="inline-layout show-query">
                    <a href="{{ route('page.profile_by_id', ['scheme'=>$scheme]) }}" data-table="{{ $data->code }}">{{ t('profile.sections.tables.all') }}</a>
                    @foreach($data->table_subtitles as $sk => $subtitle)
                        @if(!empty($subtitle->table_subtitle))
                        :: <a style="display:inline-block;" href="{{ route('page.profile_by_id', ['scheme'=>$scheme]) }}" data-table="{{ $data->code }}" data-q="{{ $sk }}">{{ $subtitle->table_subtitle }}</a>
                        @endif
                    @endforeach
                </div>
                <br>
                @endif
            </div>
            @include('partials.longread._blocks._table', ['data' => $data,'single'=>$single])

            @if($total > count($results))
                <div class="text-center" style="padding-top: 20px;">
                    <a target="_blank" href="{{ route('page.profile.table', ['scheme'=>$scheme,'code'=>$data->code,'setting_id'=>$object->setting->id]) }}" class="link_pagination">{{ t('profile.sections.tables.buttonMore') }}</a>
                </div>
            @endif
        </div>
    </div>
@else

    @if(!isset($single))
        <div class="bg_grey block_statistic" style="padding: 10px 0;">
            <div class="container">
                <div class="bg_white">
    @endif

<div class="block_table">
    <div class="title-container" style="display: block;">

        @if(!empty($data->title))
            <h3 style="display: inline-block;">{{ $data->title }}</h3>
        @endif

        @if(!empty($data->table_subtitles))
            <div style="vertical-align: text-bottom;margin-bottom: 0px;" class="inline-layout show-query">
                <a href="{{ route('page.profile_by_id', ['scheme'=>$scheme]) }}" data-table="{{ $data->code }}">{{ t('profile.sections.tables.all') }}</a>
                @foreach($data->table_subtitles as $sk => $subtitle)
                    @if(!empty($subtitle->table_subtitle))
                        :: <a style="display:inline-block;" href="{{ route('page.profile_by_id', ['scheme'=>$scheme]) }}" data-table="{{ $data->code }}" data-q="{{ $sk }}">{{ $subtitle->table_subtitle }}</a>
                    @endif
                @endforeach
            </div>
        @endif

        @if(isset($single))
        <div class="inline-layout export-block">
            @if($user && $single))
                <div class="inline-layout">
                    <a href="{{ \URL::current() }}/export" class="export"></a>
                </div>
            @endif
        </div>
        @endif
    </div>

    @if(isset($single))
        @include('partials.longread._blocks._table', ['data' => $data,'single'=>$single])
    @elseif(!isset($single))
        @include('partials.longread._blocks._table', ['data' => $data, 'results' =>$data->results, 'single'=>true])
    @endif

    @if(isset($single) && !$single)
        @if($total > count($results))
            <div class="text-center" style="padding-top: 20px;">
                <a target="_blank" href="{{ route('page.profile.table', ['scheme'=>$scheme,'code'=>$data->code,'setting_id'=>$object->setting->id]) }}" class="link_pagination">{{ t('profile.sections.tables.buttonMore') }}</a>
            </div>
        @endif
    @endif

    @if(isset($single))
        @if($object->setting && $object->setting->is_export)
            <div class="block_download pull-right">
                <a href="#" class="link_download">{{ t('table.download') }}</a>
            </div>
        @endif
    @endif

    <div class="clearfix"></div>
</div>

    @if(!isset($single))
                </div>
            </div>
        </div>
    @endif
@endif
<style type="text/css">
    .show-query a {
        margin-left: 10px;
    }
    .show-query {
        display: inline-block;
        clear: both;
        vertical-align: sub;
        margin-top: 20px;
        margin-bottom: 40px;
    }
    .show-query .active,
    .show-query .active:focus {
        color:black;
        cursor: default;
        text-decoration: none;
    }
</style>
@endif