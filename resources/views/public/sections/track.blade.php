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
