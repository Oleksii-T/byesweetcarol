@foreach ($posts as $post)
    <article>
        <a href="{{route('posts.show', $post)}}">{{$post->title}}</a>
    </article>
@endforeach
