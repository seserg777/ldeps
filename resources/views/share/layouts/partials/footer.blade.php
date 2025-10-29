<!-- Footer -->
<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 mb-4">
                <h5 class="text-uppercase fw-bold mb-3">
                    <i class="fas fa-shopping-bag me-2"></i>Каталог товарів
                </h5>
                <p class="text-muted">
                    Ваш надійний партнер у світі якісних товарів. 
                    Широкий асортимент, доступні ціни та швидка доставка.
                </p>
                <div class="social-links">
                    <a href="#" class="text-light me-3" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="text-light me-3" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="text-light me-3" title="Telegram">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                    <a href="#" class="text-light" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Швидкі посилання</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="{{ route('products.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Головна
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('categories.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Категорії
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('sale-banners.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Акції
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="{{ route('cart.index') }}" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Кошик
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Customer Service -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Сервіс</h6>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Доставка
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Оплата
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Повернення
                        </a>
                    </li>
                    <li class="mb-2">
                        <a href="#" class="text-muted text-decoration-none">
                            <i class="fas fa-chevron-right me-1"></i>Гарантія
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 mb-4">
                <h6 class="text-uppercase fw-bold mb-3">Контакти</h6>
                <div class="contact-info">
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                        <span class="text-muted">м. Київ, вул. Хрещатик, 1</span>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-phone me-2 text-primary"></i>
                        <a href="tel:+380671234567" class="text-muted text-decoration-none">
                            +38 (067) 123-45-67
                        </a>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-envelope me-2 text-primary"></i>
                        <a href="mailto:info@example.com" class="text-muted text-decoration-none">
                            info@example.com
                        </a>
                    </div>
                    <div class="mb-3">
                        <i class="fas fa-clock me-2 text-primary"></i>
                        <span class="text-muted">Пн-Пт: 9:00-18:00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Bar -->
        <hr class="my-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="text-muted mb-0">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Laravel') }}. Всі права захищені.
                </p>
            </div>
            <div class="col-md-6 text-end">
                <div class="payment-methods">
                    <span class="text-muted me-3">Приймаємо:</span>
                    <i class="fab fa-cc-visa me-2 text-light"></i>
                    <i class="fab fa-cc-mastercard me-2 text-light"></i>
                    <i class="fab fa-cc-paypal me-2 text-light"></i>
                    <i class="fas fa-credit-card me-2 text-light"></i>
                </div>
            </div>
        </div>
    </div>
</footer>
