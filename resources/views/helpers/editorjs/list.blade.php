@if ($data['style'] == 'unordered')
<ul>
@else
<ol>
@endif

@foreach ($data['items'] as $item)
    <li>{{ $item }}</li>
@endforeach

@if ($data['style'] == 'unordered')
</ol>
@else
</ol>
@endif

