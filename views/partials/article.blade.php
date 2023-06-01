<article class="c-article c-article--readable-width s-article u-clearfix" id="article" {!! !empty($postLanguage) ? 'lang="' . $postLanguage . '"' : '' !!}>

    <!-- Title -->
    @section('article.title.before')@show
    @if ($postTitleFiltered || $callToActionItems['floating'])
        @group([
            'justifyContent' => 'space-between',
            'classList' => ['u-padding__top--4']
        ])
            @if ($postTitleFiltered)
                @typography(['element' => 'h1', 'variant' => 'h1'])
                    {!! $postTitleFiltered !!}
                @endtypography
            @endif
            @if ($callToActionItems['floating'])
                @icon($callToActionItems['floating'])
                @endicon
            @endif
        @endgroup
    @endif
    @section('article.title.after')@show

    <!-- Blog style author signature -->
    @if (!$postTypeDetails->hierarchical && $isBlogStyle)
        @section('article.signature.after')@show
        @signature([
            'author' => $signature->name,
            'avatar_size' => 'sm',
            'avatar' => $signature->avatar,
            'authorRole' => $signature->role,
            'link' => $signature->link,
            'published' => $signature->published,
            'updated' => $signature->updated,
            'updatedLabel' => $publishTranslations->updated,
            'publishedLabel' => $publishTranslations->publish
        ])
        @endsignature
        @section('article.signature.after')@show
    @endif

    <!-- Content -->
    @section('article.content.before')@show
    @typography([
        'classList' => ['o-container']
    ])
        {!! $postContentFiltered !!}
    @endtypography
    @section('article.content.after')@show

    <!-- Terms -->
    @section('article.terms.before')@show
    @if (isset($terms))
        @tags(['tags' => $terms])
        @endtags
    @endif
    @section('article.terms.after')@show

</article>
