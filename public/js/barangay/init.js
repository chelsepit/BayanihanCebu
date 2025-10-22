/**
 * Initialization and Event Listeners
 * Sets up DOMContentLoaded handlers and global event listeners
 */

// Primary initialization - Load resource needs on page load
document.addEventListener("DOMContentLoaded", function () {
    console.log("Initializing Barangay Dashboard...");

    // Load first tab by default
    loadResourceNeeds();

    // Initialize photo upload handlers
    if (typeof initPhotoUpload === "function") {
        initPhotoUpload();
    }

    console.log("Barangay Dashboard initialized");
});

// Secondary initialization - Setup matching system event listeners
document.addEventListener("DOMContentLoaded", function () {
    const matchRequestsTab = document.querySelector(
        '[data-tab="match-requests"]',
    );
    const myRequestsTab = document.querySelector('[data-tab="my-requests"]');
    const activeMatchesTab = document.querySelector(
        '[data-tab="active-matches"]',
    );

    if (matchRequestsTab) {
        matchRequestsTab.addEventListener("click", loadIncomingRequests);
    }
    if (myRequestsTab) {
        myRequestsTab.addEventListener("click", loadMyRequests);
    }
    if (activeMatchesTab) {
        activeMatchesTab.addEventListener("click", loadActiveMatches);
    }

    // Initial load to get badge counts (silent load)
    if (typeof loadIncomingRequests === "function") {
        loadIncomingRequests(true); // true = silent load for badge only
    }
});

console.log("✅ Barangay Dashboard scripts loaded successfully");
