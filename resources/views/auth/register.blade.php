<x-layout.auth>
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: linear-gradient(135deg, #034c8f 0%, #00a8e3 100%);">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">RAKOMSIS</h1>
                <p class="text-gray-100">Create your account</p>
            </div>

            <!-- Register Form -->
            <form method="POST" action="{{ url('register') }}" class="bg-white rounded-lg shadow-xl p-8 space-y-5">
                @csrf

                <!-- Name Field -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-2">
                        Full Name
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
                        placeholder="John Doe">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
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
                            onclick="togglePasswordVisibility(this)">
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
                    <p class="mt-1.5 text-xs text-gray-600">Minimum 8 characters</p>
                </div>

                <!-- Confirm Password Field -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition pr-10 @error('password_confirmation') border-red-500 @enderror"
                            placeholder="••••••••">
                        <button type="button"
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                            onclick="togglePasswordConfirmVisibility(this)">
                            <svg id="eyeIconConfirm" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Terms Checkbox -->
                <div>
                    <label class="flex items-start">
                        <input type="checkbox" name="terms" required
                            class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-[#034c8f] cursor-pointer mt-1 accent-[#034c8f]">
                        <span class="ml-2 text-sm text-gray-700">
                            I agree to the
                            <button type="button" id="term-of-service"
                                class="font-medium underline text-[#034c8f] hover:text-[#002f5f]">
                                Terms of Service</button>
                            and
                            <button type="button" id="privacy-policy"
                                class="font-medium underline text-[#034c8f] hover:text-[#002f5f]">
                                Privacy Policy</button>
                        </span>
                    </label>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Register Button -->
                <button type="submit"
                    class="w-full text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color: #034c8f;">
                    Create Account
                </button>
            </form>

            <!-- Terms and Privacy Modals -->
            <div id="term-of-service-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
                <div class="bg-white rounded-xl w-full max-w-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Terms of Service (SaaS Standard)</h3>
                        <button id="close-term-modal" class="text-gray-500 hover:text-gray-800">✕</button>
                    </div>
                    <div class="p-6 text-sm text-gray-700 space-y-3">
                        <p><strong>Service Provision:</strong> We provide the application as a hosted service; access requires registration and acceptance of security policy.</p>
                        <p><strong>Availability:</strong> We aim for 99.9% uptime and scheduled maintenance windows, with notifications given in advance.</p>
                        <p><strong>User Responsibility:</strong> Users must keep credentials secure, use supported browsers, and report incidents promptly.</p>
                        <p><strong>Data Ownership:</strong> Customer owns data; the platform stores it with encryption in transit and at rest.</p>
                        <p><strong>Support:</strong> We apply tiered support levels and service response times defined in the SLA.</p>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 text-right">
                        <button id="close-term-cta" class="px-4 py-2 rounded-lg bg-[#034c8f] text-white font-semibold hover:bg-[#023c77]">Got it</button>
                    </div>
                </div>
            </div>

            <div id="privacy-policy-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4 py-8 hidden">
                <div class="bg-white rounded-xl w-full max-w-lg shadow-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">Privacy Policy (SaaS Standard)</h3>
                        <button id="close-privacy-modal" class="text-gray-500 hover:text-gray-800">✕</button>
                    </div>
                    <div class="p-6 text-sm text-gray-700 space-y-3">
                        <p><strong>Data Collection:</strong> We collect personal and usage data necessary for service operation and improvement.</p>
                        <p><strong>Data Usage:</strong> Data is used only to support service delivery, security, and product analytics.</p>
                        <p><strong>Data Security:</strong> Data is encrypted in transit and at rest; access is restricted to authorized personnel.</p>
                        <p><strong>Data Retention:</strong> Data is retained as specified in the retention policy and exported upon user request.</p>
                        <p><strong>User Rights:</strong> Users may request deletion, correction, or export of their personal data.</p>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200 text-right">
                        <button id="close-privacy-cta" class="px-4 py-2 rounded-lg bg-[#034c8f] text-white font-semibold hover:bg-[#023c77]">Got it</button>
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="mt-6 flex items-center">
                <div class="flex-1 border-t border-gray-300"></div>
                <div class="px-3 text-sm text-gray-400">or</div>
                <div class="flex-1 border-t border-gray-300"></div>
            </div>

            <!-- Google Signup Button -->
            <a href="#"
                class="mt-6 w-full flex items-center justify-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-900 font-semibold py-2.5 px-4 rounded-lg transition duration-200">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                        fill="#4285F4" />
                    <path
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                        fill="#34A853" />
                    <path
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                        fill="#FBBC05" />
                    <path
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                        fill="#EA4335" />
                </svg>
                Sign up with Google
            </a>

            <!-- Login Link -->
            <p class="mt-6 text-center text-gray-100">
                Already have an account?
                <a href="{{ url('login') }}" class="font-semibold transition" style="color: #000000;">
                    Sign in here
                </a>
            </p>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(button) {
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

        function togglePasswordConfirmVisibility(button) {
            const passwordInput = document.getElementById('password_confirmation');
            const eyeIcon = document.getElementById('eyeIconConfirm');

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

        document.getElementById('term-of-service').addEventListener('click', function () {
            document.getElementById('term-of-service-modal').classList.remove('hidden');
        });
        document.getElementById('privacy-policy').addEventListener('click', function () {
            document.getElementById('privacy-policy-modal').classList.remove('hidden');
        });

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        document.getElementById('close-term-modal').addEventListener('click', function () {
            closeModal('term-of-service-modal');
        });
        document.getElementById('close-term-cta').addEventListener('click', function () {
            closeModal('term-of-service-modal');
        });

        document.getElementById('close-privacy-modal').addEventListener('click', function () {
            closeModal('privacy-policy-modal');
        });
        document.getElementById('close-privacy-cta').addEventListener('click', function () {
            closeModal('privacy-policy-modal');
        });
    </script>
</x-layout.auth>
