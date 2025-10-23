/**
 * Messenger-Style Chat Boxes for Barangay Dashboard
 * Facebook Messenger-like floating chat interface
 */

let openChatBoxes = new Map(); // matchId -> { element, interval }

function viewConversation(matchId) {
    // Check if chat box already exists
    if (openChatBoxes.has(matchId)) {
        // If minimized, maximize it
        const existingBox = openChatBoxes.get(matchId).element;
        const chatBody = existingBox.querySelector(".chat-body");
        const chatFooter = existingBox.querySelector(".chat-footer");
        if (chatBody.classList.contains("hidden")) {
            chatBody.classList.remove("hidden");
            chatFooter.classList.remove("hidden");
        }
        // Bring to front by re-appending
        existingBox.parentElement.appendChild(existingBox);
        return;
    }

    // Create new chat box from template
    const template = document.getElementById("chat-box-template");
    const chatBox = template.content
        .cloneNode(true)
        .querySelector(".chat-box");
    chatBox.setAttribute("data-match-id", matchId);

    // Add to container
    const container = document.getElementById("chat-boxes-container");
    container.appendChild(chatBox);

    // Load conversation
    loadChatConversation(matchId, chatBox);

    // Start auto-refresh
    const refreshInterval = setInterval(() => {
        loadChatConversation(matchId, chatBox, true);
    }, 5000);

    // Store reference
    openChatBoxes.set(matchId, {
        element: chatBox,
        interval: refreshInterval,
    });
}

function closeChatBox(button) {
    const chatBox = button.closest(".chat-box");
    const matchId = parseInt(chatBox.getAttribute("data-match-id"));

    // Clear interval
    if (openChatBoxes.has(matchId)) {
        clearInterval(openChatBoxes.get(matchId).interval);
        openChatBoxes.delete(matchId);
    }

    // Remove element
    chatBox.remove();
}

function minimizeChatBox(button) {
    const chatBox = button.closest(".chat-box");
    const chatBody = chatBox.querySelector(".chat-body");
    const chatFooter = chatBox.querySelector(".chat-footer");
    const icon = button.querySelector("i");

    if (chatBody.classList.contains("hidden")) {
        // Maximize
        chatBody.classList.remove("hidden");
        chatFooter.classList.remove("hidden");
        icon.classList.remove("fa-window-maximize");
        icon.classList.add("fa-minus");
    } else {
        // Minimize
        chatBody.classList.add("hidden");
        chatFooter.classList.add("hidden");
        icon.classList.remove("fa-minus");
        icon.classList.add("fa-window-maximize");
    }
}

async function loadChatConversation(matchId, chatBox, silent = false) {
    try {
        const messagesContainer = chatBox.querySelector(".chat-body");

        if (!silent) {
            messagesContainer.innerHTML = `
                <div class="text-center py-12">
                    <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#CA6702] mb-4"></div>
                    <p class="text-gray-600">Loading conversation...</p>
                </div>
            `;
        }

        const response = await fetchAPI(
            `/api/bdrrmc/matches/${matchId}/conversation`,
        );

        if (!response.success) {
            // No conversation yet
            messagesContainer.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-comments-slash text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Conversation Yet</h3>
                    <p class="text-gray-500 mb-4">
                        The conversation will start once you accept the match request.
                    </p>
                </div>
            `;
            return;
        }

        // Update chat header
        const matchInfo = response.match;
        const chatTitle = chatBox.querySelector(".chat-title");
        const chatSubtitle = chatBox.querySelector(".chat-subtitle");

        // Determine the other party
        const otherParty =
            matchInfo.donating_barangay === matchInfo.my_barangay
                ? matchInfo.requesting_barangay
                : matchInfo.donating_barangay;

        chatTitle.textContent = otherParty;
        chatSubtitle.textContent = matchInfo.resource_need;

        // Display messages
        displayChatMessages(chatBox, response.conversation.messages);
    } catch (error) {
        console.error("Error loading conversation:", error);
        const messagesContainer = chatBox.querySelector(".chat-body");
        messagesContainer.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-exclamation-circle text-4xl text-red-400 mb-2"></i>
                <p class="text-xs text-gray-500">${error.message || "Failed to load"}</p>
            </div>
        `;
    }
}

function displayChatMessages(chatBox, messages) {
    const container = chatBox.querySelector(".chat-body");

    if (!messages || messages.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                <p class="text-xs text-gray-500">No messages yet</p>
            </div>
        `;
        return;
    }

    // Get current barangay ID from session or DOM
    const myBarangayId = parseInt(document.body.dataset.barangayId || window.currentBarangayId);

    // Render messages (Messenger-style bubbles)
    const html = messages
        .map((msg) => {
            const senderName = msg.sender_name || "Unknown";

            // Determine if this message is from me
            // Check if sender_barangay_id matches my barangay
            const isMe = msg.sender_barangay_id === myBarangayId;

            let bgColor, textColor, initial;
            if (msg.sender_role === "ldrrmo") {
                bgColor = "bg-indigo-600";
                textColor = "text-white";
                initial = "L";
            } else if (msg.sender_role === "requester") {
                bgColor = "bg-blue-500";
                textColor = "text-white";
                initial = "R";
            } else {
                bgColor = "bg-green-500";
                textColor = "text-white";
                initial = "D";
            }

            // My messages on the right, others on the left (Messenger style)
            if (isMe) {
                return `
                    <div class="flex items-start gap-2 justify-end mb-3">
                        <div class="flex flex-col items-end max-w-[85%]">
                            <p class="text-xs text-gray-600 mb-1">You</p>
                            <div class="${bgColor} ${textColor} rounded-2xl px-4 py-2">
                                <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">${formatTimeSimple(msg.created_at)}</p>
                        </div>
                    </div>
                `;
            } else {
                return `
                    <div class="flex items-start gap-2 mb-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full ${bgColor} flex items-center justify-center text-white text-xs font-bold">
                            ${initial}
                        </div>
                        <div class="flex flex-col max-w-[85%]">
                            <p class="text-xs text-gray-600 mb-1">${escapeHtml(senderName)}</p>
                            <div class="${bgColor} ${textColor} rounded-2xl px-4 py-2">
                                <p class="text-sm whitespace-pre-wrap break-words">${escapeHtml(msg.message)}</p>
                            </div>
                            <p class="text-xs text-gray-400 mt-1">${formatTimeSimple(msg.created_at)}</p>
                        </div>
                    </div>
                `;
            }
        })
        .join("");

    container.innerHTML = html;

    // Scroll to bottom
    setTimeout(() => {
        container.scrollTop = container.scrollHeight;
    }, 100);
}

function formatTimeSimple(dateString) {
    if (!dateString) return "";
    const date = new Date(dateString);
    return date.toLocaleTimeString("en-US", {
        hour: "numeric",
        minute: "2-digit",
    });
}

async function sendChatMessage(event, form) {
    event.preventDefault();

    const chatBox = form.closest(".chat-box");
    const matchId = parseInt(chatBox.getAttribute("data-match-id"));
    const input = form.querySelector(".message-input");
    const message = input.value.trim();

    if (!message) return;

    // Disable input while sending
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalBtnText = submitBtn.innerHTML;
    input.disabled = true;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

    try {
        const response = await fetchAPI(
            `/api/bdrrmc/matches/${matchId}/messages`,
            {
                method: "POST",
                body: JSON.stringify({ message }),
            },
        );

        if (response.success) {
            // Clear input
            input.value = "";

            // Reload conversation
            await loadChatConversation(matchId, chatBox, true);
        } else {
            alert("❌ Error: " + (response.message || "Failed to send message"));
        }
    } catch (error) {
        console.error("Error sending message:", error);
        alert("❌ Failed to send message. Please try again.");
    } finally {
        // Re-enable input
        input.disabled = false;
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalBtnText;
        input.focus();
    }
}

console.log("✅ Messenger-style chat boxes loaded");
