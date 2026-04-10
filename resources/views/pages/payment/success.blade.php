<x-layout.auth>
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: linear-gradient(140deg, #e7f7ee 0%, #f0f9ff 50%, #ffffff 100%);">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-green-100 p-8 sm:p-10 text-center">
            <div
                class="mx-auto mb-6 w-20 h-20 rounded-full bg-green-100 text-green-600 flex items-center justify-center ring-8 ring-green-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <p class="text-sm font-semibold tracking-wide uppercase text-green-600">Payment Status</p>
            <h1 class="mt-2 text-3xl sm:text-4xl font-bold text-gray-900">Payment Successful</h1>
            <p class="mt-4 text-gray-600 leading-relaxed">
                Thank you. Your payment has been received successfully. You can continue to the subscription page to
                review your subscription details.
            </p>

            <div class="mt-8">
                <a href="{{ route('subscriptions.index') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-[#034c8f] px-6 py-3 text-white font-semibold shadow-md hover:bg-[#023765] transition-colors">
                    Go To Subscription Page
                </a>
            </div>
        </div>
    </div>
</x-layout.auth>
