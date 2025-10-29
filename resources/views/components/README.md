# Modal Components

## Базовый модальный компонент

`modal.blade.php` - это базовый компонент для создания модальных окон, который расширяют другие специализированные модальные окна.

### Параметры

- `id` - уникальный идентификатор модального окна
- `title` - заголовок модального окна
- `size` - размер (sm, md, lg, xl)
- `closable` - можно ли закрыть (true/false)
- `backdrop` - закрытие по клику на фон (true/false)
- `keyboard` - закрытие по Escape (true/false)
- `dynamicTitle` - динамический заголовок через Alpine.js (true/false)

### Использование

```html
<!-- Простое модальное окно -->
<x-modal id="myModal" title="Заголовок">
    <p>Содержимое модального окна</p>
</x-modal>

<!-- Модальное окно с футером -->
<x-modal id="confirmModal" title="Подтверждение" size="sm">
    <p>Вы уверены?</p>
    
    <x-slot name="footer">
        <button @click="$refs.confirmModal.close()">Отмена</button>
        <button @click="confirm()">Подтвердить</button>
    </x-slot>
</x-modal>

<!-- Модальное окно с динамическим заголовком -->
<x-modal id="dynamicModal" title="'Заголовок: ' + count" :dynamic-title="true">
    <p>Содержимое</p>
</x-modal>
```

### Управление через Alpine.js

```html
<!-- Открытие -->
<button @click="$refs.myModal.open()">Открыть</button>

<!-- Закрытие -->
<button @click="$refs.myModal.close()">Закрыть</button>

<!-- Переключение -->
<button @click="$refs.myModal.toggle()">Переключить</button>
```

## Специализированные модальные окна

### Cart Modal

Модальное окно корзины с товарами.

```html
<x-cart-modal />
```

**Функции:**
- Автоматическая загрузка товаров
- Удаление товаров
- Подсчёт суммы
- Переход к полной корзине

### Product Quick View

Модальное окно быстрого просмотра товара.

```html
<x-product-quick-view />
```

**Функции:**
- Загрузка данных товара
- Добавление в корзину
- Добавление в избранное
- Переход к странице товара

## Создание собственного модального окна

```html
<!-- my-custom-modal.blade.php -->
<div x-data="myCustomModal()">
    <x-modal 
        id="myCustomModal" 
        title="Моё модальное окно"
        size="lg"
        :closable="true"
    >
        <!-- Содержимое -->
        <p>Содержимое моего модального окна</p>
        
        <!-- Футер -->
        <x-slot name="footer">
            <button @click="$refs.myCustomModal.close()">Закрыть</button>
        </x-slot>
    </x-modal>
</div>

<script>
function myCustomModal() {
    return {
        // Ваша логика Alpine.js
        open() {
            this.$refs.myCustomModal.open();
        },
        
        close() {
            this.$refs.myCustomModal.close();
        }
    }
}
</script>
```

## Преимущества архитектуры

1. **Переиспользование** - базовый компонент для всех модальных окон
2. **Консистентность** - одинаковый внешний вид и поведение
3. **Гибкость** - легко создавать новые модальные окна
4. **Поддержка** - изменения в базовом компоненте применяются ко всем
5. **Производительность** - один компонент для всех модальных окон
