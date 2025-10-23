/**
 * Donations Management
 * Handles physical and online donations (loading, recording, distributing)
 */

/**
 * Loads and displays physical donations
 * @async
 * @returns {Promise<void>}
 */
async function loadPhysicalDonations() {
    const tbody = document.getElementById("donationsList");
    tbody.innerHTML =
        '<tr><td colspan="7" class="px-4 py-8 text-center"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></td></tr>';

    try {
        const response = await fetch("/api/bdrrmc/physical-donations");
        const donations = await response.json();

        if (donations.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-box-open text-5xl mb-4 text-gray-300"></i>
                        <p class="text-lg">No physical donations recorded yet.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = donations
            .map(
                (donation) => `
            <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-3">
                    <a href="#" onclick="viewDonationDetails(${donation.id}); return false;" class="text-blue-600 hover:underline font-medium">${donation.tracking_code}</a>
                </td>
                <td class="px-4 py-3 text-gray-800">${donation.donor_name}</td>
                <td class="px-4 py-3 text-gray-600">${formatDateShort(donation.recorded_at)}</td>
                <td class="px-4 py-3 text-gray-600">${formatCategory(donation.category)}</td>
                <td class="px-4 py-3 text-gray-600">${donation.quantity}</td>
                <td class="px-4 py-3">
                    <span class="px-3 py-1 text-xs font-semibold rounded ${getDistributionStatusBadge(donation.distribution_status)}">
                        ${formatStatus(donation.distribution_status).toUpperCase()}
                    </span>
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        ${
                            donation.distribution_status === "fully_distributed"
                                ? `
                            <button onclick="viewDistributionDetails(${donation.id})" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                <i class="fas fa-camera"></i> View Distribution
                            </button>
                        `
                                : donation.distribution_status ===
                                    "partially_distributed"
                                  ? `
                            <button onclick="viewDistributionDetails(${donation.id})" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                <i class="fas fa-eye"></i> View
                            </button>
                            <button onclick="openDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                                <i class="fas fa-check-circle"></i> Mark Complete
                            </button>
                        `
                                  : `
                            <button onclick="openPartialDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm text-gray-700 hover:bg-gray-100 rounded transition">
                                Partially Dist.
                            </button>
                            <button onclick="openDistributeModal(${donation.id}, '${donation.tracking_code}')" class="flex items-center gap-2 px-3 py-1.5 text-sm bg-teal-500 text-white rounded hover:bg-teal-600 transition">
                                <i class="fas fa-check-circle"></i> Fully Dist.
                            </button>
                        `
                        }
                    </div>
                </td>
            </tr>
        `,
            )
            .join("");
    } catch (error) {
        console.error("Error loading donations:", error);
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-red-500">
                    <i class="fas fa-exclamation-circle text-5xl mb-4"></i>
                    <p class="text-lg">Error loading donations</p>
                </td>
            </tr>
        `;
    }
}

/**
 * Loads and displays online donations
 * @async
 * @returns {Promise<void>}
 */
async function loadOnlineDonations() {
    const tbody = document.getElementById("onlineDonationsList");

    try {
        const response = await fetch("/api/bdrrmc/online-donations");
        const data = await response.json();

        if (!data.success || !data.donations || data.donations.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="7" class="px-4 py-12 text-center text-gray-500"><i class="fas fa-globe text-5xl mb-4 text-gray-300"></i><p class="text-lg">No online donations yet.</p><p class="text-sm mt-2">Online donations from residents will appear here.</p></td></tr>';
            return;
        }

        tbody.innerHTML = data.donations
            .map((donation) => {
                const amount = parseFloat(donation.amount).toLocaleString(
                    "en-PH",
                    { minimumFractionDigits: 2 },
                );
                const paymentMethod = donation.payment_method
                    ? '<span class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-800">' +
                      donation.payment_method +
                      "</span>"
                    : '<span class="text-gray-400">-</span>';

                let verificationBadge = "";
                if (donation.verification_status === "verified") {
                    verificationBadge =
                        '<span class="px-2 py-1 rounded text-xs bg-green-100 text-green-800">Verified</span>';
                } else if (donation.verification_status === "pending") {
                    verificationBadge =
                        '<span class="px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-800">Pending</span>';
                } else {
                    verificationBadge =
                        '<span class="px-2 py-1 rounded text-xs bg-red-100 text-red-800">Rejected</span>';
                }

                const blockchain = donation.blockchain_verified
                    ? '<a href="' +
                      donation.explorer_url +
                      '" target="_blank" class="text-green-600 hover:text-green-800"><i class="fas fa-check-circle"></i> Verified</a>'
                    : '<span class="text-gray-400"><i class="fas fa-clock"></i> Pending</span>';

                let actionButton = '';
                if (donation.verification_status === 'pending') {
                    actionButton = '<button onclick="openVerificationModal(' + donation.id + ')" class="px-3 py-1 bg-blue-600 text-white rounded text-xs hover:bg-blue-700">Verify</button>';
                } else if (donation.verification_status === 'verified') {
                    actionButton = '<span class="text-xs text-gray-500">Verified</span>';
                } else {
                    actionButton = '<span class="text-xs text-red-500">Rejected</span>';
                }

                return (
                    '<tr class="border-b hover:bg-gray-50">' +
                    '<td class="px-4 py-3 text-sm">' +
                    donation.created_at +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm">' +
                    donation.donor_name +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm font-semibold">₱' +
                    amount +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm">' +
                    paymentMethod +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm">' +
                    verificationBadge +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm">' +
                    blockchain +
                    "</td>" +
                    '<td class="px-4 py-3 text-sm">' +
                    actionButton +
                    "</td>" +
                    "</tr>"
                );
            })
            .join("");
    } catch (error) {
        console.error("Error loading online donations:", error);
        tbody.innerHTML =
            '<tr><td colspan="7" class="px-4 py-6 text-center text-red-500"><i class="fas fa-exclamation-triangle mb-2"></i><p>Error loading donations. Please try again.</p></td></tr>';
    }
}

// Verification Modal Functions
let currentDonationId = null;

function openVerificationModal(donationId) {
    currentDonationId = donationId;
    document.getElementById('verificationModal').classList.remove('hidden');
}

function closeVerificationModal() {
    currentDonationId = null;
    document.getElementById('verificationModal').classList.add('hidden');
    document.getElementById('rejectionForm').classList.add('hidden');
    document.getElementById('rejectionReason').value = '';
}

function showRejectionForm() {
    document.getElementById('rejectionForm').classList.remove('hidden');
}

function hideRejectionForm() {
    document.getElementById('rejectionForm').classList.add('hidden');
    document.getElementById('rejectionReason').value = '';
}

async function confirmVerification(action) {
    if (!currentDonationId) return;

    try {
        const data = { action };
        if (action === 'reject') {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) {
                alert('Please provide a reason for rejection');
                return;
            }
            data.rejection_reason = reason;
        }

        const response = await fetchAPI(`/api/bdrrmc/donations/${currentDonationId}/verify`, {
            method: 'POST',
            body: JSON.stringify(data)
        });

        if (response.success) {
            alert(response.message);
            closeVerificationModal();
            loadOnlineDonations(); // Reload the list
        } else {
            alert('Error: ' + response.message);
        }
    } catch (error) {
        console.error('Error verifying donation:', error);
        alert('Failed to verify donation. Please try again.');
    }
}

/**
 * Views detailed information about a donation (redirects to distribution details)
 * @async
 * @param {number} donationId - The ID of the donation
 * @returns {Promise<void>}
 */
async function viewDonationDetails(donationId) {
    viewDistributionDetails(donationId);
}

/**
 * Views distribution details for a donation
 * @async
 * @param {number} donationId - The ID of the donation
 * @returns {Promise<void>}
 */
async function viewDistributionDetails(donationId) {
    try {
        const response = await fetch(
            `/api/bdrrmc/physical-donations/${donationId}`,
        );
        const donation = await response.json();

        // Populate modal with donation info
        document.getElementById("viewTrackingCode").textContent =
            donation.tracking_code;
        document.getElementById("viewDonorName").textContent =
            donation.donor_name;
        document.getElementById("viewCategory").textContent = formatCategory(
            donation.category,
        );
        document.getElementById("viewQuantity").textContent = donation.quantity;
        document.getElementById("viewDateReceived").textContent =
            formatDateShort(donation.recorded_at);
        document.getElementById("viewItems").textContent =
            donation.items_description;

        // Distribution status badge
        const statusBadge = document.getElementById("viewDistStatus");
        statusBadge.textContent = formatStatus(
            donation.distribution_status,
        ).toUpperCase();
        statusBadge.className = `px-3 py-1 text-xs font-semibold rounded ${getDistributionStatusBadge(donation.distribution_status)}`;

        // Get latest distribution
        if (donation.distributions && donation.distributions.length > 0) {
            const latestDist =
                donation.distributions[donation.distributions.length - 1];

            document.getElementById("viewDistDate").textContent =
                formatDateShort(latestDist.distributed_at);
            document.getElementById("viewDistTo").textContent =
                latestDist.distributed_to;
            document.getElementById("viewDistQuantity").textContent =
                latestDist.quantity_distributed;

            // Show notes if available
            if (latestDist.notes) {
                document.getElementById("viewNotesSection").style.display =
                    "block";
                document.getElementById("viewNotes").textContent =
                    latestDist.notes;
            } else {
                document.getElementById("viewNotesSection").style.display =
                    "none";
            }

            // Display photos
            const photosGrid = document.getElementById("viewPhotosGrid");
            if (latestDist.photo_urls && latestDist.photo_urls.length > 0) {
                photosGrid.innerHTML = latestDist.photo_urls
                    .map(
                        (photo) => `
                    <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 cursor-pointer hover:border-blue-500 transition" onclick="openPhotoModal('${photo}')">
                        <img src="${photo}" class="w-full h-full object-cover" alt="Distribution Photo">
                    </div>
                `,
                    )
                    .join("");
            } else {
                photosGrid.innerHTML =
                    '<p class="text-sm text-gray-500 col-span-3">No photos available</p>';
            }
        } else {
            // No distribution yet
            document.getElementById("viewDistDate").textContent =
                "Not distributed yet";
            document.getElementById("viewDistTo").textContent = "---";
            document.getElementById("viewDistQuantity").textContent = "---";
            document.getElementById("viewNotesSection").style.display = "none";
            document.getElementById("viewPhotosGrid").innerHTML =
                '<p class="text-sm text-gray-500 col-span-3">No distribution recorded yet</p>';
        }

        // Open modal
        document
            .getElementById("viewDistributionModal")
            .classList.add("active");
    } catch (error) {
        console.error("Error loading distribution details:", error);
        alert("Error loading distribution details");
    }
}

/**
 * Opens a full-screen photo modal
 * @param {string} photoUrl - The URL of the photo to display
 */
function openPhotoModal(photoUrl) {
    // Create full-screen photo viewer
    const modal = document.createElement("div");
    modal.className = "modal active";
    modal.innerHTML = `
        <div class="max-w-4xl w-full mx-4">
            <div class="bg-white rounded-lg p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Distribution Photo</h3>
                    <button onclick="this.closest('.modal').remove()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>
                <img src="${photoUrl}" class="w-full rounded" alt="Distribution Photo">
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

/**
 * Prints a receipt for a donation
 */
function printReceipt() {
    const trackingCode = document.getElementById(
        "generatedTrackingCode",
    ).textContent;
    const printContent = `
        <div style="padding: 40px; font-family: Arial, sans-serif;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="color: #0D47A1; margin-bottom: 10px;">BayanihanCebu</h1>
                <h2 style="color: #666;">Donation Receipt</h2>
            </div>
            <div style="background: #f3f4f6; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <p style="margin: 0 0 10px 0;"><strong>Tracking Code:</strong></p>
                <p style="font-size: 24px; color: #0D47A1; font-weight: bold; margin: 0;">${trackingCode}</p>
            </div>
            <p style="color: #666; margin-bottom: 10px;">Date: ${new Date().toLocaleDateString()}</p>
            <p style="color: #666;">Thank you for your generous donation!</p>
        </div>
    `;

    document.getElementById("printReceipt").innerHTML = printContent;
    window.print();
}

/**
 * Gets the CSS badge class for distribution status
 * @param {string} status - The distribution status
 * @returns {string} CSS class string
 */
function getDistributionStatusBadge(status) {
    const badges = {
        pending_distribution: "bg-yellow-100 text-yellow-700",
        partially_distributed: "bg-blue-100 text-blue-700",
        fully_distributed: "bg-green-100 text-green-700",
    };
    return badges[status] || "bg-gray-100 text-gray-700";
}
