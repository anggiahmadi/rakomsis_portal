<x-layout.auth>
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: linear-gradient(135deg, #034c8f 0%, #00a8e3 100%);">
        <div class="w-full max-w-md">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">RAKOMSIS</h1>
                <p class="text-gray-100">Reset your password</p>
            </div>

            <!-- Status Message -->
            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('status') }}</p>
                </div>
            @endif

            <!-- Forgot Password Form -->
            <form method="POST" action="{{ url('password/email') }}" class="bg-white rounded-lg shadow-xl p-8 space-y-6">
                @csrf

                <!-- Instructions -->
                <p class="text-sm text-gray-700">
                    Enter your email address and we'll send you a link to reset your password.
                </p>

                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">
                        Email Address
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                        placeholder="you@example.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full text-white font-semibold py-2.5 px-4 rounded-lg transition duration-200 ease-in-out hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                    style="background-color: #034c8f;">
                    Send Reset Link
                </button>
            </form>

            <!-- Back to Login -->
            <p class="mt-6 text-center">
                <a href="{{ url('login') }}" class="font-semibold text-sm transition" style="color: #000000;">
                    ← Back to Sign In
                </a>
            </p>
        </div>
    </div>
</x-layout.auth>
