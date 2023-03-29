require('./bootstrap');

const userIdElement = document.querySelector('meta[name="user-id"]');

if (userIdElement) {
    const userId = userIdElement.getAttribute('content');

    Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            // Display a toastr notification with the notification message
           console.log(notification.message, 'New Incoming Document');
        });
}
