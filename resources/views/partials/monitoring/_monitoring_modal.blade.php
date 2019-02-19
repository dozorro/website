<div data-form-modal="contract-docs" class="none">
    <div id="overlay" class="overlay2" data-form-modal="contract-docs"></div>
    <div class="modal_div show welcome-modal" data-form-modal="contract-docs" style="overflow: scroll;">
        <div class="modal_close"></div>
        <div class="content-holder">
            <h3>{{t('monitoring.modal_window.title')}}</h3>
            <div style="text-align: left;">
                <p>
                    @foreach($item->__documents as $document)
                        <a href="{{$document->url}}" target="_blank">{{$document->title}}</a><br>
                    @endforeach
                </p>
            </div>
        </div>
    </div>
</div>
