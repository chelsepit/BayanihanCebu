// BayanihanCebu City Dashboard - Map Management
// Shows ALL barangays including safe ones

// Check if map container exists before initializing
if (!document.getElementById("cityBarangayMap")) {
    console.log(
        "City barangay map container not found, skipping initialization",
    );
} else {
    console.log("Initializing city barangay map...");
}

// Initialize map centered on Cebu City
const cityBarangayMap = document.getElementById("cityBarangayMap")
    ? L.map("cityBarangayMap").setView([10.3157, 123.8854], 12)
    : null;

// Add OpenStreetMap tile layer
if (cityBarangayMap) {
    L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
        attribution:
            '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(cityBarangayMap);
}

// Store markers for later reference
const cityMarkers = {};
let cityCurrentZoomLevel = 12;

// Track current map zoom
if (cityBarangayMap) {
    cityBarangayMap.on("zoomend", function () {
        cityCurrentZoomLevel = cityBarangayMap.getZoom();
        updateCityMarkerSizes();
    });
}

// Custom marker icon function - Based on disaster status
function createCityCustomIcon(status, zoomLevel = 12) {
    // Status-based color coding
    const statusColors = {
        safe: "#10b981", // Green - Safe
        warning: "#f59e0b", // Amber - Warning
        critical: "#f97316", // Orange - Critical
        emergency: "#ef4444", // Red - Emergency
    };

    const pinColor = statusColors[status] || statusColors["safe"];

    // Base pin sizes by status
    const baseSizes = {
        emergency: 38, // Largest
        critical: 30, // Large
        warning: 24, // Medium
        safe: 20, // Small
    };

    const baseSize = baseSizes[status] || 20;

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
function updateCityMarkerSizes() {
    Object.keys(cityMarkers).forEach((barangayId) => {
        const markerData = cityMarkers[barangayId];
        if (markerData && markerData.marker) {
            const newIcon = createCityCustomIcon(
                markerData.status,
                cityCurrentZoomLevel,
            );
            markerData.marker.setIcon(newIcon);
        }
    });
}

// Format currency
function formatCityCurrency(amount) {
    return (
        "₱" +
        parseFloat(amount).toLocaleString("en-PH", {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0,
        })
    );
}

// Format number
function formatCityNumber(num) {
    return parseInt(num).toLocaleString("en-PH");
}

// Disaster type icons
const cityDisasterIcons = {
    flood: "🌊",
    fire: "🔥",
    earthquake: "🏚️",
    typhoon: "🌀",
    landslide: "⛰️",
    other: "❓",
};

// Get status color
function getCityStatusColor(status) {
    const colors = {
        safe: "#10b981",
        warning: "#f59e0b",
        critical: "#f97316",
        emergency: "#ef4444",
    };
    return colors[status] || colors["safe"];
}

// Load and display map data
function loadCityMapData() {
    if (!cityBarangayMap) {
        console.error("City barangay map not initialized");
        return;
    }

    fetch("/api/barangays")
        .then((response) => response.json())
        .then((data) => {
            // Clear existing markers
            Object.values(cityMarkers).forEach((markerData) => {
                if (markerData && markerData.marker) {
                    cityBarangayMap.removeLayer(markerData.marker);
                }
            });

            // Reset markers object
            for (let key in cityMarkers) {
                delete cityMarkers[key];
            }

            let totalDonations = 0;
            let totalFamilies = 0;
            let affectedCount = 0;

            // Add markers for ALL barangays (including safe ones)
            data.forEach((barangay) => {
                const barangayStatus = barangay.status || "safe";

                const marker = L.marker(
                    [barangay.latitude, barangay.longitude],
                    {
                        icon: createCityCustomIcon(
                            barangayStatus,
                            cityCurrentZoomLevel,
                        ),
                    },
                ).addTo(cityBarangayMap);

                // Store marker reference with metadata
                cityMarkers[barangay.id] = {
                    marker: marker,
                    status: barangayStatus,
                };

                // Build Resource Needs HTML
                const resourceNeeds = barangay.resource_needs || [];
                let resourceNeedsHtml = "";
                if (resourceNeeds.length > 0) {
                    resourceNeedsHtml = `
                        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e5e7eb;">
                            <div style="font-weight: 700; font-size: 14px; margin-bottom: 10px; color: #1f2937;">
                                📋 Resource Needs (${resourceNeeds.length})
                            </div>
                    `;

                    resourceNeeds.forEach((need) => {
                        const urgencyColor = getCityStatusColor(
                            need.urgency || "warning",
                        );
                        resourceNeedsHtml += `
                            <div style="background: #f9fafb; border-left: 4px solid ${urgencyColor}; padding: 8px; margin-bottom: 6px; border-radius: 4px;">
                                <div style="font-weight: 600; color: #1f2937; font-size: 13px;">
                                    ${need.category ? need.category.toUpperCase() : "General"}
                                </div>
                                <div style="font-size: 11px; color: #6b7280; margin-top: 2px;">
                                    ${need.description || "No description"}
                                </div>
                                <div style="font-size: 11px; color: #1f2937; margin-top: 2px;">
                                    <strong>Qty:</strong> ${need.quantity} • <strong>Status:</strong> ${need.status}
                                </div>
                            </div>
                        `;
                    });

                    resourceNeedsHtml += "</div>";
                }

                // Google Maps link
                const googleMapsLink = `https://www.google.com/maps?q=${barangay.latitude},${barangay.longitude}`;

                // Create popup content
                const popupContent = `
                    <div style="padding: 12px; min-width: 300px;">
                        <div style="background: linear-gradient(135deg, #1D4ED8 0%, #1e40af 100%); color: white; margin: -12px -12px 12px -12px; padding: 12px; border-radius: 6px 6px 0 0;">
                            <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 700;">${barangay.name}</h3>
                            <p style="margin: 0; font-size: 12px; opacity: 0.9;">${barangay.city || "Cebu City"}</p>
                        </div>

                        <div style="padding: 0 4px;">
                            <div style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                <div style="font-size: 11px; color: #6b7280; margin-bottom: 3px;">Status</div>
                                <div style="display: inline-block; background: ${barangayStatus === "emergency" ? "#fee2e2" : barangayStatus === "critical" ? "#fed7aa" : barangayStatus === "warning" ? "#fef3c7" : "#d1fae5"}; color: ${barangayStatus === "emergency" ? "#991b1b" : barangayStatus === "critical" ? "#9a3412" : barangayStatus === "warning" ? "#92400e" : "#065f46"}; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: 700; text-transform: uppercase;">
                                    ${barangayStatus}
                                </div>
                            </div>

                            ${
                                barangayStatus !== "safe" &&
                                barangay.disaster_type
                                    ? `
                                <div style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 3px;">Disaster Type</div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1f2937;">
                                        ${cityDisasterIcons[barangay.disaster_type] || ""} ${barangay.disaster_type.charAt(0).toUpperCase() + barangay.disaster_type.slice(1)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${
                                barangayStatus !== "safe" &&
                                barangay.affected_families
                                    ? `
                                <div style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 3px;">Affected Families</div>
                                    <div style="font-size: 13px; font-weight: 600; color: #1f2937;">
                                        ${formatCityNumber(barangay.affected_families)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${
                                barangay.total_raised
                                    ? `
                                <div style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                    <div style="font-size: 11px; color: #6b7280; margin-bottom: 3px;">Donations Received</div>
                                    <div style="font-size: 13px; font-weight: 600; color: #10b981;">
                                        ${formatCityCurrency(barangay.total_raised)}
                                    </div>
                                </div>
                            `
                                    : ""
                            }

                            ${resourceNeedsHtml}

                            <div style="margin-top: 12px;">
                                <a href="${googleMapsLink}" target="_blank" style="display: block; text-align: center; background: #1D4ED8; color: white; padding: 8px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 12px;">
                                    📍 View on Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                `;

                marker.bindPopup(popupContent, {
                    maxWidth: 350,
                    maxHeight: 450,
                    className: "custom-leaflet-popup",
                });

                // Update statistics (only for non-safe barangays)
                if (barangayStatus !== "safe") {
                    totalDonations += parseFloat(barangay.total_raised) || 0;
                    totalFamilies += parseInt(barangay.affected_families) || 0;
                    affectedCount++;
                }
            });

            // Update summary statistics
            document.getElementById("cityTotalDonations").textContent =
                formatCityCurrency(totalDonations);
            document.getElementById("cityAffectedFamilies").textContent =
                formatCityNumber(totalFamilies);
            document.getElementById("cityAffectedBarangays").textContent =
                affectedCount;

            // Load donors count from statistics API
            fetch("/api/statistics")
                .then((response) => response.json())
                .then((stats) => {
                    document.getElementById("cityTotalDonors").textContent =
                        formatCityNumber(stats.total_donors || 0);
                })
                .catch((error) => {
                    console.error("Error loading statistics:", error);
                    document.getElementById("cityTotalDonors").textContent =
                        "0";
                });

            // Populate barangay list (ALL barangays)
            const barangayList = document.getElementById("cityBarangayList");

            // Sort: emergency > critical > warning > safe
            const sortedBarangays = data.sort((a, b) => {
                const statusOrder = {
                    emergency: 0,
                    critical: 1,
                    warning: 2,
                    safe: 3,
                };
                return statusOrder[a.status] - statusOrder[b.status];
            });

            barangayList.innerHTML = sortedBarangays
                .map((barangay) => {
                    const barangayStatus = barangay.status || "safe";
                    const statusColor = getCityStatusColor(barangayStatus);
                    const needsCount = barangay.resource_needs_count || 0;

                    return `
                        <div class="city-barangay-item" onclick="focusCityBarangay('${barangay.id}')">
                            <div class="city-barangay-info-summary">
                                <div class="city-barangay-name-summary">📍 ${barangay.name}</div>
                                <div class="city-barangay-meta">
                                    ${barangayStatus !== "safe" ? `${barangay.affected_families || 0} families • ` : ""}${formatCityCurrency(barangay.total_raised || 0)} raised
                                </div>
                                ${
                                    needsCount > 0
                                        ? `
                                    <div style="font-size: 10px; color: ${statusColor}; font-weight: 600; margin-top: 3px;">
                                        ${needsCount} resource need${needsCount > 1 ? "s" : ""}
                                    </div>
                                `
                                        : ""
                                }
                            </div>
                            <span class="city-status-badge ${barangayStatus}">
                                ${barangayStatus.toUpperCase()}
                            </span>
                        </div>
                    `;
                })
                .join("");
        })
        .catch((error) => {
            console.error("Error loading city map data:", error);
            const barangayList = document.getElementById("cityBarangayList");
            if (barangayList) {
                barangayList.innerHTML = `
                    <div style="text-align: center; padding: 20px; color: #ef4444;">
                        <div style="font-size: 32px; margin-bottom: 8px;">⚠️</div>
                        <div style="font-weight: 600;">Error Loading Data</div>
                        <div style="font-size: 12px; color: #6b7280;">Please refresh the page</div>
                    </div>
                `;
            }
        });
}

// Function to focus on a specific barangay
function focusCityBarangay(barangayId) {
    if (
        cityBarangayMap &&
        cityMarkers[barangayId] &&
        cityMarkers[barangayId].marker
    ) {
        cityBarangayMap.setView(cityMarkers[barangayId].marker.getLatLng(), 15);
        cityMarkers[barangayId].marker.openPopup();
    }
}

// Initialize map data on page load (only if we're on the map tab)
if (document.getElementById("cityBarangayMap")) {
    loadCityMapData();

    // Auto-refresh map data every 60 seconds
    setInterval(() => {
        loadCityMapData();
    }, 60000);
}

console.log("✅ City Dashboard Map loaded");
