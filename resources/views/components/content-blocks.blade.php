@foreach ($blocks as $block)
    @php
        $items = $block->items->sortBy('order');
        $hasGallery = $items->where('type', \App\Enums\BlockItemType::IMAGE_GALLERY)->count();
    @endphp
    <section class="section {{$hasGallery ? 'screens' : 'article'}} cblock" id="{{$block->ident}}">
        @foreach ($items as $item)
            @switch($item->type->value)
                @case(\App\Enums\BlockItemType::TITLE_H2->value)
                    <h2>
                        @if ($type??false)
                            <span>
                        @endif
                        {{$item->value_simple}}
                        @if ($type??false)
                            </span>
                        @endif
                    </h2>
                    @break
                @case(\App\Enums\BlockItemType::TITLE_H3->value)
                    <h3>{{$item->value_simple}}</h3>
                    @break
                @case(\App\Enums\BlockItemType::TITLE_H4->value)
                    <h4>{{$item->value_simple}}</h4>
                    @break
                @case(\App\Enums\BlockItemType::TEXT->value)
                    {!!$item->value_simple!!}
                    @break
                @case(\App\Enums\BlockItemType::IMAGE->value)
                    <div class="image article__image">
                        <img src="{{$item->file()->url}}" alt="{{$item->file()->alt}}" title="{{$item->file()->title}}" class="lazyload preview-image"/>
                    </div>
                    @break
                @case(\App\Enums\BlockItemType::IMAGE_SMALL->value)
                    <div class="article__image_small">
                        <img src="{{$item->file()->url}}" alt="{{$item->file()->alt}}" title="{{$item->file()->title}}" class="lazyload" />
                    </div>
                    @break
                @case(\App\Enums\BlockItemType::IMAGE_TITLE->value)
                    <h4>
                        <img src="{{$item->file()->url}}" class="ls-is-cached lazyloaded" alt="{{$item->file()->alt}}">
                        {{$item->value['title']}}
                    </h4>
                    @break
                @case(\App\Enums\BlockItemType::IMAGE_TEXT->value)
                    <div class="desc">
                        <div>
                            <img src="{{$item->file()->url}}" class="ls-is-cached lazyloaded" alt="{{$item->file()->alt}}">
                            <div>
                                {!!$item->value['text']!!}
                            </div>
                        </div>
                    </div>
                    @break
                @case('youtube')
                    <div class="article__video">
                        {!!$item->value_simple!!}
                    </div>
                    @break
                @case(\App\Enums\BlockItemType::CARDS->value)
                    <ul class="type-list mission__list">
                        @foreach ($item->value['cards'] as $card)
                            <li class="type-item">
                                @if ($card['image']??false)
                                    <div class="type-item__image">
                                        <img src="{{$card['image']['url']}}" alt="{{$card['image']['alt']}}" title="{{$card['image']['title']}}" />
                                    </div>
                                @endif
                                <div class="type-item__title">{{$card['title']}}</div>
                                <p class="type-item__text">{{$card['text']}}</p>
                            </li>
                        @endforeach
                    </ul>
                    @break
                @case(\App\Enums\BlockItemType::IMAGE_GALLERY->value)
                    @if (count($item->value_simple) > 2)
                        <div class="screens-slider {{$block->blockable_id == 389 ? 'no-breakpoints' : ''}}">
                            @foreach ($item->value_simple as $image)
                                <div class="screens-slider__item">
                                    <a href="{{$image['url']}}" data-fancybox="group" data-group="{{$item->id}}">
                                        <img src="{{$image['url']}}" alt="{{$image['alt']}}" title="{{$image['title']}}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="screens-slider-two">
                            @foreach ($item->value_simple as $image)
                                <div class="screens-slider__item">
                                    <a href="{{$image['url']}}" data-fancybox="group" data-group="item-{{$item->id}}">
                                        <img src="{{$image['url']}}" alt="{{$image['alt']}}" title="{{$image['title']}}">
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    @break
            @endswitch
        @endforeach
    </section>
@endforeach
