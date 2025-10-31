@php
  $type = $type ?? 'random';
  $limit = (int)($limit ?? 3);
  $endpoint = "/products/html?limit={$limit}" . ($type === 'random' ? '&random=1' : '');
@endphp
<section
  x-data="{
    html: '',
    loading: true,
    async load() {
      try {
        const res = await fetch('{{ $endpoint }}', { headers: { 'Accept': 'text/html' } });
        this.html = res.ok ? await res.text() : '';
      } catch (e) { this.html = ''; }
      this.loading = false;
    }
  }"
  x-init="load()"
  class="products-module">
  <template x-if="loading">
    <div class="pm-skeleton d-flex gap-3">
      <div class="pm-card ph" style="width: 260px;">
        <div class="img ph" style="height: 160px;"></div>
        <div class="line ph" style="height:14px;margin-top:10px;width:80%"></div>
        <div class="line ph" style="height:14px;margin-top:6px;width:60%"></div>
      </div>
      <div class="pm-card ph" style="width: 260px;">
        <div class="img ph" style="height: 160px;"></div>
        <div class="line ph" style="height:14px;margin-top:10px;width:80%"></div>
        <div class="line ph" style="height:14px;margin-top:6px;width:60%"></div>
      </div>
      <div class="pm-card ph" style="width: 260px;">
        <div class="img ph" style="height: 160px;"></div>
        <div class="line ph" style="height:14px;margin-top:10px;width:80%"></div>
        <div class="line ph" style="height:14px;margin-top:6px;width:60%"></div>
      </div>
    </div>
  </template>
  <div x-show="!loading" x-html="html"></div>
</section>


