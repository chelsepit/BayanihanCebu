/**
 * Photo Upload Handling
 * Handles photo upload for distribution evidence
 */

let uploadedPhotos = [];
const MAX_PHOTOS = 5;

/**
 * Initializes photo upload event listener
 * Should be called after DOM is loaded
 */
function initPhotoUpload() {
    const photoInput = document.getElementById("photoInput");
    if (!photoInput) {
        console.warn("Photo input element not found");
        return;
    }

    photoInput.addEventListener("change", handlePhotoSelection);
}

/**
 * Handles photo selection and validation
 * @param {Event} e - The change event from file input
 */
function handlePhotoSelection(e) {
    const files = Array.from(e.target.files);
    const photoError = document.getElementById("photoError");
    const photoCount = document.getElementById("photoCount");
    const submitBtn = document.getElementById("submitDistribution");

    // Validate file count
    if (files.length > MAX_PHOTOS) {
        photoError.textContent = `Please select exactly ${MAX_PHOTOS} photos. You selected ${files.length}.`;
        photoError.classList.remove("hidden");
        photoCount.classList.add("hidden");
        submitBtn.disabled = true;
        return;
    }

    if (files.length < MAX_PHOTOS) {
        photoError.textContent = `Please select exactly ${MAX_PHOTOS} photos. You selected ${files.length}.`;
        photoError.classList.remove("hidden");
        photoCount.classList.add("hidden");
        submitBtn.disabled = true;
        return;
    }

    // Validate file types and sizes
    const validFiles = files.filter((file) => {
        const isValidType = ["image/png", "image/jpeg", "image/jpg"].includes(
            file.type,
        );
        const isValidSize = file.size <= 10 * 1024 * 1024; // 10MB
        return isValidType && isValidSize;
    });

    if (validFiles.length !== files.length) {
        photoError.textContent =
            "Some files are invalid. Only PNG/JPG under 10MB allowed.";
        photoError.classList.remove("hidden");
        photoCount.classList.add("hidden");
        submitBtn.disabled = true;
        return;
    }

    // All validations passed
    photoError.classList.add("hidden");
    photoCount.textContent = `✓ ${files.length} photos selected`;
    photoCount.classList.remove("hidden");
    submitBtn.disabled = false;

    // Convert to base64 and preview
    uploadedPhotos = [];
    const previewGrid = document.getElementById("photoPreviewGrid");
    previewGrid.innerHTML = "";
    previewGrid.classList.remove("hidden");

    files.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (event) {
            uploadedPhotos.push(event.target.result);

            // Create preview
            const previewDiv = document.createElement("div");
            previewDiv.className = "relative";
            previewDiv.innerHTML = `
                <img src="${event.target.result}" class="w-full h-20 object-cover rounded border-2 border-green-500">
                <div class="absolute top-1 right-1 bg-green-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                    ${index + 1}
                </div>
            `;
            previewGrid.appendChild(previewDiv);
        };
        reader.readAsDataURL(file);
    });
}

/**
 * Clears all uploaded photos
 */
function clearUploadedPhotos() {
    uploadedPhotos = [];
    const photoInput = document.getElementById("photoInput");
    if (photoInput) {
        photoInput.value = "";
    }
    const previewGrid = document.getElementById("photoPreviewGrid");
    if (previewGrid) {
        previewGrid.innerHTML = "";
        previewGrid.classList.add("hidden");
    }
    const photoCount = document.getElementById("photoCount");
    if (photoCount) {
        photoCount.classList.add("hidden");
    }
    const photoError = document.getElementById("photoError");
    if (photoError) {
        photoError.classList.add("hidden");
    }
}

/**
 * Gets the uploaded photos array
 * @returns {Array<string>} Array of base64 encoded photo strings
 */
function getUploadedPhotos() {
    return uploadedPhotos;
}

/**
 * Validates if the required number of photos are uploaded
 * @returns {boolean} True if valid number of photos are uploaded
 */
function validatePhotoCount() {
    return uploadedPhotos.length === MAX_PHOTOS;
}
