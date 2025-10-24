@extends('layouts.app')

@section('title', 'Terms of Service')

@section('content')
<div class="legal-page-wrapper">
    <div class="container">

        <div class="legal-layout-grid">

            <aside class="legal-sidebar">
                <nav>
                    <p class="sidebar-title">On this page</p>
                    <ul>
                        <li><a href="#introduction">Introduction</a></li>
                        <li><a href="#acceptance-of-terms">1. Acceptance of Terms</a></li>
                        <li><a href="#our-services">2. Our Services</a></li>
                        <li><a href="#user-responsibilities">3. User Responsibilities</a></li>
                        <li><a href="#donation-terms">4. Donation Terms</a></li>
                        <li><a href="#intellectual-property">5. Intellectual Property</a></li>
                        <li><a href="#disclaimers">6. Disclaimers and Limitation of Liability</a></li>
                        <li><a href="#governing-law">7. Governing Law</a></li>
                        <li><a href="#changes-to-terms">8. Changes to Terms</a></li>
                        <li><a href="#contact-us">9. Contact Us</a></li>
                    </ul>
                </nav>
            </aside>

            <main class="legal-main-content">

                <div class="legal-main-header">
                    <h1 class="text-3xl font-bold text-gray-900 mb-4">Terms of Service</h1>
                    <p class="text-gray-600 text-lg mb-6">Understand your rights and responsibilities when using the Bayanihan Cebu platform.</p>
                    <div class="space-y-1">
                        <p class="text-sm text-gray-500">Last Updated: January 2025</p>
                        <p class="text-sm text-gray-500">Version 1.0</p>
                    </div>
                </div>

                <div class="legal-content">

                    <div class="legal-section" id="introduction" style="scroll-margin-top: 2rem;">
                         <div class="section-content" style="padding-top: 0;">
                            <div class="legal-intro-card">
                                <p>
                                    Welcome to <strong>Bayanihan Cebu</strong>! These Terms of Service ("Terms") govern your access to and use of the Bayanihan Cebu platform, which facilitates transparent disaster relief and donation management. By accessing or using our platform, you agree to be bound by these Terms.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="legal-section" id="acceptance-of-terms">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">1. Acceptance of Terms</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700">
                                By creating an account, making a donation, or otherwise using our services, you signify your agreement to these Terms. If you do not agree to these Terms, you may not access or use the platform.
                            </p>
                        </div>
                    </div>

                    <div class="legal-section" id="our-services">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">2. Our Services</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700 mb-4">Bayanihan Cebu provides a platform for:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                <li>Facilitating monetary and in-kind donations for disaster relief.</li>
                                <li>Tracking donations from donor to beneficiary with transparency.</li>
                                <li>Coordinating relief efforts among Local Government Units (LGUs), Barangay Disaster Risk Reduction and Management Councils (BDRRMCs), Local Disaster Risk Reduction and Management Councils (LDRRMCs), and residents.</li>
                                <li>Providing information and updates on disaster situations and relief operations.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="legal-section" id="user-responsibilities">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">3. User Responsibilities</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700 mb-4">As a user of Bayanihan Cebu, you agree to:</p>
                            <ul class="list-disc list-inside space-y-2 text-gray-700">
                                <li>Provide accurate and truthful information when registering, donating, or interacting with the platform.</li>
                                <li>Use the platform only for lawful purposes and in accordance with these Terms.</li>
                                <li>Respect the privacy and rights of other users and stakeholders.</li>
                                <li>Not engage in any activity that could harm, disrupt, or impair the functionality of the platform.</li>
                                <li>For donors, ensure that your donations are made voluntarily and with legitimate funds.</li>
                                <li>For LGUs/DRRMCs, ensure accurate reporting and transparent distribution of aid.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="legal-section" id="donation-terms">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">4. Donation Terms</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700">
                                All donations made through Bayanihan Cebu are voluntary and non-refundable, unless otherwise specified by law or our internal policies. We strive to ensure that donations reach their intended beneficiaries efficiently and transparently. While we use blockchain technology for enhanced transparency, we cannot guarantee the outcome of every relief effort, which can be influenced by external factors.
                            </p>
                        </div>
                    </div>

                    <div class="legal-section" id="intellectual-property">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">5. Intellectual Property</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700">
                                All content, trademarks, and intellectual property on the Bayanihan Cebu platform are owned by or licensed to us. You may not use, reproduce, or distribute any content from the platform without our express written permission.
                            </p>
                        </div>
                    </div>

                    <div class="legal-section" id="disclaimers">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">6. Disclaimers and Limitation of Liability</h2>
                        </div>
                        <div class="section-content">
                            <div class="bg-red-50 border-l-4 border-red-400 text-red-800 p-4 rounded-r-lg">
                                <div class="flex items-center">
                                                                        <p class="text-sm">The Bayanihan Cebu platform is provided "as is" and "as available" without any warranties, express or implied. We do not guarantee that the platform will be error-free or uninterrupted. To the fullest extent permitted by law, Bayanihan Cebu shall not be liable for any indirect, incidental, special, consequential, or punitive damages, or any loss of profits or revenues, whether incurred directly or indirectly, or any loss of data, use, goodwill, or other intangible losses, resulting from (a) your access to or use of or inability to access or use the platform; (b) any conduct or content of any third party on the platform.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="legal-section" id="governing-law">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">7. Governing Law</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700">
                                These Terms shall be governed and construed in accordance with the laws of the Philippines, without regard to its conflict of law provisions.
                            </p>
                        </div>
                    </div>

                    <div class="legal-section" id="changes-to-terms">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">8. Changes to Terms</h2>
                        </div>
                        <div class="section-content">
                            <p class="text-gray-700">
                                We reserve the right to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.
                            </p>
                        </div>
                    </div>

                    <div class="legal-section" id="contact-us">
                        <div class="section-header">
                            <h2 class="text-2xl font-semibold text-gray-800">9. Contact Us</h2>
                        </div>
                        <div class="section-content">
                            <div class="bg-green-50 border-l-4 border-green-400 text-green-800 p-4 rounded-r-lg">
                                <div class="flex items-center">
                                                                        <div>
                                        <h3 class="font-semibold text-lg mb-1">Have Questions?</h3>
                                        <p class="text-sm">If you have any questions about these Terms, please contact us at <a href="mailto:info@donortrack.ph" class="text-green-700 hover:underline font-medium">info@donortrack.ph</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #CA6702; /* Updated primary color */
    --primary-color-dark: #BB3E03;
    --text-primary: #1a202c;
    --text-secondary: #4a5568;
    --text-light: #718096;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --border-color: #e2e8f0;
}

/* Page Wrapper */
.legal-page-wrapper {
    min-height: 100vh;
    background-color: var(--bg-light);
    padding: 4rem 0;
    font-family: 'Inter', sans-serif; /* Assuming Inter is used elsewhere */
}

/* Make container wider for 2-col layout */
.container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 1.5rem; /* Adjusted padding */
}

/* New 2-Column Grid Layout */
.legal-layout-grid {
    display: grid;
    grid-template-columns: 280px 1fr; /* Slightly wider sidebar */
    gap: 4rem; /* Increased gap */
    align-items: flex-start;
}

/* New Sticky Sidebar */
.legal-sidebar {
    position: sticky;
    top: 2rem;
    max-height: calc(100vh - 4rem);
    overflow-y: auto;
    padding-right: 1rem; /* Add padding for scrollbar */
}

.sidebar-title {
    font-size: 0.85rem; /* Slightly smaller */
    font-weight: 700; /* Bolder */
    color: var(--text-light); /* Lighter color */
    margin-bottom: 1.25rem; /* More space */
    text-transform: uppercase;
    letter-spacing: 0.08em; /* More letter spacing */
}

.legal-sidebar nav ul {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem; /* Increased gap */
}

.legal-sidebar nav li a {
    display: block;
    padding: 0.85rem 1rem; /* Adjusted padding */
    color: var(--text-secondary);
    font-weight: 500;
    text-decoration: none;
    border-radius: 10px; /* Slightly more rounded */
    transition: all 0.2s ease;
    font-size: 0.9rem; /* Slightly smaller */
}

.legal-sidebar nav li a:hover {
    background: #fff7ed; /* Light orange background */
    color: var(--primary-color); /* Primary color on hover */
}
/* You can add a 'active' class with JS (IntersectionObserver) */
.legal-sidebar nav li a.active {
    background: #fff7ed;
    color: var(--primary-color);
    font-weight: 600;
}


/* --- Main Content Styling --- */

/* Simplified Page Header */
.legal-main-header {
    margin-bottom: 3rem;
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 2rem;
}

/* Removed .legal-icon */

.legal-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 1rem;
}

.legal-subtitle {
    font-size: 1.15rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin-bottom: 2rem;
}

.legal-meta {
    display: flex;
    gap: 1.5rem; /* Adjusted gap */
    justify-content: flex-start;
    align-items: center; /* Align items vertically */
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: var(--text-secondary);
    font-size: 0.9rem; /* Slightly smaller */
}

.meta-item .material-symbols-outlined {
    font-size: 18px; /* Smaller icon */
    color: var(--primary-color);
}


/* Content Cards */
.legal-content {
    max-width: 900px;
}

.legal-intro-card {
    background: var(--bg-white);
    padding: 2rem;
    border-radius: 15px;
    margin-bottom: 3rem;
    border-left: 4px solid var(--primary-color);
    box-shadow: 0 4px 10px rgba(0,0,0,0.05); /* Added shadow */
}

.legal-intro-card p {
    color: var(--text-secondary);
    line-height: 1.7;
    font-size: 1.05rem;
}

.legal-section {
    background: var(--bg-white);
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
    overflow: hidden;
    scroll-margin-top: 2rem;
}

/* Simplified Section Header */
.section-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-primary);
}

/* Removed .section-title-wrapper and .section-icon */

.section-title {
    font-size: 1.75rem; /* Slightly larger */
    font-weight: 700; /* Bolder */
    margin: 0;
}

/* Section Content */
.section-content {
    padding: 2rem;
}
/* Fix for intro card section */
.legal-section#introduction .section-content {
    padding: 0;
}
.legal-section#introduction {
    background: transparent;
    box-shadow: none;
}


.section-intro {
    color: var(--text-secondary);
    font-size: 1rem;
    margin-bottom: 1.5rem;
    line-height: 1.7;
}

/* Typography and spacing improvements */
.legal-content p {
    line-height: 1.75;
    margin-bottom: 1rem;
}
.legal-section + .legal-section {
    border-top: 1px solid var(--border-color);
    margin-top: 2rem;
    padding-top: 2rem;
}
/* Info Cards Grid - Replaced with Tailwind grid */
/* Styled List - Replaced with Tailwind list */
/* Share Grid - Replaced with Tailwind grid */
/* Highlight Boxes - Replaced with Tailwind alert style */
/* Contact Card - Replaced with Tailwind alert style */


/* Responsive Design */
@media (max-width: 1024px) {
    .legal-layout-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .legal-sidebar {
        position: static;
        max-height: none;
        overflow-y: visible;
        margin-bottom: 2rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 1rem;
        padding-right: 0;
    }

    .legal-sidebar nav ul {
        flex-direction: row;
        flex-wrap: wrap;
        gap: 0.75rem; /* Adjusted gap for horizontal layout */
    }

    .legal-sidebar nav li a {
        padding: 0.6rem 0.9rem; /* Adjusted padding for smaller screens */
        font-size: 0.875rem;
    }
}


@media (max-width: 768px) {
    .legal-title {
        font-size: 2rem;
    }

    .section-title {
        font-size: 1.5rem;
    }

    .legal-content .grid { /* Target the new Tailwind grids */
        grid-template-columns: 1fr;
    }

    .section-content, .section-header, .legal-intro-card, .bg-blue-50, .bg-green-50 {
        padding: 1.5rem;
    }
}
</style>
@endsection
