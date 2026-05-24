@props(["title", "description" => "", "imageUrl" => "", "pageUrl" => ""])
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="music.song">
<meta property="og:url" content="{{ $pageUrl ?: request()->url() }}">
@if ($imageUrl)
    <meta property="og:image" content="{{ $imageUrl }}">
    <meta name="twitter:image" content="{{ $imageUrl }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
