{{-- TinyMCE Rich Text Editor Component --}}
@once
    @push('scripts-head')
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY', 'no-api-key') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Wait a bit for Alpine.js tabs to render
            setTimeout(function() {
                if (document.querySelector('.tinymce-editor')) {
                    tinymce.init({
                        selector: '.tinymce-editor',
                        height: 400,
                        menubar: false,
                        license_key: 'gpl',
                        plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table code help wordcount',
                        toolbar: 'undo redo | blocks | bold italic forecolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | removeformat | link image | code | help',
                        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 14px; }',
                        branding: false,
                        promotion: false,
                        setup: function(editor) {
                            editor.on('change', function() {
                                tinymce.triggerSave();
                            });
                            
                            // Re-initialize when parent tab becomes visible
                            const textarea = editor.getElement();
                            const container = textarea.closest('[x-show]');
                            if (container) {
                                const observer = new MutationObserver(function() {
                                    if (container.style.display !== 'none') {
                                        editor.show();
                                    }
                                });
                                observer.observe(container, { attributes: true, attributeFilter: ['style'] });
                            }
                        }
                    });
                }
            }, 100);
        });
    </script>
    @endpush
@endonce

