// BayanihanCebu Welcome Page - Map & Data Management

// Initialize map centered on Cebu City
const map = L.map("barangayMap").setView([10.3157, 123.8854], 12);

// Add OpenStreetMap tile layer
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution:
        '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19,
}).addTo(map);

// Store markers for later reference
const markers = {};
let currentZoomLevel = 12;

// Track current map zoom
map.on("zoomend", function () {
    currentZoomLevel = map.getZoom();
    updateMarkerSizes();
});

// Custom marker icon function - Standardized pins by urgency only
function createCustomIcon(
    status,
    urgencyLevel = "warning",
    resourceCount = 0,
    zoomLevel = 12,
) {
    // Urgency-based color coding
    const urgencyColors = {
        emergency: "#ef4444", // Red - Emergency
        critical: "#f97316", // Orange - Critical
        warning: "#f59e0b", // Amber - Warning
    };

    const pinColor = urgencyColors[urgencyLevel] || urgencyColors["warning"];

    // Standardized base pin sizes - ONLY based on urgency level
    const baseSizes = {
        emergency: 40, // Largest
        critical: 32, // Large
        warning: 26, // Medium
    };

    const baseSize = baseSizes[urgencyLevel] || 26;

    // Zoom-responsive scaling
    let zoomMultiplier = 1.0;
    if (zoomLevel >= 18) {
        zoomMultiplier = 1.8;
    } else if (zoomLevel >= 15) {
        zoomMultiplier = 1.4;
    } else if (zoomLevel >= 12) {
        zoomMultiplier = 1.15;
    }

    const finalSize = Math.round(baseSize * zoomMultiplier);
    const iconAnchor = [finalSize / 2, finalSize];

    return L.divIcon({
        className: "custom-marker-icon",
        html: `<div style="
            background-color: ${pinColor};
            width: ${finalSize}px;
            height: ${finalSize}px;
            border-radius: 50% 50% 50% 0;
            transform: rotate(-45deg);
            border: 3px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            transition: all 0.3s ease;
            cursor: pointer;
        "></div>`,
        iconSize: [finalSize, finalSize],
        iconAnchor: iconAnchor,
        popupAnchor: [0, -iconAnchor[1]],
    });
}

// Update marker sizes when zoom changes
function updateMarkerSizes() {
    Object.keys(markers).forEach((barangayId) => {
        const markerData = markers[barangayId];
        if (markerData && markerData.marker) {
            const newIcon = createCustomIcon(
                markerData.status,
                markerData.urgency,
                markerData.resourceCount,
                currentZoomLevel,
            );
            markerData.marker.setIcon(newIcon);
        }
    });
}

// Format currency
function formatCurrency(amount) {
    return (
        "₱" +
        parseFloat(amount).toLocaleString("en-PH", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        })
    );
}

// Format number
function formatNumber(num) {
    return parseInt(num).toLocaleString("en-PH");
}

// Disaster type icons
const disasterIcons = {
    flood: "🌊",
    fire: "🔥",
    earthquake: "🏚️",
    typhoon: "🌀",
    landslide: "⛰️",
    other: "❓",
};

// Get urgency badge color
function getUrgencyColor(urgency) {
    const colors = {
        emergency: "#ef4444",
        critical: "#f97316",
        warning: "#f59e0b",
    };
    return colors[urgency] || colors["warning"];
}

// Get highest urgency level from resource needs
function getHighestUrgency(resourceNeeds) {
    if (!resourceNeeds || resourceNeeds.length === 0) return "warning";

    const urgencyOrder = { emergency: 3, critical: 2, warning: 1 };
    let highest = "warning";
    let highestValue = 0;

    resourceNeeds.forEach((need) => {
        const value = urgencyOrder[need.urgency] || 0;
        if (value > highestValue) {
            highestValue = value;
            highest = need.urgency;
        }
    });

    return highest;
}

// Load and display map data
function loadMapData() {
    fetch("/api/barangays")
        .then((response) => response.json())
        .then((data) => {
            // Clear existing markers
            Object.values(markers).forEach((markerData) => {
                if (markerData && markerData.marker) {
                    map.removeLayer(markerData.marker);
                }
            });

            // Reset markers object
            for (let key in markers) {
                delete markers[key];
            }

            let totalDonations = 0;
            let totalFamilies = 0;
            let affectedCount = 0;

            // Add markers for each barangay - SKIP SAFE STATUS
            data.forEach((barangay) => {
                // Skip safe barangays - only show barangays with active needs
                if (barangay.status === "safe") {
                    return;
                }

                // Get resource needs data
                const resourceNeeds = barangay.resource_needs || [];
                const resourceCount = barangay.resource_needs_count || 0;
                // Use barangay's disaster_status for pin color, not resource urgency
                const barangayStatus = barangay.status || "warning";

                const marker = L.marker(
                    [barangay.latitude, barangay.longitude],
                    {
                        icon: createCustomIcon(
                            barangayStatus,
                            barangayStatus, // Use barangay status for color
                            resourceCount,
                            currentZoomLevel,
                        ),
                    },
                ).addTo(map);

                // Store marker reference with metadata
                markers[barangay.id] = {
                    marker: marker,
                    status: barangayStatus,
                    urgency: barangayStatus, // Use barangay status
                    resourceCount: resourceCount,
                };

                // Build Resource Needs HTML with enhanced details
                let resourceNeedsHtml = "";
                if (resourceNeeds.length > 0) {
                    resourceNeedsHtml = `
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                            <div style="font-weight: 700; font-size: 15px; margin-bottom: 10px; color: #1f2937; display: flex; align-items: center; gap: 8px;">
                                📋 Resource Needs
                                <span style="background: #3b82f6; color: white; padding: 2px 8px; border-radius: 12px; font-size: 11px;">
                                    ${resourceCount}
                                </span>
                            </div>
                    `;

                    resourceNeeds.forEach((need) => {
                        const urgencyColor = getUrgencyColor(need.urgency);
                        resourceNeedsHtml += `
                            <div style="background: #f9fafb; border-left: 4px solid ${urgencyColor}; padding: 10px; margin-bottom: 8px; border-radius: 4px;">
                                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 6px;">
                                    <div style="font-weight: 600; color: #1f2937; font-size: 14px;">
                                        ${need.category ? need.category.toUpperCase() : "General"}
                                    </div>
                                    <div style="background: ${urgencyColor}; color: white; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                        ${need.urgency}
                                    </div>
                                </div>
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">
                                    ${need.description || "No description"}
                                </div>
                                <div style="font-size: 12px; color: #1f2937; margin-bottom: 4px;">
                                    <strong>Quantity:</strong> ${need.quantity}
                                </div>
                                <div style="display: flex; justify-content: space-between; font-size: 11px; color: #9ca3af; margin-top: 4px;">
                                    <span>Status: <span style="text-transform: uppercase; font-weight: 600;">${need.status}</span></span>
                                    ${need.created_at ? `<span>Added: ${need.created_at}</span>` : ""}
                                </div>
                            </div>
                        `;
                    });

                    resourceNeedsHtml += "</div>";
                }

                // Google Maps link
                const googleMapsLink = `https://www.google.com/maps?q=${barangay.latitude},${barangay.longitude}`;

                // Create enhanced popup content
                const popupContent = `
                    <div style="padding: 15px; min-width: 320px; max-width: 400px;">
                        <div style="background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%); color: white; margin: -15px -15px 15px -15px; padding: 15px; border-radius: 8px 8px 0 0;">
                            <h3 style="margin: 0 0 5px 0; font-size: 18px; font-weight: 700;">${barangay.name}</h3>
                            <p style="margin: 0; font-size: 13px; opacity: 0.9;">${barangay.city || "Cebu City"}</p>
                        </div>

                        <div style="padding: 0 5px;">
                            <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Status</div>
                                <div style="display: inline-block; background: ${barangay.status === "emergency" ? "#fee2e2" : barangay.status === "critical" ? "#fed7aa" : barangay.status === "warning" ? "#fef3c7" : "#d1fae5"}; color: ${barangay.status === "emergency" ? "#991b1b" : barangay.status === "critical" ? "#9a3412" : barangay.status === "warning" ? "#92400e" : "#065f46"}; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: 700; text-transform: uppercase;">
                                    ${barangay.status}
                                </div>
                            </div>

                            ${
                                barangay.disaster_type
                                    ? `
                                <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Disaster Type</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                        ${disasterIcons[barangay.disaster_type] || ""} ${barangay.disaster_type.charAt(0).toUpperCase() + barangay.disaster_type.slice(1)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${
                                barangay.affected_families
                                    ? `
                                <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Affected Families</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #1f2937;">
                                        ${formatNumber(barangay.affected_families)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${
                                barangay.total_raised
                                    ? `
                                <div style="padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">Donations Received</div>
                                    <div style="font-size: 14px; font-weight: 600; color: #10b981;">
                                        ${formatCurrency(barangay.total_raised)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${resourceNeedsHtml}

                            <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                                <a href="${googleMapsLink}" target="_blank" style="display: block; text-align: center; background: #3b82f6; color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 13px; margin-bottom: 8px;">
                                    📍 View on Google Maps
                                </a>
                                ${
                                    barangay.status !== "safe" &&
                                    resourceCount > 0
                                        ? `
                                    <button onclick="window.location.href='/donate/${barangay.id}'" style="width: 100%; background: #ef4444; color: white; padding: 10px; border: none; border-radius: 8px; font-weight: 600; font-size: 13px; cursor: pointer;">
                                        ❤️ Donate to ${barangay.name}
                                    </button>
                                `
                                        : ""
                                }
                            </div>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 420,
                    maxHeight: 500,
                    className: "custom-leaflet-popup",
                });

                // Update statistics
                if (barangay.status !== "safe") {
                    totalDonations += parseFloat(barangay.total_raised) || 0;
                    totalFamilies += parseInt(barangay.affected_families) || 0;
                    affectedCount++;
                }
            });

            // Update summary statistics in map section
            document.getElementById("totalDonations").textContent =
                formatCurrency(totalDonations);
            document.getElementById("affectedFamilies").textContent =
                formatNumber(totalFamilies);
            document.getElementById("affectedBarangays").textContent =
                affectedCount;

            // Populate barangay list (only affected ones with resource needs)
            const barangayList = document.getElementById("barangayList");
            const affectedBarangays = data.filter((b) => b.status !== "safe");

            if (affectedBarangays.length === 0) {
                barangayList.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #10b981;">
                        <div style="font-size: 48px; margin-bottom: 10px;">✅</div>
                        <div style="font-weight: 600;">All Clear</div>
                        <div style="font-size: 13px; color: #6b7280;">No barangays currently need assistance</div>
                    </div>
                `;
            } else {
                barangayList.innerHTML = affectedBarangays
                    .sort((a, b) => {
                        const urgencyOrder = {
                            emergency: 3,
                            critical: 2,
                            warning: 1,
                        };
                        const statusOrder = {
                            emergency: 0,
                            critical: 1,
                            warning: 2,
                        };

                        // Sort by highest urgency first, then by status
                        const aUrgency = a.highest_urgency || "warning";
                        const bUrgency = b.highest_urgency || "warning";

                        if (urgencyOrder[aUrgency] !== urgencyOrder[bUrgency]) {
                            return (
                                urgencyOrder[bUrgency] - urgencyOrder[aUrgency]
                            );
                        }

                        return statusOrder[a.status] - statusOrder[b.status];
                    })
                    .map((barangay) => {
                        const highestUrgency =
                            barangay.highest_urgency || "warning";
                        const urgencyColor = getUrgencyColor(highestUrgency);
                        const needsCount = barangay.resource_needs_count || 0;

                        return `
                            <div class="barangay-item" onclick="focusBarangay('${barangay.id}')" style="position: relative; cursor: pointer;">
                                ${
                                    needsCount > 0
                                        ? `
                                    <div style="position: absolute; top: -5px; right: -5px; background: ${urgencyColor}; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                                        ${needsCount}
                                    </div>
                                `
                                        : ""
                                }
                                <div class="barangay-info-summary">
                                    <div class="barangay-name-summary">📍 ${barangay.name}</div>
                                    <div class="barangay-meta">
                                        ${barangay.affected_families || 0} families • ${formatCurrency(barangay.total_raised || 0)} raised
                                    </div>
                                    ${
                                        needsCount > 0
                                            ? `
                                        <div style="font-size: 11px; color: ${urgencyColor}; font-weight: 600; margin-top: 4px;">
                                            ${needsCount} resource need${needsCount > 1 ? "s" : ""} • ${highestUrgency.toUpperCase()} urgency
                                        </div>
                                    `
                                            : ""
                                    }
                                </div>
                                <span class="status-badge ${barangay.status}">
                                    ${barangay.status.toUpperCase()}
                                </span>
                            </div>
                        `;
                    })
                    .join("");
            }
        })
        .catch((error) => {
            console.error("Error loading map data:", error);
            const barangayList = document.getElementById("barangayList");
            if (barangayList) {
                barangayList.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #ef4444;">
                        <div style="font-size: 48px; margin-bottom: 10px;">⚠️</div>
                        <div style="font-weight: 600;">Error Loading Data</div>
                        <div style="font-size: 13px; color: #6b7280;">Please refresh the page</div>
                    </div>
                `;
            }
        });
}

// Function to focus on a specific barangay
function focusBarangay(barangayId) {
    if (markers[barangayId] && markers[barangayId].marker) {
        map.setView(markers[barangayId].marker.getLatLng(), 15);
        markers[barangayId].marker.openPopup();
    }
}

// Load hero statistics from API
function loadHeroStatistics() {
    fetch("/api/statistics")
        .then((response) => response.json())
        .then((stats) => {
            document.getElementById("heroTotalDonations").textContent =
                formatCurrency(stats.total_donations || 0);
            document.getElementById("heroAffectedFamilies").textContent =
                formatNumber(stats.total_affected_families || 0);
            document.getElementById("heroVerifiedTransactions").textContent =
                formatNumber(stats.total_donors || 0);
            document.getElementById("heroActiveFundraisers").textContent =
                formatNumber(stats.barangays_affected || 0);
        })
        .catch((error) => {
            console.error("Error loading hero statistics:", error);
        });
}

// Initialize map and data on page load
loadMapData();
loadHeroStatistics();

// Auto-refresh map data every 30 seconds for real-time updates
setInterval(() => {
    loadMapData();
    loadHeroStatistics();
}, 30000);

// Smooth scroll for anchor links
document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute("href"));
        if (target) {
            target.scrollIntoView({
                behavior: "smooth",
                block: "start",
            });
        }
    });
});
