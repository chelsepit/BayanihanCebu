<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Verified Transactions - BayanihanCebu</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --success-color: #10b981;
            --purple-color: #9333ea; /* Kept for reference, but replaced by orange */
            /* ADDED: New orange color palette */
            --orange-color: #f97316;       /* Main orange for text */
            --orange-hover-color: #c2410c; /* Darker orange for hover */
            --orange-bg-light: #fff7ed;   /* Light orange for badge backgrounds */
            --orange-border-light: #fed7aa;/* Light orange for badge borders */
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --white: #ffffff;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.6;
            color: var(--gray-800);
            background-color: var(--gray-50);
        }

        a {
            text-decoration: none;
        }

        /* Header Styles */
        .page-header {
            background: linear-gradient(to right, #1d4ed8, #1e3a8a);
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 16px 24px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }

        .header-subtitle {
            color: #bfdbfe;
            font-size: 14px;
            margin: 4px 0 0 0;
        }

        .back-button {
            background: #2563eb;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #1d4ed8;
        }

        .back-button svg {
            margin-right: 8px;
        }

        /* Main Container */
        .main-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 3rem 1rem;
        }

        /* Page Title Section */
        .page-title-section {
            text-align: center;
            margin-bottom: 3rem;
        }

        .icon-wrapper {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 5rem;
            height: 5rem;
            background: linear-gradient(to bottom right, #f3e8ff, #dbeafe);
            border-radius: 9999px;
            margin-bottom: 1.5rem;
        }

        .icon-wrapper svg {
            width: 2.5rem;
            height: 2.5rem;
            color: var(--primary-color);
        }

        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.75rem;
        }

        .page-description {
            font-size: 1.125rem;
            color: var(--gray-600);
            max-width: 42rem;
            margin: 0 auto;
        }

        /* Filter Section */
        .filter-section {
            background: var(--white);
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        .filter-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group.search-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }

        .filter-select, .filter-input {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
            border: 1px solid var(--gray-300);
            border-radius: 0.375rem;
            font-family: inherit;
        }

        .filter-select {
            background: white;
            cursor: pointer;
        }

        .filter-input {
            flex: 1;
        }

        .filter-select:focus, .filter-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .result-count {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--gray-600);
        }

        /* Loading and Error States */
        .loading-container {
            text-align: center;
            padding: 5rem 0;
        }

        .spinner {
            display: inline-block;
            width: 4rem;
            height: 4rem;
            border: 2px solid var(--gray-200);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            margin-top: 1.5rem;
            color: var(--gray-600);
            font-size: 1.125rem;
        }

        .error-container {
            background: #fef2f2;
            border: 1px solid #fecaca;
            padding: 1.5rem;
            border-radius: 0.5rem;
            display: none;
        }

        .error-content {
            display: flex;
            align-items: center;
        }

        .error-icon {
            width: 1.5rem;
            height: 1.5rem;
            color: #ef4444;
            margin-right: 0.75rem;
        }

        .error-text {
            color: #b91c1c;
        }

        /* Table Styles */
        .table-container {
            background: var(--white);
            border-radius: 0.5rem;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
            overflow: hidden;
            display: none;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .donations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .donations-table thead {
            background: var(--gray-100);
            border-bottom: 2px solid var(--gray-300);
        }

        .donations-table th {
            padding: 0.75rem 1.5rem;
            text-align: left;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .donations-table th.text-right {
            text-align: right;
        }

        .donations-table tbody {
            background: var(--white);
        }

        .donations-table tr {
            border-top: 1px solid var(--gray-200);
            transition: background-color 0.2s;
        }

        .donations-table tbody tr:nth-child(even) {
            background-color: var(--gray-50);
        }

        .donations-table tbody tr:hover {
            background: var(--gray-200);
        }

        .donations-table td {
            padding: 1rem 1.5rem;
        }

        /* Table Cell Content */
        .tracking-link {
            font-family: ui-monospace, monospace;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--primary-color);
            transition: color 0.2s;
        }

        .tracking-link:hover {
            color: #1e40af;
        }

        .badge-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.375rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.125rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 1px solid;
        }

        .badge-verified {
            background: #f0fdf4;
            color: #15803d;
            border-color: #bbf7d0;
        }

        .badge-verified svg {
            width: 0.75rem;
            height: 0.75rem;
            margin-right: 0.25rem;
        }

        .badge-type {
            padding: 0.25rem 0.625rem;
            border: 1px solid;
        }

        /* MODIFIED: Changed physical badge to use orange colors */
        .badge-physical {
            background: var(--orange-bg-light);
            color: var(--orange-color);
            border-color: var(--orange-border-light);
        }

        .badge-monetary {
            background: #f0fdf4;
            color: var(--success-color);
            border-color: #bbf7d0;
        }

        .time-ago {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .details-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: 0.25rem;
        }

        .details-description {
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-bottom: 0.5rem;
        }

        .payment-method {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.5rem;
            background: var(--gray-100);
            border-radius: 0.25rem;
            font-size: 0.75rem;
            font-weight: 500;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .donor-info {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .donor-info .label {
            font-weight: 500;
            color: var(--gray-800);
        }

        .blockchain-link {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            font-weight: 500;
            margin-top: 0.5rem;
            transition: color 0.2s;
        }

        /* MODIFIED: Changed .purple to .orange */
        .blockchain-link.orange {
            color: var(--orange-color);
        }

        .blockchain-link.orange:hover {
            color: var(--orange-hover-color);
        }

        .blockchain-link.green {
            color: var(--success-color);
        }

        .blockchain-link.green:hover {
            color: #166534;
        }

        .blockchain-link svg {
            width: 0.75rem;
            height: 0.75rem;
            margin-right: 0.25rem;
        }

        .tx-hash {
            font-size: 0.75rem;
            color: var(--gray-400);
            font-family: ui-monospace, monospace;
            margin-top: 0.25rem;
        }

        .blockchain-badge {
            display: inline-flex;
            align-items: center;
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        /* MODIFIED: Changed .purple to .orange */
        .blockchain-badge.orange {
            color: var(--orange-color);
        }

        .blockchain-badge.green {
            color: var(--success-color);
        }

        .blockchain-badge svg {
            width: 0.875rem;
            height: 0.875rem;
            margin-right: 0.25rem;
        }

        .value-amount {
            font-size: 0.875rem;
            font-weight: 700;
        }

        .value-amount.large {
            font-size: 1.125rem;
        }

        /* MODIFIED: Changed .purple to .orange */
        .value-amount.orange {
            color: var(--orange-color);
        }

        .value-amount.green {
            color: var(--success-color);
        }

        .value-label {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .text-right {
            text-align: right;
        }

        .whitespace-nowrap {
            white-space: nowrap;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 5rem 0;
            background: var(--white);
            border-radius: 0.5rem;
            border: 1px solid var(--gray-200);
            margin-top: 1.5rem;
            display: none;
        }

        .empty-icon {
            width: 4rem;
            height: 4rem;
            color: var(--gray-400);
            margin: 0 auto 1rem;
        }

        .empty-text {
            color: var(--gray-600);
        }

        /* Utility Classes */
        .hidden {
            display: none !important;
        }

        /* Footer Styles */
        .footer {
            background-color: var(--gray-900);
            color: var(--white);
            padding: 3rem 0;
            margin-top: 4rem;
        }

        .footer .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .footer-col h4 {
            color: var(--white);
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .footer-col p {
            color: var(--gray-400);
            font-size: 0.875rem;
            line-height: 1.5;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
        }

        .footer-col ul li {
            margin-bottom: 0.75rem;
        }

        .footer-col ul li a {
            color: var(--gray-400);
            font-size: 0.875rem;
            text-decoration: none;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-col ul li a:hover {
            color: var(--white);
        }

        .contact-info-footer {
            color: var(--gray-400);
            font-size: 0.875rem;
        }

        .contact-info-footer p {
            margin-bottom: 0.5rem;
        }

        /* Footer Styles */
        .footer {
            background-color: #f8fafc;
            padding: 4rem 0 0 0;
            color: #1e293b;
        }

        .footer .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1.5fr;
            gap: 4rem;
            margin-bottom: 3rem;
        }

        .footer-col h4 {
            color: #0f172a;
            font-size: 1.125rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            position: relative;
        }

        .footer-col h4::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: -0.5rem;
            width: 2rem;
            height: 2px;
            background: #2563eb;
        }

        .footer-col p {
            color: #475569;
            line-height: 1.75;
        }

        .footer-col ul {
            list-style: none;
            padding: 0;
        }

        .footer-col ul li {
            margin-bottom: 0.75rem;
        }

        .footer-col ul li a {
            color: #475569;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-col ul li a:hover {
            color: #2563eb;
        }

        .contact-info-footer p {
            margin-bottom: 0.5rem;
            color: #475569;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .social-link {
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            background-color: #f1f5f9;
            color: #475569;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .social-link:hover {
            background-color: #2563eb;
            color: white;
        }

        .footer-bottom-content {
            border-top: 1px solid #e2e8f0;
            padding: 2rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #64748b;
        }

        .footer-bottom-links {
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .footer-bottom-links a {
            color: #64748b;
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-bottom-links a:hover {
            color: #2563eb;
        }

        .footer-bottom-links span {
            color: #cbd5e1;
        }

        .footer-badge {
            background: linear-gradient(90deg, #2563eb 0%, #06b6d4 100%);
            color: white;
            text-align: center;
            padding: 1rem;
            font-weight: 500;
            margin-top: 2rem;
        }

        .footer-badge p {
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .footer-grid {
                grid-template-columns: 1fr 1fr;
                gap: 3rem;
            }
        }

        @media (max-width: 768px) {
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-bottom-content {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .footer-bottom-links {
                justify-content: center;
            }

            .header-content {
                flex-direction: column;
                gap: 1rem;
            }

            .page-title {
                font-size: 1.875rem;
            }

            .filter-container {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-group.search-group {
                width: 100%;
            }

            .donations-table {
                font-size: 0.875rem;
            }

            .donations-table th,
            .donations-table td {
                padding: 0.75rem 0.5rem;
            }
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <header class="page-header">
        <div class="header-container">
            <div class="header-content">
                <div>
                    <h1 class="header-title">BayanihanCebu - Verified Transactions</h1>
                    <p class="header-subtitle">Complete blockchain-verified donation history</p>
                </div>
                <a href="{{ route('home') }}" class="back-button">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                    Back to Home
                </a>
            </div>
        </div>
    </header>

    {{-- Content Wrapper --}}
    <div class="main-container">

        {{-- Page Title --}}
        <div class="page-title-section">
            <div class="icon-wrapper">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="page-title">All Verified Transactions</h1>
            <p class="page-description">Every donation recorded on the Lisk Sepolia blockchain for complete transparency and accountability</p>
        </div>

        {{-- Filters --}}
        <div class="filter-section">
            <div class="filter-container">
                <div class="filter-group">
                    <label class="filter-label">Type:</label>
                    <select id="typeFilter" class="filter-select">
                        <option value="all">All Donations</option>
                        <option value="physical">Non-Monetary Only</option>
                        <option value="online">Monetary Only</option>
                    </select>
                </div>
                <div class="filter-group search-group">
                    <label class="filter-label">Search:</label>
                    <input type="text" id="searchInput" placeholder="Tracking code, donor name..." class="filter-input">
                </div>
                <div class="filter-group">
                    <span id="resultCount" class="result-count"></span>
                </div>
            </div>
        </div>

        {{-- Loading --}}
        <div id="loading" class="loading-container">
            <div class="spinner"></div>
            <p class="loading-text">Loading verified transactions...</p>
        </div>

        {{-- Error --}}
        <div id="error" class="error-container">
            <div class="error-content">
                <svg class="error-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="error-text">Failed to load transactions. Please try again later.</p>
            </div>
        </div>

        {{-- Table --}}
        <div id="donationsTable" class="table-container">
            <div class="table-wrapper">
                <table class="donations-table">
                    <thead>
                        <tr>
                            <th>Tracking Code</th>
                            <th>Type</th>
                            <th>Details</th>
                            <th class="text-right">Value</th>
                        </tr>
                    </thead>
                    <tbody id="donationsTableBody">
                        {{-- Table rows go here --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Empty --}}
        <div id="emptyState" class="empty-state">
            <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            <p class="empty-text">No transactions found matching your filters.</p>
        </div>

    </div>

    {{-- JavaScript --}}
    <script>
        let allDonations = [];
        let filteredDonations = [];

        async function loadAllDonations() {
            try {
                const response = await fetch('/api/donations/recent-verified');
                const data = await response.json();

                if (!data.success) {
                    throw new Error('Failed to load donations');
                }

                allDonations = data.donations;
                filteredDonations = allDonations;
                displayDonations();
            } catch (error) {
                console.error('Error loading donations:', error);
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('error').style.display = 'block';
            }
        }

        function applyFilters() {
            const typeFilter = document.getElementById('typeFilter').value;
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();

            filteredDonations = allDonations.filter(donation => {
                // Type filter
                if (typeFilter !== 'all' && donation.type !== typeFilter) {
                    return false;
                }

                // Search filter
                if (searchTerm) {
                    const searchableText = `${donation.tracking_code} ${donation.donor_name} ${donation.category || ''} ${donation.items_description || ''}`.toLowerCase();
                    if (!searchableText.includes(searchTerm)) {
                        return false;
                    }
                }

                return true;
            });

            displayDonations();
        }

        function displayDonations() {
            document.getElementById('loading').classList.add('hidden');
            document.getElementById('error').classList.add('hidden');

            const table = document.getElementById('donationsTable');
            const tbody = document.getElementById('donationsTableBody');
            const emptyState = document.getElementById('emptyState');
            const resultCount = document.getElementById('resultCount');

            resultCount.textContent = `${filteredDonations.length} transaction${filteredDonations.length !== 1 ? 's' : ''}`;

            if (filteredDonations.length === 0) {
                table.style.display = 'none';
                emptyState.style.display = 'block';
                return;
            }

            emptyState.style.display = 'none';
            table.style.display = 'block';
            tbody.innerHTML = filteredDonations.map(donation => createTableRow(donation)).join('');
        }

        function createTableRow(donation) {
            const isPhysical = donation.type === 'physical';
            const typeBadgeClass = isPhysical ? 'badge-physical' : 'badge-monetary';
            // MODIFIED: This now uses 'orange' instead of 'purple' for physical donations
            const colorClass = isPhysical ? 'orange' : 'green';

            return `
                <tr>
                    <td class="whitespace-nowrap">
                        <a href="/donation/track?tracking_code=${donation.tracking_code}" class="tracking-link">
                            ${donation.tracking_code}
                        </a>
                        <div class="badge-row">
                            <span class="badge badge-verified">
                                <svg fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                Verified
                            </span>
                            <span class="time-ago">${donation.time_ago}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-type ${typeBadgeClass}">
                            ${isPhysical ? 'Non-Monetary' : 'Monetary'}
                        </span>
                    </td>
                    <td>
                        ${isPhysical ? `
                            <div class="details-title">${donation.category}</div>
                            <div class="details-description">${donation.items_description}</div>
                        ` : `
                            <div class="payment-method">${donation.payment_method || 'GCash'}</div>
                        `}
                        <div class="donor-info">
                            <span class="label">Donor:</span> ${donation.donor_name}
                        </div>
                        ${donation.barangay_name ? `
                            <div class="donor-info">
                                <span class="label">Barangay:</span> ${donation.barangay_name}
                            </div>
                        ` : ''}
                        ${donation.blockchain_tx_hash ? `
                            <div>
                                <a href="${donation.explorer_url}" target="_blank" class="blockchain-link ${colorClass}">
                                    <svg fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    View on Blockchain Explorer
                                </a>
                            </div>
                            <div class="tx-hash" title="${donation.blockchain_tx_hash}">
                                TX: ${donation.blockchain_tx_hash.substring(0, 30)}...
                            </div>
                        ` : ''}
                        <div class="blockchain-badge ${colorClass}">
                            <svg fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Permanently recorded on blockchain
                        </div>
                    </td>
                    <td class="whitespace-nowrap text-right">
                        ${isPhysical ? `
                            <div class="value-amount ${colorClass}">₱${parseFloat(donation.estimated_value).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                            <div class="value-label">Est. Value</div>
                        ` : `
                            <div class="value-amount large ${colorClass}">₱${parseFloat(donation.amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                        `}
                    </td>
                </tr>
            `;
        }

        // Event listeners
        document.getElementById('typeFilter').addEventListener('change', applyFilters);
        document.getElementById('searchInput').addEventListener('input', applyFilters);

        // Load donations on page load
        document.addEventListener('DOMContentLoaded', loadAllDonations);
    </script>

    @include('partials.footer')
</body>
</html>