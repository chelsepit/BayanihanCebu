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
