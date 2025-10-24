{{-- Physical Donation Tracking View - EXACT UI MATCH --}}
{{-- resources/views/donations/partials/track-physical.blade.php --}}

<!-- Main Content -->
<div style="background: #f9fafb; min-height: calc(100vh - 60px); padding: 24px 0;">
    <div style="max-width: 720px; margin: 0 auto; padding: 0 16px;">

        <!-- Tracking Code Header Card -->
        <div style="background: white; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 12px;">
                <div>
                    <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; font-weight: 500;">Tracking Code</p>
                    <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0 0 8px 0; letter-spacing: -0.5px;">{{ $donation->tracking_code }}</h1>
                    <span style="display: inline-block; background: #ddd6fe; color: #7c3aed; font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 12px; text-transform: uppercase; letter-spacing: 0.3px;">Physical Donation</span>
                </div>
                <div>
                    @php
                        $statusColors = [
                            'pending_distribution' => ['bg' => '#fbbf24', 'text' => '#000'],
                            'partially_distributed' => ['bg' => '#3b82f6', 'text' => '#fff'],
                            'fully_distributed' => ['bg' => '#10b981', 'text' => '#fff'],
                        ];
                        $statusColor = $statusColors[$donation->distribution_status] ?? ['bg' => '#fbbf24', 'text' => '#000'];
                    @endphp
                    <span style="display: inline-block; background: {{ $statusColor['bg'] }}; color: {{ $statusColor['text'] }}; font-size: 10px; font-weight: 600; padding: 6px 14px; border-radius: 12px; text-transform: uppercase; white-space: nowrap;">
                        {{ str_replace('_', ' ', $donation->distribution_status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Stats Grid (2x2) -->
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 16px;">
            <!-- Donor Name -->
            <div style="background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 36px; height: 36px; background: #ddd6fe; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" fill="#7c3aed" viewBox="0 0 24 24">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 10px; color: #6b7280; margin: 0 0 3px 0; font-weight: 500;">Donor Name</p>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $donation->donor_name }}</p>
                    </div>
                </div>
            </div>

            <!-- Category -->
            <div style="background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 36px; height: 36px; background: #dbeafe; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" fill="#2563eb" viewBox="0 0 24 24">
                            <path d="M21.41 11.58l-9-9C12.05 2.22 11.55 2 11 2H4c-1.1 0-2 .9-2 2v7c0 .55.22 1.05.59 1.42l9 9c.36.36.86.58 1.41.58.55 0 1.05-.22 1.41-.59l7-7c.37-.36.59-.86.59-1.41 0-.55-.23-1.06-.59-1.42zM5.5 7C4.67 7 4 6.33 4 5.5S4.67 4 5.5 4 7 4.67 7 5.5 6.33 7 5.5 7z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 10px; color: #6b7280; margin: 0 0 3px 0; font-weight: 500;">Category</p>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ ucfirst($donation->category) }}</p>
                    </div>
                </div>
            </div>

            <!-- Estimated Value -->
            <div style="background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 36px; height: 36px; background: #d1fae5; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" fill="#059669" viewBox="0 0 24 24">
                            <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 10px; color: #6b7280; margin: 0 0 3px 0; font-weight: 500;">Estimated Value</p>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">₱{{ number_format($donation->estimated_value, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Quantity -->
            <div style="background: white; border-radius: 8px; padding: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div style="display: flex; align-items: flex-start; gap: 12px;">
                    <div style="width: 36px; height: 36px; background: #fed7aa; border-radius: 6px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                        <svg width="18" height="18" fill="#ea580c" viewBox="0 0 24 24">
                            <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                        </svg>
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <p style="font-size: 10px; color: #6b7280; margin: 0 0 3px 0; font-weight: 500;">Quantity</p>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $donation->quantity }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Details Card -->
        <div style="background: white; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Donation Details</h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 20px;">
                <!-- Left Column -->
                <div>
                    <h4 style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 14px 0;">Donation Information</h4>
                    <div style="font-size: 12px; line-height: 1.8;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Donor Name:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right; max-width: 60%;">{{ $donation->donor_name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Category:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right;">{{ ucfirst($donation->category) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Quantity:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right;">{{ $donation->quantity }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Estimated Value:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right;">₱{{ number_format($donation->estimated_value, 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #6b7280;">Date Donated:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right;">{{ $donation->recorded_at->format('M d, Y h:i A') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <h4 style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 14px 0;">Beneficiary Information</h4>
                    <div style="font-size: 12px; line-height: 1.8;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Barangay:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right;">{{ $donation->barangay->name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #6b7280;">Intended Recipients:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right; max-width: 60%;">{{ $donation->intended_recipients }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: #6b7280;">Recorded By:</span>
                            <span style="color: #111827; font-weight: 600; text-align: right; max-width: 60%;">{{ $donation->recorder->full_name ?? 'BDRRMC Officer CC001' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Items Donated -->
            <div style="background: #f9fafb; border-radius: 6px; padding: 14px; margin-bottom: 16px;">
                <h4 style="font-size: 12px; font-weight: 600; color: #111827; margin: 0 0 6px 0;">Items Donated</h4>
                <p style="font-size: 12px; color: #4b5563; margin: 0; line-height: 1.5;">{{ $donation->items_description }}</p>
                @if($donation->notes)
                <p style="font-size: 12px; color: #6b7280; margin: 10px 0 0 0; padding-top: 10px; border-top: 1px solid #e5e7eb;"><strong>Notes:</strong> {{ $donation->notes }}</p>
                @endif
            </div>

            <!-- Blockchain Status -->
            <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 6px; padding: 14px; display: flex; align-items: flex-start; gap: 12px;">
                <div style="width: 36px; height: 36px; background: #fbbf24; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    @if($donation->blockchain_status === 'confirmed')
                    <svg width="18" height="18" fill="#fff" viewBox="0 0 24 24">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    @else
                    <svg width="18" height="18" fill="#fff" viewBox="0 0 24 24" class="animate-spin">
                        <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
                    </svg>
                    @endif
                </div>
                <div style="flex: 1;">
                    <h4 style="font-size: 12px; font-weight: 600; color: #92400e; margin: 0 0 4px 0;">Blockchain Status: {{ strtoupper($donation->blockchain_status ?? 'PENDING') }}</h4>
                    <p style="font-size: 11px; color: #92400e; margin: 0;">
                        @if($donation->blockchain_status === 'confirmed')
                        This donation has been permanently recorded on the blockchain.
                        @else
                        Your donation is being recorded on the blockchain. This may take a few minutes.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Distribution Status / Pending Message -->
        @if($donation->distributions->count() == 0)
        <div style="background: #fef3c7; border: 1px solid #fbbf24; border-radius: 8px; padding: 28px; margin-bottom: 16px; text-align: center;">
            <svg width="56" height="56" fill="#f59e0b" viewBox="0 0 24 24" style="margin: 0 auto 14px;">
                <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
            </svg>
            <p style="font-size: 12px; font-weight: 600; color: #92400e; margin: 0 0 4px 0;">This donation is pending distribution to beneficiaries.</p>
            <p style="font-size: 11px; color: #b45309; margin: 0;">Check back soon for updates!</p>
        </div>
        @endif

        <!-- Donation Timeline -->
        <div style="background: white; border-radius: 8px; padding: 20px 24px; margin-bottom: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
            <h3 style="font-size: 15px; font-weight: 600; color: #111827; margin: 0 0 20px 0;">Donation Timeline</h3>

            <div style="position: relative; padding-left: 28px;">
                <!-- Timeline Line -->
                <div style="position: absolute; left: 11px; top: 0; bottom: 0; width: 2px; background: #e5e7eb;"></div>

                <!-- Step 1: Donation Received -->
                <div style="position: relative; margin-bottom: 20px;">
                    <div style="position: absolute; left: -28px; top: 2px; width: 24px; height: 24px; background: #10b981; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb;">
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">Donation Received</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 0;">{{ $donation->recorded_at->format('M d, Y h:i A') }}</p>
                    </div>
                </div>

                <!-- Step 2: Blockchain Verification -->
                <div style="position: relative; margin-bottom: 20px;">
                    <div style="position: absolute; left: -28px; top: 2px; width: 24px; height: 24px; background: {{ $donation->blockchain_status === 'confirmed' ? '#10b981' : '#fbbf24' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb;">
                        @if($donation->blockchain_status === 'confirmed')
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        @else
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24" class="animate-spin">
                            <path d="M12 4V1L8 5l4 4V6c3.31 0 6 2.69 6 6 0 1.01-.25 1.97-.7 2.8l1.46 1.46C19.54 15.03 20 13.57 20 12c0-4.42-3.58-8-8-8zm0 14c-3.31 0-6-2.69-6-6 0-1.01.25-1.97.7-2.8L5.24 7.74C4.46 8.97 4 10.43 4 12c0 4.42 3.58 8 8 8v3l4-4-4-4v3z"/>
                        </svg>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">Blockchain Verification</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 0;">
                            @if($donation->blockchain_recorded_at)
                            {{ $donation->blockchain_recorded_at->format('M d, Y h:i A') }}
                            @else
                            In progress...
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Step 3: Distribution to Beneficiaries -->
                <div style="position: relative; margin-bottom: 20px;">
                    <div style="position: absolute; left: -28px; top: 2px; width: 24px; height: 24px; background: {{ $donation->distributions->count() > 0 ? '#10b981' : '#9ca3af' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb;">
                        @if($donation->distributions->count() > 0)
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        @else
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">Distribution to Beneficiaries</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 0;">
                            @if($donation->distributions->count() > 0)
                            {{ $donation->distributions->first()->distributed_at->format('M d, Y h:i A') }}
                            @else
                            Pending
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Step 4: Fully Distributed -->
                <div style="position: relative;">
                    <div style="position: absolute; left: -28px; top: 2px; width: 24px; height: 24px; background: {{ $donation->distribution_status === 'fully_distributed' ? '#10b981' : '#9ca3af' }}; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid #fff; box-shadow: 0 0 0 1px #e5e7eb;">
                        @if($donation->distribution_status === 'fully_distributed')
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                        </svg>
                        @else
                        <svg width="12" height="12" fill="#fff" viewBox="0 0 24 24">
                            <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                        </svg>
                        @endif
                    </div>
                    <div>
                        <p style="font-size: 13px; font-weight: 600; color: #111827; margin: 0 0 2px 0;">Fully Distributed</p>
                        <p style="font-size: 11px; color: #6b7280; margin: 0;">
                            @if($donation->distribution_status === 'fully_distributed')
                            Completed
                            @else
                            Awaiting completion
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: 10px; margin-bottom: 24px;" class="no-print">
            <a href="{{ route('home') }}" style="flex: 1; background: #2563eb; color: white; text-decoration: none; padding: 11px 20px; border-radius: 6px; font-size: 13px; font-weight: 500; text-align: center; display: flex; align-items: center; justify-content: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 6px;">
                    <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                </svg>
                Return to Map
            </a>
            <button onclick="window.print()" style="flex: 1; background: #6b7280; color: white; padding: 11px 20px; border-radius: 6px; font-size: 13px; font-weight: 500; text-align: center; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 6px;">
                    <path d="M19 8H5c-1.66 0-3 1.34-3 3v6h4v4h12v-4h4v-6c0-1.66-1.34-3-3-3zm-3 11H8v-5h8v5zm3-7c-.55 0-1-.45-1-1s.45-1 1-1 1 .45 1 1-.45 1-1 1zm-1-9H6v4h12V3z"/>
                </svg>
                Print Receipt
            </button>
            <button onclick="copyTrackingCode()" style="flex: 1; background: #7c3aed; color: white; padding: 11px 20px; border-radius: 6px; font-size: 13px; font-weight: 500; text-align: center; display: flex; align-items: center; justify-content: center; border: none; cursor: pointer; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <svg width="15" height="15" fill="currentColor" viewBox="0 0 24 24" style="margin-right: 6px;">
                    <path d="M16 1H4c-1.1 0-2 .9-2 2v14h2V3h12V1zm3 4H8c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h11c1.1 0 2-.9 2-2V7c0-1.1-.9-2-2-2zm0 16H8V7h11v14z"/>
                </svg>
                Copy Tracking Code
            </button>
        </div>

    </div>
</div>

<style>
@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin {
    animation: spin 1s linear infinite;
}
@media print {
    .no-print { display: none !important; }
}
</style>

<script>
function copyTrackingCode() {
    const trackingCode = '{{ $donation->tracking_code }}';
    navigator.clipboard.writeText(trackingCode).then(() => {
        alert('Tracking code copied to clipboard!');
    }).catch(() => {
        alert('Failed to copy. Please copy manually: {{ $donation->tracking_code }}');
    });
}
</script>
