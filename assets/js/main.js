/* NGO Website - front-end behaviour */
(function () {
  'use strict';

  /* AOS scroll animations */
  if (window.AOS) {
    AOS.init({ duration: 650, once: true, offset: 60, disable: window.matchMedia('(prefers-reduced-motion: reduce)').matches });
  }

  /* Hero slider */
  if (window.Swiper && document.querySelector('.hero-swiper')) {
    new Swiper('.hero-swiper', {
      loop: true,
      autoplay: { delay: 5500, disableOnInteraction: false },
      pagination: { el: '.hero-swiper .swiper-pagination', clickable: true },
      speed: 700
    });
  }

  /* Testimonials slider */
  if (window.Swiper && document.querySelector('.testimonial-swiper')) {
    new Swiper('.testimonial-swiper', {
      loop: true,
      autoplay: { delay: 6000 },
      spaceBetween: 24,
      slidesPerView: 1,
      breakpoints: { 768: { slidesPerView: 2 }, 1100: { slidesPerView: 3 } },
      pagination: { el: '.testimonial-swiper .swiper-pagination', clickable: true }
    });
  }

  /* Animated statistic counters */
  var counters = document.querySelectorAll('[data-count]');
  if (counters.length && 'IntersectionObserver' in window) {
    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        io.unobserve(el);
        var target = parseInt(el.getAttribute('data-count'), 10) || 0;
        var duration = 1600, start = null;
        function step(ts) {
          if (!start) start = ts;
          var p = Math.min((ts - start) / duration, 1);
          el.textContent = Math.floor(target * (1 - Math.pow(1 - p, 3))).toLocaleString('en-IN');
          if (p < 1) requestAnimationFrame(step);
          else el.textContent = target.toLocaleString('en-IN') + (el.dataset.suffix || '');
        }
        requestAnimationFrame(step);
      });
    }, { threshold: 0.4 });
    counters.forEach(function (c) { io.observe(c); });
  }

  /* Gallery lightbox */
  var lightbox = document.getElementById('lightbox');
  if (lightbox) {
    var lbImg = lightbox.querySelector('img');
    document.querySelectorAll('.gallery-item[data-full]').forEach(function (item) {
      item.addEventListener('click', function (e) {
        e.preventDefault();
        lbImg.src = item.getAttribute('data-full');
        lbImg.alt = item.getAttribute('data-caption') || 'Gallery image';
        lightbox.classList.add('open');
      });
    });
    lightbox.addEventListener('click', function (e) {
      if (e.target === lightbox || e.target.closest('.close-btn')) lightbox.classList.remove('open');
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') lightbox.classList.remove('open');
    });
  }

  /* Gallery category filter */
  var filterBtns = document.querySelectorAll('[data-filter]');
  if (filterBtns.length) {
    filterBtns.forEach(function (btn) {
      btn.addEventListener('click', function () {
        filterBtns.forEach(function (b) { b.classList.remove('btn-blue'); b.classList.add('btn-outline-nav'); });
        btn.classList.add('btn-blue'); btn.classList.remove('btn-outline-nav');
        var f = btn.getAttribute('data-filter');
        document.querySelectorAll('[data-album]').forEach(function (it) {
          it.style.display = (f === 'all' || it.getAttribute('data-album') === f) ? '' : 'none';
        });
      });
    });
  }

  /* Mark active nav link */
  var path = window.location.pathname.replace(/\/+$/, '');
  document.querySelectorAll('.site-nav .nav-link[href]').forEach(function (a) {
    var href = a.getAttribute('href').replace(/\/+$/, '');
    if (href && href === window.location.origin + path) a.classList.add('active');
  });

  /* Lazy-load images that opted in */
  document.querySelectorAll('img:not([loading])').forEach(function (img) {
    img.setAttribute('loading', 'lazy');
  });
})();
