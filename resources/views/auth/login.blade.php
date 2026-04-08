<x-layout.auth>
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: linear-gradient(135deg, #034c8f 0%, #00a8e3 100%);">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">RAKOMSIS PORTAL</h1>
                <p class="text-gray-100">Sign in to your account, or back to <a href="https://rakomsis.com" target="_blank"
                        rel="noopener noreferrer" class="text-primary-500 hover:text-blue-700">RAKOMSIS</a></p>
            </div>

            <div class="bg-white rounded-2xl shadow-xl border border-white/30 overflow-hidden">
                <!-- Login Form -->
                <form method="POST" action="{{ url('login') }}" class="p-8 space-y-6">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                            Email Address
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autofocus
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                            placeholder="you@example.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-900 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition pr-10 @error('password') border-red-500 @enderror"
                                placeholder="••••••••">
                            <button type="button"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                                onclick="togglePasswordVisibility()">
                                <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-[#034c8f] cursor-pointer accent-[#034c8f]">
                            <span class="ml-2 text-sm text-gray-700">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-medium transition"
                            style="color: #034c8f;">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Login Button -->
                    <button type="submit"
                        class="w-full text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: #034c8f;">
                        Sign In
                    </button>
                </form>

                <div class="px-8 pb-8">
                    <div class="flex items-center">
                        <div class="flex-1 border-t border-slate-200"></div>
                        <div class="px-3 text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">or continue
                            with</div>
                        <div class="flex-1 border-t border-slate-200"></div>
                    </div>

                    @if (config('services.google.client_id'))
                        <div class="mt-5 bg-gradient-to-r from-[#f4fbff] to-[#eef7ff] px-4 py-4 shadow-sm">
                            <p class="mb-3 text-center text-sm font-medium text-slate-700">Use your Google account to
                                sign in faster.</p>
                            <div id="google-login-button" class="w-full flex justify-center"></div>
                        </div>
                    @else
                        <div class="mt-5 bg-yellow-50 px-4 py-3 text-sm text-yellow-800">
                            Google login is unavailable because <strong>GOOGLE_CLIENT_ID</strong> is not configured.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Register Link -->
            <p class="mt-6 text-center text-gray-100">
                Don't have an account?
                <a href="{{ route('register') }}" class="font-semibold transition" style="color: #000000;">
                    Sign up here
                </a>
            </p>
        </div>
    </div>

    @if (config('services.google.client_id'))
        <script src="https://accounts.google.com/gsi/client" async defer></script>
    @endif

    <script>
        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0M15 12a3 3 0 11-6 0 3 3 0 016 0M6.633 7.653a8.96 8.96 0 011.457-1.784m4.4 6.327a3.375 3.375 0 01-3-3m9.124-2.675A9.001 9.001 0 005.064 7.59m5.858.908a3 3 0 11-5.364 3.364M3 3l18 18"></path>';
            } else {
                passwordInput.type = 'password';
                eyeIcon.innerHTML =
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        async function handleGoogleCredentialResponse(response) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            try {
                const loginResponse = await fetch('{{ route('google.login') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        credential: response.credential,
                    }),
                });

                const result = await loginResponse.json();

                if (!loginResponse.ok) {
                    throw result;
                }

                window.location.href = result.redirect;
            } catch (error) {
                alert(error.message || 'Google login failed.');
            }
        }

        window.addEventListener('load', function() {
            if (!window.google || !document.getElementById('google-login-button')) {
                return;
            }

            window.google.accounts.id.initialize({
                client_id: '{{ config('services.google.client_id') }}',
                client_secret: '{{ config('services.google.client_secret') }}',
                callback: handleGoogleCredentialResponse,
            });

            window.google.accounts.id.renderButton(
                document.getElementById('google-login-button'), {
                    theme: 'filled_blue',
                    size: 'large',
                    shape: 'rectangular',
                    text: 'continue_with',
                    width: Math.min(360, document.getElementById('google-login-button')?.offsetWidth || 320),
                }
            );
        });
    </script>
    </x-layout>
