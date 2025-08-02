<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Hanaya') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/components.js'])
    
    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js" defer></script>
    
    <!-- CSP Compliant Scripts -->
    <script src="{{ asset('js/app-main.js') }}" defer></script>
    <script src="{{ asset('js/category-products.js') }}" defer></script>
    <script src="{{ asset('js/navigation.js') }}" defer></script>
    {{-- <script
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap&loading=async"
    async defer></script> --}}

    {{-- <script async
    src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&loading=async&libraries=places&callback=initMap">
</script> --}}


    
    <!-- Page-specific scripts -->
    @stack('scripts')
    
    <!-- CSS cho prose content - giống admin -->
    <style>
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
        .prose strong, .prose b {
            font-weight: 600;
        }
        .prose em, .prose i {
            font-style: italic;
        }
        .prose ul, .prose ol {
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
            quotes: "\201C""\201D""\2018""\2019";
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
    </style>
</head>

<body class="font-sans antialiased bg-gray-100 transition-colors duration-300">
    <!-- Loading overlay đặt ngay sau <body> -->
    <div id="pageLoadingOverlay" class="hidden fixed inset-0 z-50 bg-white bg-opacity-80 backdrop-blur-sm">
        <div class="text-2xl text-pink-500 flex flex-col items-center justify-center h-full">
            <svg class="animate-spin h-14 w-14 mb-4 text-pink-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
            </svg>
            <div class="text-xl font-semibold text-pink-600 tracking-wide animate-pulse">Loading...</div>
        </div>
    </div>
    <div class="min-h-screen bg-gray-100 transition-colors duration-300">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
        <footer class="bg-gray-800 text-gray-200 py-8 mt-10 flex flex-col transition-colors duration-300">
            <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-8">

                <div>
                    <h3 class="text-lg font-semibold mb-4">{{ config('constants.shop_name') }}</h3>
                    <p class="text-sm">
                        Specializing in premium flowers, meaningful gifts and decorative accessories.
                    </p>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="text-sm space-y-2">
                        <li><a href="/" class="hover:text-white">Home</a></li>
                        <li><a href="/products" class="hover:text-white">Products</a></li>
                        <li><a href="/about" class="hover:text-white">About</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-lg font-semibold mb-4">Contact</h3>
                    <p class="text-sm"> Address: {{ config('constants.shop_address') }}</p>
                    <p class="text-sm"> Phone: {{ config('constants.shop_phone') }}</p>
                    <p class="text-sm"> Email: {{ config('constants.shop_email') }}</p>
                </div>
            </div>

            <div class="text-center text-sm text-gray-400 dark:text-gray-500 mt-8 border-t border-gray-700 dark:border-gray-600 pt-4 transition-colors duration-300">
                &copy; {{ date('Y') }} Hanaya.
            </div>
        </footer>
    </div>

    <!-- Chatbot Component -->
    <x-chatbot />
</body>

</html>
