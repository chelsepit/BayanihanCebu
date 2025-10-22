/**
 * Resource Needs Management
 * Handles loading, creating, and managing resource needs/requests for barangays
 */

/**
 * Loads and displays resource needs/requests
 * @async
 * @returns {Promise<void>}
 */
async function loadResourceNeeds() {
    const container = document.getElementById('needsList');
    container.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-gray-400"></i></div>';

    try {
        const response = await fetch('/api/bdrrmc/needs');
        const needs = await response.json();

        if (needs.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <i class="fas fa-clipboard-list text-5xl mb-4 text-gray-300"></i>
                    <p class="text-lg">No resource requests yet.</p>
                    <p class="text-sm mt-2">Click "Create Request" to add your first resource need.</p>
                </div>
            `;
            document.getElementById('bulkActionsBar').classList.add('hidden');
            return;
        }

        // Show bulk actions bar
        document.getElementById('bulkActionsBar').classList.remove('hidden');

        // Update counts
        const pendingCount = needs.filter(n => n.status !== 'fulfilled').length;
        const fulfilledCount = needs.filter(n => n.status === 'fulfilled').length;
        document.getElementById('activeRequestsCount').textContent = pendingCount;
        document.getElementById('needsCount').textContent = needs.length;
        document.getElementById('fulfilledCount').textContent = fulfilledCount;

        container.innerHTML = needs.map(need => `
            <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition ${need.status === 'fulfilled' ? 'bg-green-50 opacity-75' : ''}">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h3 class="text-lg font-semibold text-gray-800">${formatCategory(need.category)}</h3>
                            <span class="px-3 py-1 text-xs font-semibold rounded ${getUrgencyBadge(need.urgency)}">
                                ${need.urgency.toUpperCase()}
                            </span>
                            <span class="px-3 py-1 text-xs font-semibold rounded ${getNeedStatusBadge(need.status)}">
                                ${formatStatus(need.status)}
                            </span>
                        </div>
                        <p class="text-gray-700 mb-4">${need.description}</p>
                        <div class="grid grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Quantity:</p>
                                <p class="font-medium text-gray-800">${need.quantity}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Affected Families:</p>
                                <p class="font-medium text-gray-800">120</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Created:</p>
                                <p class="font-medium text-gray-800">${formatDate(need.created_at)}</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4 flex flex-col gap-2">
                        ${need.status !== 'fulfilled' ? `
                            <button onclick="markNeedAsFulfilled(${need.id})" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition text-sm flex items-center gap-2">
                                <i class="fas fa-check"></i> Mark as Fulfilled
                            </button>
                            <button onclick="updateNeedStatus(${need.id})" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition text-sm flex items-center gap-2">
                                <i class="fas fa-edit"></i> Update Status
                            </button>
                        ` : `
                            <button onclick="removeNeed(${need.id})" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition text-sm flex items-center gap-2">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                            <button onclick="markNeedAsPending(${need.id})" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 transition text-sm flex items-center gap-2">
                                <i class="fas fa-undo"></i> Reopen
                            </button>
                        `}
                    </div>
                </div>
            </div>
        `).join('');

    } catch (error) {
        console.error('Error loading needs:', error);
        container.innerHTML = `
            <div class="text-center py-12 text-red-500">
                <i class="fas fa-exclamation-circle text-5xl mb-4"></i>
                <p class="text-lg">Error loading resource requests</p>
            </div>
        `;
    }
}

/**
 * Marks a single resource need as fulfilled
 * @async
 * @param {number} needId - The ID of the resource need
 * @returns {Promise<void>}
 */
async function markNeedAsFulfilled(needId) {
    if (!confirm('Mark this resource request as fulfilled?')) return;

    try {
        // csrfToken from utils.js
        const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: 'fulfilled' })
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Resource request marked as fulfilled!');
            loadResourceNeeds();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error updating status.');
    }
}

/**
 * Updates the status of a resource need with user selection
 * @async
 * @param {number} needId - The ID of the resource need
 * @returns {Promise<void>}
 */
async function updateNeedStatus(needId) {
    const newStatus = prompt(
        'Update status:\n\n' +
        '1 = pending (not yet fulfilled)\n' +
        '2 = partially_fulfilled (some items received)\n' +
        '3 = fulfilled (completely fulfilled)\n\n' +
        'Enter 1, 2, or 3:',
        '1'
    );

    const statusMap = {
        '1': 'pending',
        '2': 'partially_fulfilled',
        '3': 'fulfilled'
    };

    if (!statusMap[newStatus]) {
        alert('❌ Invalid selection. Please enter 1, 2, or 3.');
        return;
    }

    try {
        // csrfToken from utils.js
        const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: statusMap[newStatus] })
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Status updated successfully!');
            loadResourceNeeds();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error updating status.');
    }
}

/**
 * Marks a fulfilled need as pending (reopens it)
 * @async
 * @param {number} needId - The ID of the resource need
 * @returns {Promise<void>}
 */
async function markNeedAsPending(needId) {
    if (!confirm('Reopen this resource request?\n\nThis will change the status back to "pending".')) {
        return;
    }

    try {
        // csrfToken from utils.js
        const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ status: 'pending' })
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Resource request reopened successfully!');
            loadResourceNeeds();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error reopening resource request.');
    }
}

/**
 * Removes a resource need from the system
 * @async
 * @param {number} needId - The ID of the resource need
 * @returns {Promise<void>}
 */
async function removeNeed(needId) {
    if (!confirm('⚠️ Remove this fulfilled request from the list?\n\nThis action CANNOT be undone.')) {
        return;
    }

    try {
        // csrfToken from utils.js
        const response = await fetch(`/api/bdrrmc/needs/${needId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('✅ Resource request removed successfully!');
            loadResourceNeeds();
        }
    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error removing resource request.');
    }
}

/**
 * Marks all pending resource requests as fulfilled (bulk action)
 * @async
 * @returns {Promise<void>}
 */
async function markAllAsFulfilled() {
    if (!confirm('⚠️ Mark ALL pending resource requests as fulfilled?\n\nThis will mark all pending and partially fulfilled requests as completed.')) {
        return;
    }

    try {
        const response = await fetch('/api/bdrrmc/needs');
        const needs = await response.json();

        const pendingNeeds = needs.filter(n => n.status !== 'fulfilled');

        if (pendingNeeds.length === 0) {
            alert('ℹ️ No pending requests to mark as fulfilled.');
            return;
        }

        // Show progress
        const progressMsg = `Marking ${pendingNeeds.length} requests as fulfilled...`;
        console.log(progressMsg);

        // csrfToken from utils.js

        // Mark each as fulfilled
        let successCount = 0;
        for (const need of pendingNeeds) {
            try {
                const res = await fetch(`/api/bdrrmc/needs/${need.id}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ status: 'fulfilled' })
                });

                if (res.ok) successCount++;
            } catch (err) {
                console.error(`Failed to mark need ${need.id}:`, err);
            }
        }

        alert(`✅ Successfully marked ${successCount} of ${pendingNeeds.length} requests as fulfilled!`);
        loadResourceNeeds();

    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error marking requests as fulfilled.');
    }
}

/**
 * Removes all fulfilled resource requests (bulk action)
 * @async
 * @returns {Promise<void>}
 */
async function removeAllFulfilled() {
    try {
        const response = await fetch('/api/bdrrmc/needs');
        const needs = await response.json();

        const fulfilledNeeds = needs.filter(n => n.status === 'fulfilled');

        if (fulfilledNeeds.length === 0) {
            alert('ℹ️ No fulfilled requests to remove.');
            return;
        }

        if (!confirm(`⚠️ PERMANENTLY DELETE ${fulfilledNeeds.length} fulfilled resource requests?\n\n⚡ This action CANNOT be undone!\n\nThe requests will be removed from the database.`)) {
            return;
        }

        // Double confirmation for safety
        if (!confirm(`⚠️ Are you ABSOLUTELY SURE?\n\nThis will delete ${fulfilledNeeds.length} requests permanently.`)) {
            return;
        }

        // Show progress
        console.log(`Deleting ${fulfilledNeeds.length} fulfilled requests...`);

        // csrfToken from utils.js

        // Delete each fulfilled need
        let successCount = 0;
        for (const need of fulfilledNeeds) {
            try {
                const res = await fetch(`/api/bdrrmc/needs/${need.id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                if (res.ok) successCount++;
            } catch (err) {
                console.error(`Failed to delete need ${need.id}:`, err);
            }
        }

        alert(`✅ Successfully removed ${successCount} of ${fulfilledNeeds.length} fulfilled requests!`);
        loadResourceNeeds();

    } catch (error) {
        console.error('Error:', error);
        alert('❌ Error removing fulfilled requests.');
    }
}

/**
 * Gets the CSS badge class for urgency levels
 * @param {string} urgency - The urgency level (low, medium, high, critical)
 * @returns {string} CSS class string
 */
function getUrgencyBadge(urgency) {
    const badges = {
        'low': 'bg-gray-100 text-gray-700',
        'medium': 'bg-yellow-100 text-yellow-700',
        'high': 'bg-orange-100 text-orange-700',
        'critical': 'bg-red-100 text-red-700'
    };
    return badges[urgency] || 'bg-gray-100 text-gray-700';
}

/**
 * Gets the CSS badge class for need status
 * @param {string} status - The need status (pending, partially_fulfilled, fulfilled)
 * @returns {string} CSS class string
 */
function getNeedStatusBadge(status) {
    const badges = {
        'pending': 'bg-yellow-100 text-yellow-700',
        'partially_fulfilled': 'bg-blue-100 text-blue-700',
        'fulfilled': 'bg-green-100 text-green-700'
    };
    return badges[status] || 'bg-gray-100 text-gray-700';
}
