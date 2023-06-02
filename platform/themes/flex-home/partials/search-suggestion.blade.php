<ul>
    @foreach($items as $item)
        <li>
            <p><a href="{{ $item->url }}">{{ $item->name }}</a></p>
            <p>{{ $item->city->name ? $item->city->name . ', ' : null }}{{ $item->state->name }}</p>
        </li>
    @endforeach
</ul>
