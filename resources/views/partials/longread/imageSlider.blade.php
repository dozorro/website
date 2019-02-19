@if (!empty($data->images))
    <div class="c-main-slider">
        <div class="c-main-slider__slider jsMainSlider" data-js="imageSlider" data-autoplay="{{ !empty($data->is_autoplay) ? $data->is_autoplay : false }}">
            @foreach($data->images as $image)
                <div class="c-main-slider__slide" style="background-image: url('{{ \App\Helpers::getStoragePath($image->disk_name) }}');">
                    <div class="c-main-slider__slide-bgcolor c-main-slider__slide-bgcolor--opacity-05">
                        <div class="container">
                            <div class="c-main-slider__table">
                                <div class="c-main-slider__cell">
                                    @if(!empty($image->title))
                                        <h1>{{ $image->title }}</h1>
                                    @endif
                                    @if(!empty($image->description))                                    
                                        <p>{{ $image->description }}</p>
                                    @endif
                                    @if(!empty($image->button_title))
                                        <div class="c-main-slider__link-wrap">
                                            <a href="#" class="c-main-slider__link">{{t('longreads.go_to')}}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
