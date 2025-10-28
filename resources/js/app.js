import './bootstrap';

// FullCalendar imports
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

// Make FullCalendar available globally
window.Calendar = Calendar;
window.dayGridPlugin = dayGridPlugin;
window.timeGridPlugin = timeGridPlugin;
window.interactionPlugin = interactionPlugin;

// Video player functionality
class VideoPlayer {
    constructor(container) {
        this.container = container;
        this.video = container.querySelector('video');
        this.initializePlayer();
    }

    initializePlayer() {
        // Add custom controls and functionality here
        this.addEventListeners();
    }

    addEventListeners() {
        // Video player event listeners
        if (this.video) {
            this.video.addEventListener('play', () => {
                console.log('Video started playing');
            });

            this.video.addEventListener('pause', () => {
                console.log('Video paused');
            });

            this.video.addEventListener('ended', () => {
                console.log('Video ended');
            });
        }
    }
}

// Initialize video players on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize video players
    const videoContainers = document.querySelectorAll('.video-player');
    videoContainers.forEach(container => {
        new VideoPlayer(container);
    });

    // Initialize FullCalendar if element exists
    const calendarEl = document.getElementById('calendar');
    if (calendarEl && window.Calendar) {
        const calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: '/api/schedules', // Will be implemented in controller
            editable: true,
            selectable: true,
            select: function(info) {
                // Handle date selection for creating new schedule
                console.log('Selected date:', info.startStr);
            },
            eventClick: function(info) {
                // Handle event click
                console.log('Event clicked:', info.event.title);
            }
        });
        calendar.render();
    }
});

// Dark mode toggle functionality
window.toggleDarkMode = function() {
    const html = document.documentElement;
    const isDark = html.classList.contains('dark');

    if (isDark) {
        html.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    } else {
        html.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    }
};

// Initialize theme on load
document.addEventListener('DOMContentLoaded', function() {
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    }
});
