<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BayanihanCebu - Transparent Disaster Relief for Cebu</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="{{ asset('js/simple-realtime.js') }}"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            color: white;
            padding: 60px 20px 80px;
            position: relative;
        }

        .hero-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 60px;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
        }

        .hero-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .logo-text h1 {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .logo-text p {
            font-size: 13px;
            opacity: 0.9;
            margin: 0;
        }

        .sign-in-btn {
            background: white;
            color: #1e40af;
            padding: 10px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .sign-in-btn:hover {
            background: #f0f9ff;
            transform: translateY(-2px);
        }

        .hero-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-main {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin-bottom: 60px;
        }

        .hero-text h2 {
            font-size: 42px;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 20px;
        }

        .hero-text p {
            font-size: 18px;
            opacity: 0.95;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-donate {
            background: #ef4444;
            color: white;
        }

        .btn-donate:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        .btn-track {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .btn-track:hover {
            background: white;
            color: #1e40af;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .blockchain-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 30px 20px;
            border-radius: 12px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-number {
            font-size: 36px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .stat-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .verified-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: #10b981;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
        }

        /* Map Section */
        .map-section {
              padding: 80px 20px;
                background: #f9fafb;
                position: relative;
}

        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 10px;
            text-align: center
        }

        .section-header p {
            font-size: 18px;
            color: #6b7280;
            text-align: center
        }

        .status-legend {
            display: flex;
            justify-content: center;
            gap: 25px;
            margin-bottom: 40px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #4b5563;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .legend-dot.safe { background: #10b981; }
        .legend-dot.warning { background: #f59e0b; }
        .legend-dot.critical { background: #f97316; }
        .legend-dot.emergency { background: #ef4444; }

        /* Map Container with Summary Panel - Optimized layout */
        .map-container {
            max-width: 1400px;
            margin: 30px auto 60px;
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 24px;
            height: 830px;
            align-items: start;
            position: relative;
            z-index: 1;
        }

        .map-wrapper {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        position: relative;
        height: 100%;
        z-index: 1;
        }

        #barangayMap {
            width: 100%;
            height: 100%;
        }

        /* Map Legend */
        .map-legend-overlay {
            position: absolute;
            bottom: 20px;
            left: 20px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }

        .legend-title {
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 14px;
            color: #1f2937;
        }

        .legend-items {
            display: flex;
            gap: 15px;
        }

        /* Summary Panel - No scrolling, fit content properly */
        .summary-panel {
               display: flex;
                flex-direction: column;
                gap: 20px;
                overflow: visible;
                height: 100%;
                position: relative;
                z-index: 2;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .card-title {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .card-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
        }

        /* Stats Grid in Summary */
        .stats-grid-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .stat-box {
            text-align: center;
            padding: 15px 10px;
            background: #f9fafb;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .stat-box .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 4px;
        }

        .stat-box .stat-label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Barangay List in Summary - Adjusted height for better fit */
        .barangay-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 280px;
            overflow-y: auto;
        }

        /* Scrollbar styling for barangay list */
        .barangay-list::-webkit-scrollbar {
            width: 6px;
        }

        .barangay-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .barangay-list::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .barangay-list::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .barangay-item {
            padding: 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .barangay-item:hover {
            border-color: #3b82f6;
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
            transform: translateX(2px);
        }

        .barangay-info-summary {
            flex: 1;
        }

        .barangay-name-summary {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .barangay-meta {
            font-size: 12px;
            color: #6b7280;
        }

        /* Quick Actions */
        .quick-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .action-btn {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .action-btn-primary {
            background: #ef4444;
            color: white;
        }

        .action-btn-primary:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .action-btn-secondary {
            background: #3b82f6;
            color: white;
        }

        .action-btn-secondary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* Barangay Cards Grid - BELOW THE MAP */
        .barangay-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            max-width: 1200px;
            margin: 80px auto 0px;
            padding: 0 20px;
            clear: both;
            position: relative;
            z-index: 0;
        }

        .barangay-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
            z-index: 3;
        }

        .barangay-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }

        .barangay-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .barangay-name {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-badge.safe {
            background: #d1fae5;
            color: #065f46;
        }

        .status-badge.warning {
            background: #fef3c7;
            color: #92400e;
        }

        .status-badge.critical {
            background: #fed7aa;
            color: #9a3412;
        }

        .status-badge.emergency {
            background: #fee2e2;
            color: #991b1b;
        }

        .barangay-info {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .barangay-stats {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
            margin-bottom: 12px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-item-label {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .stat-item-value {
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
        }

        .urgent-needs {
            margin-bottom: 15px;
        }

        .urgent-needs-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .needs-tags {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .need-tag {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .donate-btn {
            width: 100%;
            background: #ef4444;
            color: white;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .donate-btn:hover {
            background: #dc2626;
            transform: translateY(-2px);
        }

        /* Track Donation Section */
        .track-section {
            padding: 80px 20px;
            background: white;
        }

        .track-container {
            max-width: 700px;
            margin: 0 auto;
            text-align: center;
        }

        .track-container h2 {
            font-size: 32px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .track-container p {
            font-size: 16px;
            color: #6b7280;
            margin-bottom: 30px;
        }

        .track-form {
            display: flex;
            gap: 12px;
            max-width: 600px;
            margin: 0 auto;
        }

        .track-input {
            flex: 1;
            padding: 14px 20px;
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .track-input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .track-btn {
            background: #3b82f6;
            color: white;
            padding: 14px 32px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .track-btn:hover {
            background: #2563eb;
        }

        /* Trust Section */
        .trust-section {
            padding: 80px 20px;
            background: #f9fafb;
        }

        .trust-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .trust-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .trust-header h2 {
            font-size: 36px;
            font-weight: 700;
            color: #1f2937;
        }

        .trust-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .trust-card {
            background: white;
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .trust-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateY(-4px);
        }

        .trust-icon {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
        }

        .trust-icon.icon-users {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .trust-icon.icon-chart {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .trust-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .trust-card p {
            font-size: 15px;
            color: #6b7280;
            line-height: 1.6;
        }

        /* Responsive */
        @media (max-width: 968px) {
            .hero-main {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .barangay-cards {
                  grid-template-columns: repeat(2, 1fr);
                   padding: 0 40px;
            }

            .trust-grid {
                grid-template-columns: 1fr;
            }

            .hero-text h2 {
                font-size: 32px;
            }

            .map-container {
                height: 500px;
                 margin-bottom: 40px;
            }
        }

        @media (max-width: 640px) {
            .hero-header {
                flex-direction: column;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;

            }

            .barangay-cards {
                grid-template-columns: 1fr;
                 padding: 200px 20px;
            }

            .track-form {
                flex-direction: column;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .map-container {
              height: 400px;
        grid-template-columns: 1fr;
        margin-bottom: 30px;
            }
            .summary-panel {
        height: auto;
    }
        } {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .legend-items {
                flex-direction: column;
                gap: 8px;
            }
        }

        /* Leaflet Popup Customization */
        .leaflet-popup-content-wrapper {
            padding: 0;
            overflow: hidden;
        }

        .leaflet-popup-content {
            margin: 0;
            max-height: 500px;
            overflow-y: auto;
        }

        /* Custom scrollbar for popup */
        .leaflet-popup-content::-webkit-scrollbar {
            width: 6px;
        }

        .leaflet-popup-content::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .leaflet-popup-content::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .leaflet-popup-content::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Modal Animation */
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }

        /* ========================================
           DONATION MODAL STYLES
           ======================================== */

        /* Modal Backdrop */
        #donationModal {
            backdrop-filter: blur(8px);
            animation: fadeIn 0.3s ease-out;
        }

        /* Modal Content */
        #donationModal .bg-white {
            max-height: 95vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        /* Form Inputs */
        #donationModal input[type="text"],
        #donationModal input[type="email"],
        #donationModal input[type="tel"],
        #donationModal input[type="number"],
        #donationModal select {
            transition: all 0.2s ease;
            font-size: 14px;
        }

        #donationModal input:focus,
        #donationModal select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        #donationModal input::placeholder {
            color: #9ca3af;
        }

        /* Payment Method Options */
        .payment-option {
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .payment-option:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        .payment-option.selected {
            border-width: 3px !important;
            box-shadow: 0 8px 24px rgba(59, 130, 246, 0.2) !important;
            transform: scale(1.05);
        }

        .payment-option.selected::after {
            content: '✓';
            position: absolute;
            top: 4px;
            right: 4px;
            background: #3b82f6;
            color: white;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        /* Anonymous Toggle */
        #anonymousCheckbox {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        #anonymousCheckbox:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }

        /* Anonymous Notice */
        #anonymousNotice {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Submit Button */
        #donationModal button[type="submit"] {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        #donationModal button[type="submit"]:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(37, 99, 235, 0.3);
        }

        #donationModal button[type="submit"]:active:not(:disabled) {
            transform: translateY(0);
        }

        #donationModal button[type="submit"]:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        /* Cancel Button */
        #donationModal button[type="button"] {
            transition: all 0.2s ease;
        }

        #donationModal button[type="button"]:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Labels */
        #donationModal label {
            font-weight: 600;
            color: #374151;
        }

        /* Info Box */
        #donationModal .bg-blue-50 {
            border-left: 4px solid #3b82f6;
            transition: all 0.2s ease;
        }

        #donationModal .bg-blue-50:hover {
            background-color: #dbeafe;
        }

        /* Close Button */
        #donationModal .text-gray-400 {
            transition: all 0.2s ease;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        #donationModal .text-gray-400:hover {
            background-color: #f3f4f6;
            color: #1f2937;
            transform: rotate(90deg);
        }

        /* Amount Input Special Styling */
        #donationModal input[type="number"] {
            font-size: 18px;
            font-weight: 600;
            color: #059669;
        }

        /* Select Dropdown */
        #donationModal select {
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        /* Payment Method Icons */
        .payment-option svg {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Modal Header */
        #donationModal h2 {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Scrollbar Styling for Modal */
        #donationModal .bg-white::-webkit-scrollbar {
            width: 8px;
        }

        #donationModal .bg-white::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        #donationModal .bg-white::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        #donationModal .bg-white::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Responsive Modal */
        @media (max-width: 640px) {
            #donationModal .bg-white {
                margin: 1rem;
                max-width: calc(100% - 2rem);
                padding: 1.5rem;
            }

            .payment-option {
                padding: 0.75rem;
            }

            #donationModal h2 {
                font-size: 1.25rem;
            }
        }
    </style>
</head>
<body>

    {{-- Success/Error Notification Banner --}}
    @if(session('success') || session('error') || session('info'))
        <div id="notificationBanner" class="fixed top-0 left-0 right-0 z-50 animate-slideDown">
            @if(session('success'))
                <div class="bg-green-500 text-white px-6 py-4 shadow-lg">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-check-circle text-2xl"></i>
                            <div>
                                <h4 class="font-bold text-lg">{{ session('success') }}</h4>
                                @if(session('tracking_code'))
                                    <p class="text-sm mt-1">
                                        Your Tracking Code: <span class="font-mono font-bold text-lg">{{ session('tracking_code') }}</span>
                                    </p>
                                    <p class="text-xs mt-1 opacity-90">
                                        Save this code to track your donation on the blockchain!
                                    </p>
                                @endif
                            </div>
                        </div>
                        <button onclick="closeBanner()" class="text-white hover:text-gray-200 text-2xl font-bold ml-4">×</button>
                    </div>
                </div>
            @elseif(session('error'))
                <div class="bg-red-500 text-white px-6 py-4 shadow-lg">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-exclamation-circle text-2xl"></i>
                            <div>
                                <h4 class="font-bold">{{ session('error') }}</h4>
                            </div>
                        </div>
                        <button onclick="closeBanner()" class="text-white hover:text-gray-200 text-2xl font-bold ml-4">×</button>
                    </div>
                </div>
            @elseif(session('info'))
                <div class="bg-blue-500 text-white px-6 py-4 shadow-lg">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-info-circle text-2xl"></i>
                            <div>
                                <h4 class="font-bold">{{ session('info') }}</h4>
                            </div>
                        </div>
                        <button onclick="closeBanner()" class="text-white hover:text-gray-200 text-2xl font-bold ml-4">×</button>
                    </div>
                </div>
            @endif
        </div>

        <style>
            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                    opacity: 0;
                }
                to {
                    transform: translateY(0);
                    opacity: 1;
                }
            }

            .animate-slideDown {
                animation: slideDown 0.4s ease-out;
            }
        </style>

        <script>
            function closeBanner() {
                const banner = document.getElementById('notificationBanner');
                banner.style.animation = 'slideUp 0.3s ease-in';
                setTimeout(() => {
                    banner.remove();
                }, 300);
            }

            // Auto-close after 10 seconds
            setTimeout(() => {
                const banner = document.getElementById('notificationBanner');
                if (banner) {
                    closeBanner();
                }
            }, 10000);
        </script>
    @endif

    {{-- Hero Section --}}
    <section class="hero-section">
        <div class="hero-header">
            <div class="hero-logo">
                <div class="logo-icon">🛡️</div>
                <div class="logo-text">
                    <h1>BayanihanCebu</h1>
                    <p>Philippines Disaster Relief</p>
                </div>
            </div>
            <a href="{{ route('login') }}" class="sign-in-btn">Sign In</a>
        </div>

        <div class="hero-content">
            <div class="hero-main">
                <div class="hero-text">
                    <h2>Transparent Disaster Relief for Cebu</h2>
                    <p>Every donation is tracked on the blockchain. Every peso reaches those in need. Join us in building a more transparent and efficient disaster relief system.</p>
                    <div class="hero-buttons">
                        <a href="#donate" class="btn btn-donate">
                            ❤️ Donate Now
                        </a>
                        <a href="#track" class="btn btn-track">
                            🔍 Track Donation
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1559027615-cd4628902d4a?w=600&h=400&fit=crop" alt="Helping Hands">
                    <div class="blockchain-badge">
                        ✓ Blockchain Verified
                    </div>
                </div>
            </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="heroTotalDonations">₱0</div>
                    <div class="stat-label">Total Donations</div>
                    <div class="verified-badge">✓ Verified</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroAffectedFamilies">0</div>
                    <div class="stat-label">Families Affected</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroVerifiedTransactions">0</div>
                    <div class="stat-label">Verified Transactions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="heroActiveFundraisers">0</div>
                    <div class="stat-label">Active Fundraisers</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Map Section --}}
    <section class="map-section" id="map">
        <div class="section-header">
            <h2>Live Disaster Map of Cebu</h2>
            <p>Real-time status of barangays across Cebu City</p>
        </div>

        {{-- Legend removed from top - now only showing in map overlay --}}

        {{-- Map Container with Summary Panel (Version 1 Style) --}}
        <div class="map-container">
            {{-- Interactive Map --}}
            <div class="map-wrapper">
                <div id="barangayMap"></div>

                {{-- Map Legend Overlay - Updated to match urgency levels --}}
                <div class="map-legend-overlay">
                    <div class="legend-title">📍 Urgency Levels</div>
                    <div class="legend-items">
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #3b82f6;"></span>
                            <span>Low</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #f59e0b;"></span>
                            <span>Medium</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #f97316;"></span>
                            <span>High</span>
                        </div>
                        <div class="legend-item">
                            <span class="legend-dot" style="background: #ef4444;"></span>
                            <span>Critical</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary Panel --}}
            <div class="summary-panel">

                {{-- City Statistics --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">📊</div>
                        City Overview
                    </div>
                    <div class="stats-grid-summary">
                        <div class="stat-box">
                            <div class="stat-number" id="totalDonations">₱0</div>
                            <div class="stat-label">Total Donations</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="affectedFamilies">0</div>
                            <div class="stat-label">Affected Families</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="affectedBarangays">0</div>
                            <div class="stat-label">Barangays Affected</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-number" id="totalDonors">0</div>
                            <div class="stat-label">Donors</div>
                        </div>
                    </div>
                </div>

                {{-- Affected Barangays List --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">⚠️</div>
                        Barangays Needing Help
                    </div>
                    <div class="barangay-list" id="barangayList">
                        <div style="text-align: center; padding: 20px; color: #6b7280;">
                            <i class="fas fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="summary-card">
                    <div class="card-title">
                        <div class="card-icon">⚡</div>
                        Quick Actions
                    </div>
                    <div class="quick-actions">
                        <a href="#donate" class="action-btn action-btn-primary">
                            ❤️ Make a Donation
                        </a>
                        <a href="#track" class="action-btn action-btn-secondary">
                            🔍 Track My Donation
                        </a>
                    </div>
                </div>

            </div>
        </div>

        {{-- Barangay Cards BELOW the Map --}}
        <div class="barangay-cards">
            @foreach($barangays as $barangay)
                <div class="barangay-card">
                    <div class="barangay-header">
                        <div class="barangay-name">📍 {{ $barangay->name }}</div>
                        <div class="status-badge {{ $barangay->disaster_status }}">
                            {{ ucfirst($barangay->disaster_status) }}
                        </div>
                    </div>

                    @if($barangay->disaster_status === 'safe')
                        <div class="barangay-info">All clear - no active disasters</div>
                    @else
                        {{-- Disaster Type --}}
                        @if($barangay->disaster_type)
                            <div class="barangay-info" style="margin-bottom: 12px;">
                                <strong>Type:</strong>
                                @php
                                    $disasterIcons = [
                                        'flood' => '🌊',
                                        'fire' => '🔥',
                                        'earthquake' => '🏚️',
                                        'typhoon' => '🌀',
                                        'landslide' => '⛰️',
                                        'other' => '❓'
                                    ];
                                @endphp
                                {{ $disasterIcons[$barangay->disaster_type] ?? '' }} {{ ucfirst($barangay->disaster_type) }}
                            </div>
                        @endif

                        {{-- Stats --}}
                        <div class="barangay-stats">
                            <div class="stat-item">
                                <div class="stat-item-label">Affected Families</div>
                                <div class="stat-item-value">{{ $barangay->affected_families }}</div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-item-label">Donations Received</div>
                                <div class="stat-item-value">₱{{ number_format($barangay->total_raised, 0) }}</div>
                            </div>
                        </div>

                        {{-- Resource Needs Section --}}
                        @if($barangay->resourceNeeds->where('status', '!=', 'fulfilled')->count() > 0)
                            <div class="urgent-needs">
                                <div class="urgent-needs-label">Resource Needs:</div>
                                <div class="needs-tags">
                                    @foreach($barangay->resourceNeeds->where('status', '!=', 'fulfilled')->unique('category') as $need)
                                        <span class="need-tag">{{ ucfirst($need->category) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <button class="donate-btn" onclick="openDonationModal('{{ $barangay->barangay_id }}')">
                            Donate to {{ $barangay->name }}
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </section>


{{-- Track Donation Section --}}
<section class="track-section" id="track">
    <div class="track-container">
        <h2>Track Your Donation</h2>
        <p>Enter your tracking code to see blockchain verification and how your donation is being used</p>
        <form class="track-form" action="{{ route('donation.track') }}" method="POST">
            @csrf
            <input
                type="text"
                name="tracking_code"
                class="track-input"
                placeholder="Enter Tracking Code (e.g., CC002-2025-00001)"
                required
            >
            <button type="submit" class="track-btn">
                🔍 Track
            </button>
        </form>
    </div>
</section>

    {{-- Trust Section --}}
    <section class="trust-section">
        <div class="trust-container">
            <div class="trust-header">
                <h2>Why Trust BayanihanCebu?</h2>
            </div>
            <div class="trust-grid">
                <div class="trust-card">
                    <div class="trust-icon">
                        🛡️
                    </div>
                    <h3>Blockchain Verified</h3>
                    <p>Every transaction is recorded on the Lisk blockchain for complete transparency</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon icon-users">
                        👥
                    </div>
                    <h3>Direct to Barangays</h3>
                    <p>Donations go directly to affected communities, managed by local BDRRMC officers</p>
                </div>
                <div class="trust-card">
                    <div class="trust-icon icon-chart">
                        📊
                    </div>
                    <h3>Real-Time Tracking</h3>
                    <p>See exactly how your donation is being used with live updates and receipts</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Leaflet JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>

    <script>
        // Initialize map centered on Cebu City
        const map = L.map('barangayMap').setView([10.3157, 123.8854], 12);

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19,
        }).addTo(map);

        // Store markers for later reference
        const markers = {};
        let currentZoomLevel = 12;

        // Track current map zoom
        map.on('zoomend', function() {
            currentZoomLevel = map.getZoom();
            updateMarkerSizes();
        });

        // Custom marker icon function - Standardized pins by urgency only
        function createCustomIcon(status, urgencyLevel = 'low', resourceCount = 0, zoomLevel = 12) {
            // Urgency-based color coding
            const urgencyColors = {
                critical: '#ef4444',  // Red - Emergency/Critical
                high: '#f97316',      // Orange - High
                medium: '#f59e0b',    // Amber - Medium/Warning
                low: '#3b82f6'        // Blue - Low
            };

            const pinColor = urgencyColors[urgencyLevel] || urgencyColors['low'];

            // Standardized base pin sizes - ONLY based on urgency level
            const baseSizes = {
                critical: 40,  // Largest
                high: 32,      // Large
                medium: 26,    // Medium
                low: 22        // Small
            };

            const baseSize = baseSizes[urgencyLevel] || 22;

            // Zoom-responsive scaling
            let zoomMultiplier = 1.0;
            if (zoomLevel >= 18) {
                zoomMultiplier = 1.8;
            } else if (zoomLevel >= 15) {
                zoomMultiplier = 1.4;
            } else if (zoomLevel >= 12) {
                zoomMultiplier = 1.15;
            }

            const finalSize = Math.round(baseSize * zoomMultiplier);
            const iconAnchor = [finalSize / 2, finalSize];

            return L.divIcon({
                className: 'custom-marker-icon',
                html: `<div style="
                    background-color: ${pinColor};
                    width: ${finalSize}px;
                    height: ${finalSize}px;
                    border-radius: 50% 50% 50% 0;
                    transform: rotate(-45deg);
                    border: 3px solid white;
                    box-shadow: 0 4px 15px rgba(0,0,0,0.5);
                    transition: all 0.3s ease;
                    cursor: pointer;
                "></div>`,
                iconSize: [finalSize, finalSize],
                iconAnchor: iconAnchor,
                popupAnchor: [0, -iconAnchor[1]]
            });
        }

        // Update marker sizes when zoom changes
        function updateMarkerSizes() {
            Object.keys(markers).forEach(barangayId => {
                const markerData = markers[barangayId];
                if (markerData && markerData.marker) {
                    const newIcon = createCustomIcon(
                        markerData.status,
                        markerData.urgency,
                        markerData.resourceCount,
                        currentZoomLevel
                    );
                    markerData.marker.setIcon(newIcon);
                }
            });
        }

        // Format currency
        function formatCurrency(amount) {
            return '₱' + parseFloat(amount).toLocaleString('en-PH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        }

        // Format number
        function formatNumber(num) {
            return parseInt(num).toLocaleString('en-PH');
        }

        // Disaster type icons
        const disasterIcons = {
            'flood': '🌊',
            'fire': '🔥',
            'earthquake': '🏚️',
            'typhoon': '🌀',
            'landslide': '⛰️',
            'other': '❓'
        };

        // Get urgency badge color
        function getUrgencyColor(urgency) {
            const colors = {
                'critical': '#ef4444',
                'high': '#f97316',
                'medium': '#f59e0b',
                'low': '#3b82f6'
            };
            return colors[urgency] || colors['low'];
        }

        // Get highest urgency level from resource needs
        function getHighestUrgency(resourceNeeds) {
            if (!resourceNeeds || resourceNeeds.length === 0) return 'low';

            const urgencyOrder = { critical: 4, high: 3, medium: 2, low: 1 };
            let highest = 'low';
            let highestValue = 0;

            resourceNeeds.forEach(need => {
                const value = urgencyOrder[need.urgency] || 0;
                if (value > highestValue) {
                    highestValue = value;
                    highest = need.urgency;
                }
            });

            return highest;
        }

        // Load and display map data
        function loadMapData() {
            fetch('/api/barangays')
                .then(response => response.json())
                .then(data => {
                    // Clear existing markers
                    Object.values(markers).forEach(markerData => {
                        if (markerData && markerData.marker) {
                            map.removeLayer(markerData.marker);
                        }
                    });

                    // Reset markers object
                    for (let key in markers) {
                        delete markers[key];
                    }

                    let totalDonations = 0;
                    let totalFamilies = 0;
                    let affectedCount = 0;

                    // Add markers for each barangay - SKIP SAFE STATUS
                    data.forEach(barangay => {
                        // Skip safe barangays - only show barangays with active needs
                        if (barangay.status === 'safe') {
                            return;
                        }

                        // Get resource needs data
                        const resourceNeeds = barangay.resource_needs || [];
                        const resourceCount = barangay.resource_needs_count || 0;
                        const highestUrgency = barangay.highest_urgency || 'low';

                        const marker = L.marker([barangay.latitude, barangay.longitude], {
                            icon: createCustomIcon(
                                barangay.status,
                                highestUrgency,
                                resourceCount,
                                currentZoomLevel
                            )
                        }).addTo(map);

                        // Store marker reference with metadata
                        markers[barangay.id] = {
                            marker: marker,
                            status: barangay.status,
                            urgency: highestUrgency,
                            resourceCount: resourceCount
                        };

                        // Build Resource Needs HTML with enhanced details
                        let resourceNeedsHtml = '';
                        if (resourceNeeds.length > 0) {
                            resourceNeedsHtml = `
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                                    <div style="font-weight: 700; font-size: 15px; margin-bottom: 10px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                                        📋 Resource Needs
                                        <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                                            ${resourceCount}
                                        </span>
                                    </div>
                            `;

                            resourceNeeds.forEach(need => {
                                const urgencyColor = getUrgencyColor(need.urgency);
                                resourceNeedsHtml += `
                                    <div style="background: #f9fafb; border-left: 4px solid ${urgencyColor}; padding: 10px; margin-bottom: 8px; border-radius: 4px;">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                                            <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                                ${need.category ? need.category.toUpperCase() : 'General'}
                                            </div>
                                            <div style="background: ${urgencyColor}; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                                ${need.urgency}
                                            </div>
                                        </div>
                                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                                            ${need.description || 'No description'}
                                        </div>
                                        <div style="font-size: 12px; color: #1f2937; margin-bottom: 4px;">
                                            <strong>Quantity:</strong> ${need.quantity}
                                        </div>
                                        <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-top: 4px;">
                                            <span>Status: <span style="text-transform: uppercase; font-weight: 600;">${need.status}</span></span>
                                            ${need.created_at ? `<span>Added: ${need.created_at}</span>` : ''}
                                        </div>
                                    </div>
                                `;
                            });

                            resourceNeedsHtml += '</div>';
                        }

                        // Google Maps link
                        const googleMapsLink = `https://www.google.com/maps?q=${barangay.latitude},${barangay.longitude}`;

                        // Create enhanced popup content
                        const popupContent = `
                            <div style="padding: 15px; min-width: 320px; max-width: 400px;">
                                <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; margin: -15px -15px 15px -15px; padding: 15px; border-radius: 8px 8px 0 0;">
                                    <h3 style="margin: 0 0 5px 0; font-size: 18px; font-weight: 700;">${barangay.name}</h3>
                                    <p style="margin: 0; font-size: 13px; opacity: 0.9;">${barangay.city || 'Cebu City'}</p>
                                </div>

                                <div style="padding: 0 5px;">
                                    <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                        <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Status</div>
                                        <div style="display: inline-block; background: ${barangay.status === 'emergency' ? '#fee2e2' : barangay.status === 'critical' ? '#fed7aa' : barangay.status === 'warning' ? '#fef3c7' : '#d1fae5'}; color: ${barangay.status === 'emergency' ? '#991b1b' : barangay.status === 'critical' ? '#9a3412' : barangay.status === 'warning' ? '#92400e' : '#065f46'}; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                            ${barangay.status}
                                        </div>
                                    </div>

                                    ${barangay.disaster_type ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Disaster Type</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                                ${disasterIcons[barangay.disaster_type] || ''} ${barangay.disaster_type.charAt(0).toUpperCase() + barangay.disaster_type.slice(1)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${barangay.affected_families ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Affected Families</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                                ${formatNumber(barangay.affected_families)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${barangay.total_raised ? `
                                        <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                            <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Donations Received</div>
                                            <div style="font-size: 14px; font-weight: 600; color: #10b981;">
                                                ${formatCurrency(barangay.total_raised)}
                                            </div>
                                        </div>
                                    ` : ''}

                                    ${resourceNeedsHtml}

                                    <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                                        <a href="${googleMapsLink}" target="_blank" style="display: block; text-align: center; background: #3b82f6; color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; margin-bottom: 8px;">
                                            📍 View on Google Maps
                                        </a>
                                        ${barangay.status !== 'safe' && resourceCount > 0 ? `
                                            <button onclick="openDonationModal('${barangay.barangay_id}')" style="width: 100%; background: #ef4444; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                                                ❤️ Donate to ${barangay.name}
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                            </div>
                        `;

                        marker.bindPopup(popupContent, {
                            maxWidth: 420,
                            maxHeight: 500,
                            className: 'custom-leaflet-popup'
                        });

                        // Click only - no hover interaction

                        // Update statistics
                        if (barangay.status !== 'safe') {
                            totalDonations += parseFloat(barangay.total_raised) || 0;
                            totalFamilies += parseInt(barangay.affected_families) || 0;
                            affectedCount++;
                        }
                    });

                    // Update summary statistics in map section
                    document.getElementById('totalDonations').textContent = formatCurrency(totalDonations);
                    document.getElementById('affectedFamilies').textContent = formatNumber(totalFamilies);
                    document.getElementById('affectedBarangays').textContent = affectedCount;

                    // Populate barangay list (only affected ones with resource needs)
                    const barangayList = document.getElementById('barangayList');
                    const affectedBarangays = data.filter(b => b.status !== 'safe');

                    if (affectedBarangays.length === 0) {
                        barangayList.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #10b981;">
                                <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                                <div style="font-weight: 600;">All Clear</div>
                                <div style="font-size: 13px; color: #6b7280;">No barangays currently need assistance</div>
                            </div>
                        `;
                    } else {
                        barangayList.innerHTML = affectedBarangays
                            .sort((a, b) => {
                                const urgencyOrder = { critical: 4, high: 3, medium: 2, low: 1 };
                                const statusOrder = { emergency: 0, critical: 1, warning: 2 };

                                // Sort by highest urgency first, then by status
                                const aUrgency = a.highest_urgency || 'low';
                                const bUrgency = b.highest_urgency || 'low';

                                if (urgencyOrder[aUrgency] !== urgencyOrder[bUrgency]) {
                                    return urgencyOrder[bUrgency] - urgencyOrder[aUrgency];
                                }

                                return statusOrder[a.status] - statusOrder[b.status];
                            })
                            .map(barangay => {
                                const highestUrgency = barangay.highest_urgency || 'low';
                                const urgencyColor = getUrgencyColor(highestUrgency);
                                const needsCount = barangay.resource_needs_count || 0;

                                return `
                                    <div class="barangay-item" onclick="focusBarangay('${barangay.id}')" style="position: relative; cursor: pointer;">
                                        ${needsCount > 0 ? `
                                            <div style="position: absolute; top: -5px; right: -5px; background: ${urgencyColor}; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                                ${needsCount}
                                            </div>
                                        ` : ''}
                                        <div class="barangay-info-summary">
                                            <div class="barangay-name-summary">📍 ${barangay.name}</div>
                                            <div class="barangay-meta">
                                                ${barangay.affected_families || 0} families • ${formatCurrency(barangay.total_raised || 0)} raised
                                            </div>
                                            ${needsCount > 0 ? `
                                                <div style="font-size: 11px; color: ${urgencyColor}; font-weight: 600; margin-top: 4px;">
                                                    ${needsCount} resource need${needsCount > 1 ? 's' : ''} • ${highestUrgency.toUpperCase()} urgency
                                                </div>
                                            ` : ''}
                                        </div>
                                        <span class="status-badge ${barangay.status}">
                                            ${barangay.status.toUpperCase()}
                                        </span>
                                    </div>
                                `;
                            }).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading map data:', error);
                    const barangayList = document.getElementById('barangayList');
                    if (barangayList) {
                        barangayList.innerHTML = `
                            <div style="text-align: center; padding: 20px; color: #ef4444;">
                                <div style="font-size: 48px; margin-bottom: 10px;">⚠️</div>
                                <div style="font-weight: 600;">Error Loading Data</div>
                                <div style="font-size: 13px; color: #6b7280;">Please refresh the page</div>
                            </div>
                        `;
                    }
                });
        }

        // Function to focus on a specific barangay
        function focusBarangay(barangayId) {
            if (markers[barangayId] && markers[barangayId].marker) {
                map.setView(markers[barangayId].marker.getLatLng(), 15);
                markers[barangayId].marker.openPopup();
            }
        }

        // Load hero statistics from API
        function loadHeroStatistics() {
            fetch('/api/statistics')
                .then(response => response.json())
                .then(stats => {
                    document.getElementById('heroTotalDonations').textContent = formatCurrency(stats.total_donations || 0);
                    document.getElementById('heroAffectedFamilies').textContent = formatNumber(stats.total_affected_families || 0);
                    document.getElementById('heroVerifiedTransactions').textContent = formatNumber(stats.total_donors || 0);
                    document.getElementById('heroActiveFundraisers').textContent = formatNumber(stats.barangays_affected || 0);
                })
                .catch(error => {
                    console.error('Error loading hero statistics:', error);
                });
        }

        // Initialize map and data on page load
        loadMapData();
        loadHeroStatistics();

        // Auto-refresh map data every 30 seconds for real-time updates
        setInterval(() => {
            loadMapData();
            loadHeroStatistics();
        }, 30000);

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <!-- Donation Modal -->
    <!-- Donation Modal -->
    <div id="donationModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg p-8 relative animate-fadeIn">

            <!-- Header -->
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                    💖 Make a Donation
                </h2>
                <button onclick="closeDonationModal()" class="text-gray-400 hover:text-gray-600 text-xl font-bold">
                    ×
                </button>
            </div>

            <p class="text-sm text-gray-500 mb-6">
                Support a barangay in need. Your donation will be processed securely via PayMongo.
            </p>

            <!-- Donation Form -->
            <form id="donationForm" class="space-y-4">
                @csrf

                <!-- Barangay Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Barangay</label>
                    <select name="barangay_id" id="barangaySelect" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose a barangay...</option>
                        @foreach($barangays as $b)
                            <option value="{{ $b->barangay_id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Amount -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PHP)</label>
                    <input type="number" name="amount" min="1" required placeholder="1000"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Donation Type (Hidden - Only Monetary Accepted) -->
                <input type="hidden" name="donation_type" value="monetary">

                <!-- Personal Info -->
                <div class="pt-3 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-sm font-semibold text-gray-700">Donor Details</h3>

                        <!-- Anonymous Toggle -->
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" id="anonymousCheckbox" class="w-4 h-4 text-blue-600 rounded focus:ring-2 focus:ring-blue-500">
                            <span class="text-xs font-medium text-gray-600">Donate Anonymously</span>
                        </label>
                    </div>

                    <div id="personalInfoSection">
                        <input type="text" name="donor_name" id="donorNameInput" placeholder="Full Name *" required
                            value="{{ session('user_name') ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-blue-500">

                        <input type="email" name="donor_email" placeholder="Email (optional for tracking)"
                            value="{{ session('email') ?? '' }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mb-2 focus:ring-2 focus:ring-blue-500">

                        <input type="tel" name="donor_phone" placeholder="Phone (optional)"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div id="anonymousNotice" class="hidden mt-2 p-2 bg-gray-50 border border-gray-200 rounded-lg">
                        <p class="text-xs text-gray-600">
                            <i class="fas fa-user-secret mr-1"></i>
                            Your donation will be listed as "Anonymous Donor". You'll still receive a tracking code via your payment method.
                        </p>
                    </div>
                </div>

                <!-- Payment Method (Icons) -->
                <div class="pt-4 border-t border-gray-100">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Payment Method</h3>

                    <div class="grid grid-cols-3 gap-3">
                        <!-- GCash -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-blue-500 hover:bg-blue-50 transition"
                            data-method="gcash" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center">
                                <svg viewBox="0 0 48 48" class="h-full w-full">
                                    <rect fill="#2E7CF6" width="48" height="48" rx="8"/>
                                    <text x="24" y="32" font-family="Arial, sans-serif" font-size="20" font-weight="bold" fill="white" text-anchor="middle">G</text>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">GCash</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>

                        <!-- GrabPay -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-green-500 hover:bg-green-50 transition"
                            data-method="grabpay" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center bg-green-600 rounded-lg">
                                <span class="text-white font-bold text-lg">Grab</span>
                            </div>
                            <span class="text-xs font-medium text-gray-700">GrabPay</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>

                        <!-- PayMaya -->
                        <div class="payment-option cursor-pointer border-2 border-gray-300 rounded-xl p-3 flex flex-col items-center hover:border-purple-500 hover:bg-purple-50 transition"
                            data-method="paymaya" onclick="selectPaymentMethod(this)">
                            <div class="h-10 w-10 mb-2 flex items-center justify-center">
                                <svg viewBox="0 0 48 48" class="h-full w-full">
                                    <defs>
                                        <linearGradient id="mayaGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#00D632;stop-opacity:1" />
                                            <stop offset="100%" style="stop-color:#00B528;stop-opacity:1" />
                                        </linearGradient>
                                    </defs>
                                    <rect fill="url(#mayaGradient)" width="48" height="48" rx="8"/>
                                    <text x="24" y="32" font-family="Arial, sans-serif" font-size="18" font-weight="bold" fill="white" text-anchor="middle">Maya</text>
                                </svg>
                            </div>
                            <span class="text-xs font-medium text-gray-700">Maya</span>
                            <span class="text-[10px] text-gray-500 mt-1">E-Wallet</span>
                        </div>
                    </div>

                    <input type="hidden" name="payment_method" id="paymentMethod" required>

                    <!-- Payment method error -->
                    <p id="paymentMethodError" class="text-red-500 text-xs mt-2 hidden">
                        <i class="fas fa-exclamation-circle mr-1"></i>Please select a payment method
                    </p>
                </div>

                <!-- Payment Info -->
                <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-3">
                    <p class="text-xs text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Secure Payment:</strong> You'll be redirected to PayMongo's secure checkout page.
                        Your donation will be recorded on the Lisk blockchain for transparency.
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex gap-3 pt-5">
                    <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-lg font-semibold text-sm transition">
                        Proceed to Payment
                    </button>
                    <button type="button" onclick="closeDonationModal()"
                        class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2.5 rounded-lg font-semibold text-sm transition">
                        Cancel
                    </button>
                </div>
            </form>

            <!-- Success Message -->
            <div id="successMessage" class="hidden mt-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <h4 class="font-bold text-green-800 mb-1">✅ Donation Successful!</h4>
                <p class="text-sm text-green-700 mb-1">Your tracking code:
                    <span id="trackingCodeDisplay" class="font-mono font-bold"></span>
                </p>
                <p class="text-xs text-green-600">Blockchain verification in progress...</p>
                <button onclick="window.location.href='#donations'"
                    class="w-full mt-3 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg font-semibold text-sm transition">
                    View My Donations
                </button>
            </div>

        </div>
    </div>

    <script>
    // Load barangays into modal select
    async function loadBarangaysForModal() {
        try {
            const response = await fetch('/api/barangays');
            const barangays = await response.json();
            const select = document.getElementById('barangaySelect');
            select.innerHTML = '<option value="">Choose a barangay...</option>';
            barangays.forEach(b => {
                // API returns 'id' field which contains barangay_id value
                select.innerHTML += `<option value="${b.id}">${b.name}</option>`;
            });
        } catch (error) {
            console.error('Failed to load barangays:', error);
        }
    }

    // Payment method selection
    function selectPaymentMethod(element) {
        // Remove 'selected' class from all payment options
        document.querySelectorAll('.payment-option').forEach(opt => {
            opt.classList.remove('selected');
        });

        // Add 'selected' class to clicked option
        element.classList.add('selected');

        // Get method and convert to API format
        const method = element.dataset.method;
        const methodMap = {
            'gcash': 'gcash',
            'grabpay': 'grab_pay',
            'paymaya': 'paymaya'
        };

        // Set the hidden input value
        document.getElementById('paymentMethod').value = methodMap[method] || method;

        // Hide error message
        const errorEl = document.getElementById('paymentMethodError');
        if (errorEl) {
            errorEl.classList.add('hidden');
        }
    }

    // Copy tracking code
    function copyTrackingCode() {
        const trackingCode = document.getElementById('trackingCodeDisplay').textContent;
        const copyBtn = document.getElementById('copyBtn');

        navigator.clipboard.writeText(trackingCode).then(() => {
            const originalHTML = copyBtn.innerHTML;
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            copyBtn.classList.add('bg-green-700');

            setTimeout(() => {
                copyBtn.innerHTML = originalHTML;
                copyBtn.classList.remove('bg-green-700');
            }, 2000);
        });
    }

    // Show/hide personal info based on anonymous checkbox
    document.addEventListener('DOMContentLoaded', function() {
        const anonymousCheckbox = document.getElementById('anonymousCheckbox');
        if (anonymousCheckbox) {
            anonymousCheckbox.addEventListener('change', function(e) {
                const personalInfo = document.getElementById('personalInfoSection');
                const anonymousNotice = document.getElementById('anonymousNotice');
                const nameInput = document.getElementById('donorNameInput');

                if (e.target.checked) {
                    personalInfo.style.display = 'none';
                    anonymousNotice.classList.remove('hidden');
                    nameInput.removeAttribute('required');
                    // Set anonymous donor name
                    nameInput.value = 'Anonymous Donor';
                } else {
                    personalInfo.style.display = 'block';
                    anonymousNotice.classList.add('hidden');
                    nameInput.setAttribute('required', 'required');
                    nameInput.value = '{{ session('user_name') ?? '' }}';
                }
            });
        }
    });

    // Handle form submission
    document.getElementById('donationForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // Validate payment method
        const paymentMethod = document.getElementById('paymentMethod').value;
        if (!paymentMethod) {
            document.getElementById('paymentMethodError').classList.remove('hidden');
            return;
        }

        const formData = new FormData(e.target);

        // Safe check for anonymous checkbox
        const anonymousCheckbox = document.getElementById('anonymousCheckbox');
        const isAnonymous = anonymousCheckbox ? anonymousCheckbox.checked : false;

        const data = {
            barangay_id: formData.get('barangay_id'),
            amount: parseFloat(formData.get('amount')),
            donation_type: formData.get('donation_type'),
            donor_name: isAnonymous ? 'Anonymous Donor' : formData.get('donor_name'),
            donor_email: isAnonymous ? null : (formData.get('donor_email') || null),
            donor_phone: isAnonymous ? null : (formData.get('donor_phone') || null),
            payment_method: paymentMethod, // Already converted to grab_pay in selectPaymentMethod
            is_anonymous: isAnonymous
        };

        // Validation
        if (!data.barangay_id) {
            alert('Please select a barangay');
            return;
        }

        if (data.amount < 100) {
            alert('Minimum donation amount is PHP 100');
            return;
        }

        if (!isAnonymous && !data.donor_name) {
            alert('Please enter your name or check "Donate Anonymously"');
            return;
        }

        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

        try {
            // Use the new public anonymous donation endpoint
            const response = await fetch('/donations/create-payment-public', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success && result.data && result.data.checkout_url) {
                // Store tracking code for when user returns
                sessionStorage.setItem('pending_tracking_code', result.data.tracking_code);

                // Redirect to PayMongo checkout
                window.location.href = result.data.checkout_url;
            } else {
                alert('Error: ' + (result.message || 'Something went wrong'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        } catch (error) {
            console.error('Donation error:', error);
            alert('Failed to submit donation. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    function openDonationModal(barangayId = null) {
        const modal = document.getElementById('donationModal');
        modal.classList.remove('hidden');
        modal.style.display = 'flex';

        // Load barangays if not loaded
        loadBarangaysForModal();

        // Pre-select barangay if provided
        if (barangayId) {
            setTimeout(() => {
                document.getElementById('barangaySelect').value = barangayId;
            }, 100);
        }
    }

    function closeDonationModal() {
        const modal = document.getElementById('donationModal');
        modal.classList.add('hidden');
        modal.style.display = 'none';

        // Reset form
        document.getElementById('donationForm').reset();
        document.getElementById('donationForm').style.display = 'block';
        const successMsg = document.getElementById('successMessage');
        successMsg.style.display = 'none';
        successMsg.classList.add('hidden');
    }
    </script>

@include('partials.footer')
</body>
</html>
