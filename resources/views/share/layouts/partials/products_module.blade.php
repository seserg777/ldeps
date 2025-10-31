@php
  $type = $type ?? 'random';
  $limit = (int)($limit ?? 6);

  // Server-side load via internal request to API
  $req = Illuminate\Http\Request::create('/api/products', 'GET', [ 'per_page' => max(1,$limit) ]);
  /** @var Illuminate\Http\JsonResponse $json */
  $json = app(\App\Http\Controllers\Web\ProductController::class)->getProducts($req);
  $data = $json->getData(true);
  $items = [];
  if (isset($data['products']) && is_array($data['products'])) {
      $items = $data['products'];
  } elseif (isset($data['data']) && is_array($data['data'])) {
      $items = $data['data'];
  } elseif (!empty($data['categories']) && is_array($data['categories'])) {
      foreach ($data['categories'] as $cat) {
          if (!empty($cat['products']) && is_array($cat['products'])) {
              foreach ($cat['products'] as $p) { $items[] = $p; }
          }
      }
  }
  if ($type === 'random' && !empty($items)) { shuffle($items); }
  $items = array_slice($items, 0, max(1,$limit));
@endphp

<section class="products-module pg">
  <div class="pg-grid">
    @foreach ($items as $p)
      @php
        $name = $p['name'] ?? $p['title'] ?? '';
        $image = $p['thumbnail_url'] ?? $p['image'] ?? $p['img'] ?? '/images/placeholder.png';
        $url = $p['url'] ?? (isset($p['full_path']) ? route('products.show-by-path', $p['full_path']) : '#');
        $price = $p['formatted_price'] ?? ($p['price'] ?? null);
      @endphp
      <a class="pg-card" href="{{ $url }}">
        <div class="pg-img"><img src="{{ $image }}" alt="{{ $name }}"></div>
        <div class="pg-title">{{ $name }}</div>
        @if($price)
          <div class="pg-price">{{ is_string($price) ? $price : ($price . ' ₴') }}</div>
        @endif
      </a>
    @endforeach
  </div>
</section>


