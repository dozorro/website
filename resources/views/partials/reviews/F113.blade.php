@if(!empty($review->json->supplierDutiesExecution))
    <div class="reviews__stars">
        <h3>Оцініть якість виконання постачальником своїх обов’язків:</h3>
        <ul class="tender-stars tender-stars--{{ $review->json->supplierDutiesExecution }}">
            <li></li><li></li><li></li><li></li><li></li>
        </ul>
    </div>
    @if(!empty($review->json->supplierDutiesExecutionComment))
        <div class="reviews__body">
            <div class="reviews__text">
                <p>{!! auto_format($review->json->supplierDutiesExecutionComment) !!}</p>
            </div>
        </div>
    @endif
@endif
