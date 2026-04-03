@extends('components.layout.dashboard')

@section('title', 'Account Settings')

@section('content')
    <div class="space-y-6">
        <!-- Change Password Section -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-6">
                <svg class="w-6 h-6 mr-3" style="color: #034c8f;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                    </path>
                </svg>
                <h2 class="text-xl font-semibold text-gray-900">Change Password</h2>
            </div>

            <p class="text-gray-600 text-sm mb-6">Update your password to keep your account secure. You'll need to enter
                your current password to confirm the change.</p>

            <form method="POST" action="{{ route('dashboard.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Current Password -->
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current
                        Password</label>
                    <div class="relative">
                        <input type="password" id="current_password" name="current_password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition pr-12 @error('current_password') border-red-500 @enderror"
                            placeholder="Enter your current password">
                        <button type="button" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('current_password', 'toggle-current')">
                            <svg id="toggle-current-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition pr-12 @error('password') border-red-500 @enderror"
                            placeholder="Enter your new password">
                        <button type="button" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('password', 'toggle-new')">
                            <svg id="toggle-new-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-2 text-sm text-gray-600">Must be at least 8 characters long and include uppercase,
                        lowercase, and numbers.</p>
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                        Password</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition pr-12 @error('password_confirmation') border-red-500 @enderror"
                            placeholder="Confirm your new password">
                        <button type="button" class="absolute right-4 top-3.5 text-gray-500 hover:text-gray-700"
                            onclick="togglePasswordVisibility('password_confirmation', 'toggle-confirm')">
                            <svg id="toggle-confirm-icon" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd"
                                    d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: #034c8f;">
                        Update Password
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Security Information -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 mr-3" style="color: #00a8e3;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m7 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900">Account Security</h3>
            </div>

            <div class="space-y-4">
                <!-- Email Verification -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Email Verified</p>
                        <p class="text-sm text-gray-600">
                            {{ Auth::user()->email_verified_at ? 'Your email address is verified' : 'Please verify your email' }}
                        </p>
                    </div>
                    <div>
                        @if (Auth::user()->email_verified_at)
                            <span
                                class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium">Verified</span>
                        @else
                            <span
                                class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium">Pending</span>
                        @endif
                    </div>
                </div>

                <!-- Two-Factor Authentication (Placeholder) -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Two-Factor Authentication</p>
                        <p class="text-sm text-gray-600">Add an extra layer of security to your account</p>
                    </div>
                    <button type="button"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition disabled:opacity-50"
                        disabled>
                        Coming Soon
                    </button>
                </div>

                <!-- Last Password Change -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Last Password Change</p>
                        <p class="text-sm text-gray-600">You last changed your password on
                            {{ Auth::user()->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>

                <!-- Active Sessions (Placeholder) -->
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-900">Active Sessions</p>
                        <p class="text-sm text-gray-600">Manage your active login sessions</p>
                    </div>
                    <button type="button"
                        class="px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition disabled:opacity-50"
                        disabled>
                        Coming Soon
                    </button>
                </div>
            </div>
        </div>

        <!-- Password Rules -->
        <div class="bg-blue-50 border-l-4 rounded-r-lg p-4" style="border-left-color: #034c8f;">
            <h4 class="font-semibold text-gray-900 mb-2">Password Requirements</h4>
            <ul class="text-sm text-gray-700 space-y-1">
                <li class="flex items-center"><span class="mr-2">•</span> At least 8 characters</li>
                <li class="flex items-center"><span class="mr-2">•</span> Contain at least one uppercase letter (A-Z)
                </li>
                <li class="flex items-center"><span class="mr-2">•</span> Contain at least one lowercase letter (a-z)
                </li>
                <li class="flex items-center"><span class="mr-2">•</span> Contain at least one number (0-9)</li>
            </ul>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId, toggleId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(toggleId + '-icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.innerHTML =
                    '<path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"></path><path d="M6.633 16.478A9.967 9.967 0 0112 21c4.478 0 8.268-2.943 9.543-7M19.5 4.5l-4.5 4.5m0-5l4.5 4.5"></path>';
            } else {
                field.type = 'password';
                icon.innerHTML =
                    '<path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>';
            }
        }
    </script>
@endsection
