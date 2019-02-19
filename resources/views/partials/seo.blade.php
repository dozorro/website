@if (isset($seo))
    @if(isset($seo->title))
        <title>{{ $seo->title }}</title>
    @endif
    @if(isset($seo->description))
        <meta name="description" content="{{ $seo->description }}" />
    @endif
    @if(isset($seo->keywords))
        <meta name="keywords" content="{{ $seo->keywords }}" />
    @endif
    @if(isset($seo->canonical) && $seo->canonical)
        <link href="{{$seo->canonical}}" rel="canonical" />
    @endif
    @if(!empty($seo->meta_tags))
        {!!$seo->meta_tags!!}
    @endif
    @if(isset($seo->og_title) && $seo->og_title)
        <meta property="og:title" content="{{ $seo->og_title }}" />
    @endif
    @if(isset($seo->og_url) && $seo->og_url)
        <meta property="og:url" content="{{ $seo->og_url }}" />
    @endif
    @if(isset($seo->og_image) && $seo->og_image)
        <meta property="og:image" content="{{ $seo->og_image }}" />
    @endif
    @if(isset($seo->og_description) && $seo->og_description)
        <meta property="og:description" content="{{ $seo->og_description }}" />
    @endif
    @if(!empty($seo->body_tags))
        @push('scripts')
            {!! $seo->body_tags !!}
        @endpush
    @endif
@endif