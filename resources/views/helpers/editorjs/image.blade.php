<div class="@if($data['withBackground']) bg-secondary @endif text-center">
    <img src="{{ $data['file']['url'] }}" class="img-fluid @if($data['withBorder']) border rounded @endif @if(! $data['stretched']) w-75 @endif" title="{{ $data['caption'] ?? '' }}" alt="{{ $data['caption'] ?? '' }}" />
</div>
