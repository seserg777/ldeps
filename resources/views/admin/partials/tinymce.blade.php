{{-- TinyMCE Rich Text Editor Component --}}
@once
    @push('scripts-head')
    <script src="https://cdn.tiny.cloud/1/{{ env('TINYMCE_API_KEY', 'no-api-key') }}/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                    }
                });
            }
        });
    </script>
    @endpush
@endonce

