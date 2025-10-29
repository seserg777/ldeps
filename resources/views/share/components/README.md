# Modal Components

## Base Modal Component

`modal.blade.php` is the base component for creating modal windows that other specialized modal windows extend.

### Parameters

- `id` - unique identifier for the modal window
- `title` - modal window title
- `size` - size (sm, md, lg, xl)
- `closable` - whether it can be closed (true/false)
- `backdrop` - close on backdrop click (true/false)
- `keyboard` - close on Escape key (true/false)
- `dynamicTitle` - dynamic title via Alpine.js (true/false)

### Usage

```html
<!-- Simple modal window -->
<x-modal id="myModal" title="Title">
    <p>Modal window content</p>
</x-modal>

<!-- Modal with footer -->
<x-modal id="confirmModal" title="Confirmation" size="sm">
    <p>Are you sure?</p>
    
    <x-slot name="footer">
        <button @click="$refs.confirmModal.close()">Cancel</button>
        <button @click="confirm()">Confirm</button>
    </x-slot>
</x-modal>

<!-- Modal with dynamic title -->
<x-modal id="dynamicModal" title="'Title: ' + count" :dynamic-title="true">
    <p>Content</p>
</x-modal>
```

### Control via Alpine.js

```html
<!-- Open -->
<button @click="$refs.myModal.open()">Open</button>

<!-- Close -->
<button @click="$refs.myModal.close()">Close</button>

<!-- Toggle -->
<button @click="$refs.myModal.toggle()">Toggle</button>
```

## Specialized Modal Windows

### Cart Modal

Shopping cart modal with products.

```html
<x-cart-modal />
```

**Features:**
- Automatic product loading
- Product removal
- Total calculation
- Navigate to full cart page

### Product Quick View

Product quick view modal.

```html
<x-product-quick-view />
```

**Features:**
- Product data loading
- Add to cart
- Add to wishlist
- Navigate to product page

## Creating Custom Modal Window

```html
<!-- my-custom-modal.blade.php -->
<div x-data="myCustomModal()">
    <x-modal 
        id="myCustomModal" 
        title="My Modal Window"
        size="lg"
        :closable="true"
    >
        <!-- Content -->
        <p>My modal window content</p>
        
        <!-- Footer -->
        <x-slot name="footer">
            <button @click="$refs.myCustomModal.close()">Close</button>
        </x-slot>
    </x-modal>
</div>

<script>
function myCustomModal() {
    return {
        // Your Alpine.js logic
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

## Architecture Benefits

1. **Reusability** - base component for all modal windows
2. **Consistency** - same appearance and behavior
3. **Flexibility** - easy to create new modal windows
4. **Maintainability** - changes in base component apply to all
5. **Performance** - one component for all modal windows
