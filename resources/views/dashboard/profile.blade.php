@extends('components.layout.dashboard')

@section('title', 'My Profile')

@section('content')
    <div class="space-y-6">
        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
            <div class="bg-gradient-to-r from-[#034c8f] to-[#00a8e3] h-32"></div>
            <div class="px-6 py-4">
                <div class="flex items-end -mt-16 mb-6">
                    <div
                        class="w-24 h-24 bg-gradient-to-br from-[#034c8f] to-[#00a8e3] rounded-full border-4 border-white flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div class="ml-4 mb-2">
                        <h1 class="text-3xl font-bold text-gray-900">{{ Auth::user()->name }}</h1>
                        <p class="text-gray-600">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Edit Profile Information</h2>

            <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Full Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="{{ old('name', Auth::user()->name) }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('name') border-red-500 @enderror"
                        placeholder="Enter your full name">
                    @error('name')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="{{ old('email', Auth::user()->email) }}"
                        required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('email') border-red-500 @enderror"
                        placeholder="Enter your email address">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number <span
                            class="text-gray-500">(Optional)</span></label>
                    <input type="tel" id="phone" name="phone"
                        value="{{ old('phone', Auth::user()->phone ?? '') }}"
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#034c8f] focus:border-transparent outline-none transition @error('phone') border-red-500 @enderror"
                        placeholder="Enter your phone number">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Created -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Account Created</label>
                    <div class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-gray-600">
                        {{ Auth::user()->created_at->format('F d, Y \a\t h:i A') }}
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex gap-4 pt-4 border-t border-gray-200">
                    <button type="submit"
                        class="px-6 py-2.5 text-white font-semibold rounded-lg transition duration-200 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2"
                        style="background-color: #034c8f;">
                        Save Changes
                    </button>
                    <a href="{{ route('dashboard') }}"
                        class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Profile Stats -->
        <div class="grid grid-cols-2 gap-6 mt-6">
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Last Login</p>
                <p class="text-lg font-semibold text-gray-900 mt-1">
                    <span style="color: #034c8f;">Just now</span>
                </p>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <p class="text-gray-600 text-sm">Account Status</p>
                <p class="text-lg font-semibold text-gray-900 mt-1">
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">Active</span>
                </p>
            </div>
        </div>
    </div>
@endsection
