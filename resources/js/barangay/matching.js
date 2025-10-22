/**
 * Matching System
 * Handles incoming requests, my requests, active matches, and conversations
 */

let currentMatchId = null;
let currentConversationMatchId = null;
let currentConversationData = null;
let messagePollingInterval = null;
let isAtBottom = true;

/**
 * Generic API fetch wrapper with error handling
 * @async
 * @param {string} url - API endpoint URL
 * @param {Object} options - Fetch options
 * @returns {Promise<Object>} JSON response
 */
async function fetchAPI(url, options = {}) {
    try {
        // csrfToken from utils.js
        const response = await fetch(url, {
            ...options,
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                ...options.headers,
            },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`API Error (${url}):`, error);
        throw error;
    }
}

/**
 * Loads incoming match requests (for donors)
 * @async
 * @param {boolean} silentLoad - If true, only updates badge without displaying
 * @returns {Promise<void>}
 */
async function loadIncomingRequests(silentLoad = false) {
    if (!silentLoad) {
        document.getElementById("incoming-requests-list").innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading requests...</p>
            </div>
        `;
    }

    try {
        const response = await fetchAPI("/api/bdrrmc/matches/incoming");
        const matches = response.data || [];
        const counts = response.counts || {
            pending: 0,
            accepted: 0,
            rejected: 0,
        };

        // Update badge (only show pending requests)
        const badge = document.getElementById("incoming-requests-badge");
        if (counts.pending > 0) {
            badge.classList.remove("hidden");
            badge.textContent = counts.pending;
        } else {
            badge.classList.add("hidden");
        }

        // Update stats
        document.getElementById("stats-pending-requests").textContent =
            counts.pending;
        document.getElementById("stats-accepted-requests").textContent =
            counts.accepted;
        document.getElementById("stats-rejected-requests").textContent =
            counts.rejected;

        if (!silentLoad) {
            displayIncomingRequests(matches);
        }
    } catch (error) {
        console.error("Error loading incoming requests:", error);
        if (!silentLoad) {
            document.getElementById("incoming-requests-list").innerHTML = `
                <div class="text-center py-8 text-red-500">
                    <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                    <p>Failed to load requests</p>
                </div>
            `;
        }
    }
}

/**
 * Displays incoming match requests
 * @param {Array} requests - Array of match request objects
 */
function displayIncomingRequests(requests) {
    const container = document.getElementById("incoming-requests-list");

    if (!requests || requests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Match Requests</h3>
                <p class="text-gray-500">You don't have any match requests at the moment.</p>
            </div>
        `;
        return;
    }

    const html = requests
        .map((request) => {
            const statusBadge = getStatusBadge(request.status);
            const isPending = request.status === "pending";

            return `
        <div class="border-2 ${isPending ? "border-blue-200 bg-blue-50" : request.status === "accepted" ? "border-green-200 bg-green-50" : "border-red-200 bg-red-50"} rounded-lg p-5 hover:shadow-lg transition">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        ${statusBadge}
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">
                            <i class="fas fa-map-marker-alt mr-1"></i>${request.requesting_barangay.name}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        Request from ${request.requesting_barangay.name}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Received: ${request.created_at}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <!-- What they need -->
                <div class="bg-white border-2 border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-circle mr-2 text-blue-600"></i>
                        They Need
                    </h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-semibold">Category:</span> ${request.resource_need.category}</p>
                        <p><span class="font-semibold">Quantity:</span> ${request.resource_need.quantity}</p>
                        <p>
                            <span class="font-semibold">Urgency:</span>
                            <span class="px-2 py-1 rounded-full text-xs font-bold ${getUrgencyColor(request.resource_need.urgency)}">
                                ${request.resource_need.urgency.toUpperCase()}
                            </span>
                        </p>
                        ${request.resource_need.description ? `<p class="text-xs text-gray-600 mt-2">${request.resource_need.description}</p>` : ""}
                    </div>
                </div>

                <!-- Your donation -->
                <div class="bg-white border-2 border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                        <i class="fas fa-hands-helping mr-2 text-green-600"></i>
                        Your Donation
                    </h4>
                    <div class="space-y-1 text-sm">
                        <p><span class="font-semibold">Category:</span> ${request.physical_donation.category}</p>
                        <p><span class="font-semibold">Available:</span> ${request.physical_donation.quantity}</p>
                        <p><span class="font-semibold">Tracking:</span> ${request.physical_donation.tracking_code}</p>
                    </div>
                </div>
            </div>

            ${
                request.ldrrmo_message
                    ? `
            <div class="bg-white border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-comment-alt mr-1"></i>LDRRMO Message:</p>
                <p class="text-sm text-gray-600">${request.ldrrmo_message}</p>
            </div>
            `
                    : ""
            }

            ${
                request.barangay_response
                    ? `
            <div class="bg-white border border-gray-200 rounded-lg p-3 mb-4">
                <p class="text-sm font-semibold text-gray-700 mb-1"><i class="fas fa-reply mr-1"></i>Your Response:</p>
                <p class="text-sm text-gray-600">${request.barangay_response}</p>
            </div>
            `
                    : ""
            }

            <div class="flex gap-3 justify-end">
                ${
                    isPending
                        ? `
                <button onclick="openRespondModal(${request.id}, 'reject')"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-semibold">
                    <i class="fas fa-times mr-2"></i>Reject
                </button>
                <button onclick="openRespondModal(${request.id}, 'accept')"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold">
                    <i class="fas fa-check mr-2"></i>Accept & Start Conversation
                </button>
                `
                        : request.status === "accepted"
                          ? `
                <button onclick="viewConversation(${request.id})"
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-comments mr-2"></i>View Conversation
                </button>
                `
                          : ""
                }
            </div>
        </div>
        `;
        })
        .join("");

    container.innerHTML = html;
}

/**
 * Loads my match requests (for requesters)
 * @async
 * @returns {Promise<void>}
 */
async function loadMyRequests() {
    document.getElementById("my-requests-list").innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading your requests...</p>
        </div>
    `;

    try {
        const response = await fetchAPI("/api/bdrrmc/matches/my-requests");
        const matches = response.data || [];
        displayMyRequests(matches);
    } catch (error) {
        console.error("Error loading my requests:", error);
        document.getElementById("my-requests-list").innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load requests</p>
            </div>
        `;
    }
}

/**
 * Displays my match requests
 * @param {Array} requests - Array of match request objects
 */
function displayMyRequests(requests) {
    const container = document.getElementById("my-requests-list");

    // Filter to show only pending and rejected requests (not accepted)
    const pendingRequests = requests.filter((r) => r.status !== "accepted");

    if (!pendingRequests || pendingRequests.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-paper-plane text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Pending Requests</h3>
                <p class="text-gray-500">You don't have any pending match requests. Accepted matches appear in the "Active Matches" tab.</p>
            </div>
        `;
        return;
    }

    const html = pendingRequests
        .map((request) => {
            // Safe access to nested properties with fallbacks
            const donatingBarangay =
                request.donating_barangay?.name ||
                request.donating_barangay ||
                "Unknown Barangay";
            const initiatedAt =
                request.initiated_at || request.created_at || "Unknown date";
            const resourceNeed = request.resource_need || {};
            const category = resourceNeed.category || "Unknown category";
            const quantity = resourceNeed.quantity || "Unknown quantity";
            const donationItems =
                request.donation_items || "Items not specified";
            const statusLabel =
                request.status_label ||
                (request.status ? request.status.toUpperCase() : "PENDING");

            return `
        <div class="border rounded-lg p-5 hover:shadow-md transition">
            <div class="flex items-start justify-between mb-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold ${getStatusColor(request.status)}">
                            <i class="${getStatusIcon(request.status)} mr-1"></i>
                            ${statusLabel}
                        </span>
                    </div>

                    <h3 class="text-lg font-bold text-gray-900">
                        Match with ${donatingBarangay}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-calendar mr-1"></i>
                        Initiated: ${initiatedAt}
                    </p>
                </div>
            </div>

            <div class="bg-gray-50 border rounded-lg p-3 mb-3">
                <p class="text-sm"><span class="font-semibold">Your Need:</span> ${category} - ${quantity}</p>
                <p class="text-sm"><span class="font-semibold">Donor Has:</span> ${donationItems}</p>
            </div>

            ${
                request.response_message
                    ? `
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-3">
                    <p class="text-xs text-blue-600 font-semibold mb-1">Response from ${donatingBarangay}:</p>
                    <p class="text-sm text-gray-800">"${request.response_message}"</p>
                </div>
            `
                    : ""
            }

            <div class="flex gap-2 justify-end">
                ${
                    request.status === "pending"
                        ? `
                    <button class="px-4 py-2 bg-gray-200 text-gray-600 rounded-lg cursor-not-allowed" disabled>
                        <i class="fas fa-clock mr-2"></i>Waiting for Response
                    </button>
                `
                        : ""
                }
                ${
                    request.status === "rejected"
                        ? `
                    <div class="px-4 py-2 bg-red-100 text-red-700 rounded-lg text-sm">
                        <i class="fas fa-times-circle mr-2"></i>This request was declined
                    </div>
                `
                        : ""
                }
            </div>
        </div>
    `;
        })
        .join("");

    container.innerHTML = html;
}

/**
 * Loads active matches (for both donors and requesters)
 * @async
 * @returns {Promise<void>}
 */
async function loadActiveMatches() {
    document.getElementById("active-matches-list").innerHTML = `
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
            <p>Loading active matches...</p>
        </div>
    `;

    try {
        const response = await fetchAPI("/api/bdrrmc/matches/active");
        const matches = response.data || [];

        // Update badge (show total number or unread count)
        const badge = document.getElementById("active-matches-badge");
        const unreadTotal = matches.reduce(
            (sum, m) => sum + (m.conversation?.unread_count || 0),
            0,
        );
        if (unreadTotal > 0) {
            badge.classList.remove("hidden");
            badge.textContent = unreadTotal;
        } else {
            badge.classList.add("hidden");
        }

        displayActiveMatches(matches);
    } catch (error) {
        console.error("Error loading active matches:", error);
        document.getElementById("active-matches-list").innerHTML = `
            <div class="text-center py-8 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load matches</p>
            </div>
        `;
    }
}

/**
 * Displays active matches
 * @param {Array} matches - Array of active match objects
 */
function displayActiveMatches(matches) {
    const container = document.getElementById("active-matches-list");

    if (!matches || matches.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-comments text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700 mb-2">No Active Matches</h3>
                <p class="text-gray-500">You don't have any active conversations at the moment.</p>
            </div>
        `;
        return;
    }

    const html = matches
        .map((match) => {
            // Safe access to nested properties with fallbacks (matching backend API structure)
            const otherBarangay =
                match.other_barangay?.name ||
                match.other_barangay ||
                "Unknown Barangay";
            const role = match.my_role || match.role || "participant";
            const acceptedAt =
                match.accepted_at ||
                match.created_at ||
                match.updated_at ||
                "Unknown date";
            const lastMessageAt =
                match.conversation?.last_message_at ||
                match.last_message_at ||
                "No messages yet";
            const resourceCategory =
                match.resource?.category ||
                match.resource_category ||
                match.resource_need?.category ||
                "Unknown resource";
            const totalMessages =
                match.conversation?.total_messages ||
                match.total_messages ||
                match.conversation?.message_count ||
                0;
            const unreadMessages =
                match.conversation?.unread_count || match.unread_messages || 0;
            const lastMessage =
                match.conversation?.last_message || match.last_message || "";

            return `
        <div class="border-2 border-green-200 rounded-lg p-5 bg-green-50 hover:shadow-lg transition cursor-pointer"
             onclick="viewConversation(${match.id})">

            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold">
                            <i class="fas fa-check-circle mr-1"></i>Active Match
                        </span>
                        ${
                            unreadMessages > 0
                                ? `
                            <span class="px-3 py-1 bg-red-500 text-white rounded-full text-xs font-bold animate-pulse">
                                <i class="fas fa-envelope mr-1"></i>${unreadMessages} New
                            </span>
                        `
                                : ""
                        }
                    </div>

                    <h3 class="text-lg font-bold text-gray-900 mb-1">
                        ${role === "requester" ? "Receiving from" : "Donating to"} ${otherBarangay}
                    </h3>
                    <p class="text-sm text-gray-600">
                        <i class="fas fa-handshake mr-1"></i>
                        Match accepted ${acceptedAt}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-xs text-gray-500 mb-1">Last message:</p>
                    <p class="text-sm font-semibold text-gray-700">${lastMessageAt}</p>
                </div>
            </div>

            <div class="bg-white border rounded-lg p-3 mb-3">
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-gray-600">Resource:</p>
                        <p class="font-semibold">${resourceCategory}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Total Messages:</p>
                        <p class="font-semibold">${totalMessages}</p>
                    </div>
                </div>
            </div>

            ${
                lastMessage
                    ? `
                <div class="bg-gray-50 border rounded-lg p-3 mb-3">
                    <p class="text-xs text-gray-500 mb-1">Latest message:</p>
                    <p class="text-sm text-gray-800">"${lastMessage.substring(0, 100)}${lastMessage.length > 100 ? "..." : ""}"</p>
                </div>
            `
                    : ""
            }

            <div class="flex gap-2 justify-end">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-semibold">
                    <i class="fas fa-comments mr-2"></i>Open Conversation
                </button>
            </div>
        </div>
    `;
        })
        .join("");

    container.innerHTML = html;
}

/**
 * Opens the respond modal for accepting or rejecting a match
 * @async
 * @param {number} matchId - The ID of the match
 * @param {string} action - Either 'accept' or 'reject'
 * @returns {Promise<void>}
 */
async function openRespondModal(matchId, action) {
    currentMatchId = matchId;

    // Get match details
    try {
        const response = await fetchAPI("/api/bdrrmc/matches/incoming");
        const matches = response.data || [];
        const match = matches.find((r) => r.id === matchId);

        if (!match) {
            alert("Match not found");
            return;
        }

        // Update modal title
        const title =
            action === "accept"
                ? "✅ Accept Match Request"
                : "❌ Reject Match Request";
        document.getElementById("respondModalTitle").textContent = title;

        // Display match details
        document.getElementById("respondModalContent").innerHTML = `
            <div class="bg-gray-50 border rounded-lg p-4 mb-4">
                <h4 class="font-semibold text-gray-900 mb-3">Match Details</h4>

                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <p class="text-xs text-gray-600">Requesting Barangay</p>
                        <p class="font-semibold">${match.requesting_barangay.name}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600">Status</p>
                        <p class="font-semibold">${getStatusBadge(match.status)}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-600 font-semibold mb-1">They Need:</p>
                        <p class="text-sm font-semibold">${match.resource_need.category}</p>
                        <p class="text-sm text-gray-700">${match.resource_need.quantity}</p>
                        <p class="text-xs mt-1">${getUrgencyColor(match.resource_need.urgency)}</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded p-3">
                        <p class="text-xs text-green-600 font-semibold mb-1">You Have:</p>
                        <p class="text-sm font-semibold">${match.physical_donation.category}</p>
                        <p class="text-sm text-gray-700">${match.physical_donation.quantity}</p>
                        <p class="text-xs text-gray-500 mt-1">Code: ${match.physical_donation.tracking_code}</p>
                    </div>
                </div>

                ${
                    match.ldrrmo_message
                        ? `
                    <div class="mt-3 bg-blue-50 border border-blue-200 rounded p-3">
                        <p class="text-xs text-blue-600 font-semibold mb-1">Message from LDRRMO:</p>
                        <p class="text-sm text-gray-800">${match.ldrrmo_message}</p>
                    </div>
                `
                        : ""
                }
            </div>

            ${
                action === "accept"
                    ? `
                <div class="bg-green-50 border-2 border-green-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-green-900 mb-2 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        What happens when you accept?
                    </h4>
                    <ul class="text-sm text-green-800 space-y-1 list-disc list-inside">
                        <li>A conversation will be created with ${match.requesting_barangay.name}</li>
                        <li>Both barangays will be able to coordinate the transfer</li>
                        <li>You can discuss pickup/delivery details and timing</li>
                        <li>Your donation will be marked as "in process"</li>
                    </ul>
                </div>
            `
                    : `
                <div class="bg-red-50 border-2 border-red-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-red-900 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Please provide a reason for rejection
                    </h4>
                    <p class="text-sm text-red-800">
                        Help ${match.requesting_barangay.name} understand why you cannot fulfill this request.
                    </p>
                </div>
            `
            }
        `;

        // Show/hide buttons
        if (action === "accept") {
            document
                .getElementById("acceptMatchBtn")
                .classList.remove("hidden");
            document.getElementById("rejectMatchBtn").classList.add("hidden");
            document.getElementById("responseMessage").placeholder =
                'Example: "Happy to help! We can arrange pickup this week. What time works best for you?"';
        } else {
            document.getElementById("acceptMatchBtn").classList.add("hidden");
            document
                .getElementById("rejectMatchBtn")
                .classList.remove("hidden");
            document.getElementById("responseMessage").placeholder =
                'Example: "Sorry, this donation has already been committed to another barangay." or "We need to keep this for our own residents."';
        }

        // Clear previous message
        document.getElementById("responseMessage").value = "";

        // Show modal
        document.getElementById("respondMatchModal").classList.remove("hidden");
    } catch (error) {
        console.error("Error loading match details:", error);
        alert("Failed to load match details");
    }
}

/**
 * Submits an accept response for a match
 * @async
 * @returns {Promise<void>}
 */
async function submitAccept() {
    const message = document.getElementById("responseMessage").value.trim();

    if (!message) {
        alert("⚠️ Please enter a message to the requesting barangay");
        return;
    }

    if (!currentMatchId) {
        alert("Error: No match selected");
        return;
    }

    try {
        const response = await fetchAPI(
            `/api/bdrrmc/matches/${currentMatchId}/respond`,
            {
                method: "POST",
                body: JSON.stringify({
                    action: "accept",
                    message: message,
                }),
            },
        );

        if (response.success) {
            alert(
                "✅ Match Accepted!\n\n" +
                    "A conversation has been created. You can now coordinate with the requesting barangay.\n\n" +
                    'View it in the "Active Matches" tab.',
            );

            closeRespondModal();
            loadIncomingRequests();
            loadActiveMatches();

            // Refresh notifications
            if (typeof loadNotifications === "function") {
                loadNotifications();
            }
        } else {
            alert("❌ Error: " + response.message);
        }
    } catch (error) {
        console.error("Error accepting match:", error);
        alert("Failed to accept match. Please try again.");
    }
}

/**
 * Submits a reject response for a match
 * @async
 * @returns {Promise<void>}
 */
async function submitReject() {
    const message = document.getElementById("responseMessage").value.trim();

    if (!message) {
        alert("⚠️ Please provide a reason for rejecting this request");
        return;
    }

    if (!currentMatchId) {
        alert("Error: No match selected");
        return;
    }

    if (!confirm("Are you sure you want to reject this match request?")) {
        return;
    }

    try {
        const response = await fetchAPI(
            `/api/bdrrmc/matches/${currentMatchId}/respond`,
            {
                method: "POST",
                body: JSON.stringify({
                    action: "reject",
                    message: message,
                }),
            },
        );

        if (response.success) {
            alert(
                "✅ Match request has been rejected.\n\nThe requesting barangay has been notified.",
            );

            closeRespondModal();
            loadIncomingRequests();

            // Refresh notifications
            if (typeof loadNotifications === "function") {
                loadNotifications();
            }
        } else {
            alert("❌ Error: " + response.message);
        }
    } catch (error) {
        console.error("Error rejecting match:", error);
        alert("Failed to reject match. Please try again.");
    }
}

/**
 * Opens a conversation for a match
 * @async
 * @param {number} matchId - The ID of the match
 * @returns {Promise<void>}
 */
async function viewConversation(matchId) {
    currentConversationMatchId = matchId;

    // Show modal
    document.getElementById("conversationModal").classList.remove("hidden");

    // Load conversation
    await loadConversation(matchId);

    // Start polling for new messages every 5 seconds
    messagePollingInterval = setInterval(() => {
        loadConversation(matchId, true); // true = silent update
    }, 5000);

    // Focus on message input
    document.getElementById("messageInput").focus();
}

/**
 * Loads a conversation and its messages
 * @async
 * @param {number} matchId - The ID of the match
 * @param {boolean} silentUpdate - If true, doesn't show loading spinner
 * @returns {Promise<void>}
 */
async function loadConversation(matchId, silentUpdate = false) {
    if (!silentUpdate) {
        document.getElementById("messagesContainer").innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-spinner fa-spin text-3xl mb-2"></i>
                <p>Loading conversation...</p>
            </div>
        `;
    }

    try {
        const data = await fetchAPI(
            `/api/bdrrmc/matches/${matchId}/conversation`,
        );
        currentConversationData = data;

        // Determine other barangay based on role
        const otherBarangay =
            data.my_role === "requester"
                ? data.participants.donor.name
                : data.participants.requester.name;

        // Update header
        document.getElementById("conversationTitle").textContent =
            `Conversation with ${otherBarangay}`;
        document.getElementById("conversationSubtitle").textContent =
            `Match #${matchId} • ${data.my_role === "requester" ? "Receiving" : "Donating"} ${data.resource_details.category}`;

        // Update status badge
        const statusBadge = document.getElementById("conversationStatus");
        if (data.is_active) {
            statusBadge.innerHTML =
                '<i class="fas fa-circle text-xs mr-1"></i>Active';
            statusBadge.className =
                "px-3 py-1 bg-green-500 text-white rounded-full text-xs font-bold";
            document
                .getElementById("completeMatchBtn")
                .classList.remove("hidden");
        } else {
            statusBadge.innerHTML =
                '<i class="fas fa-check-circle text-xs mr-1"></i>Completed';
            statusBadge.className =
                "px-3 py-1 bg-gray-500 text-white rounded-full text-xs font-bold";
            document.getElementById("completeMatchBtn").classList.add("hidden");
        }

        // Update match info banner
        document.getElementById("matchInfoBanner").innerHTML = `
            <div class="flex items-center justify-between text-sm">
                <div class="flex items-center gap-6">
                    <div>
                        <span class="text-gray-600">Category:</span>
                        <span class="font-semibold text-indigo-600">${data.resource_details.category}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Need:</span>
                        <span class="font-semibold">${data.resource_details.quantity_needed}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Available:</span>
                        <span class="font-semibold">${data.resource_details.quantity_available}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Messages:</span>
                        <span class="font-semibold">${data.messages.length}</span>
                    </div>
                </div>
                <span class="px-3 py-1 ${getUrgencyColor(data.resource_details.urgency)} rounded-full text-xs font-bold">
                    ${data.resource_details.urgency.toUpperCase()}
                </span>
            </div>
        `;

        // Get current barangay ID from session
        const currentBarangayId = '{{ session("barangay_id") }}';

        // Display messages
        displayMessages(data.messages, currentBarangayId, silentUpdate);

        // Disable input if conversation is closed
        const messageInput = document.getElementById("messageInput");
        const sendBtn = document.getElementById("sendMessageBtn");
        if (!data.is_active) {
            messageInput.disabled = true;
            messageInput.placeholder =
                "This conversation has been completed and is now read-only";
            sendBtn.disabled = true;
            sendBtn.classList.add("opacity-50", "cursor-not-allowed");
        } else {
            messageInput.disabled = false;
            messageInput.placeholder = "Type your message...";
            sendBtn.disabled = false;
            sendBtn.classList.remove("opacity-50", "cursor-not-allowed");
        }
    } catch (error) {
        console.error("Error loading conversation:", error);
        document.getElementById("messagesContainer").innerHTML = `
            <div class="text-center py-12 text-red-500">
                <i class="fas fa-exclamation-circle text-3xl mb-2"></i>
                <p>Failed to load conversation</p>
            </div>
        `;
    }
}

/**
 * Displays messages in the conversation
 * @param {Array} messages - Array of message objects
 * @param {string} currentBarangayId - The current user's barangay ID
 * @param {boolean} silentUpdate - If true, preserves scroll position
 */
function displayMessages(messages, currentBarangayId, silentUpdate = false) {
    const container = document.getElementById("messagesContainer");

    // Check if user is at bottom before update
    const wasAtBottom = isAtBottom;
    if (!silentUpdate) {
        isAtBottom = true;
    }

    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12 text-gray-400">
                <i class="fas fa-comments text-6xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-600 mb-2">Start the Conversation</h3>
                <p class="text-sm">Send your first message to begin coordinating the resource transfer.</p>
            </div>
        `;
        return;
    }

    const html = messages
        .map((msg) => {
            // Use is_mine from backend instead of calculating
            const isOwn = msg.is_mine;
            const isSystem = msg.is_system || msg.message_type === "system";

            if (isSystem) {
                return `
                <div class="flex justify-center my-6">
                    <div class="bg-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium">
                        <i class="fas fa-info-circle mr-1"></i>${escapeHtml(msg.message)}
                    </div>
                </div>
            `;
            }

            return `
            <div class="flex ${isOwn ? "justify-end" : "justify-start"} mb-4">
                <div class="max-w-[70%]">
                    ${
                        !isOwn
                            ? `
                        <p class="text-xs text-gray-500 mb-1 ml-2">${escapeHtml(msg.sender_name || "Unknown")}</p>
                    `
                            : ""
                    }

                    <div class="px-4 py-3 rounded-lg ${
                        isOwn
                            ? "bg-indigo-600 text-white rounded-br-none"
                            : "bg-white border border-gray-200 text-gray-800 rounded-bl-none"
                    }">
                        <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>

                        ${
                            msg.attachment
                                ? `
                            <div class="mt-2 pt-2 border-t ${isOwn ? "border-indigo-500" : "border-gray-200"}">
                                <a href="${msg.attachment.url}"
                                   target="_blank"
                                   class="text-xs ${isOwn ? "text-indigo-200 hover:text-white" : "text-indigo-600 hover:text-indigo-800"} hover:underline flex items-center">
                                    <i class="${msg.attachment.icon || "fas fa-paperclip"} mr-1"></i>
                                    ${escapeHtml(msg.attachment.name || "Attachment")}
                                </a>
                            </div>
                        `
                                : ""
                        }

                        <div class="flex items-center justify-end mt-1 gap-2">
                            <p class="text-xs ${isOwn ? "text-indigo-200" : "text-gray-500"}">
                                ${msg.time_ago || msg.created_at}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        })
        .join("");

    container.innerHTML = html;

    // Scroll to bottom if user was at bottom or if it's a new load
    if (wasAtBottom || !silentUpdate) {
        scrollToBottom();
    }

    // Setup scroll listener
    container.onscroll = () => {
        const { scrollTop, scrollHeight, clientHeight } = container;
        isAtBottom = scrollTop + clientHeight >= scrollHeight - 50;
    };
}

/**
 * Scrolls the messages container to the bottom
 */
function scrollToBottom() {
    const container = document.getElementById("messagesContainer");
    container.scrollTop = container.scrollHeight;
}

/**
 * Sends a message in the current conversation
 * @async
 * @returns {Promise<void>}
 */
async function sendMessage() {
    const input = document.getElementById("messageInput");
    const message = input.value.trim();

    if (!message) {
        alert("⚠️ Please enter a message");
        return;
    }

    if (!currentConversationMatchId) {
        alert("Error: No conversation selected");
        return;
    }

    // Disable button while sending
    const sendBtn = document.getElementById("sendMessageBtn");
    const originalText = sendBtn.innerHTML;
    sendBtn.disabled = true;
    sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';

    try {
        const response = await fetchAPI(
            `/api/bdrrmc/matches/${currentConversationMatchId}/messages`,
            {
                method: "POST",
                body: JSON.stringify({ message }),
            },
        );

        if (response.success) {
            // Clear input
            input.value = "";

            // Reload messages
            await loadConversation(currentConversationMatchId, true);

            // Scroll to bottom
            scrollToBottom();

            // Focus back on input
            input.focus();
        } else {
            alert("❌ Error: " + response.message);
        }
    } catch (error) {
        console.error("Error sending message:", error);
        alert("Failed to send message. Please try again.");
    } finally {
        sendBtn.disabled = false;
        sendBtn.innerHTML = originalText;
    }
}

/**
 * Handles keydown events in the message input
 * @param {KeyboardEvent} event - The keyboard event
 */
function handleMessageKeydown(event) {
    // Send on Enter (without Shift)
    if (event.key === "Enter" && !event.shiftKey) {
        event.preventDefault();
        sendMessage();
    }
}

/**
 * Marks messages as read for the current match
 * @async
 * @param {number} matchId - The ID of the match
 * @returns {Promise<void>}
 */
async function markMessagesAsRead(matchId) {
    try {
        await fetchAPI(`/api/bdrrmc/matches/${matchId}/messages/mark-read`, {
            method: "POST",
        });

        // Update notification count
        if (typeof loadNotifications === "function") {
            loadNotifications();
        }
    } catch (error) {
        console.error("Error marking messages as read:", error);
    }
}

/**
 * Closes the conversation modal
 */
function closeConversation() {
    // Stop polling
    if (messagePollingInterval) {
        clearInterval(messagePollingInterval);
        messagePollingInterval = null;
    }

    // Hide modal
    document.getElementById("conversationModal").classList.add("hidden");

    // Reset state
    currentConversationMatchId = null;
    currentConversationData = null;
    isAtBottom = true;

    // Reload lists to update counts
    if (typeof loadActiveMatches === "function") {
        loadActiveMatches();
    }
    if (typeof loadMyMatches === "function") {
        loadMyMatches();
    }
}

/**
 * Escapes HTML special characters
 * @param {string} text - Text to escape
 * @returns {string} Escaped HTML
 */
function escapeHtml(text) {
    const div = document.createElement("div");
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Gets the status badge HTML for a match status
 * @param {string} status - The match status
 * @returns {string} HTML string for the badge
 */
function getStatusBadge(status) {
    const badges = {
        pending:
            '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold"><i class="fas fa-clock mr-1"></i>Pending</span>',
        accepted:
            '<span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold"><i class="fas fa-check-circle mr-1"></i>Accepted</span>',
        rejected:
            '<span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold"><i class="fas fa-times-circle mr-1"></i>Rejected</span>',
        completed:
            '<span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-bold"><i class="fas fa-flag-checkered mr-1"></i>Completed</span>',
        cancelled:
            '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold"><i class="fas fa-ban mr-1"></i>Cancelled</span>',
    };
    return (
        badges[status] ||
        '<span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">' +
            status +
            "</span>"
    );
}

/**
 * Gets the CSS class for urgency color
 * @param {string} urgency - The urgency level
 * @returns {string} CSS class string
 */
function getUrgencyColor(urgency) {
    const colors = {
        low: "bg-gray-100 text-gray-700",
        medium: "bg-blue-100 text-blue-700",
        high: "bg-orange-100 text-orange-700",
        critical: "bg-red-100 text-red-700",
    };
    return colors[urgency] || "bg-gray-100 text-gray-700";
}

/**
 * Gets the CSS class for status color
 * @param {string} status - The match status
 * @returns {string} CSS class string
 */
function getStatusColor(status) {
    const colors = {
        pending: "bg-yellow-100 text-yellow-700",
        accepted: "bg-green-100 text-green-700",
        rejected: "bg-red-100 text-red-700",
        completed: "bg-blue-100 text-blue-700",
        cancelled: "bg-gray-100 text-gray-700",
    };
    return colors[status] || "bg-gray-100 text-gray-700";
}

/**
 * Gets the icon class for a match status
 * @param {string} status - The match status
 * @returns {string} Font Awesome icon class
 */
function getStatusIcon(status) {
    const icons = {
        pending: "fas fa-clock",
        accepted: "fas fa-check-circle",
        rejected: "fas fa-times-circle",
        completed: "fas fa-flag-checkered",
        cancelled: "fas fa-ban",
    };
    return icons[status] || "fas fa-question-circle";
}

console.log("✅ BDRRMC Match Requests system loaded");
console.log("✅ Conversation UI loaded");
