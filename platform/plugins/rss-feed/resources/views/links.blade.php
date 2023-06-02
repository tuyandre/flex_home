@foreach($feeds as $name => $feed)
    <link rel="alternate" type="{{ \Botble\RssFeed\Helpers\FeedContentType::forLink($feed['format'] ?? 'atom') }}" href="{{ route("feeds.{$name}") }}" title="{{ $feed['title'] }}">
@endforeach
