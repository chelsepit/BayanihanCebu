/**
 * Tab Management for Barangay Dashboard
 * ======================================
 * Handles tab switching and content loading
 */

/**
 * Switch to a specific tab
 */
function switchTab(tabName) {
    showTab(tabName);
}

/**
 * Show a specific tab and load its content
 */
function showTab(tabName) {
    // Remove active class from all tab contents
    document.querySelectorAll(".tab-content").forEach((tab) => {
        tab.classList.remove("active");
    });

    // Remove active class from all tab buttons (.tab-btn and .tab-button)
    document.querySelectorAll(".tab-btn, .tab-button").forEach((btn) => {
        btn.classList.remove("active");
        btn.classList.remove("text-indigo-600", "border-indigo-600");
        btn.classList.add("text-gray-600", "border-transparent");
    });

    // Add active class to the clicked tab content
    const tabContent = document.getElementById(tabName + "-tab");
    if (tabContent) {
        tabContent.classList.add("active");
    }

    // Add active class to the clicked button
    const activeButton =
        document.querySelector(`[data-tab="${tabName}"]`) || event?.target;
    if (activeButton) {
        activeButton.classList.add("active");
        if (activeButton.classList.contains("tab-button")) {
            activeButton.classList.remove(
                "text-gray-600",
                "border-transparent",
            );
            activeButton.classList.add("text-indigo-600", "border-indigo-600");
        }
    }

    // Load data for specific tabs
    if (tabName === "physical") {
        loadPhysicalDonations();
    } else if (tabName === "needs") {
        loadResourceNeeds();
    } else if (tabName === "online") {
        loadOnlineDonations();
    } else if (tabName === "match-requests") {
        loadIncomingRequests();
    } else if (tabName === "my-requests") {
        loadMyRequests();
    } else if (tabName === "active-matches") {
        loadActiveMatches();
    }
}
