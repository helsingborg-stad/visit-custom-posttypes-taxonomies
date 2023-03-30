@extends('templates.single')

@section('hero-top-sidebar')
    @if (!empty($featuredImage->src[0]))
        @hero([
            'image' => $featuredImage->src[0]
        ])
        @endhero
        @include('partials.navigation.fixed')
    @endif
@stop
