    <footer>
      <div class="container">
        <div class="footer-container">
          <div class="footer-col">
            <h4>Trade Author</h4>
            <p style="color: var(--text-muted); margin-bottom: 20px;">
              Professional trading platform for cryptocurrencies and stocks with enterprise security.
            </p>
          </div>

          <div class="footer-col">
            <h4>Markets</h4>
            <ul class="footer-links">
              <li><a href="markets">Cryptocurrencies</a></li>
              <li><a href="markets">Stocks & ETFs</a></li>
              <li><a href="markets">Commodities</a></li>
              <li><a href="markets">Forex</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Resources</h4>
            <ul class="footer-links">
              <li><a href="contact">Help Center</a></li>
              <li><a href="markets">Trading Academy</a></li>
              <li><a href="markets">Market Analysis</a></li>
              <li><a href="markets">API Documentation</a></li>
            </ul>
          </div>

          <div class="footer-col">
            <h4>Company</h4>
            <ul class="footer-links">
              <li><a href="about">About Us</a></li>
              <li><a href="contact">Careers</a></li>
              <li><a href="terms">Terms of Service</a></li>
              <li><a href="privacy">Privacy Policy</a></li>
            </ul>
          </div>
        </div>

        <div class="footer-bottom">
          <p>© <?= date('Y'); ?> Trade Author. All rights reserved.</p>
        </div>
      </div>
    </footer>

    <script>
      // Mobile navigation functionality
      const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
      const mobileNav = document.querySelector('.mobile-nav');
      const overlay = document.querySelector('.overlay');
      const body = document.body;

      function toggleMobileNav() {
        mobileNav.classList.toggle('active');
        overlay.classList.toggle('active');
        body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';

        // Change icon based on menu state
        const icon = mobileNavToggle.querySelector('i');
        if (mobileNav.classList.contains('active')) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-times');
        } else {
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      }

      mobileNavToggle.addEventListener('click', toggleMobileNav);
      overlay.addEventListener('click', toggleMobileNav);

      // Close mobile nav when clicking on links
      const mobileNavLinks = document.querySelectorAll('.mobile-nav a');
      mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
          mobileNav.classList.remove('active');
          overlay.classList.remove('active');
          body.style.overflow = '';

          const icon = mobileNavToggle.querySelector('i');
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        });
      });

      // Handle window resize
      window.addEventListener('resize', () => {
        if (window.innerWidth > 968) {
          mobileNav.classList.remove('active');
          overlay.classList.remove('active');
          body.style.overflow = '';

          const icon = mobileNavToggle.querySelector('i');
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      });

      // Tab functionality for markets
      const tabs = document.querySelectorAll('.tab');
      tabs.forEach(tab => {
        tab.addEventListener('click', () => {
          tabs.forEach(t => t.classList.remove('active'));
          tab.classList.add('active');
        });
      });

      // Simple animation for elements
      document.addEventListener("DOMContentLoaded", function() {
        const marketCards = document.querySelectorAll(".market-card");
        const featureCards = document.querySelectorAll(".feature-card");

        // Animate elements on scroll
        function animateOnScroll() {
          marketCards.forEach((card) => {
            const position = card.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;

            if (position < screenPosition) {
              card.style.opacity = 1;
              card.style.transform = "translateY(0)";
            }
          });

          featureCards.forEach((card) => {
            const position = card.getBoundingClientRect().top;
            const screenPosition = window.innerHeight / 1.3;

            if (position < screenPosition) {
              card.style.opacity = 1;
              card.style.transform = "translateY(0)";
            }
          });
        }

        // Initialize animation styles
        marketCards.forEach((card) => {
          card.style.opacity = 0;
          card.style.transform = "translateY(20px)";
          card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        featureCards.forEach((card) => {
          card.style.opacity = 0;
          card.style.transform = "translateY(20px)";
          card.style.transition = "opacity 0.5s ease, transform 0.5s ease";
        });

        // Listen for scroll events
        window.addEventListener("scroll", animateOnScroll);
        // Initial check
        animateOnScroll();
      });
    </script>
    </body>

    </html>