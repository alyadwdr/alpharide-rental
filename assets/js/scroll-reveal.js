/* ========================================
   SCROLL REVEAL ENGINE
   Gunakan: assets/js/scroll-reveal.js
   
   Cara pakai di HTML:
     <div data-reveal="fade-up">...</div>
     <div data-reveal="fade-left" data-delay="200">...</div>
     <div data-stagger>
       <div data-reveal="fade-up">Item 1</div>
       <div data-reveal="fade-up">Item 2</div>
       <div data-reveal="fade-up">Item 3</div>
     </div>
   ======================================== */

(function () {
    'use strict';

    /* ── Konfigurasi ── */
    var CONFIG = {
        rootMargin: '0px 0px -60px 0px',   // trigger 60px sebelum bottom viewport
        threshold: 0.15,                     // 15% elemen terlihat → trigger
        staggerStep: 100                     // ms per anak di stagger
    };

    /* ── Tunggu DOM siap ── */
    document.addEventListener('DOMContentLoaded', function () {
        init();
    });

    function init() {
        /* 1. Cari semua elemen dengan data-reveal */
        var elements = document.querySelectorAll('[data-reveal]');

        if (elements.length === 0) return; // tidak ada elemen → keluar

        /* 2. Tambahkan custom delay (data-delay) sebagai inline style */
        elements.forEach(function (el) {
            var delay = el.getAttribute('data-delay');
            if (delay) {
                el.style.transitionDelay = delay + 'ms';
            }
        });

        /* 3. Tambahkan stagger delay pada anak-anak dari parent [data-stagger] */
        var staggerParents = document.querySelectorAll('[data-stagger]');
        staggerParents.forEach(function (parent) {
            var children = parent.querySelectorAll(':scope > [data-reveal]');
            children.forEach(function (child, index) {
                var baseDelay = parseInt(child.getAttribute('data-delay') || '0', 10);
                var staggerDelay = baseDelay + (index * CONFIG.staggerStep);
                child.style.transitionDelay = staggerDelay + 'ms';
            });
        });

        /* 4. Buat IntersectionObserver */
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    // Tambahkan class "visible" → trigger CSS transition
                    entry.target.classList.add('visible');

                    // Setelah animasi selesai, stop observing (satu kali saja)
                    // Kalau mau animasi ulang saat scroll balik, hapus baris ini:
                    observer.unobserve(entry.target);
                }
            });
        }, {
            rootMargin: CONFIG.rootMargin,
            threshold: CONFIG.threshold
        });

        /* 5. Observasi setiap elemen reveal */
        elements.forEach(function (el) {
            observer.observe(el);
        });
    }

})();