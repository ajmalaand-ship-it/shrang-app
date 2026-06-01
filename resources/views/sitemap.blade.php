<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

    <url>
        <loc>{{ config('app.url') }}</loc>
        <changefreq>weekly</changefreq>
        <priority>1.0</priority>
    </url>

    <url>
        <loc>{{ config('app.url') }}/discover</loc>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>

    @foreach($clips as $clip)
    <url>
        <loc>{{ config('app.url') }}/player/{{ $clip->slug }}</loc>
        <lastmod>{{ $clip->updated_at->toAtomString() }}</lastmod>
        <changefreq>monthly</changefreq>
        <priority>0.6</priority>
    </url>
    @endforeach

</urlset>
