<div class="admin-brand">
    <span>DEPS Admin</span>
    <span class="muted" style="margin-left:auto; font-weight:400; font-size:12px;">v1</span>
  </div>
  <nav class="admin-menu">
    <div class="muted">Панель</div>
    <a href="{{ route('admin.dashboard') }}">Дашборд</a>
    <div class="muted" style="margin-top:10px;">Каталог</div>
    <a href="{{ route('admin.products') }}">Товары</a>
    <a href="{{ route('admin.categories') }}">Категории</a>
    <a href="{{ route('admin.manufacturers') }}">Производители</a>
    <div class="muted" style="margin-top:10px;">Продажи</div>
    <a href="{{ route('admin.orders') }}">Заказы</a>
    <a href="{{ route('admin.customers') }}">Клиенты</a>
  </nav>


