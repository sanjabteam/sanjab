<div class="card">
    <a href="{{ $data['link'] }}">
        <div class="card-body">
            <div class="row">
                @if(is_array($data['meta']) && !empty(array_get($data, 'meta.image.url')))
                    <img class="col-3 card-img-top" src="{{ $data['meta']['image']['url'] }}" alt="Card image cap">
                @endif
                <h5 class="col-9 card-title">{{ array_get($data, 'meta.title') }}</h5>
            </div>
            <p class="card-text">{{ array_get($data, 'meta.description') }}</p>
        </div>
    </a>
</div>
