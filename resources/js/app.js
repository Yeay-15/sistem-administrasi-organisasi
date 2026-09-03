// ============ REVEAL ON SCROLL ============
// Menambahkan class "is-visible" ke elemen ".reveal" secara bertahap saat
// elemen tersebut masuk ke area pandang (viewport), memberi efek konten
// "dimuat satu per satu" seperti pada situs Ormawa UT — tanpa benar-benar
// menunda pemuatan data, cukup animasi kemunculannya.
function initRevealOnScroll() {
    const items = document.querySelectorAll('.reveal:not(.is-visible)');
    if (!items.length) return;

    if (!('IntersectionObserver' in window)) {
        items.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    items.forEach((el) => observer.observe(el));
}

document.addEventListener('DOMContentLoaded', initRevealOnScroll);
document.addEventListener('livewire:navigated', initRevealOnScroll);
