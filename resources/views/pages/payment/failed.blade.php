<x-layout.auth>
    <div class="min-h-screen flex items-center justify-center px-4 py-12"
        style="background: linear-gradient(140deg, #ffecec 0%, #fff7ed 50%, #ffffff 100%);">
        <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl border border-red-100 p-8 sm:p-10 text-center">
            <div
                class="mx-auto mb-6 w-20 h-20 rounded-full bg-red-100 text-red-600 flex items-center justify-center ring-8 ring-red-50">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>

            <p class="text-sm font-semibold tracking-wide uppercase text-red-600">Payment Status</p>
            <h1 class="mt-2 text-3xl sm:text-4xl font-bold text-gray-900">Payment Failed</h1>
            <p class="mt-4 text-gray-600 leading-relaxed">
                Your payment was not completed. Please return to the subscription page and try the payment process
                again.
            </p>

            <div class="mt-8">
                <a href="{{ route('subscriptions.index') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-[#034c8f] px-6 py-3 text-white font-semibold shadow-md hover:bg-[#023765] transition-colors">
                    Back To Subscription Page
                </a>
            </div>
        </div>
    </div>
</x-layout.auth>
