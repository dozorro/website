@extends('layouts.app')

@section('content')

    @include('partials/profile/_header')

    <div class="bg_grey block_statistic">
        <div class="container">
            <div class="bg_white">
                <form class="block_profile_tabs" id="profile-spinner">

                        <ul class="nav inline-layout" role="tablist">
                            @if(in_array($profile->partyRole, ['procuringEntity','both']))
                            <li @if($roleType != 'role2'){{'class=active'}}@endif>
                                <a data-href="{{ $role1Href }}" data-id="role1" href="#role1" data-toggle="tab">{{ t('profile.header.procuringEntityMenu') }}</a>
                            </li>
                            @endif
                            @if(in_array($profile->partyRole, ['tenderer','both']))
                            <li @if(!in_array($profile->partyRole, ['procuringEntity','both']) || $roleType == 'role2'){{'class=active'}}@endif>
                                <a data-href="{{ $role2Href }}" data-id="role2" href="#role2" data-toggle="tab">{{ t('profile.header.tendererMenu') }}</a>
                            </li>
                            @endif
                        </ul>

						<ul class="list_templates inline-layout metricsTplNew">
                            @foreach($groupTemplates as $role => $object)
                                @if($roleType == $role)
                                    @if(!$object->templates->isEmpty())
                                        @foreach($object->templates as $tpl)
                                            <li @if($object->selectedTpl == $tpl->id){{'class=active'}}@endif>
                                                <a data-role="{{ $role }}" data-tpl="{{ $tpl->id }}" href="#">{{ $tpl->translate()->name }}</a>
                                            </li>
                                        @endforeach
                                    @endif
                                    @if(!empty($customTpl) && $user)
                                    <li @if($object->selectedTpl == 'custom'){{'class=active'}}@endif>
                                        <a data-type="custom" data-role="{{ $role }}" data-tpl="{{ $customTpl }}" href="#">Персональний</a>
                                    </li>
                                    @endif
                                    <li @if($tplType == 'all' || $object->selectedTpl == 'all'){{'class=active'}}@endif>
                                        <a data-type="all" data-role="{{ $role }}" data-tpl="all" href="#">Всі показники</a>
                                    </li>
                                @endif
                            @endforeach
						</ul>

                        <div class="tab-content" style="display:block;">
                            @foreach($groupTemplates as $role => $object)
                                <div class="tab-pane
                                @if($roleType == $role)
                                    {{'active'}}
                                @endif
                                " id="{{ $role }}">
                                    <div class="form-holder">
                                        <select class="metricsTpl">
                                            @if(!$object->templates->isEmpty())
                                                @foreach($object->templates as $tpl)
                                                    <option @if($object->selectedTpl == $tpl->id){{'selected'}}@endif value="{{ $tpl->id }}">{{ $tpl->translate()->name }}</option>
                                                @endforeach
                                            @endif
                                            @if(!empty($customTpl) && $user)
                                            <option data-type="custom" value="{{ $customTpl }}" @if($object->selectedTpl == 'custom'){{'selected'}}@endif>{{ t('profile.template.custom') }}</option>
                                            @endif
                                            <option data-type="all" value="all" @if($tplType == 'all' || $object->selectedTpl == 'all'){{'selected'}}@endif>{{ t('profile.template.allMetrics') }}</option>
                                        </select>
                                    </div>
                            @if($tplType == 'all')
                                @include('partials.profile._metrics')
                            @else
                                @if(count($object->groupMetricsData) > 0)
                                    @foreach($object->groupMetricsData as $row => $metricsData)
                                            <div class="tender-items groupMetricsData" data-row="{{ $row }}">
                                                <div class="inline-layout list_item_statistic">
                                                    @foreach($metricsData as $index => $_data)
                                                        @include('partials/profile/item')
                                                    @endforeach
                                                </div>
                                            </div>
                                    @endforeach
                                @else
                                    <div class="tender-items groupMetricsData" data-row="1">
                                        <div class="inline-layout list_item_statistic">
                                            @foreach(range(1, 5) as $index)
                                                @include('partials/profile/item')
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="filter_tender row show-feedback-row" style="padding: 0;display:none;">
                                    <div class="form-group inline-layout" style="text-align: center;">
                                        <button class="profile-save">{{ t('profile.custom.save') }}</button>
                                    </div>
                                </div>

                                @include('partials.profile._static')

                                @if(!empty($object->blocks))
                                    <?php
                                        $closeDiv = true;
                                    ?>

                                    @foreach($object->blocks as $k => $block)
                                        @if($closeDiv)
                                            <div class="row ">
                                            <?php
                                                $closeDiv = false;
                                            ?>
                                        @endif

                                        @if($block->alias == 'table')
                                            @include('partials.longread.' . $block->alias, [
                                                'data' => $block->data['data'],
                                                'results' => $block->data['results'],
                                                'total' => $block->data['total'],
                                                'single' => false,
                                            ])
                                        @else
                                            @include('partials.longread.' . $block->alias, [
                                                'data' => $block->value
                                            ])
                                        @endif

                                        @if(empty($object->blocks[($k+1)]) || $object->blocks[($k+1)]->value->position == 'full')
                                            </div>
                                            <?php
                                                $closeDiv = true;
                                            ?>
                                        @endif
                                    @endforeach
                                @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @include('partials/profile/handler', [
        'saveTemplateRoute' => route('template_save'),
        'saveCustomTemplateRoute' => route('template_save_custom'),
        'profileRoute' => route('page.profile_by_id', ['scheme'=>$scheme]),
    ])
@endpush
