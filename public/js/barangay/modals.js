/**
 * Modal Management
 * Handles all modal-related functions (open/close modals, form handling)
 */

// Note: csrfToken is defined globally in utils.js

/**
 * Opens the edit status modal and loads current barangay information
 * @async
 * @returns {Promise<void>}
 */
async function openEditStatusModal() {
    try {
        const response = await fetch("/api/bdrrmc/my-barangay");
        const barangay = await response.json();

        // ✅ UPDATED: Use donation_status instead of disaster_status
        document.getElementById("editDonationStatus").value =
            barangay.donation_status || "pending";
        document.getElementById("editAffectedFamilies").value =
            barangay.affected_families || 0;
        document.getElementById("editNeedsSummary").value =
            barangay.needs_summary || "";

        document.getElementById("editStatusModal").classList.add("active");
    } catch (error) {
        console.error("Error loading barangay info:", error);
        alert("Error loading barangay information. Please try again.");
    }
}

/**
 * Closes the edit status modal
 */
function closeEditStatusModal() {
    document.getElementById("editStatusModal").classList.remove("active");
}

/**
 * Opens the record donation modal
 */
function openRecordModal() {
    document.getElementById("recordModal").classList.add("active");
    document.getElementById("recordDonationForm").reset();
}

/**
 * Closes the record donation modal
 */
function closeRecordModal() {
    document.getElementById("recordModal").classList.remove("active");
}

/**
 * Closes the success modal
 */
function closeSuccessModal() {
    document.getElementById("successModal").classList.remove("active");
}

/**
 * Opens the distribute modal for a donation
 * @param {number} donationId - The ID of the donation
 * @param {string} trackingCode - The tracking code of the donation
 */
function openDistributeModal(donationId, trackingCode) {
    document.getElementById("distributeDonationId").value = donationId;
    document.getElementById("distributeTrackingCode").textContent =
        trackingCode;
    document.getElementById("distributeModal").classList.add("active");
    document.getElementById("distributeForm").reset();
    uploadedPhotos = [];
    document.getElementById("photoPreviewGrid").innerHTML = "";
    document.getElementById("photoPreviewGrid").classList.add("hidden");
    document.getElementById("photoError").classList.add("hidden");
    document.getElementById("photoCount").classList.add("hidden");
    document.getElementById("submitDistribution").disabled = true;
}

/**
 * Opens the distribute modal with partial distribution status pre-selected
 * @param {number} donationId - The ID of the donation
 * @param {string} trackingCode - The tracking code of the donation
 */
function openPartialDistributeModal(donationId, trackingCode) {
    openDistributeModal(donationId, trackingCode);
    document.querySelector('select[name="distribution_status"]').value =
        "partially_distributed";
}

/**
 * Closes the distribute modal
 */
function closeDistributeModal() {
    document.getElementById("distributeModal").classList.remove("active");
    uploadedPhotos = [];
}

/**
 * Closes the view distribution modal
 */
function closeViewDistributionModal() {
    document.getElementById("viewDistributionModal").classList.remove("active");
}

/**
 * Opens the resource need modal
 */
function openNeedModal() {
    document.getElementById("needModal").classList.add("active");
    document.getElementById("needForm").reset();
}

/**
 * Closes the resource need modal
 */
function closeNeedModal() {
    document.getElementById("needModal").classList.remove("active");
}

/**
 * Closes the respond to match modal
 */
function closeRespondModal() {
    document.getElementById("respondMatchModal").classList.add("hidden");
    currentMatchId = null;
}

/**
 * Opens the complete match modal
 */
function openCompleteMatchModal() {
    document.getElementById("completionNotes").value = "";
    document.getElementById("completeMatchModal").classList.remove("hidden");
}

/**
 * Closes the complete match modal
 */
function closeCompleteMatchModal() {
    document.getElementById("completeMatchModal").classList.add("hidden");
}

/**
 * Confirms and submits match completion
 * @async
 * @returns {Promise<void>}
 */
async function confirmCompleteMatch() {
    if (!currentConversationMatchId) {
        alert("Error: No match selected");
        return;
    }

    const notes = document.getElementById("completionNotes").value.trim();

    if (
        !confirm(
            "Are you sure you want to mark this match as complete? This action cannot be undone.",
        )
    ) {
        return;
    }

    try {
        const response = await fetchAPI(
            `/api/bdrrmc/matches/${currentConversationMatchId}/complete`,
            {
                method: "POST",
                body: JSON.stringify({
                    completion_notes:
                        notes || "Transfer completed successfully",
                }),
            },
        );

        if (response.success) {
            alert(
                "✅ Match Marked as Complete!\n\n" +
                    "The resource transfer has been recorded as successful.\n" +
                    "• Donation status: Updated to distributed\n" +
                    "• Resource need: Marked as fulfilled\n" +
                    "• Conversation: Now read-only\n\n" +
                    "Thank you for coordinating this resource transfer!",
            );

            closeCompleteMatchModal();

            // Reload conversation to show completed state
            await loadConversation(currentConversationMatchId);

            // Update notification count
            if (typeof loadNotifications === "function") {
                loadNotifications();
            }
        } else {
            alert("❌ Error: " + response.message);
        }
    } catch (error) {
        console.error("Error completing match:", error);
        alert("Failed to complete match. Please try again.");
    }
}

// Event listener for edit status form submission
document
    .getElementById("editStatusForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        // ✅ UPDATED: Use donation_status instead of disaster_status
        const data = {
            donation_status: formData.get("donation_status"),
            affected_families: parseInt(formData.get("affected_families")) || 0,
            needs_summary: formData.get("needs_summary"),
        };

        // ✅ UPDATED: If completed, reset affected families to 0
        if (data.donation_status === "completed") {
            data.affected_families = 0;
        }

        try {
            const response = await fetch("/api/bdrrmc/my-barangay", {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                closeEditStatusModal();
                alert(
                    "✅ Barangay donation status updated successfully!\n\nThe changes will be reflected on the LDRRMO city map immediately.",
                );
                location.reload();
            } else {
                alert("❌ Error updating barangay donation status. Please try again.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("❌ Error updating barangay donation status. Please try again.");
        }
    });

// ✅ UPDATED: Event listener for donation status change
document
    .getElementById("editDonationStatus")
    .addEventListener("change", function (e) {
        // If status is "completed", reset affected families to 0
        if (e.target.value === "completed") {
            document.getElementById("editAffectedFamilies").value = 0;
            document.getElementById("editAffectedFamilies").disabled = true;
        } else {
            document.getElementById("editAffectedFamilies").disabled = false;
        }
    });

// Event listener for record donation form submission
document
    .getElementById("recordDonationForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch("/api/bdrrmc/physical-donations", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                closeRecordModal();
                document.getElementById("generatedTrackingCode").textContent =
                    result.tracking_code;
                document.getElementById("successModal").classList.add("active");
                loadPhysicalDonations();
            } else {
                alert("Error recording donation. Please try again.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Error recording donation. Please try again.");
        }
    });

// Event listener for distribute form submission
document
    .getElementById("distributeForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        // Validate photos
        if (uploadedPhotos.length !== MAX_PHOTOS) {
            alert(
                `Please upload exactly ${MAX_PHOTOS} photos before submitting.`,
            );
            return;
        }

        const donationId = document.getElementById(
            "distributeDonationId",
        ).value;
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        data.photo_urls = uploadedPhotos; // Add photos to data

        try {
            const response = await fetch(
                `/api/bdrrmc/physical-donations/${donationId}/distribute`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: JSON.stringify(data),
                },
            );

            const result = await response.json();

            if (result.success) {
                closeDistributeModal();
                uploadedPhotos = []; // Reset photos
                alert(
                    "Distribution recorded successfully with photo evidence!",
                );
                loadPhysicalDonations();
            } else {
                alert("Error recording distribution. Please try again.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Error recording distribution. Please try again.");
        }
    });

// Event listener for need form submission
document
    .getElementById("needForm")
    .addEventListener("submit", async function (e) {
        e.preventDefault();

        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch("/api/bdrrmc/needs", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify(data),
            });

            const result = await response.json();

            if (result.success) {
                closeNeedModal();
                alert("Resource request created successfully!");
                loadResourceNeeds();
            } else {
                alert("Error creating resource request. Please try again.");
            }
        } catch (error) {
            console.error("Error:", error);
            alert("Error creating resource request. Please try again.");
        }
    });
