<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/components.js'])

    <!-- CSS cho CKEditor & TinyMCE content -->
    <style>
        .ck-editor__editable_inline {
            min-height: 350px;
        }

        .ck.ck-toolbar {
            border-top: 1px solid #c4c4c4;
            border-left: 1px solid #c4c4c4;
            border-right: 1px solid #c4c4c4;
        }

        .ck.ck-editor__main>.ck-editor__editable {
            border-left: 1px solid #c4c4c4;
            border-right: 1px solid #c4c4c4;
            border-bottom: 1px solid #c4c4c4;
        }

        /* TinyMCE content style fix - Bỏ CSS làm mất format */
        .prose {
            color: #374151;
            max-width: none;
        }

        .prose h1 {
            font-size: 2.25em;
            font-weight: 800;
            line-height: 1.1111111;
            margin-top: 0;
            margin-bottom: 0.8888889em;
        }

        .prose h2 {
            font-size: 1.875em;
            font-weight: 700;
            line-height: 1.3333333;
            margin-top: 2em;
            margin-bottom: 1em;
        }

        .prose h3 {
            font-size: 1.5em;
            font-weight: 600;
            line-height: 1.6;
            margin-top: 1.6em;
            margin-bottom: 0.6em;
        }

        .prose strong,
        .prose b {
            font-weight: 600;
        }

        .prose em,
        .prose i {
            font-style: italic;
        }

        .prose ul,
        .prose ol {
            margin-top: 1.25em;
            margin-bottom: 1.25em;
            padding-left: 1.625em;
        }

        .prose li {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose blockquote {
            font-weight: 500;
            font-style: italic;
            color: #374151;
            border-left-width: 0.25rem;
            border-left-color: #d1d5db;
            quotes: "\201C" "\201D" "\2018" "\2019";
            margin-top: 1.6em;
            margin-bottom: 1.6em;
            padding-left: 1em;
        }

        .prose img {
            margin-top: 2em;
            margin-bottom: 2em;
            max-width: 100%;
            height: auto;
        }

        .prose p {
            margin-top: 1.25em;
            margin-bottom: 1.25em;
        }

        /* TinyMCE image upload styling */
        .tox .tox-dialog__body-content {
            padding: 1rem;
        }

        .tox .tox-dialog__title {
            color: #374151;
            font-weight: 600;
        }

        .tox-dialog .tox-textfield {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            padding: 0.5rem;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-100">
    <!-- Loading overlay đặt ngay sau <body> -->
    <div id="pageLoadingOverlay"
        class="hidden fixed inset-0 z-50 bg-white bg-opacity-70">
        <div class="text-2xl text-blue-600 flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-10 w-10 mr-3 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            Loading...
        </div>
    </div>
    <div class="min-h-screen">
        @include('layouts.admin-nav')
        @hasSection('header')
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    @yield('header')
                </div>
            </header>
        @endif
        <main>
            @yield('content')
        </main>
    </div>

    <!-- Load TinyMCE từ CDN - Editor đầy đủ tính năng -->
<script src="https://cdn.tiny.cloud/1/y1zo0i12q8i692ria3ibrw4baa79o7h6yaa1tgqpy03fwz1x/tinymce/6/tinymce.min.js"
    referrerpolicy="origin"></script>

<!-- Include our CSP-compliant components -->
@vite('resources/js/components.js')

<script>
    // CSP-compliant TinyMCE initialization
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof tinymce !== 'undefined') {
            tinymce.init({
                selector: 'textarea.description',
                height: 400,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help | image | media | link',
                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
                branding: false,
                promotion: false,
                // CSP compliant settings
                allow_script_urls: false,
                convert_urls: false,
                setup: function(editor) {
                    // TinyMCE initialized successfully
                }
            });
        }

        // Initialize image preview
        if (typeof initImagePreview === 'function') {
            initImagePreview();
        }
    });
</script>

    <script>
        // Function to hide loading overlay
        function hideAdminPageLoadingOverlay() {
            const overlay = document.getElementById('pageLoadingOverlay');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('flex', 'items-center', 'justify-center');
            }
        }

        // Hide overlay when page loads
        window.addEventListener('load', hideAdminPageLoadingOverlay);

        // Hide overlay when navigating with browser back/forward buttons
        window.addEventListener('popstate', hideAdminPageLoadingOverlay);

        document.addEventListener('DOMContentLoaded', function() {
            // Hide overlay on initial page load
            hideAdminPageLoadingOverlay();
            
            // Chặn click trên nav-link và delay chuyển trang
            document.querySelectorAll('nav a, .admin-nav a, .sidebar a').forEach(link => {
                link.addEventListener('click', function(e) {
                    // Chỉ xử lý link nội bộ (không có target _blank, không phải anchor, không có modifier key)
                    if (
                        this.target === '_blank' ||
                        this.href.startsWith('javascript:') ||
                        this.href === '#' ||
                        e.ctrlKey || e.shiftKey || e.metaKey || e.altKey
                    ) return;
                    e.preventDefault();
                    const overlay = document.getElementById('pageLoadingOverlay');
                    overlay.classList.remove('hidden');
                    overlay.classList.add('flex', 'items-center', 'justify-center');
                    setTimeout(() => {
                        window.location.href = this.href;
                    }, 150);
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
