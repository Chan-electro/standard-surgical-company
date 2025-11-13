document.addEventListener('DOMContentLoaded', () => {
    const navToggle = document.querySelector('.nav-toggle');
    const navList = document.getElementById('primary-nav');
    if (navToggle && navList) {
        navToggle.addEventListener('click', () => {
            const expanded = navToggle.getAttribute('aria-expanded') === 'true';
            navToggle.setAttribute('aria-expanded', String(!expanded));
            navList.classList.toggle('is-open');
        });

        navList.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => {
                navList.classList.remove('is-open');
                navToggle.setAttribute('aria-expanded', 'false');
            });
        });
    }

    const header = document.querySelector('.site-header');
    const setHeaderState = () => {
        if (!header) {
            return;
        }
        if (window.scrollY > 12) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
    };
    setHeaderState();
    window.addEventListener('scroll', setHeaderState, { passive: true });

    const motionQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    const prefersReducedMotion = motionQuery.matches;

    const revealElements = document.querySelectorAll('[data-animate]');
    if (revealElements.length) {
        if (prefersReducedMotion || !('IntersectionObserver' in window)) {
            revealElements.forEach((el) => el.classList.add('is-visible'));
        } else {
            const observer = new IntersectionObserver(
                (entries, obs) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('is-visible');
                            obs.unobserve(entry.target);
                        }
                    });
                },
                { threshold: 0.15, rootMargin: '0px 0px -40px 0px' }
            );
            revealElements.forEach((el) => observer.observe(el));
        }
    }

    const carousel = document.querySelector('[data-component="client-carousel"] .client-carousel');
    if (carousel && !prefersReducedMotion) {
        let index = 0;
        const items = carousel.querySelectorAll('.client-carousel__item');
        if (items.length > 0) {
            setInterval(() => {
                index = (index + 1) % items.length;
                carousel.scrollTo({ left: items[index].offsetLeft, behavior: 'smooth' });
            }, 4200);
        }
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
            setInterval(() => moveTo(current + 1), 6500);
        }
    }
});
