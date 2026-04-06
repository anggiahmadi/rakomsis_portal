<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal - {{ config('app.name', 'Rakomsis') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg">
            <div class="p-6 border-b border-gray-200">
                <h1 class="text-2xl font-bold" style="color: #034c8f;">RAKOMSIS</h1>
                <p class="text-sm text-gray-600 mt-1">Portal</p>
            </div>

            @include('components.layout.nav')
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation Bar -->
            <div class="bg-white shadow-sm border-b border-gray-200 px-6 py-4">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-semibold text-gray-900">@yield('title', 'Dashboard')</h2>
                    @php
                        $headerNotifications = [];

                        if (session('success')) {
                            $headerNotifications[] = [
                                'type' => 'success',
                                'title' => 'Success',
                                'message' => session('success'),
                            ];
                        }

                        if (session('error')) {
                            $headerNotifications[] = [
                                'type' => 'error',
                                'title' => 'Error',
                                'message' => session('error'),
                            ];
                        }

                        if (session('warning')) {
                            $headerNotifications[] = [
                                'type' => 'warning',
                                'title' => 'Warning',
                                'message' => session('warning'),
                            ];
                        }

                        if (session('info')) {
                            $headerNotifications[] = [
                                'type' => 'info',
                                'title' => 'Information',
                                'message' => session('info'),
                            ];
                        }

                        if ($errors->any()) {
                            $headerNotifications[] = [
                                'type' => 'error',
                                'title' => 'Validation Error',
                                'message' => $errors->first(),
                            ];
                        }

                        $notificationCount = count($headerNotifications);
                    @endphp
                    <div class="flex items-center space-x-4">
                        <div class="relative" id="header-notification-wrapper">
                            <button type="button" id="header-notification-toggle"
                                class="relative p-2 rounded-full text-gray-600 hover:bg-gray-100 hover:text-[#034c8f] focus:outline-none focus:ring-2 focus:ring-[#00a8e3]"
                                aria-label="Notifications" aria-expanded="false">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                @if ($notificationCount > 0)
                                    <span
                                        class="absolute -top-1 -right-1 min-w-[1.1rem] h-[1.1rem] px-1 rounded-full bg-red-500 text-white text-[10px] leading-[1.1rem] text-center font-semibold">
                                        {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                                    </span>
                                @endif
                            </button>

                            <div id="header-notification-panel"
                                class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-xl z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <h4 class="text-sm font-semibold text-gray-900">Notifications</h4>
                                    <span class="text-xs text-gray-500">{{ $notificationCount }} new</span>
                                </div>
                                <div class="max-h-72 overflow-y-auto">
                                    @forelse ($headerNotifications as $notification)
                                        <div class="px-4 py-3 border-b border-gray-100 last:border-b-0">
                                            <p
                                                class="text-xs font-semibold {{ $notification['type'] === 'error' ? 'text-red-600' : ($notification['type'] === 'warning' ? 'text-yellow-600' : ($notification['type'] === 'success' ? 'text-green-600' : 'text-blue-600')) }}">
                                                {{ $notification['title'] }}
                                            </p>
                                            <p class="text-sm text-gray-700 mt-1">{{ $notification['message'] }}</p>
                                        </div>
                                    @empty
                                        <div class="px-4 py-6 text-center text-sm text-gray-500">
                                            No new notifications.
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-[#034c8f] to-[#00a8e3] rounded-full flex items-center justify-center text-white font-semibold">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page Content -->
            <div class="flex-1 overflow-auto p-6">
                <!-- Flash Messages -->
                @if (session()->hasAny(['success', 'error', 'warning', 'info']))
                    <div class="mb-6 space-y-3">
                        @if (session('success'))
                            <div class="flex items-start p-4 bg-green-50 border-l-4 border-green-400 rounded-r-lg"
                                data-flash-message data-flash-type="success">
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" data-dismiss="flash-message"
                                            class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600">
                                            <span class="sr-only">Dismiss</span>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="flex items-start p-4 bg-red-50 border-l-4 border-red-400 rounded-r-lg"
                                data-flash-message>
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" data-dismiss="flash-message"
                                            class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600">
                                            <span class="sr-only">Dismiss</span>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('warning'))
                            <div class="flex items-start p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-r-lg"
                                data-flash-message>
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-yellow-800">{{ session('warning') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" data-dismiss="flash-message"
                                            class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600">
                                            <span class="sr-only">Dismiss</span>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if (session('info'))
                            <div class="flex items-start p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg"
                                data-flash-message>
                                <div class="flex-shrink-0">
                                    <svg class="w-5 h-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                                </div>
                                <div class="ml-auto pl-3">
                                    <div class="-mx-1.5 -my-1.5">
                                        <button type="button" data-dismiss="flash-message"
                                            class="inline-flex bg-blue-50 rounded-md p-1.5 text-blue-500 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-50 focus:ring-blue-600">
                                            <span class="sr-only">Dismiss</span>
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <h3 class="text-sm font-medium text-red-800 mb-2">Oops! There were errors:</h3>
                        <ul class="text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script>
        // Flash message dismiss functionality
        document.addEventListener('DOMContentLoaded', function() {
            const notificationToggle = document.getElementById('header-notification-toggle');
            const notificationPanel = document.getElementById('header-notification-panel');
            const notificationWrapper = document.getElementById('header-notification-wrapper');

            if (notificationToggle && notificationPanel && notificationWrapper) {
                notificationToggle.addEventListener('click', function(event) {
                    event.stopPropagation();
                    const isHidden = notificationPanel.classList.contains('hidden');
                    notificationPanel.classList.toggle('hidden');
                    notificationToggle.setAttribute('aria-expanded', String(isHidden));
                });

                document.addEventListener('click', function(event) {
                    if (!notificationWrapper.contains(event.target)) {
                        notificationPanel.classList.add('hidden');
                        notificationToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }

            // Handle dismiss buttons for flash messages
            const dismissButtons = document.querySelectorAll('[data-dismiss="flash-message"]');
            dismissButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const messageContainer = this.closest('[data-flash-message]');
                    if (messageContainer) {
                        messageContainer.style.transition = 'opacity 0.3s ease-out';
                        messageContainer.style.opacity = '0';
                        setTimeout(() => {
                            messageContainer.remove();
                        }, 300);
                    }
                });
            });

            // Auto-dismiss success messages after 5 seconds
            const successMessages = document.querySelectorAll('[data-flash-type="success"]');
            successMessages.forEach(message => {
                setTimeout(() => {
                    message.style.transition = 'opacity 0.3s ease-out';
                    message.style.opacity = '0';
                    setTimeout(() => {
                        message.remove();
                    }, 300);
                }, 5000);
            });
        });
    </script>
</body>

</html>
