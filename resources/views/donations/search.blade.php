<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Your Donation - BayanihanCebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .track-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 1rem;
        }

        .track-container {
            background: white;
            border-radius: 1rem;
            padding: 3rem 2rem;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .track-container h2 {
            font-size: 2rem;
            font-weight: 800;
            color: #1f2937;
            margin-bottom: 1rem;
            text-align: center;
        }

        .track-container p {
            color: #6b7280;
            text-align: center;
            margin-bottom: 2rem;
        }

        .track-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .track-input {
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .track-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .track-btn {
            padding: 1rem 2rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .track-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 0.875rem;
        }

        .alert-error {
            background-color: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .alert-success {
            background-color: #d1fae5;
            border: 1px solid #6ee7b7;
            color: #065f46;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: #667eea;
            text-decoration: none;
            margin-top: 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            gap: 0.75rem;
        }

        .example-codes {
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1.5rem;
        }

        .example-codes h4 {
            font-size: 0.875rem;
            font-weight: 600;
            color: #6b7280;
            margin-bottom: 0.5rem;
        }

        .example-codes ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .example-codes li {
            color: #9ca3af;
            font-size: 0.875rem;
            font-family: monospace;
            padding: 0.25rem 0;
        }
    </style>
</head>
<body>
    <section class="track-section">
        <div class="track-container">
            <h2>Track Your Donation</h2>
            <p>Enter your tracking code to see blockchain verification and how your donation is being used</p>

            @if(session('error'))
                <div class="alert alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form class="track-form" action="{{ route('donation.track') }}" method="POST">
                @csrf
                <input
                    type="text"
                    name="tracking_code"
                    class="track-input"
                    placeholder="Enter Tracking Code (e.g., CC001-2025-00001)"
                    value="{{ old('tracking_code') }}"
                    required
                    autofocus
                >
                <button type="submit" class="track-btn">
                    🔍 Track Donation
                </button>
            </form>

            <div class="example-codes">
                <h4>Example Tracking Code Formats:</h4>
                <ul>
                    <li>Physical: CC001-2025-00001</li>
                    <li>Online: OD-CC001-2025-00001</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="{{ route('home') }}" class="back-link">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </section>
</body>
</html>
