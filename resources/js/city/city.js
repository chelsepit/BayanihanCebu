 // Simple Alert Modal System
        let alertCallback = null;

        function showAlert(message, title = 'Notice', showCancel = false) {
            return new Promise((resolve) => {
                const modal = document.getElementById('alertModal');
                const titleEl = document.getElementById('alertModalTitle');
                const messageEl = document.getElementById('alertModalMessage');
                const cancelBtn = document.getElementById('alertModalCancelBtn');

                titleEl.textContent = title;
                messageEl.textContent = message;

                if (showCancel) {
                    cancelBtn.classList.remove('hidden');
                } else {
                    cancelBtn.classList.add('hidden');
                }

                modal.classList.remove('hidden');
                alertCallback = resolve;
            });
        }

        function closeAlert(result) {
            document.getElementById('alertModal').classList.add('hidden');
            if (alertCallback) {
                alertCallback(result);
                alertCallback = null;
            }
        }

        // Match Success Modal System
        window.showMatchSuccessModal = function(matchData) {
            const modal = document.getElementById('matchSuccessModal');

            // Update match ID
            document.getElementById('matchSuccessId').textContent = `#${matchData.match_id}`;

            // Update barangay names
            document.getElementById('matchSuccessRequesting').textContent = matchData.requesting_barangay;
            document.getElementById('matchSuccessDonating').textContent = matchData.donating_barangay;

            // Show modal
            modal.classList.remove('hidden');
        }

        window.closeMatchSuccessModal = function() {
            document.getElementById('matchSuccessModal').classList.add('hidden');
        }

        // Override native alert with modal
        window.alert = function(message) {
            return showAlert(message);
        };

        // Override native confirm with modal
        window.confirm = function(message) {
            return showAlert(message, 'Confirm', true);
        };

        console.log('✅ Dashboard initialized successfully');


// time
(function() {
    'use strict';
    
    function updateDateTime() {
        try {
            const now = new Date();
            
            const dateOptions = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                timeZone: 'Asia/Manila'
            };
            
            const timeOptions = {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true,
                timeZone: 'Asia/Manila'
            };
            
            const dayOptions = {
                weekday: 'long',
                timeZone: 'Asia/Manila'
            };
            
            const formattedDate = now.toLocaleDateString('en-PH', dateOptions);
            const formattedTime = now.toLocaleTimeString('en-PH', timeOptions);
            const formattedDay = now.toLocaleDateString('en-PH', dayOptions);
            
            const dateElement = document.getElementById('current-date');
            const timeElement = document.getElementById('current-time');
            const dayElement = document.getElementById('current-day');
            
            if (dateElement) dateElement.textContent = formattedDate;
            if (timeElement) timeElement.textContent = formattedTime;
            if (dayElement) dayElement.textContent = formattedDay;
            
        } catch (error) {
            console.error('[LDRRMO Clock] Error updating display:', error);
        }
    }
    
    function initClock() {
        console.log('[LDRRMO Clock] Initializing real-time clock...');
        
        updateDateTime();
        
        setInterval(updateDateTime, 1000);
        
        console.log('[LDRRMO Clock] ✅ Clock initialized successfully');
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClock);
    } else {
        initClock();
    }
    
})();