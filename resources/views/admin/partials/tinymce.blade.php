{{-- TinyMCE Rich Text Editor Component (Self-hosted) --}}
@once
    @push('scripts-head')
    {{-- Self-hosted TinyMCE via Vite --}}
    @vite(['resources/js/tinymce.js'])
    @endpush
@endonce

