{{-- Map Section --}}
<section class="map-section" id="map">
    <div class="section-header">
        <h2>Live Disaster Map of Cebu</h2>
        <p>Real-time status of barangays across Cebu City</p>
    </div>

    {{-- Map Container with Summary Panel --}}
    <div class="map-container">
        {{-- Interactive Map --}}
        <div class="map-wrapper">
            <div id="barangayMap"></div>

            {{-- Map Legend Overlay --}}
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

                    <button class="donate-btn" onclick="window.location.href='/donate/{{ $barangay->barangay_id }}'">
                        Donate to {{ $barangay->name }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</section>
