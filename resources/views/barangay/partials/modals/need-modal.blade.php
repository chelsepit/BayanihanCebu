<!-- MODAL: Add Resource Need Modal -->
<div id="needModal" class="modal">
    <div class="bg-white rounded-lg shadow-2xl max-w-2xl w-full mx-4">
        <div class="border-b px-6 py-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">Create Resource Request</h3>
            <button onclick="closeNeedModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form id="needForm" class="p-6">
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency *</label>
                    <select name="urgency" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Select urgency</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="critical">Critical</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity Needed *</label>
                    <input type="text" name="quantity" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="e.g., 500 family packs">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Affected Families *</label>
                    <input type="number" name="affected_families" required class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Number of families">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                <textarea name="description" required rows="4" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Describe what you need and why..."></textarea>
            </div>

            <div class="flex gap-3 justify-end border-t pt-4">
                <button type="button" onclick="closeNeedModal()" class="px-6 py-2 border border-gray-300 rounded hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                    Create Resource Request
                </button>
            </div>
        </form>
    </div>
</div>
