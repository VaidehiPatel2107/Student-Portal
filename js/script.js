
document.addEventListener('DOMContentLoaded', () => {

  // ==========================================
  // 1. LIGHT / DARK THEME SWITCHER
  //    Uses localStorage
  // ==========================================

  const themeToggleBtn = document.getElementById('theme-toggle-btn');

  const savedTheme = localStorage.getItem('theme') || 'light';

  if (savedTheme === 'dark') {
    document.body.classList.add('dark-theme');
  }

  if (themeToggleBtn) {

    // Set correct button text when page loads
    if (savedTheme === 'dark') {
      themeToggleBtn.textContent = '☀️ Light Mode';
    } else {
      themeToggleBtn.textContent = '🌙 Dark Mode';
    }

    themeToggleBtn.addEventListener('click', () => {

      document.body.classList.toggle('dark-theme');

      const currentTheme =
        document.body.classList.contains('dark-theme')
          ? 'dark'
          : 'light';

      // Save theme preference
      localStorage.setItem('theme', currentTheme);

      // Update button text
      themeToggleBtn.textContent =
        currentTheme === 'dark'
          ? '☀️ Light Mode'
          : '🌙 Dark Mode';

    });

  }


  // ==========================================
  // 2. DISMISSIBLE NOTIFICATION BANNER
  // ==========================================

  const closeBannerBtn =
    document.getElementById('close-banner-btn');

  const banner =
    document.getElementById('top-banner');

  if (closeBannerBtn && banner) {

    closeBannerBtn.addEventListener('click', () => {

      banner.style.display = 'none';

    });

  }


  // ==========================================
  // 3. HAMBURGER MENU TOGGLE
  // ==========================================

  const hamburgerBtn =
    document.getElementById('hamburger-btn');

  const navMenu =
    document.getElementById('nav-menu');

  if (hamburgerBtn && navMenu) {

    hamburgerBtn.addEventListener('click', () => {

      // Toggle menu
      navMenu.classList.toggle('active');

      // Check whether menu is open
      const isOpen =
        navMenu.classList.contains('active');

      // Update accessibility attribute
      hamburgerBtn.setAttribute(
        'aria-expanded',
        isOpen
      );

    });

  }


  // ==========================================
  // 4. COLLAPSIBLE FAQ ACCORDION
  // ==========================================

  const faqHeaders =
    document.querySelectorAll('.faq-question');

  faqHeaders.forEach(header => {

    header.addEventListener('click', () => {

      const parent = header.parentElement;

      const answer = header.nextElementSibling;

      parent.classList.toggle('open');

      if (parent.classList.contains('open')) {

        answer.style.maxHeight =
          answer.scrollHeight + 'px';

      } else {

        answer.style.maxHeight = null;

      }

    });

  });


  // ==========================================
  // 5. IMAGE / CONTENT SLIDER
  // ==========================================

  const slides =
    document.querySelectorAll('.slide-item');

  const prevSlideBtn =
    document.getElementById('prev-slide');

  const nextSlideBtn =
    document.getElementById('next-slide');

  let currentSlide = 0;


  function showSlide(index) {

    slides.forEach((slide, i) => {

      slide.classList.remove('active');

      if (i === index) {
        slide.classList.add('active');
      }

    });

  }


  if (slides.length > 0) {

    // Next slide
    if (nextSlideBtn) {

      nextSlideBtn.addEventListener('click', () => {

        currentSlide =
          (currentSlide + 1) % slides.length;

        showSlide(currentSlide);

      });

    }


    // Previous slide
    if (prevSlideBtn) {

      prevSlideBtn.addEventListener('click', () => {

        currentSlide =
          (currentSlide - 1 + slides.length)
          % slides.length;

        showSlide(currentSlide);

      });

    }

  }


  // ==========================================
  // 6. MODAL POPUP
  // ==========================================

  const openModalBtn =
    document.getElementById('open-modal-btn');

  const closeModalBtn =
    document.getElementById('close-modal-btn');

  const modal =
    document.getElementById('custom-modal');


  // Open modal
  if (openModalBtn && modal) {

    openModalBtn.addEventListener('click', () => {

      modal.classList.add('show');

    });

  }


  // Close modal
  if (closeModalBtn && modal) {

    closeModalBtn.addEventListener('click', () => {

      modal.classList.remove('show');

    });

  }


  // Close modal by clicking outside the modal box
  window.addEventListener('click', (event) => {

    if (event.target === modal) {

      modal.classList.remove('show');

    }

  });

});

