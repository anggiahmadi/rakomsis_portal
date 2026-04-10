<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Opening Invoice</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 560px;
            background: #ffffff;
            border: 1px solid #dbeafe;
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
            padding: 32px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 24px;
            line-height: 1.3;
        }

        p {
            margin: 0 0 12px;
            line-height: 1.6;
            color: #475569;
        }

        .success {
            margin: 0 0 20px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #f0fdf4;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .button-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .button-secondary {
            background: #e2e8f0;
            color: #0f172a;
        }

        .hint {
            margin-top: 16px;
            font-size: 14px;
            color: #64748b;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <div class="card">
            <div class="success">{{ $successMessage }}</div>
            <h1>Opening your invoice</h1>
            <p id="invoice-status">We are opening the Xendit invoice in a new tab and returning you to the subscription list.</p>
            <p>If nothing opens, use the button below.</p>

            <div class="actions">
                <a id="open-invoice-link" class="button button-primary" href="{{ $invoiceUrl }}" target="_blank"
                    rel="noopener noreferrer">Open Invoice</a>
                <a class="button button-secondary" href="{{ $redirectUrl }}">Back to Subscriptions</a>
            </div>

            <p class="hint">You will be redirected automatically in a moment.</p>
        </div>
    </div>

    <script>
        const invoiceUrl = @json($invoiceUrl);
        const redirectUrl = @json($redirectUrl);
        const statusNode = document.getElementById('invoice-status');

        let popup = null;

        try {
            popup = window.open(invoiceUrl, '_blank', 'noopener,noreferrer');
        } catch (error) {
            popup = null;
        }

        if (!popup) {
            statusNode.textContent =
                'Your browser may have blocked the new tab. Please use the "Open Invoice" button below.';
        }

        window.setTimeout(() => {
            window.location.replace(redirectUrl);
        }, 1500);
    </script>

    <noscript>
        <meta http-equiv="refresh" content="3;url={{ $redirectUrl }}">
    </noscript>
</body>

</html>
