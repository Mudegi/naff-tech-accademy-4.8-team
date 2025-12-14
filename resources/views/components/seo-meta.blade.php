{{-- SEO Meta Tags Component --}}
@props([
    'title' => config('app.name'),
    'description' => 'Quality education platform for Ugandan students',
    'keywords' => 'Uganda education, online learning, academic videos',
    'image' => asset('images/og-image.jpg'),
    'url' => url()->current(),
    'type' => 'website',
    'author' => 'Naf Academy',
    'robots' => 'index, follow',
    'canonical' => url()->current(),
    'schema' => null
])

<!-- Primary Meta Tags -->
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="{{ $author }}">
<meta name="robots" content="{{ $robots }}">

<!-- Canonical URL -->
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="{{ config('app.name') }}">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:url" content="{{ $url }}">
<meta property="twitter:title" content="{{ $title }}">
<meta property="twitter:description" content="{{ $description }}">
<meta property="twitter:image" content="{{ $image }}">

<!-- Additional Meta Tags -->
<meta name="language" content="English">
<meta name="revisit-after" content="7 days">
<meta name="geo.region" content="UG">
<meta name="geo.placename" content="Uganda">

@if($schema)
<!-- Structured Data (Schema.org) -->
<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
@endif

{{ $slot }}

