@if ($description)
    @collection([
        'unbox' => true,
        'classList' => ['u-padding--2', 'u-margin__top--2', 'u-border']
    ])
        {!! $description !!}
    @endcollection
@endif
