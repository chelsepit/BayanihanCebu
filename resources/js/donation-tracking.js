/**
 * Unified Donation Tracking System
 * Handles both Online and Physical Donations
 */

document.addEventListener("DOMContentLoaded", function () {
    const trackingForm = document.getElementById("tracking-form");
    const resultsContainer = document.getElementById("tracking-results");
    const loadingSpinner = document.getElementById("loading-spinner");
    const errorMessage = document.getElementById("error-message");

    if (trackingForm) {
        trackingForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const trackingCode = document
                .getElementById("tracking_code")
                .value.trim();

            if (!trackingCode) {
                showError("Please enter a tracking code");
                return;
            }

            await trackDonation(trackingCode);
        });
    }

    async function trackDonation(trackingCode) {
        // Show loading state
        showLoading();
        hideError();
        hideResults();

        try {
            const response = await fetch("/api/donations/track", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                },
                body: JSON.stringify({ tracking_code: trackingCode }),
            });

            const data = await response.json();

            hideLoading();

            if (data.success) {
                displayResults(data);
            } else {
                showError(data.message || "Donation not found");
            }
        } catch (error) {
            hideLoading();
            showError(
                "An error occurred while tracking your donation. Please try again.",
            );
            console.error("Tracking error:", error);
        }
    }

    function displayResults(data) {
        const { donation_type, donation } = data;

        if (donation_type === "online") {
            displayOnlineDonation(donation);
        } else if (donation_type === "physical") {
            displayPhysicalDonation(donation);
        }

        resultsContainer.classList.remove("hidden");
    }

    function displayOnlineDonation(donation) {
        const html = `
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-6 border-b">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
                        <p class="text-2xl font-bold text-gray-800">${donation.tracking_code}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full">
                            Online Donation
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 ${getStatusColor(donation.verification_status)} font-semibold rounded-full">
                            ${donation.verification_status.toUpperCase()}
                        </span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Donor:</span>
                                <span class="font-semibold text-gray-800">${donation.donor_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Amount:</span>
                                <span class="font-semibold text-gray-800">₱${parseFloat(donation.amount).toLocaleString("en-PH", { minimumFractionDigits: 2 })}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment Method:</span>
                                <span class="font-semibold text-gray-800 uppercase">${donation.payment_method}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-semibold text-gray-800">${donation.created_at}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Barangay:</span>
                                <span class="font-semibold text-gray-800">${donation.barangay_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Disaster:</span>
                                <span class="font-semibold text-gray-800">${donation.disaster_title}</span>
                            </div>
                            ${
                                donation.verified_by
                                    ? `
                            <div class="flex justify-between">
                                <span class="text-gray-600">Verified By:</span>
                                <span class="font-semibold text-gray-800">${donation.verified_by}</span>
                            </div>
                            `
                                    : ""
                            }
                        </div>
                    </div>
                </div>

                <!-- Blockchain Status -->
                ${renderBlockchainStatus(donation)}

                <!-- Explorer Link -->
                ${
                    donation.explorer_url
                        ? `
                <div class="mt-4 text-center">
                    <a href="${donation.explorer_url}" target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800">
                        View on Blockchain Explorer
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>
                `
                        : ""
                }
            </div>
        `;

        resultsContainer.innerHTML = html;
    }

    function displayPhysicalDonation(donation) {
        const html = `
            <div class="bg-white rounded-lg shadow-lg p-8 mb-6">
                <!-- Header -->
                <div class="flex items-center justify-between mb-6 pb-6 border-b">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tracking Code</p>
                        <p class="text-2xl font-bold text-gray-800">${donation.tracking_code}</p>
                        <span class="inline-block mt-2 px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full">
                            Physical Donation
                        </span>
                    </div>
                    <div class="text-right">
                        <span class="px-4 py-2 ${getDistributionStatusColor(donation.distribution_status)} font-semibold rounded-full">
                            ${donation.distribution_status.replace(/_/g, " ").toUpperCase()}
                        </span>
                    </div>
                </div>

                <!-- Details Grid -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Donation Details</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Donor:</span>
                                <span class="font-semibold text-gray-800">${donation.donor_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Category:</span>
                                <span class="font-semibold text-gray-800">${donation.category}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Quantity:</span>
                                <span class="font-semibold text-gray-800">${donation.quantity}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Est. Value:</span>
                                <span class="font-semibold text-gray-800">₱${parseFloat(donation.estimated_value).toLocaleString("en-PH", { minimumFractionDigits: 2 })}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Beneficiary</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Barangay:</span>
                                <span class="font-semibold text-gray-800">${donation.barangay_name}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Recipients:</span>
                                <span class="font-semibold text-gray-800">${donation.intended_recipients}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Recorded By:</span>
                                <span class="font-semibold text-gray-800">${donation.recorded_by || "N/A"}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Description -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="text-sm font-semibold text-gray-700 mb-2">Items Donated</h4>
                    <p class="text-gray-800">${donation.items_description}</p>
                </div>

                <!-- Blockchain Status -->
                ${renderBlockchainStatus(donation)}

                <!-- Distribution History -->
                ${
                    donation.distributions && donation.distributions.length > 0
                        ? `
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Distribution History</h3>
                    <div class="space-y-4">
                        ${donation.distributions
                            .map(
                                (dist) => `
                            <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-gray-800">${dist.distributed_to}</h4>
                                        <p class="text-sm text-gray-600">Quantity: ${dist.quantity_distributed}</p>
                                        ${dist.notes ? `<p class="text-sm text-gray-600 mt-1">${dist.notes}</p>` : ""}
                                        <p class="text-xs text-gray-500 mt-2">By: ${dist.distributed_by}</p>
                                    </div>
                                    <div class="text-right text-sm text-gray-600">
                                        ${dist.distributed_at}
                                    </div>
                                </div>
                            </div>
                        `,
                            )
                            .join("")}
                    </div>
                </div>
                `
                        : `
                <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                    <p class="text-sm text-yellow-800">This donation is pending distribution to beneficiaries.</p>
                </div>
                `
                }
            </div>
        `;

        resultsContainer.innerHTML = html;
    }

    function renderBlockchainStatus(donation) {
        const status = donation.blockchain_status || "pending";
        let bgColor, textColor, icon, message;

        if (status === "confirmed") {
            bgColor = "bg-green-50";
            textColor = "text-green-800";
            icon = "✓";
            message =
                "This donation has been permanently recorded on the Lisk blockchain for transparency.";
        } else if (status === "failed") {
            bgColor = "bg-red-50";
            textColor = "text-red-800";
            icon = "✗";
            message =
                "Blockchain recording failed. The system will retry automatically.";
        } else {
            bgColor = "bg-yellow-50";
            textColor = "text-yellow-800";
            icon = "⟳";
            message =
                "Your donation is being recorded on the blockchain. This may take a few minutes.";
        }

        return `
            <div class="p-4 ${bgColor} rounded-lg border-2 border-${textColor.replace("text-", "")} mb-4">
                <div class="flex items-center">
                    <span class="text-2xl mr-3">${icon}</span>
                    <div>
                        <h4 class="font-semibold ${textColor}">Blockchain Status: ${status.toUpperCase()}</h4>
                        <p class="text-sm ${textColor}">${message}</p>
                        ${
                            donation.blockchain_tx_hash
                                ? `
                            <a href="https://sepolia-blockscout.lisk.com/tx/${donation.blockchain_tx_hash}" 
                               target="_blank" 
                               class="text-sm text-blue-600 hover:text-blue-800 inline-flex items-center mt-2">
                                View Transaction
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                </svg>
                            </a>
                        `
                                : ""
                        }
                    </div>
                </div>
            </div>
        `;
    }

    function getStatusColor(status) {
        const colors = {
            pending: "bg-yellow-100 text-yellow-800",
            verified: "bg-green-100 text-green-800",
            rejected: "bg-red-100 text-red-800",
        };
        return colors[status] || "bg-gray-100 text-gray-800";
    }

    function getDistributionStatusColor(status) {
        const colors = {
            pending_distribution: "bg-yellow-100 text-yellow-800",
            partially_distributed: "bg-blue-100 text-blue-800",
            fully_distributed: "bg-green-100 text-green-800",
        };
        return colors[status] || "bg-gray-100 text-gray-800";
    }

    function showLoading() {
        if (loadingSpinner) {
            loadingSpinner.classList.remove("hidden");
        }
    }

    function hideLoading() {
        if (loadingSpinner) {
            loadingSpinner.classList.add("hidden");
        }
    }

    function showError(message) {
        if (errorMessage) {
            errorMessage.textContent = message;
            errorMessage.classList.remove("hidden");
        }
    }

    function hideError() {
        if (errorMessage) {
            errorMessage.classList.add("hidden");
        }
    }

    function hideResults() {
        if (resultsContainer) {
            resultsContainer.classList.add("hidden");
        }
    }
});
