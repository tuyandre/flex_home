<ul>
    @foreach($projects as $item)
        <li>
            <a href="{{ $item->url }}" data-name="{{ $item->name }}" data-id="{{ $item->id }}">{{ $item->name }}</a>
        </li>
    @endforeach
</ul>
