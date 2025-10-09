<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DonorTrack - Transparent Donation Tracking for Cebu</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body>
    {{-- Navigation --}}
    <nav class="navbar">
        <div class="container">
            <div class="nav-brand">
                <img src="{{ asset('images/logo.png') }}" alt="DonorTrack" class="logo">
                <span class="brand-name">DonorTrack</span>
            </div>
            <ul class="nav-menu" id="navMenu">
                <li><a href="#home">Home</a></li>

                <li><a href="{{ route('login') }}" class="btn-login">Login</a></li>
            </ul>
            <button class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="hero" id="home">
        <div class="hero-overlay"></div>
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Transparent Donation Tracking for Cebu</h1>
                <p class="hero-subtitle">Every donation tracked. Every impact measured. Building trust through blockchain transparency.</p>
                <div class="hero-buttons">
                    <a href="{{ route('donation.track') }}" class="btn btn-primary">
                        <span class="icon">🔍</span> Track Your Donation
                    </a>
                    <a href="{{ route('barangay.map') }}" class="btn btn-secondary">
                        <span class="icon">🗺️</span> View Barangay Map
                    </a>
                </div>
            </div>

            {{-- Statistics Cards --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">1,247</div>
                    <div class="stat-label">Donations Tracked</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">38</div>
                    <div class="stat-label">Barangays Served</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">₱2.4M</div>
                    <div class="stat-label">Total Impact</div>
                </div>
            </div>
        </div>
    </section>

    {{-- Barangay Map Section --}}
    <section class="barangay-section">
        <div class="container">
            <h2 class="section-title">Cebu City Barangays</h2>
            <p class="section-subtitle">Click on any barangay to view detailed donation tracking and distribution information</p>

            <div class="map-container">
                <div class="map-wrapper">
                    <div id="barangayMap" class="map">
                        {{-- Map markers will be dynamically loaded --}}
                        {{-- Green: Active Distribution, Orange: Pending Source, Gray: Completed --}}
                    </div>
                    <div class="map-legend">
                        <div class="legend-item">
                            <span class="marker marker-green"></span> Active Distribution
                        </div>
                        <div class="legend-item">
                            <span class="marker marker-orange"></span> Pending Source
                        </div>
                        <div class="legend-item">
                            <span class="marker marker-gray"></span> Completed
                        </div>
                    </div>
                </div>

                {{-- Sidebar with Recent Activity --}}
                <div class="map-sidebar">
                    <div class="sidebar-section">
                        <h3 class="sidebar-title">
                            <span class="icon">📊</span> Recent Activity
                        </h3>
                        <ul class="activity-list">
                            {{-- Will be populated from DONATIONS table --}}
                            <li class="activity-item">
                                <span class="badge badge-blue">12</span>
                                <span>Lahug</span>
                            </li>
                            <li class="activity-item">
                                <span class="badge badge-blue">8</span>
                                <span>Apas</span>
                            </li>
                            <li class="activity-item">
                                <span class="badge badge-green">6</span>
                                <span>Kamputhaw</span>
                            </li>
                            <li class="activity-item">
                                <span class="badge badge-blue">5</span>
                                <span>Guadalupe</span>
                            </li>
                            <li class="activity-item">
                                <span class="badge badge-blue">4</span>
                                <span>Mabolo</span>
                            </li>
                        </ul>
                    </div>

                    <div class="sidebar-section">
                        <h3 class="sidebar-title">
                            <span class="icon">📈</span> Impact Overview
                        </h3>
                        <div class="impact-stats">
                            <div class="impact-item">
                                <div class="impact-number">1566</div>
                                <div class="impact-label">Families Served</div>
                            </div>
                            <div class="impact-item">
                                <div class="impact-number">284</div>
                                <div class="impact-label">Total Donations</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>About</h4>
                    <p>DonorTrack is a blockchain-based transparent donation platform, ensuring accountability and impact.</p>
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <ul>
                       <li><a href="{{ route('barangay.map') }}">Barangay Map</a></li>
                        <li><a href="{{ route('donation.track') }}">Track Donation</a></li>
                        <li><a href="{{ route('fundraisers') }}">Fundraisers</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Resources</h4>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <h4>Connect</h4>
                    <p>Email: info@donortrack.ph</p>
                    <p>Phone: +63 32 123 4567</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 DonorTrack. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
