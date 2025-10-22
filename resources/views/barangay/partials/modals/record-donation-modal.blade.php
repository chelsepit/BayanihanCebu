<!-- MODAL: Record Donation Modal -->
<div id="recordModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <div>
                <h3 class="text-xl font-semibold text-gray-800">Record Donation from Donor</h3>
                <p class="text-sm text-gray-500 mt-1">Record physical donations received at Barangay {{ $barangay->name ?? 'Lahug' }}</p>
            </div>
            <button onclick="closeRecordModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="recordDonationForm" class="p-6">
            <!-- Donor Information -->
            <div class="mb-6">
                <h4 class="text-base font-semibold text-gray-800 mb-4">Donor Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" name="donor_name" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Juan Dela Cruz">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number *</label>
                        <input type="text" name="donor_contact" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="+63 912 345 6789">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email Address (Optional)</label>
                        <input type="email" name="donor_email" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="juan@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address within Barangay *</label>
                        <input type="text" name="donor_address" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="123 Main Street">
                    </div>
                </div>
            </div>

            <!-- Donation Details -->
            <div class="mb-6">
                <h4 class="text-base font-semibold text-gray-800 mb-4">Donation Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Donation Category *</label>
                        <select name="category" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select category</option>
                            <option value="food">Food</option>
                            <option value="water">Water</option>
                            <option value="medical">Medical</option>
                            <option value="shelter">Shelter</option>
                            <option value="clothing">Clothing</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity/Amount *</label>
                        <input type="text" name="quantity" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 50 kilos, 100 pieces">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Description *</label>
                        <textarea name="items_description" required rows="3" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Please describe the items..."></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Value (Optional)</label>
                        <div class="flex items-center border border-gray-300 rounded">
                            <span class="px-3 text-gray-600">₱</span>
                            <input type="number" name="estimated_value" step="0.01" class="w-full px-3 py-2 border-0 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Allocation -->
            <div class="mb-6">
                <h4 class="text-base font-semibold text-gray-800 mb-4">Allocation</h4>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Intended Recipients *</label>
                        <input type="text" name="intended_recipients" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="General Relief Distribution">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes/Special Instructions (Optional)</label>
                        <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Any special instructions..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="flex gap-3 justify-end border-t pt-4">
                <button type="button" onclick="closeRecordModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-[#0D47A1] text-white rounded hover:bg-[#0D47A1]/90 transition">
                    Generate Tracking Code
                </button>
            </div>
        </form>
    </div>
</div>
