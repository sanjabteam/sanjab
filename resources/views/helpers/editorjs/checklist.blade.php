<ul class="list-group">
    @foreach($data['items'] as $item)
        <li class="list-group-item">@if($item['checked']) ✅ @else ❌ @endif{{ $item['text'] }}</li>
    @endforeach
</ul>
