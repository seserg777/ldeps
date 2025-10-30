<div class="rp-grid">
    @foreach(($products ?? []) as $p)
        <a class="rp-card" href="{{ $p['url'] }}">
            <div class="rp-imgwrap">
                <img class="rp-img" src="{{ $p['image'] }}" alt="{{ $p['name'] }}" loading="lazy">
            </div>
            <div class="rp-title" title="{{ $p['name'] }}">{{ $p['name'] }}</div>
        </a>
    @endforeach
</div>

