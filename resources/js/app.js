import './bootstrap';
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';

import Alpine from 'alpinejs';
import Swiper from 'swiper/bundle';
import Swal from 'sweetalert2';

window.Swal = Swal;
window.Alpine = Alpine;
Alpine.start();

// Swiper
document.addEventListener('DOMContentLoaded', () => {
    new Swiper('.swiper', {
        loop: true,
        pagination: { el: '.swiper-pagination' },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
    });
});

// FullCalendar: se inicializa después de que Livewire cargue la vista
document.addEventListener('livewire:load', () => {
    let calendarEl = document.getElementById('calendar');
    if(calendarEl) {
        let calendar = new Calendar(calendarEl, {
            plugins: [dayGridPlugin],
            initialView: 'dayGridMonth',
            locale: 'es',
            events: window.licencias || [] // Se puede pasar desde Blade con @json()
        });
        calendar.render();
    }
});
