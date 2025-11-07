document.addEventListener("DOMContentLoaded", function () {
    // Get the target type select dropdown and related sections
    const targetType = document.querySelector('select[name="target_type"]');
    const userSelect = document.getElementById("user-select");
    const companySelect = document.getElementById("company-select");
    const departmentSelect = document.getElementById("department-select");

    // Event listener for target type selection
    targetType.addEventListener("change", function () {
        // Show or hide the user, company, and department sections based on selected target type
        userSelect.style.display = this.value === "user" ? "block" : "none";
        companySelect.style.display = this.value === "company" ? "block" : "none";
        departmentSelect.style.display = this.value === "department" ? "block" : "none";
    });
});

// Tawk.to Live Chat Integration
// Initialize Tawk API for live chat functionality
var Tawk_API = Tawk_API || {}, Tawk_LoadStart = new Date();
(function() {
    var s1 = document.createElement("script"), s0 = document.getElementsByTagName("script")[0];
    s1.async = true;
    s1.src = 'https://embed.tawk.to/6808a0d064953a190caf63d4/1ipgr8c6g'; // Tawk.to embed URL
    s1.charset = 'UTF-8';
    s1.setAttribute('crossorigin', '*');
    s0.parentNode.insertBefore(s1, s0); // Insert the Tawk.to script before the first script tag
})();

// Notification Fetching and Display
// Fetch notifications and update the notification list
document.addEventListener("DOMContentLoaded", function () {
    const notificationCount = document.getElementById("notification-count");
    const notificationList = document.getElementById("notification-list");

    // Function to fetch notifications from the server
    function fetchNotifications() {
        fetch("{{ route('get.notifications') }}")
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = `<div class="header">Notifications</div>`;
                let unreadCount = 0;

                // Loop through the notifications
                if (data.length > 0) {
                    data.forEach((notification, index) => {
                        const senderName = notification.sender_name ?? "Unknown";
                        const message = notification.message ?? "No message";
                        const timeAgo = new Date(notification.created_at).toLocaleTimeString();

                        // Construct notification item
                        notificationList.innerHTML += `
                            <div class="item">
                                <strong>${senderName}</strong>: ${message}<br>
                                <small class="text-muted">${timeAgo}</small>
                            </div>`;

                        // Add a divider after each notification except the last
                        if (index < data.length - 1) {
                            notificationList.innerHTML += `<div class="divider"></div>`;
                        }

                        // Count unread notifications
                        if (notification.status === 'unread') {
                            unreadCount++;
                        }
                    });

                    notificationCount.textContent = unreadCount;
                } else {
                    // If no notifications, display default message
                    notificationCount.textContent = "0";
                    notificationList.innerHTML += `<div class="item">No new notifications.</div>`;
                }

                // Link to view all notifications
                notificationList.innerHTML += `<div class="divider"></div>
                <a href="{{ url('notifications') }}" class="item text-center">View All Notifications</a>`;
            })
            .catch(error => console.error('Error fetching notifications:', error));
    }

    // Fetch notifications every 30 seconds
    setInterval(fetchNotifications, 30000);
    fetchNotifications(); // Initial fetch
});

// Mark Notifications as Read
// Mark unread notifications as read when the page loads
document.addEventListener("DOMContentLoaded", function () {
    const notifications = document.querySelectorAll(".list-group-item");

    // Iterate through each notification item
    notifications.forEach(notification => {
        if (notification.classList.contains("unread")) {
            notification.classList.add("read"); // Mark as read
            notification.classList.remove("unread"); // Remove unread class
        }
    });
});

// User Action Tracking
// Track user actions and handle inactivity periods
document.addEventListener('DOMContentLoaded', function () {
    const sessionId = localStorage.getItem('session_id') || Date.now(); // Generate or retrieve session ID
    localStorage.setItem('session_id', sessionId);
    let inactivityTimer;

    // Function to track user actions
    function trackUserAction(action, isInactive = false) {
        const page = window.location.pathname;
        const ip = '{{ request()->ip() }}'; // Get user IP address

        // Send action data to the server
        fetch('/track-action', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                session_id: sessionId,
                page: page,
                action: action,
                ip: ip,
                is_inactive: isInactive
            })
        });
    }

    // Track initial page view
    trackUserAction('Page View');

    // Track button click events
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function () {
            trackUserAction(`Clicked: ${this.innerText}`);
        });
    });

    // Inactivity tracking (5 minutes)
    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        inactivityTimer = setTimeout(() => {
            trackUserAction('Inactivity Period', true);
        }, 300000);
    }

    // Reset inactivity timer on user interaction
    ['mousemove', 'keydown', 'click'].forEach(event => {
        window.addEventListener(event, resetInactivityTimer);
    });

    resetInactivityTimer();
});

// Role Dropdown Initialization
$(document).ready(function() {
    console.log("Role Dropdown Initialization");

    // Event listener for email selection
    $(document).on('change', '.getemail', function() {
        var selectedOption = $(this).find('option:selected');
        var email = selectedOption.data('e'); 
        var ref = selectedOption.data('ref');

        if (!email) {
            console.warn("Data attribute 'data-e' is missing or empty.");
        }

        if (!ref) {
            console.warn("Data attribute 'data-ref' is missing or empty.");
        }
    });

    // Event listener for role selection
    $(document).on('change', 'select[name="role_id"]', function() {
        var roleId = $(this).val();
        var roleName = $(this).find('option:selected').data('role');

        console.log("Role Selected:", roleName);
        console.log("Role ID:", roleId);
    });
});


 // Check if service workers are supported
if ('serviceWorker' in navigator) {
  window.addEventListener('load', function () {
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
      .then(function (registration) {
        console.log('✅ Service Worker registered with scope:', registration.scope);
      })
      .catch(function (err) {
        console.error('❌ Service Worker registration failed:', err);
      });
  });
}



// Event listener to execute code when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {

    // Get elements for notification count display and notification list container
    const notificationCount = document.getElementById("notification-count");
    const notificationList = document.getElementById("notification-list");

    /**
     * Function: fetchNotifications
     * Purpose: Fetch notifications from the server and update the notification list and count display.
     */
    function fetchNotifications() {
        // Send a fetch request to the server to get notifications data
        fetch("{{ route('get.notifications') }}")
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {

                // Initialize the notification list with a header
                notificationList.innerHTML = `<div class="header">Notifications</div>`;
                let unreadCount = 0; // Counter for unread notifications

                // Check if there are any notifications
                if (data.length > 0) {

                    // Iterate through each notification
                    data.forEach((notification, index) => {
                        // Extract notification data with fallback values
                        const senderName = notification.sender_name ?? "Unknown";
                        const message = notification.message ?? "No message";
                        const timeAgo = new Date(notification.created_at).toLocaleTimeString(); // Format the timestamp

                        // Construct HTML for each notification item
                        notificationList.innerHTML += `
                            <div class="item">
                                <strong>${senderName}</strong>: ${message}<br>
                                <small class="text-muted">${timeAgo}</small>
                            </div>
                        `;

                        // Add a divider between notifications, except for the last one
                        if (index < data.length - 1) {
                            notificationList.innerHTML += `<div class="divider"></div>`;
                        }

                        // Check if the notification is unread and increment the unread count
                        if (notification.status === 'unread') {
                            unreadCount++;
                        }
                    });

                    // Update the notification count with the number of unread notifications
                    notificationCount.textContent = unreadCount;

                } else {
                    // If no notifications, display a default message
                    notificationCount.textContent = "0";
                    notificationList.innerHTML += `<div class="item">No new notifications.</div>`;
                }

                // Add a link to view all notifications at the bottom
                notificationList.innerHTML += `<div class="divider"></div>
                <a href="{{ url('notifications') }}" class="item text-center">View All Notifications</a>`;

            })
            .catch(error => console.error('Error fetching notifications:', error)); // Log any errors during the fetch process
    }

    // Fetch notifications initially and set an interval to update every 30 seconds
    setInterval(fetchNotifications, 30000);
    fetchNotifications(); // Initial fetch when DOM is ready
});


// Event listener to execute code when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {

    // Select all elements with the class "list-group-item"
    const notifications = document.querySelectorAll(".list-group-item");

    /**
     * Iterate over each notification item and update its read status.
     * Purpose: Mark all notifications with the "unread" class as "read".
     */
    notifications.forEach(notification => {

        // Check if the notification has the "unread" class
        if (notification.classList.contains("unread")) {

            // Add the "read" class to indicate the notification has been seen
            notification.classList.add("read");

            // Remove the "unread" class to avoid duplication of classes
            notification.classList.remove("unread");
        }
    });
});



// Event listener to execute code when the DOM is fully loaded
document.addEventListener("DOMContentLoaded", function () {

    // Get necessary DOM elements
    const notificationList = document.getElementById("notification-list");
    const filter = document.getElementById("filter");
    const loadMoreBtn = document.getElementById("load-more");

    // Pagination variables
    let offset = 0;
    const limit = 5; // Number of notifications to load per request

    /**
     * Function: loadNotifications
     * Purpose: Fetch notifications from the server based on offset and limit and render them.
     */
    function loadNotifications() {

        // Fetch notifications data from the server
        fetch(`/get-notifications?offset=${offset}&limit=${limit}`)
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {

                // Handle case when there are no notifications for the first load
                if (data.length === 0 && offset === 0) {
                    notificationList.innerHTML = `<div class="alert alert-info">No notifications found.</div>`;
                    loadMoreBtn.style.display = 'none'; // Hide Load More button
                    return;
                }

                // Hide Load More button if fewer items are returned than the limit
                if (data.length < limit) {
                    loadMoreBtn.style.display = 'none';
                }

                // Iterate through the notifications and construct HTML
                data.forEach(notification => {
                    const notificationHTML = `
                        <div class="list-group-item d-flex justify-content-between align-items-center notification-item ${notification.status}" 
                             data-id="${notification.id}" 
                             data-status="${notification.status}">
                            <div>
                                <h6 class="mb-1">${notification.message}</h6>
                                <small class="text-muted">${new Date(notification.created_at).toLocaleString()}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary mark-read" data-id="${notification.id}">Mark as Read</button>
                        </div>`;
                    
                    // Append the constructed HTML to the notification list
                    notificationList.insertAdjacentHTML('beforeend', notificationHTML);
                });

                // Increment offset for the next load
                offset += limit;
            })
            .catch(error => console.error('Error loading notifications:', error)); // Handle fetch error
    }

    // Initial load of notifications
    loadNotifications();

    /**
     * Event Listener: Load More Button
     * Purpose: Load additional notifications when the button is clicked.
     */
    loadMoreBtn.addEventListener("click", function () {
        loadNotifications();
    });

    /**
     * Event Listener: Filter Notifications
     * Purpose: Filter notifications based on user selection (read, unread, today, yesterday).
     */
    filter.addEventListener("change", function () {
        const filterValue = this.value;
        const notifications = document.querySelectorAll(".notification-item");

        notifications.forEach(notification => {
            const status = notification.getAttribute("data-status");
            const date = new Date(notification.querySelector('small').innerText);

            let isVisible = false;

            // Determine visibility based on filter value
            if (filterValue === "all") {
                isVisible = true;
            } else if (filterValue === "read" && status === "read") {
                isVisible = true;
            } else if (filterValue === "unread" && status === "unread") {
                isVisible = true;
            } else if (filterValue === "today" && date.toDateString() === new Date().toDateString()) {
                isVisible = true;
            } else if (filterValue === "yesterday") {
                const yesterday = new Date();
                yesterday.setDate(yesterday.getDate() - 1);
                if (date.toDateString() === yesterday.toDateString()) {
                    isVisible = true;
                }
            }

            // Set display property based on visibility determination
            notification.style.display = isVisible ? "flex" : "none";
        });
    });

    /**
     * Event Listener: Mark as Read
     * Purpose: Update the status of a notification to "read" when the "Mark as Read" button is clicked.
     */
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("mark-read")) {
            const notificationId = e.target.getAttribute("data-id");

            // Send POST request to mark the notification as read
            fetch(`/notifications/${notificationId}/mark-read`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update notification element to reflect "read" status
                    const notification = document.querySelector(`.notification-item[data-id="${notificationId}"]`);
                    notification.classList.remove("unread");
                    notification.classList.add("read");
                    notification.setAttribute("data-status", "read");

                    // Remove the "Mark as Read" button after marking as read
                    e.target.remove();
                }
            })
            .catch(error => console.error('Error marking as read:', error)); // Handle error
        }
    });

});


// Select the "Mark All as Read" button
const allRead = document.querySelector(".read-all");

// Select all notification items with the class "single-notification"
const unread = document.querySelectorAll(".single-notification");

// Select a specific notification with the class "read" (if exists)
const read = document.querySelector(".read");

// Select the notification wrapper/container
const notifications = document.querySelector(".notification-wrapper");

/**
 * Event Listener: Mark All as Read
 * Purpose: Remove the "unread" class from all notifications when the "Mark All as Read" button is clicked.
 */
allRead.addEventListener("click", () => {

    // Iterate over all notifications
    unread.forEach(notification => {

        // Check if the notification has the "unread" class
        if (notification.classList.contains("unread")) {

            // Remove the "unread" class to mark it as read
            notification.classList.remove("unread");
        }
    });
});



