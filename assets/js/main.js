document.addEventListener("DOMContentLoaded", function() {
    // 1. Dynamic Interactive Search Functionality
    const searchInput = document.querySelector(".search-box");
    if(searchInput) {
        searchInput.addEventListener("keyup", function(e) {
            let filter = e.target.value.toLowerCase();
            let tableRows = document.querySelectorAll("table tbody tr");
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                if(text.includes(filter)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        });
    }

    // 2. Action Buttons Trigger Notifications
    const syncBtn = document.querySelector(".bi-cloud-arrow-down");
    if(syncBtn) {
        syncBtn.parentElement.addEventListener("click", function() {
            alert("Syncing system field data with central livestock directory... Complete!");
            window.location.reload();
        });
    }

    const scanBtn = document.querySelector(".bi-qr-code-scan");
    if(scanBtn) {
        scanBtn.parentElement.addEventListener("click", function() {
            alert("Initializing local RFID/Barcode scanner frequency... Holding for signal.");
        });
    }
});