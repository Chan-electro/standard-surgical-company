document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('.nav-toggle');
    const navList = document.getElementById('primary-nav');
    if (navToggle && navList) {
        navToggle.addEventListener('click', () => {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            navList.classList.toggle('is-open');
        });
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const carousel = document.querySelector('[data-component="client-carousel"] .client-carousel');
    if (carousel && !prefersReducedMotion) {
        let index = 0;
        const items = carousel.querySelectorAll('.client-carousel__item');
        if (items.length === 0) {
            return;
        }
        setInterval(() => {
            index = (index + 1) % items.length;
            carousel.scrollTo({ left: items[index].offsetLeft, behavior: 'smooth' });
        }, 4000);
    }

    const slider = document.querySelector('[data-component="testimonial-slider"]');
    if (slider) {
        const track = slider.querySelector('.testimonial-slider__track');
        const slides = Array.from(track.children);
        const prev = slider.querySelector('.slider-control.prev');
        const next = slider.querySelector('.slider-control.next');
        let current = 0;

        if (slides.length === 0) {
            return;
        }

        const moveTo = (index) => {
            current = (index + slides.length) % slides.length;
            track.style.transform = `translateX(-${current * 100}%)`;
        };

        prev?.addEventListener('click', () => moveTo(current - 1));
        next?.addEventListener('click', () => moveTo(current + 1));

        slider.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowLeft') {
                event.preventDefault();
                moveTo(current - 1);
            }
            if (event.key === 'ArrowRight') {
                event.preventDefault();
                moveTo(current + 1);
            }
        });

        if (!prefersReducedMotion && slides.length > 1) {
            setInterval(() => moveTo(current + 1), 6000);
        }
    }
});
