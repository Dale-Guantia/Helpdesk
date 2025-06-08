import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

document.addEventListener('DOMContentLoaded', () => {
    const userId = document.querySelector('meta[name="user-id"]')?.content;
    if (!userId) return console.error('User ID not found.');

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: import.meta.env.VITE_PUSHER_APP_KEY,
        cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
        forceTLS: true,
    });

    window.Echo.private(`App.Models.User.${userId}`)
        .notification((notification) => {
            console.log('🔔 Notification received:', notification);

            // Play sound
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch((err) => console.error('Sound error:', err));

            // Refresh Filament notification badge
            if (typeof Livewire !== 'undefined') {
                Livewire.dispatch('databaseNotificationsSent');
            }
        });
});
