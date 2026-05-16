<x-post-cards :posts="$posts" />

@php
    $includeQueryParams = isset($includeQueryParams) ? $includeQueryParams : [];
@endphp

{!!$posts->links('vendor.pagination.model-posts', compact('model', 'includeQueryParams'))!!}
