<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ResourceNeed;
use App\Models\PhysicalDonation;
use App\Models\DistributionLog;
use App\Models\Barangay;
use App\Models\Donation;
use App\Models\ResourceMatch;
use App\Models\MatchConversation;
use App\Models\MatchMessage;
use App\Models\MatchNotification;

class BarangayDashboardController extends Controller
{
    /**
     * Display BDRRMC Dashboard
     */
    public function index()
    {
        $userId = session('user_id');
        $barangayId = session('barangay_id');

        // Get barangay info
        $barangay = Barangay::where('barangay_id', $barangayId)->first();

        // Get updated statistics including online donations
        $stats = $this->calculateStats($barangayId);

        return view('UserDashboards.barangaydashboard', compact('barangay', 'stats'));
    }

    /**
     * Calculate statistics for the dashboard
     */
    private function calculateStats($barangayId)
    {
        $physicalCount = PhysicalDonation::where('barangay_id', $barangayId)->count();
        $physicalValue = PhysicalDonation::where('barangay_id', $barangayId)->sum('estimated_value');

        $onlineCount = Donation::where('barangay_id', $barangayId)->count();
        $onlineValue = Donation::where('barangay_id', $barangayId)->sum('amount');

        $activeRequests = ResourceNeed::where('barangay_id', $barangayId)
            ->where('status', 'pending')->count();

        $verifiedDonations = Donation::where('barangay_id', $barangayId)
            ->where('blockchain_status', 'confirmed')->count();

        $barangay = Barangay::where('barangay_id', $barangayId)->first();

        return [
            'affected_families' => $barangay->affected_families ?? 0,
            'total_donations' => $physicalCount + $onlineCount,
            'total_value' => $physicalValue + $onlineValue,
            'active_requests' => $activeRequests,
            'verified_donations' => $verifiedDonations,
            'physical_donations' => $physicalCount,
            'online_donations' => $onlineCount,
        ];
    }

    // ==================== RESOURCE NEEDS APIs ====================

    /**
     * Get all resource needs for the barangay
     */
    public function getNeeds()
    {
        $barangayId = session('barangay_id');

        $needs = ResourceNeed::where('barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($needs);
    }

    /**
     * Create a new resource need
     */
    public function createNeed(Request $request)
    {
        $barangayId = session('barangay_id');

        $validated = $request->validate([
            'category' => 'required|in:food,water,medical,shelter,clothing,other',
            'description' => 'required|string|max:1000',
            'quantity' => 'required|string|max:100',
            'urgency' => 'required|in:low,medium,high,critical',
        ]);

        $need = ResourceNeed::create([
            'barangay_id' => $barangayId,
            'category' => $validated['category'],
            'description' => $validated['description'],
            'quantity' => $validated['quantity'],
            'urgency' => $validated['urgency'],
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Resource need created successfully',
            'data' => $need
        ], 201);
    }

    /**
     * Update a resource need
     */
    public function updateNeed(Request $request, $id)
    {
        $barangayId = session('barangay_id');

        $need = ResourceNeed::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $validated = $request->validate([
            'category' => 'sometimes|in:food,water,medical,shelter,clothing,other',
            'description' => 'sometimes|string|max:1000',
            'quantity' => 'sometimes|string|max:100',
            'urgency' => 'sometimes|in:low,medium,high,critical',
            'status' => 'sometimes|in:pending,partially_fulfilled,fulfilled',
        ]);

        $need->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Resource need updated successfully',
            'data' => $need
        ]);
    }

    public function getMatchConversation($matchId)
{
    try {
        $barangayId = session('barangay_id');

        $match = ResourceMatch::with([
            'resourceNeed',
            'requestingBarangay',
            'donatingBarangay',
            'conversation.messages.senderBarangay'
        ])->findOrFail($matchId);

        // Verify this barangay is a participant
        if (!in_array($barangayId, [$match->requesting_barangay_id, $match->donating_barangay_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $conversation = $match->conversation;

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'No conversation exists for this match'
            ], 404);
        }

        // Mark messages as read for this barangay
        $conversation->markAsReadFor($barangayId);

        $isRequester = $match->requesting_barangay_id === $barangayId;

        $data = [
            'success' => true,
            'match' => [
                'id' => $match->id,
                'my_barangay' => $match->requesting_barangay_id === $barangayId ?
                    $match->requestingBarangay->name : $match->donatingBarangay->name,
                'requesting_barangay' => $match->requestingBarangay->name,
                'donating_barangay' => $match->donatingBarangay->name,
                'resource_need' => $match->resourceNeed->category . ' (' . $match->resourceNeed->quantity . ')',
            ],
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'is_active' => $conversation->is_active,
                'my_role' => $isRequester ? 'requester' : 'donor',
                'participants' => [
                    'requester' => [
                        'id' => $match->requesting_barangay_id,
                        'name' => $match->requestingBarangay->name,
                    ],
                    'donor' => [
                        'id' => $match->donating_barangay_id,
                        'name' => $match->donatingBarangay->name,
                    ],
                ],
                'resource_details' => [
                    'category' => $match->resourceNeed->category,
                    'quantity_needed' => $match->resourceNeed->quantity,
                    'quantity_available' => $match->physicalDonation->quantity,
                    'urgency' => $match->resourceNeed->urgency,
                ],
                'messages' => $conversation->messages->map(function($msg) use ($barangayId, $match) {
                    // Determine sender role
                    $senderRole = 'unknown';
                    if ($msg->sender_barangay_id === $match->requesting_barangay_id) {
                        $senderRole = 'requester';
                    } elseif ($msg->sender_barangay_id === $match->donating_barangay_id) {
                        $senderRole = 'donor';
                    } elseif ($msg->sender_user_id && !$msg->sender_barangay_id) {
                        $senderRole = 'ldrrmo';
                    }

                    return [
                        'id' => $msg->id,
                        'sender_barangay_id' => $msg->sender_barangay_id,
                        'sender_name' => $msg->sender_name,
                        'sender_role' => $senderRole,
                        'message' => $msg->message,
                        'message_type' => $msg->message_type,
                        'is_mine' => $msg->sender_barangay_id === $barangayId,
                        'is_system' => $msg->isSystemMessage(),
                        'attachment' => $msg->hasAttachment() ? [
                            'url' => $msg->attachment_url,
                            'type' => $msg->attachment_type,
                            'name' => $msg->attachment_name,
                            'icon' => $msg->attachment_icon,
                        ] : null,
                        'created_at' => $msg->created_at->format('M d, Y h:i A'),
                        'time_ago' => $msg->time_ago,
                    ];
                }),
            ],
        ];

        return response()->json($data);

    } catch (\Exception $e) {
        Log::error('Error loading conversation: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error loading conversation',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Send a message in the conversation
 */
public function sendMessage(Request $request, $matchId)
{
    try {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $userId = session('user_id');
        $barangayId = session('barangay_id');

        $match = ResourceMatch::with('conversation')->findOrFail($matchId);

        // Verify this barangay is a participant
        if (!in_array($barangayId, [$match->requesting_barangay_id, $match->donating_barangay_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $conversation = $match->conversation;

        if (!$conversation) {
            return response()->json([
                'success' => false,
                'message' => 'No conversation exists for this match'
            ], 404);
        }

        // Check if conversation is still active
        if (!$conversation->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This conversation has been closed'
            ], 400);
        }

        // Handle file attachment if present
        $attachmentData = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $path = $file->store('match_attachments', 'public');
            
            $attachmentData = [
                'url' => '/storage/' . $path,
                'type' => $file->getClientMimeType(),
                'name' => $file->getClientOriginalName(),
            ];
        }

        // Add message to conversation
        $message = $conversation->addMessage(
            $userId,
            $barangayId,
            $validated['message'],
            'text',
            $attachmentData
        );

        // Get the other barangay to notify them
        $otherBarangayId = $barangayId === $match->requesting_barangay_id ? 
            $match->donating_barangay_id : 
            $match->requesting_barangay_id;

        // Create notification for new message
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $otherBarangayId,
            'type' => 'new_message',
            'title' => 'New Message',
            'message' => "You have a new message in your conversation about {$match->resourceNeed->category} transfer.",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'message_id' => $message->id,
                'created_at' => $message->created_at->format('M d, Y h:i A'),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error sending message: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error sending message',
            'error' => $e->getMessage()
        ], 500);
    }
}

/**
 * Mark messages as read
 */
public function markMessagesAsRead($matchId)
{
    try {
        $barangayId = session('barangay_id');

        $match = ResourceMatch::with('conversation')->findOrFail($matchId);

        // Verify this barangay is a participant
        if (!in_array($barangayId, [$match->requesting_barangay_id, $match->donating_barangay_id])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $conversation = $match->conversation;

        if ($conversation) {
            $conversation->markAsReadFor($barangayId);
        }

        return response()->json([
            'success' => true,
            'message' => 'Messages marked as read'
        ]);

    } catch (\Exception $e) {
        Log::error('Error marking messages as read: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error marking messages as read',
            'error' => $e->getMessage()
        ], 500);
    }
}

//Complete a match (mark transfer as done)

public function completeMatch(Request $request, $matchId)
{
    try {
        $validated = $request->validate([
            'completion_notes' => 'nullable|string|max:1000',
        ]);

        $barangayId = session('barangay_id');

        $match = ResourceMatch::with([
            'resourceNeed',
            'physicalDonation',
            'conversation',
            'requestingBarangay',
            'donatingBarangay'
        ])->findOrFail($matchId);

        // ONLY the requesting barangay (receiver) can mark as complete
        if ($barangayId !== $match->requesting_barangay_id) {
            return response()->json([
                'success' => false,
                'message' => 'Only the receiving barangay can mark this match as complete'
            ], 403);
        }

        // Check if match is accepted
        if ($match->status !== 'accepted') {
            return response()->json([
                'success' => false,
                'message' => 'Only accepted matches can be completed'
            ], 400);
        }

        // Update match status
        $match->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completion_notes' => $validated['completion_notes'] ?? 'Transfer completed successfully',
        ]);

        // Update physical donation status
        $match->physicalDonation->update([
            'distribution_status' => 'fully_distributed',
        ]);

        // Update resource need status
        $match->resourceNeed->update([
            'status' => 'fulfilled',
        ]);

        // Close conversation
        if ($match->conversation) {
            $match->conversation->close();
        }

        // Notify both barangays
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $match->requesting_barangay_id,
            'type' => 'match_completed',
            'title' => 'Transfer Completed!',
            'message' => "The {$match->resourceNeed->category} transfer with {$match->donatingBarangay->name} has been marked as complete.",
        ]);

        MatchNotification::create([
            'resource_match_id' => $match->id,
            'barangay_id' => $match->donating_barangay_id,
            'type' => 'match_completed',
            'title' => 'Transfer Completed!',
            'message' => "The {$match->resourceNeed->category} transfer to {$match->requestingBarangay->name} has been marked as complete. Thank you for your donation!",
        ]);

        // Notify LDRRMO user
        MatchNotification::create([
            'resource_match_id' => $match->id,
            'user_id' => $match->initiated_by,
            'type' => 'match_completed',
            'title' => 'Transfer Completed',
            'message' => "Match #{$match->id} has been completed: {$match->resourceNeed->category} transferred from {$match->donatingBarangay->name} to {$match->requestingBarangay->name}.",
        ]);

        Log::info("Match completed", [
            'match_id' => $match->id,
            'requester' => $match->requestingBarangay->name,
            'donor' => $match->donatingBarangay->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Transfer marked as complete',
            'data' => [
                'match_id' => $match->id,
                'status' => $match->status,
                'completed_at' => $match->completed_at->format('M d, Y h:i A'),
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('Error completing match: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Error completing match',
            'error' => $e->getMessage()
        ], 500);
    }
}

    /**
     * Delete a resource need
     */
    public function deleteNeed($id)
    {
        $barangayId = session('barangay_id');

        $need = ResourceNeed::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $need->delete();

        return response()->json([
            'success' => true,
            'message' => 'Resource need deleted successfully'
        ]);
    }

    // ==================== PHYSICAL DONATIONS APIs ====================

    /**
     * Get all physical donations for the barangay
     */
    public function getPhysicalDonations()
    {
        $barangayId = session('barangay_id');

        $donations = PhysicalDonation::where('barangay_id', $barangayId)
            ->with(['recorder', 'distributions'])
            ->orderBy('recorded_at', 'desc')
            ->get();

        return response()->json($donations);
    }

    /**
     * Record a new physical donation
     */
public function recordDonation(Request $request)
{
    // DEBUGGING: Log that we entered this method
    Log::info('=== ENTERED recordDonation method ===', [
        'request_data' => $request->all(),
        'session_barangay' => session('barangay_id'),
        'session_user' => session('user_id')
    ]);

    try {
        $barangayId = session('barangay_id');
        $userId = session('user_id');

        Log::info('About to validate request');

        $validated = $request->validate([
            'donor_name' => 'required|string|max:100',
            'donor_contact' => 'required|string|max:20',
            'donor_email' => 'nullable|email|max:100',
            'donor_address' => 'required|string|max:500',
            'category' => 'required|in:food,water,medical,shelter,clothing,other',
            'items_description' => 'required|string|max:1000',
            'quantity' => 'required|string|max:100',
            'estimated_value' => 'nullable|numeric|min:0',
            'intended_recipients' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        Log::info('Validation passed, generating tracking code');

        // Generate tracking code
        $trackingCode = PhysicalDonation::generateTrackingCode($barangayId);

        Log::info('Tracking code generated', ['code' => $trackingCode]);

        try {
            $donation = PhysicalDonation::create([
                'barangay_id' => $barangayId,
                'tracking_code' => $trackingCode,
                'donor_name' => $validated['donor_name'],
                'donor_contact' => $validated['donor_contact'],
                'donor_email' => $validated['donor_email'],
                'donor_address' => $validated['donor_address'],
                'category' => $validated['category'],
                'items_description' => $validated['items_description'],
                'quantity' => $validated['quantity'],
                'estimated_value' => $validated['estimated_value'] ?? 0,
                'intended_recipients' => $validated['intended_recipients'],
                'notes' => $validated['notes'],
                'distribution_status' => 'pending_distribution',
                'recorded_by' => $userId,
                'recorded_at' => now(),
            ]);
        } catch (\Exception $createError) {
            Log::error('!!! FAILED TO CREATE PHYSICAL DONATION !!!', [
                'error' => $createError->getMessage(),
                'code' => $createError->getCode(),
                'file' => $createError->getFile(),
                'line' => $createError->getLine(),
                'trace' => $createError->getTraceAsString()
            ]);
            throw $createError; // Re-throw so outer catch handles it
        }

        Log::info('PhysicalDonation created successfully', ['id' => $donation->id]);

        // Dispatch blockchain recording job (runs in background via queue worker)
        \App\Jobs\RecordPhysicalDonationToBlockchain::dispatch($donation->id);

        Log::info('Blockchain job dispatched');

        Log::info('Physical donation created and blockchain recording job dispatched', [
            'tracking_code' => $trackingCode,
            'donation_id' => $donation->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Donation recorded successfully',
            'data' => $donation,
            'tracking_code' => $trackingCode
        ], 201);

    } catch (\Exception $e) {
        // Log the actual error for debugging
        Log::error('Error recording physical donation: ' . $e->getMessage());
        Log::error('Stack trace: ' . $e->getTraceAsString());

        // Return a generic error message to the user
        return response()->json([
            'success' => false,
            'message' => 'An unexpected error occurred. Please try again later.',
            'error' => $e->getMessage() // Temporarily include for debugging
        ], 500);
    }
}
    /**
     * Get single donation details
     */
    public function getDonationDetails($id)
    {
        $barangayId = session('barangay_id');

        $donation = PhysicalDonation::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->with(['recorder', 'distributions.distributor'])
            ->firstOrFail();

        return response()->json($donation);
    }

    /**
     * Record distribution of a donation
     */
    public function recordDistribution(Request $request, $id)
    {
        $barangayId = session('barangay_id');
        $userId = session('user_id');

        $donation = PhysicalDonation::where('id', $id)
            ->where('barangay_id', $barangayId)
            ->firstOrFail();

        $validated = $request->validate([
            'distributed_to' => 'required|string|max:200',
            'quantity_distributed' => 'required|string|max:100',
            'notes' => 'nullable|string|max:500',
            'distribution_status' => 'required|in:partially_distributed,fully_distributed',
            'photo_urls' => 'required|array|min:5|max:5',
            'photo_urls.*' => 'required|string',
        ]);

        // Create distribution log
        $distributionLog = DistributionLog::create([
            'physical_donation_id' => $donation->id,
            'distributed_to' => $validated['distributed_to'],
            'quantity_distributed' => $validated['quantity_distributed'],
            'distributed_by' => $userId,
            'distributed_at' => now(),
            'notes' => $validated['notes'],
            'photo_urls' => $validated['photo_urls'],
        ]);

        // Update donation status
        $donation->update([
            'distribution_status' => $validated['distribution_status']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Distribution recorded successfully',
            'data' => [
                'donation' => $donation,
                'distribution' => $distributionLog
            ]
        ]);
    }

    // ==================== ONLINE DONATIONS (READ-ONLY FOR BDRRMC) ====================

    /**
     * Get online donations for the barangay (READ-ONLY for BDRRMC)
     */
    public function getOnlineDonations()
    {
        $barangayId = session('barangay_id');

        $donations = Donation::where('barangay_id', $barangayId)
            ->with(['verifier'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($donation) {
                return [
                    'id' => $donation->id,
                    'tracking_code' => $donation->tracking_code,
                    'donor_name' => $donation->getDonorDisplayName(), // Fixed: Call as method
                    'donor_email' => $donation->is_anonymous ? null : $donation->donor_email,
                    'amount' => $donation->amount,
                    'payment_method' => $donation->payment_method,
                    'verification_status' => $donation->verification_status,
                    'verified_at' => $donation->verified_at ? $donation->verified_at->format('M d, Y') : null,
                    'blockchain_status' => $donation->blockchain_status,
                    'blockchain_verified' => $donation->blockchain_status === 'confirmed',
                    'tx_hash' => $donation->tx_hash,
                    'blockchain_tx_hash' => $donation->blockchain_tx_hash,
                    'explorer_url' => $donation->explorer_url,
                    'created_at' => $donation->created_at->format('M d, Y H:i'),
                ];
            });

        // Calculate statistics
        $stats = [
            'total_online_donations' => $donations->sum('amount'),
            'total_count' => $donations->count(),
            'verified_count' => $donations->where('verification_status', 'verified')->count(),
            'blockchain_verified_count' => $donations->where('blockchain_verified', true)->count(),
            'pending_count' => $donations->where('verification_status', 'pending')->count(),
        ];

        return response()->json([
            'success' => true,
            'donations' => $donations,
            'statistics' => $stats,
        ]);
    }

    /**
     * Get updated statistics including online donations
     */
    public function getUpdatedStats()
    {
        $barangayId = session('barangay_id');
        $stats = $this->calculateStats($barangayId);

        return response()->json($stats);
    }

    // ==================== BARANGAY INFO ====================

    /**
     * Get barangay information
     */
    public function getBarangayInfo()
    {
        $barangayId = session('barangay_id');

        $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

        return response()->json($barangay);
    }

    /**
     * Update barangay information
     */
    public function updateBarangayInfo(Request $request)
    {
        try {
            $barangayId = session('barangay_id');

            // ✅ UPDATED: Use donation_status instead of disaster_status
            $validated = $request->validate([
                'donation_status' => 'required|in:pending,in_progress,completed',
                'disaster_type' => 'nullable|in:flood,fire,earthquake,typhoon,landslide,other',
                'affected_families' => 'required|integer|min:0',
                'needs_summary' => 'nullable|string|max:1000',
                'contact_person' => 'nullable|string|max:100',
                'contact_phone' => 'nullable|string|max:20',
            ]);

            $barangay = Barangay::where('barangay_id', $barangayId)->firstOrFail();

            // ✅ UPDATED: If status is completed, clear affected_families
            if ($validated['donation_status'] === 'completed') {
                $validated['disaster_type'] = null;
                $validated['affected_families'] = 0;
                $validated['needs_summary'] = null;
            }

            $barangay->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Barangay donation status updated successfully',
                'data' => $barangay
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating barangay information',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ==================== MATCH MANAGEMENT APIs ====================

    /**
     * Get incoming match requests (where this barangay is the donor)
     */
    public function getIncomingMatches()
    {
        try {
            $barangayId = session('barangay_id');

            $matches = ResourceMatch::with([
                'resourceNeed',
                'physicalDonation',
                'requestingBarangay',
                'donatingBarangay'
            ])
            ->where('donating_barangay_id', $barangayId)
            ->whereIn('status', ['pending', 'accepted', 'rejected'])
            ->orderBy('created_at', 'desc')
            ->get();

            $data = $matches->map(function($match) {
                return [
                    'id' => $match->id,
                    'status' => $match->status,
                    'requesting_barangay' => [
                        'id' => $match->requesting_barangay_id,
                        'name' => $match->requestingBarangay->name,
                        'donation_status' => $match->requestingBarangay->donation_status, // ✅ UPDATED
                    ],
                    'resource_need' => [
                        'id' => $match->resource_need_id,
                        'category' => $match->resourceNeed->category,
                        'quantity' => $match->resourceNeed->quantity,
                        'urgency' => $match->resourceNeed->urgency,
                        'description' => $match->resourceNeed->description,
                    ],
                    'physical_donation' => [
                        'id' => $match->physical_donation_id,
                        'category' => $match->physicalDonation->category,
                        'quantity' => $match->physicalDonation->quantity,
                        'tracking_code' => $match->physicalDonation->tracking_code,
                    ],
                    'ldrrmo_message' => $match->ldrrmo_message,
                    'barangay_response' => $match->barangay_response,
                    'created_at' => $match->created_at->format('M d, Y h:i A'),
                    'updated_at' => $match->updated_at->format('M d, Y h:i A'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data,
                'counts' => [
                    'pending' => $matches->where('status', 'pending')->count(),
                    'accepted' => $matches->where('status', 'accepted')->count(),
                    'rejected' => $matches->where('status', 'rejected')->count(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching incoming matches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching incoming matches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get outgoing match requests (where this barangay is the requester)
     */
    public function getMyRequests()
    {
        try {
            $barangayId = session('barangay_id');

            $matches = ResourceMatch::with([
                'resourceNeed',
                'physicalDonation',
                'requestingBarangay',
                'donatingBarangay'
            ])
            ->where('requesting_barangay_id', $barangayId)
            ->orderBy('created_at', 'desc')
            ->get();

            $data = $matches->map(function($match) {
                return [
                    'id' => $match->id,
                    'status' => $match->status,
                    'donating_barangay' => [
                        'id' => $match->donating_barangay_id,
                        'name' => $match->donatingBarangay->name,
                    ],
                    'resource_need' => [
                        'id' => $match->resource_need_id,
                        'category' => $match->resourceNeed->category,
                        'quantity' => $match->resourceNeed->quantity,
                        'urgency' => $match->resourceNeed->urgency,
                    ],
                    'physical_donation' => [
                        'id' => $match->physical_donation_id,
                        'category' => $match->physicalDonation->category,
                        'quantity' => $match->physicalDonation->quantity,
                    ],
                    'ldrrmo_message' => $match->ldrrmo_message,
                    'barangay_response' => $match->barangay_response,
                    'created_at' => $match->created_at->format('M d, Y h:i A'),
                    'updated_at' => $match->updated_at->format('M d, Y h:i A'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching my requests: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching my requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active matches (accepted matches with active conversations)
     */
    public function getActiveMatches()
    {
        try {
            $barangayId = session('barangay_id');

            $matches = ResourceMatch::with([
                'resourceNeed',
                'physicalDonation',
                'requestingBarangay',
                'donatingBarangay',
                'conversation'
            ])
            ->where(function($query) use ($barangayId) {
                $query->where('requesting_barangay_id', $barangayId)
                      ->orWhere('donating_barangay_id', $barangayId);
            })
            ->where('status', 'accepted')
            ->orderBy('updated_at', 'desc')
            ->get();

            $data = $matches->map(function($match) use ($barangayId) {
                $isRequester = $match->requesting_barangay_id === $barangayId;
                $otherBarangay = $isRequester ? $match->donatingBarangay : $match->requestingBarangay;

                $unreadCount = 0;
                if ($match->conversation) {
                    // Check the appropriate read status field based on role
                    $readField = $isRequester ? 'is_read_by_requester' : 'is_read_by_donor';
                    $unreadCount = $match->conversation->messages()
                        ->where('sender_barangay_id', '!=', $barangayId)
                        ->where($readField, false)
                        ->count();
                }

                return [
                    'id' => $match->id,
                    'my_role' => $isRequester ? 'requester' : 'donor',
                    'other_barangay' => [
                        'id' => $otherBarangay->barangay_id,
                        'name' => $otherBarangay->name,
                    ],
                    'resource' => [
                        'category' => $match->resourceNeed->category,
                        'quantity_needed' => $match->resourceNeed->quantity,
                        'quantity_available' => $match->physicalDonation->quantity,
                    ],
                    'conversation' => $match->conversation ? [
                        'id' => $match->conversation->id,
                        'unread_count' => $unreadCount,
                        'last_message_at' => $match->conversation->updated_at->format('M d, Y h:i A'),
                    ] : null,
                    'created_at' => $match->created_at->format('M d, Y h:i A'),
                    'updated_at' => $match->updated_at->format('M d, Y h:i A'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching active matches: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching active matches',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Respond to a match request (accept or reject)
     */
    public function respondToMatch(Request $request, $matchId)
    {
        try {
            $barangayId = session('barangay_id');

            // Validate action first
            $request->validate([
                'action' => 'required|in:accept,reject',
            ]);

            $action = $request->input('action');

            // Message is required only for reject
            $validated = $request->validate([
                'message' => $action === 'reject' ? 'required|string|max:500' : 'nullable|string|max:500',
            ]);

            $match = ResourceMatch::with([
                'resourceNeed',
                'physicalDonation',
                'requestingBarangay',
                'donatingBarangay'
            ])->findOrFail($matchId);

            // Verify this barangay is the donor
            if ($match->donating_barangay_id !== $barangayId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - you are not the donor for this match'
                ], 403);
            }

            // Can only respond to pending matches
            if ($match->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This match has already been responded to'
                ], 400);
            }

            DB::beginTransaction();

            $newStatus = $action === 'accept' ? 'accepted' : 'rejected';

            $match->update([
                'status' => $newStatus,
                'barangay_response' => $validated['message'] ?? null,
                'responded_at' => now(),
            ]);

            // If accepted, create a conversation
            if ($newStatus === 'accepted') {
                $conversation = MatchConversation::create([
                    'resource_match_id' => $match->id,
                    'requesting_barangay_id' => $match->requesting_barangay_id,
                    'donating_barangay_id' => $match->donating_barangay_id,
                    'is_active' => true,
                    'last_message_at' => now(),
                    'last_message_by' => $barangayId,
                ]);

                // Add system message
                MatchMessage::create([
                    'conversation_id' => $conversation->id,
                    'sender_user_id' => session('user_id'),
                    'sender_barangay_id' => $barangayId,
                    'message' => "Match accepted by {$match->donatingBarangay->name}. Conversation started.",
                    'message_type' => 'system',
                    'is_read_by_requester' => false,
                    'is_read_by_donor' => true, // Donor created it, so marked as read for them
                ]);
            }

            // Create notification for requester
            MatchNotification::create([
                'resource_match_id' => $match->id,
                'barangay_id' => $match->requesting_barangay_id,
                'type' => $newStatus === 'accepted' ? 'match_accepted' : 'match_rejected',
                'title' => $newStatus === 'accepted' ? 'Match Request Accepted' : 'Match Request Rejected',
                'message' => $validated['message'] ?? ($newStatus === 'accepted'
                    ? "{$match->donatingBarangay->name} has accepted your match request. Start coordinating in the conversation."
                    : 'Match request was rejected.'),
                'is_read' => false,
            ]);

            // Create notification for LDRRMO user
            MatchNotification::create([
                'resource_match_id' => $match->id,
                'user_id' => $match->initiated_by,
                'type' => $newStatus === 'accepted' ? 'match_accepted' : 'match_rejected',
                'title' => $newStatus === 'accepted' ? 'Match Accepted' : 'Match Rejected',
                'message' => "{$match->donatingBarangay->name} has " . ($newStatus === 'accepted' ? 'accepted' : 'rejected') . " the match with {$match->requestingBarangay->name}.",
                'is_read' => false,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Match ' . $newStatus . ' successfully',
                'data' => [
                    'match_id' => $match->id,
                    'status' => $newStatus,
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error responding to match: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error responding to match',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify online donation (monetary)
     */
    public function verifyOnlineDonation(Request $request, $donationId)
    {
        try {
            $userId = session('user_id');
            $barangayId = session('barangay_id');

            $donation = Donation::where('id', $donationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            // Validate status
            if ($donation->verification_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'This donation has already been processed'
                ], 400);
            }

            $request->validate([
                'action' => 'required|in:verify,reject',
                'rejection_reason' => 'required_if:action,reject|max:500'
            ]);

            // Prevent rejection of blockchain-verified donations (ONLY for reject action)
            if ($request->action === 'reject' && $donation->blockchain_status === 'confirmed' && $donation->blockchain_tx_hash) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot reject a donation that has been recorded on the blockchain. The blockchain serves as immutable proof of this payment.'
                ], 400);
            }

            if ($request->action === 'verify') {
                $donation->verification_status = 'verified';
                $donation->verified_by = $userId;
                $donation->verified_at = now();
                $donation->status = 'confirmed';
                $message = 'Donation verified successfully';
            } else {
                $donation->verification_status = 'rejected';
                $donation->verified_by = $userId;
                $donation->verified_at = now();
                $donation->rejection_reason = $request->rejection_reason;
                $message = 'Donation rejected';
            }

            $donation->save();

            return response()->json([
                'success' => true,
                'message' => $message,
                'donation' => $donation
            ]);

        } catch (\Exception $e) {
            Log::error('Error verifying donation: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify donation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record physical donation to blockchain
     */
    public function recordPhysicalDonationToBlockchain($donationId)
    {
        try {
            $barangayId = session('barangay_id');

            $donation = PhysicalDonation::where('id', $donationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            // Record to blockchain
            $result = $donation->recordToBlockchain();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error recording to blockchain: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to record to blockchain',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify physical donation blockchain integrity
     */
    public function verifyPhysicalDonationBlockchain($donationId)
    {
        try {
            $barangayId = session('barangay_id');

            $donation = PhysicalDonation::where('id', $donationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            // Verify blockchain integrity
            $result = $donation->verifyBlockchainIntegrity();

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('Error verifying blockchain: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify blockchain',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get physical donation verification status
     */
    public function getPhysicalDonationVerificationStatus($donationId)
    {
        try {
            $barangayId = session('barangay_id');

            $donation = PhysicalDonation::where('id', $donationId)
                ->where('barangay_id', $barangayId)
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'verification_status' => $donation->verification_status,
                'offchain_hash' => $donation->offchain_hash,
                'onchain_hash' => $donation->onchain_hash,
                'verified_at' => $donation->verified_at,
                'last_check' => $donation->last_verification_check,
                'blockchain_status' => $donation->blockchain_status,
                'blockchain_tx_hash' => $donation->blockchain_tx_hash
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get verification status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
