/**
 * HOKIRAJA - OPTIMIZED JAVASCRIPT ENHANCEMENTS
 * Animasi, Interaksi, dan Optimasi Mobile/Desktop yang Keren
 */

class HokirajaOptimizer {
  constructor() {
    this.isMobile = window.innerWidth <= 768;
    this.isTouch = 'ontouchstart' in window;
    this.scrollElements = [];
    this.init();
  }

  init() {
    this.setupScrollReveal();
    this.setupEnhancedNavigation();
    this.setupMobileOptimizations();
    this.setupPerformanceOptimizations();
    this.setupInteractionEnhancements();
    this.setupAccessibilityFeatures();
    this.setupAnimationQueue();
  }

  // ========================================================================
  // SCROLL REVEAL ANIMATIONS
  // ========================================================================
  
  setupScrollReveal() {
    // Intersection Observer for scroll animations
    this.observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('revealed');
          this.observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    });

    // Add scroll reveal to elements
    this.addScrollRevealElements();
    
    // Setup parallax scrolling for non-mobile devices
    if (!this.isMobile) {
      this.setupParallaxScrolling();
    }
  }

  addScrollRevealElements() {
    // Auto-detect elements that should have scroll animations
    const selectors = [
      '.card', '.btn', '.form-group', '.nav-item',
      '.game-content-desktop', '.main-game-content-mobile',
      '.contact-info-sidebar', '.announcement-bar'
    ];

    selectors.forEach(selector => {
      document.querySelectorAll(selector).forEach((el, index) => {
        // Add staggered animation delays
        el.style.animationDelay = `${index * 0.1}s`;
        
        // Add appropriate scroll reveal class based on position
        if (index % 3 === 0) {
          el.classList.add('scroll-reveal');
        } else if (index % 3 === 1) {
          el.classList.add('scroll-reveal-left');
        } else {
          el.classList.add('scroll-reveal-right');
        }
        
        this.observer.observe(el);
      });
    });
  }

  setupParallaxScrolling() {
    let ticking = false;
    
    const updateParallax = () => {
      const scrollY = window.pageYOffset;
      
      // Parallax for header background
      const header = document.querySelector('.main-header');
      if (header) {
        header.style.transform = `translateY(${scrollY * 0.5}px)`;
      }
      
      // Parallax for background elements
      document.querySelectorAll('.parallax-bg').forEach(el => {
        const speed = el.dataset.speed || 0.5;
        el.style.transform = `translateY(${scrollY * speed}px)`;
      });
      
      ticking = false;
    };

    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(updateParallax);
        ticking = true;
      }
    }, { passive: true });
  }

  // ========================================================================
  // ENHANCED NAVIGATION
  // ========================================================================
  
  setupEnhancedNavigation() {
    const nav = document.querySelector('.main-header');
    if (!nav) return;

    let lastScrollY = window.pageYOffset;
    let navVisible = true;

    // Enhanced scroll behavior for navigation
    const handleNavScroll = () => {
      const currentScrollY = window.pageYOffset;
      
      // Add scrolled class for backdrop effect
      if (currentScrollY > 50) {
        nav.classList.add('nav-enhanced', 'scrolled');
      } else {
        nav.classList.remove('scrolled');
      }

      // Hide/show navigation on scroll (mobile only)
      if (this.isMobile) {
        if (currentScrollY > lastScrollY && currentScrollY > 100) {
          // Scrolling down
          if (navVisible) {
            nav.style.transform = 'translateY(-100%)';
            navVisible = false;
          }
        } else {
          // Scrolling up
          if (!navVisible) {
            nav.style.transform = 'translateY(0)';
            navVisible = true;
          }
        }
      }

      lastScrollY = currentScrollY;
    };

    // Throttled scroll listener
    let scrollTimeout;
    window.addEventListener('scroll', () => {
      if (scrollTimeout) {
        clearTimeout(scrollTimeout);
      }
      scrollTimeout = setTimeout(handleNavScroll, 10);
    }, { passive: true });

    // Enhanced navigation items
    document.querySelectorAll('.main-nav a').forEach(link => {
      link.classList.add('nav-item-enhanced');
      
      // Add ripple effect on click
      link.addEventListener('click', this.createRippleEffect.bind(this));
    });
  }

  // ========================================================================
  // MOBILE OPTIMIZATIONS
  // ========================================================================
  
  setupMobileOptimizations() {
    if (!this.isMobile) return;

    // Enhanced touch interactions
    this.setupTouchFeedback();
    
    // Optimize mobile navigation
    this.optimizeMobileNavigation();
    
    // Setup swipe gestures
    this.setupSwipeGestures();
    
    // Optimize viewport for mobile
    this.optimizeViewport();
  }

  setupTouchFeedback() {
    // Add haptic feedback for touch devices
    const touchElements = document.querySelectorAll('.btn, .nav-item, .mobile-nav-item');
    
    touchElements.forEach(el => {
      el.addEventListener('touchstart', (e) => {
        // Add touch feedback class
        el.classList.add('touching');
        
        // Vibrate if supported (very light)
        if (navigator.vibrate) {
          navigator.vibrate(10);
        }
      }, { passive: true });
      
      el.addEventListener('touchend', (e) => {
        // Remove touch feedback class
        setTimeout(() => {
          el.classList.remove('touching');
        }, 150);
      }, { passive: true });
    });
  }

  optimizeMobileNavigation() {
    const mobileNav = document.querySelector('.mobile-footer-nav');
    if (!mobileNav) return;

    mobileNav.classList.add('mobile-nav-enhanced');
    
    // Enhanced mobile nav items
    document.querySelectorAll('.mobile-footer-nav .nav-item').forEach(item => {
      item.classList.add('mobile-nav-item');
    });

    // Add safe area padding for newer iPhones
    if (CSS.supports('padding-bottom: env(safe-area-inset-bottom)')) {
      mobileNav.style.paddingBottom = 'calc(8px + env(safe-area-inset-bottom))';
    }
  }

  setupSwipeGestures() {
    let startX, startY, endX, endY;
    
    document.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      startY = e.touches[0].clientY;
    }, { passive: true });
    
    document.addEventListener('touchend', (e) => {
      if (!startX || !startY) return;
      
      endX = e.changedTouches[0].clientX;
      endY = e.changedTouches[0].clientY;
      
      const diffX = startX - endX;
      const diffY = startY - endY;
      
      // Only handle horizontal swipes
      if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 50) {
        if (diffX > 0) {
          // Swipe left
          this.handleSwipeLeft();
        } else {
          // Swipe right
          this.handleSwipeRight();
        }
      }
      
      startX = startY = endX = endY = null;
    }, { passive: true });
  }

  handleSwipeLeft() {
    // Custom swipe left actions
    console.log('Swipe left detected');
  }

  handleSwipeRight() {
    // Custom swipe right actions
    console.log('Swipe right detected');
  }

  optimizeViewport() {
    // Prevent zoom on input focus (iOS)
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
      if (input.style.fontSize < '16px') {
        input.style.fontSize = '16px';
      }
    });

    // Handle orientation change
    window.addEventListener('orientationchange', () => {
      setTimeout(() => {
        window.scrollTo(0, 1);
      }, 500);
    });
  }

  // ========================================================================
  // PERFORMANCE OPTIMIZATIONS
  // ========================================================================
  
  setupPerformanceOptimizations() {
    // Lazy load images
    this.setupLazyLoading();
    
    // Optimize animations
    this.optimizeAnimations();
    
    // Setup resource preloading
    this.preloadCriticalResources();
    
    // Monitor performance
    this.monitorPerformance();
  }

  setupLazyLoading() {
    if ('IntersectionObserver' in window) {
      const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const img = entry.target;
            if (img.dataset.src) {
              img.src = img.dataset.src;
              img.classList.add('fade-in');
              imageObserver.unobserve(img);
            }
          }
        });
      });

      document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
      });
    }
  }

  optimizeAnimations() {
    // Reduce animations for users who prefer reduced motion
    const mediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
    
    if (mediaQuery.matches) {
      document.body.classList.add('reduce-motion');
    }
    
    mediaQuery.addListener((mq) => {
      if (mq.matches) {
        document.body.classList.add('reduce-motion');
      } else {
        document.body.classList.remove('reduce-motion');
      }
    });
  }

  preloadCriticalResources() {
    // Preload critical CSS
    const criticalCSS = [
      '/assets/css/style.css',
      '/assets/css/optimization.css'
    ];
    
    criticalCSS.forEach(href => {
      const link = document.createElement('link');
      link.rel = 'preload';
      link.as = 'style';
      link.href = href;
      document.head.appendChild(link);
    });
  }

  monitorPerformance() {
    // Monitor Core Web Vitals
    if ('PerformanceObserver' in window) {
      // LCP (Largest Contentful Paint)
      new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          console.log('LCP:', entry.startTime);
        }
      }).observe({ entryTypes: ['largest-contentful-paint'] });

      // FID (First Input Delay)
      new PerformanceObserver((list) => {
        for (const entry of list.getEntries()) {
          console.log('FID:', entry.processingStart - entry.startTime);
        }
      }).observe({ entryTypes: ['first-input'] });
    }
  }

  // ========================================================================
  // INTERACTION ENHANCEMENTS
  // ========================================================================
  
  setupInteractionEnhancements() {
    // Enhanced button interactions
    this.enhanceButtons();
    
    // Enhanced form interactions
    this.enhanceForms();
    
    // Add loading states
    this.setupLoadingStates();
    
    // Enhanced tooltips
    this.setupTooltips();
  }

  enhanceButtons() {
    document.querySelectorAll('.btn').forEach(btn => {
      btn.classList.add('btn-enhanced');
      
      // Add click animation
      btn.addEventListener('click', this.createRippleEffect.bind(this));
      
      // Add random enhancement classes
      const enhancements = ['btn-glow', 'btn-wiggle', 'btn-bounce'];
      if (Math.random() > 0.7) {
        btn.classList.add(enhancements[Math.floor(Math.random() * enhancements.length)]);
      }
    });
  }

  createRippleEffect(e) {
    const button = e.currentTarget;
    const ripple = document.createElement('span');
    const rect = button.getBoundingClientRect();
    const size = Math.max(rect.width, rect.height);
    const x = e.clientX - rect.left - size / 2;
    const y = e.clientY - rect.top - size / 2;
    
    ripple.style.width = ripple.style.height = size + 'px';
    ripple.style.left = x + 'px';
    ripple.style.top = y + 'px';
    ripple.classList.add('ripple');
    
    button.appendChild(ripple);
    
    setTimeout(() => {
      ripple.remove();
    }, 600);
  }

  enhanceForms() {
    document.querySelectorAll('.form-group').forEach(group => {
      group.classList.add('form-group-enhanced');
      
      const input = group.querySelector('input, select, textarea');
      const label = group.querySelector('label');
      
      if (input && label) {
        input.classList.add('form-control-enhanced');
        label.classList.add('form-label-enhanced');
        
        // Enhanced focus/blur effects
        input.addEventListener('focus', () => {
          group.classList.add('focused');
        });
        
        input.addEventListener('blur', () => {
          group.classList.remove('focused');
        });
      }
    });
  }

  setupLoadingStates() {
    // Add loading states to forms and buttons
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', (e) => {
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
          submitBtn.classList.add('loading');
          submitBtn.innerHTML = '<div class="loading-enhanced"></div> Loading...';
        }
      });
    });
  }

  setupTooltips() {
    document.querySelectorAll('[data-tooltip]').forEach(el => {
      el.classList.add('tooltip-enhanced');
    });
  }

  // ========================================================================
  // ACCESSIBILITY FEATURES
  // ========================================================================
  
  setupAccessibilityFeatures() {
    // Enhanced keyboard navigation
    this.enhanceKeyboardNavigation();
    
    // Screen reader improvements
    this.improveScreenReaderSupport();
    
    // Focus management
    this.manageFocus();
  }

  enhanceKeyboardNavigation() {
    // Add keyboard support for custom interactive elements
    document.querySelectorAll('.nav-item, .btn, .mobile-nav-item').forEach(el => {
      el.setAttribute('tabindex', '0');
      
      el.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          el.click();
        }
      });
    });
  }

  improveScreenReaderSupport() {
    // Add ARIA labels where missing
    document.querySelectorAll('.btn').forEach(btn => {
      if (!btn.getAttribute('aria-label') && !btn.textContent.trim()) {
        btn.setAttribute('aria-label', 'Interactive button');
      }
    });
    
    // Add live region for dynamic content
    const liveRegion = document.createElement('div');
    liveRegion.setAttribute('aria-live', 'polite');
    liveRegion.setAttribute('aria-atomic', 'true');
    liveRegion.style.position = 'absolute';
    liveRegion.style.left = '-10000px';
    liveRegion.id = 'live-region';
    document.body.appendChild(liveRegion);
  }

  manageFocus() {
    // Skip to main content link
    const skipLink = document.createElement('a');
    skipLink.href = '#main-content';
    skipLink.textContent = 'Skip to main content';
    skipLink.classList.add('skip-link');
    skipLink.style.position = 'absolute';
    skipLink.style.top = '-40px';
    skipLink.style.left = '6px';
    skipLink.style.zIndex = '10000';
    skipLink.style.background = 'var(--primary-gold)';
    skipLink.style.color = '#000';
    skipLink.style.padding = '8px';
    skipLink.style.textDecoration = 'none';
    skipLink.style.borderRadius = '4px';
    
    skipLink.addEventListener('focus', () => {
      skipLink.style.top = '6px';
    });
    
    skipLink.addEventListener('blur', () => {
      skipLink.style.top = '-40px';
    });
    
    document.body.insertBefore(skipLink, document.body.firstChild);
  }

  // ========================================================================
  // ANIMATION QUEUE SYSTEM
  // ========================================================================
  
  setupAnimationQueue() {
    this.animationQueue = [];
    this.isAnimating = false;
  }

  addToAnimationQueue(element, animation, delay = 0) {
    this.animationQueue.push({ element, animation, delay });
    this.processAnimationQueue();
  }

  processAnimationQueue() {
    if (this.isAnimating || this.animationQueue.length === 0) return;
    
    this.isAnimating = true;
    const { element, animation, delay } = this.animationQueue.shift();
    
    setTimeout(() => {
      element.style.animation = animation;
      
      element.addEventListener('animationend', () => {
        this.isAnimating = false;
        this.processAnimationQueue();
      }, { once: true });
    }, delay);
  }

  // ========================================================================
  // UTILITY METHODS
  // ========================================================================
  
  announceToScreenReader(message) {
    const liveRegion = document.getElementById('live-region');
    if (liveRegion) {
      liveRegion.textContent = message;
      setTimeout(() => {
        liveRegion.textContent = '';
      }, 1000);
    }
  }

  debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  throttle(func, limit) {
    let inThrottle;
    return function() {
      const args = arguments;
      const context = this;
      if (!inThrottle) {
        func.apply(context, args);
        inThrottle = true;
        setTimeout(() => inThrottle = false, limit);
      }
    };
  }
}

// ========================================================================
// ENHANCED CSS ADDITIONS
// ========================================================================

// Add dynamic CSS for enhanced effects
const dynamicStyles = `
  .touching {
    transform: scale(0.95);
    opacity: 0.8;
  }
  
  .ripple {
    position: absolute;
    border-radius: 50%;
    transform: scale(0);
    animation: ripple-animation 0.6s linear;
    background-color: rgba(255, 255, 255, 0.6);
    pointer-events: none;
  }
  
  @keyframes ripple-animation {
    to {
      transform: scale(4);
      opacity: 0;
    }
  }
  
  .fade-in {
    opacity: 0;
    animation: fadeIn 0.6s ease-out forwards;
  }
  
  @keyframes fadeIn {
    to {
      opacity: 1;
    }
  }
  
  .reduce-motion * {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
  
  .loading {
    pointer-events: none;
    opacity: 0.6;
  }
`;

// Inject dynamic styles
const styleSheet = document.createElement('style');
styleSheet.textContent = dynamicStyles;
document.head.appendChild(styleSheet);

// ========================================================================
// INITIALIZATION
// ========================================================================

// Initialize when DOM is ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => {
    window.hokirajaOptimizer = new HokirajaOptimizer();
  });
} else {
  window.hokirajaOptimizer = new HokirajaOptimizer();
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
  module.exports = HokirajaOptimizer;
}