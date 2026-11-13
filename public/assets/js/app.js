/*!
 * Sip & Smoke — interaction layer
 * GSAP + ScrollTrigger + Lenis, with native fallbacks.
 */
(function () {
  'use strict';

  var $ = function (selector, scope) {
    return (scope || document).querySelector(selector);
  };

  var $$ = function (selector, scope) {
    return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
  };

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  var lenis = null;
  var ticker = null;
  var motionContext = null;
  var uiBound = false;
  var scrollFrame = null;

  var dialogs = {
    mobile: null,
    gallery: null,
  };

  function hasGsap() {
    return typeof window.gsap !== 'undefined' && typeof window.ScrollTrigger !== 'undefined';
  }

  function allowsMotion() {
    return !reduceMotion.matches;
  }

  function isVisible(element) {
    return !!(element && (element.offsetWidth || element.offsetHeight || element.getClientRects().length));
  }

  function isAnyDialogOpen() {
    return !!(
      (dialogs.mobile && dialogs.mobile.open) ||
      (dialogs.gallery && dialogs.gallery.open)
    );
  }

  function syncDialogState() {
    var open = isAnyDialogOpen();
    document.body.classList.toggle('is-locked', open);

    if (!lenis) return;

    if (open) {
      lenis.stop();
    } else {
      lenis.start();
    }
  }

  function scrollToTarget(target) {
    if (!target) return;

    if (lenis) {
      var margin = parseFloat(window.getComputedStyle(target).scrollMarginTop) || 0;
      lenis.scrollTo(target, {
        offset: -Math.max(parseFloat(window.getComputedStyle(document.documentElement).getPropertyValue('--header-h')) || 84, margin),
        duration: 1.05,
      });
      return;
    }

    target.scrollIntoView({
      behavior: allowsMotion() ? 'smooth' : 'auto',
      block: 'start',
    });
  }

  function startSmoothScroll() {
    if (!allowsMotion() || typeof window.Lenis === 'undefined' || !hasGsap()) return;

    lenis = new window.Lenis({
      duration: 1.1,
      smoothWheel: true,
      wheelMultiplier: 0.92,
      touchMultiplier: 1.25,
    });

    lenis.on('scroll', window.ScrollTrigger.update);

    ticker = function (time) {
      lenis.raf(time * 1000);
    };

    window.gsap.ticker.add(ticker);
    window.gsap.ticker.lagSmoothing(0);

    if (isAnyDialogOpen()) lenis.stop();
  }

  function stopSmoothScroll() {
    if (ticker && hasGsap()) {
      window.gsap.ticker.remove(ticker);
      ticker = null;
    }

    if (lenis) {
      lenis.destroy();
      lenis = null;
    }
  }

  function openMobileNav() {
    if (!dialogs.mobile) return;

    if (typeof dialogs.mobile.showModal === 'function') {
      if (!dialogs.mobile.open) dialogs.mobile.showModal();
    } else {
      dialogs.mobile.setAttribute('open', '');
    }

    syncDialogState();
  }

  function closeMobileNav() {
    if (!dialogs.mobile) return;

    if (dialogs.mobile.open) {
      dialogs.mobile.close();
    } else {
      dialogs.mobile.removeAttribute('open');
    }

    syncDialogState();
  }

  function updateHeaderAndNav() {
    var header = $('.site-header');
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 18);

    var navLinks = $$('[data-nav-section]');
    if (!navLinks.length) return;

    var sections = ['home', 'juice-bar', 'about-us', 'gallery', 'contact-us']
      .map(function (id) {
        return document.getElementById(id);
      })
      .filter(Boolean);

    var currentId = 'home';
    var threshold = window.innerHeight * 0.38;

    sections.forEach(function (section) {
      if (section.getBoundingClientRect().top <= threshold) currentId = section.id;
    });

    navLinks.forEach(function (link) {
      if (link.getAttribute('data-nav-section') === currentId) {
        link.setAttribute('aria-current', 'page');
      } else {
        link.removeAttribute('aria-current');
      }
    });
  }

  function requestScrollUpdate() {
    if (scrollFrame) return;

    scrollFrame = window.requestAnimationFrame(function () {
      scrollFrame = null;
      updateHeaderAndNav();
    });
  }

  function applyFilter(filter, animate) {
    var cards = $$('.product-card');
    var count = $('[data-result-count]');
    var buttons = $$('[data-filter]');
    var shown = 0;

    if (!cards.length) return;

    cards.forEach(function (card) {
      var visible = filter === 'all' || card.getAttribute('data-category') === filter;
      card.hidden = !visible;
      if (visible) shown += 1;
    });

    buttons.forEach(function (button) {
      var active = button.getAttribute('data-filter') === filter;
      button.classList.toggle('is-active', active);
      button.setAttribute('aria-pressed', active ? 'true' : 'false');
    });

    if (count) count.textContent = String(shown);

    if (animate && allowsMotion() && hasGsap()) {
      var visibleCards = cards.filter(function (card) {
        return !card.hidden;
      });

      window.gsap.fromTo(
        visibleCards,
        { autoAlpha: 0, y: 12 },
        {
          autoAlpha: 1,
          y: 0,
          duration: 0.34,
          ease: 'power2.out',
          stagger: 0.025,
          overwrite: true,
          clearProps: 'transform',
        }
      );
    }

    if (hasGsap()) {
      window.setTimeout(function () {
        window.ScrollTrigger.refresh();
      }, 40);
    }
  }

  function bindUi() {
    if (uiBound) return;
    uiBound = true;

    dialogs.mobile = $('#mobile-nav');
    dialogs.gallery = $('#gallery-dialog');

    var navToggle = $('[data-nav-toggle]');

    if (navToggle) {
      navToggle.addEventListener('click', function () {
        openMobileNav();
        navToggle.setAttribute('aria-expanded', 'true');
      });
    }

    $$('[data-nav-close]').forEach(function (button) {
      button.addEventListener('click', closeMobileNav);
    });

    $$('[data-nav-link]').forEach(function (link) {
      link.addEventListener('click', function () {
        if (dialogs.mobile && dialogs.mobile.open) closeMobileNav();
      });
    });

    if (dialogs.mobile) {
      dialogs.mobile.addEventListener('close', function () {
        if (navToggle) {
          navToggle.setAttribute('aria-expanded', 'false');
          navToggle.focus({ preventScroll: true });
        }

        syncDialogState();
      });

      dialogs.mobile.addEventListener('click', function (event) {
        if (event.target === dialogs.mobile) closeMobileNav();
      });
    }

    // Close the mobile menu when returning to a desktop layout.
    var desktopQuery = window.matchMedia('(min-width: 1120px)');
    var handleDesktopChange = function (event) {
      if (event.matches && dialogs.mobile && dialogs.mobile.open) closeMobileNav();
    };

    if (typeof desktopQuery.addEventListener === 'function') {
      desktopQuery.addEventListener('change', handleDesktopChange);
    } else if (typeof desktopQuery.addListener === 'function') {
      desktopQuery.addListener(handleDesktopChange);
    }

    // Filter buttons.
    $$('[data-filter]').forEach(function (button) {
      button.addEventListener('click', function () {
        applyFilter(button.getAttribute('data-filter'), true);
      });
    });

    // Delegated same-page navigation so anchors keep working if content changes.
    document.addEventListener('click', function (event) {
      var link = event.target.closest('a[href^="#"]');
      if (!link) return;
      if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

      var hash = link.getAttribute('href');
      if (!hash || hash === '#') return;

      var target = document.querySelector(hash);
      if (!target) return;

      event.preventDefault();

      if (dialogs.mobile && dialogs.mobile.open) closeMobileNav();

      window.requestAnimationFrame(function () {
        window.setTimeout(function () {
          scrollToTarget(target);
        }, 20);
      });

      if (window.history && window.history.pushState) {
        window.history.pushState(null, '', hash);
      }
    });

    window.addEventListener('scroll', requestScrollUpdate, { passive: true });
    window.addEventListener('resize', requestScrollUpdate);

    applyFilter('all', false);
    updateHeaderAndNav();
    initMenuDeck();
    initGallery();
  }

  function initMenuDeck() {
    var deck = $('[data-menu-deck]');
    if (!deck || deck.getAttribute('data-ready') === 'true') return;
    deck.setAttribute('data-ready', 'true');

    var cards = $$('.menu-card', deck);
    var current = $('[data-deck-current]');
    if (!cards.length) return;

    var order = cards.slice();
    var drag = null;
    var busy = false;
    var threshold = 84;

    function depth(index) {
      return {
        y: index * 11,
        scale: Math.max(0.82, 1 - index * 0.035),
        visible: index < 3,
      };
    }

    function baseTransform(index) {
      var d = depth(index);
      return 'translate3d(0,' + d.y + 'px,0) scale(' + d.scale + ')';
    }

    function paint() {
      order.forEach(function (card, index) {
        var d = depth(index);
        card.style.transition = 'none';
        card.style.transform = baseTransform(index);
        card.style.opacity = d.visible ? '1' : '0';
        card.style.zIndex = String(cards.length - index);
        card.style.pointerEvents = index === 0 ? 'auto' : 'none';
        card.setAttribute('aria-hidden', index === 0 ? 'false' : 'true');
      });

      if (current) {
        var page = order[0].getAttribute('data-page') || '1';
        current.textContent = page.length < 2 ? '0' + page : page;
      }

      window.requestAnimationFrame(function () {
        order.forEach(function (card) {
          card.style.transition = '';
        });
      });
    }

    function dragTop(dx, dy) {
      var card = order[0];
      if (!card) return;

      card.style.transition = 'none';
      card.style.transform =
        'translate3d(' + dx + 'px,' + dy + 'px,0) rotate(' + dx * 0.045 + 'deg)';

      var under = order[1];
      if (!under) return;

      var progress = Math.min(1, Math.abs(dx) / threshold);
      under.style.transition = 'none';
      under.style.transform =
        'translate3d(0,' + 11 * (1 - progress) + 'px,0) scale(' + (0.965 + 0.035 * progress) + ')';
      under.style.opacity = '1';
    }

    function settle() {
      if (busy) return;
      busy = true;

      var duration = allowsMotion() ? 480 : 0;
      var card = order[0];

      if (card) {
        card.style.transition = 'transform ' + duration + 'ms var(--ease-out)';
        card.style.transform = baseTransform(0);
      }

      if (order[1]) {
        order[1].style.transition = 'transform ' + duration + 'ms var(--ease-out)';
        order[1].style.transform = baseTransform(1);
      }

      window.setTimeout(function () {
        paint();
        busy = false;
      }, duration + 20);
    }

    function advance(direction) {
      if (busy) return;
      busy = true;

      var card = order[0];
      var under = order[1];
      var duration = allowsMotion() ? 420 : 0;

      // Going back brings the last card up from the bottom of the stack; show
      // it under the current page before the exit animation so the transition
      // reads in the same direction as the button.
      if (direction < 0 && order.length > 1) {
        under = order.pop();
        order.splice(1, 0, under);
        under.style.transition = 'none';
        under.style.transform = baseTransform(1);
        under.style.opacity = '1';
        under.style.zIndex = String(cards.length - 1);
        under.style.pointerEvents = 'none';
        under.setAttribute('aria-hidden', 'true');
      }

      card.style.transition =
        'transform ' + duration + 'ms cubic-bezier(0.22, 1, 0.36, 1), opacity ' + duration + 'ms linear';
      card.style.transform =
        'translate3d(' + direction * (deck.clientWidth * 1.05) + 'px, 34px, 0) rotate(' + direction * 14 + 'deg)';
      card.style.opacity = '0';

      if (under) {
        under.style.transition = 'transform ' + duration + 'ms cubic-bezier(0.22, 1, 0.36, 1)';
        under.style.transform = baseTransform(0);
        under.style.opacity = '1';
      }

      window.setTimeout(function () {
        if (direction > 0) {
          order.push(order.shift());
        } else {
          order.shift();
          order.push(card);
        }

        paint();
        busy = false;
      }, duration + 20);
    }

    deck.addEventListener('pointerdown', function (event) {
      if (busy || event.button !== 0) return;

      var card = event.target.closest('.menu-card');
      if (!card || card !== order[0]) return;

      drag = {
        id: event.pointerId,
        x: event.clientX,
        y: event.clientY,
        lastX: event.clientX,
        lastT: performance.now(),
        vx: 0,
        moved: false,
      };

      card.style.transition = 'none';

      if (deck.setPointerCapture) {
        try {
          deck.setPointerCapture(event.pointerId);
        } catch (error) {
          /* pointer capture is best-effort */
        }
      }
    });

    deck.addEventListener('pointermove', function (event) {
      if (!drag || event.pointerId !== drag.id) return;

      var dx = event.clientX - drag.x;
      var dy = event.clientY - drag.y;
      var now = performance.now();

      if (Math.abs(dx) > 5 || Math.abs(dy) > 5) drag.moved = true;

      drag.vx = (event.clientX - drag.lastX) / Math.max(16, now - drag.lastT);
      drag.lastX = event.clientX;
      drag.lastT = now;

      event.preventDefault();
      dragTop(dx, dy * 0.5);
    });

    function endDrag(event) {
      if (!drag || event.pointerId !== drag.id) return;

      var gesture = drag;
      drag = null;

      var dx = event.clientX - gesture.x;

      if (!gesture.moved) {
        if (order[0]) window.location.href = ((window.SIP_ROUTES && window.SIP_ROUTES.menu) || '/menu') + '#page-' + (order[0].getAttribute('data-page') || '1');
        return;
      }

      if (Math.abs(dx) > threshold || Math.abs(gesture.vx) > 0.55) {
        advance(dx < 0 ? -1 : 1);
      } else {
        settle();
      }
    }

    deck.addEventListener('pointerup', endDrag);
    deck.addEventListener('pointercancel', function (event) {
      if (!drag || event.pointerId !== drag.id) return;
      drag = null;
      settle();
    });

    deck.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowLeft' || event.key === 'ArrowRight') {
        event.preventDefault();
        advance(event.key === 'ArrowRight' ? 1 : -1);
      } else if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        if (order[0]) window.location.href = ((window.SIP_ROUTES && window.SIP_ROUTES.menu) || '/menu') + '#page-' + (order[0].getAttribute('data-page') || '1');
      }
    });

    $$('[data-deck-dir]').forEach(function (button) {
      button.addEventListener('click', function () {
        advance(parseInt(button.getAttribute('data-deck-dir'), 10) > 0 ? 1 : -1);
      });
    });

    paint();
  }

  function initGallery() {
    var dialog = $('#gallery-dialog');
    if (!dialog) return;

    var items = $$('[data-gallery-item]');
    if (!items.length) return;

    var image = $('[data-gallery-img]', dialog);
    var count = $('[data-gallery-count]', dialog);
    var caption = $('[data-gallery-caption]', dialog);
    var index = 0;
    var swipe = null;

    function show(next) {
      index = (next + items.length) % items.length;

      var item = items[index];
      image.src = item.getAttribute('data-full') || '';
      image.alt = item.getAttribute('data-alt') || '';

      if (caption) caption.textContent = item.getAttribute('data-caption') || '';
      if (count) {
        count.textContent = String(index + 1).padStart(2, '0') + ' / ' + items.length;
      }
    }

    function open(next) {
      if (!items[next]) return;
      show(next);

      if (typeof dialog.showModal === 'function') {
        if (!dialog.open) dialog.showModal();
      } else {
        dialog.setAttribute('open', '');
      }

      syncDialogState();
    }

    items.forEach(function (item, itemIndex) {
      item.addEventListener('click', function () {
        open(itemIndex);
      });
    });

    $$('[data-gallery-close]', dialog).forEach(function (button) {
      button.addEventListener('click', function () {
        if (dialog.open) dialog.close();
      });
    });

    var previous = $('[data-gallery-prev]', dialog);
    var following = $('[data-gallery-next]', dialog);

    if (previous) {
      previous.addEventListener('click', function () {
        show(index - 1);
      });
    }

    if (following) {
      following.addEventListener('click', function () {
        show(index + 1);
      });
    }

    dialog.addEventListener('close', function () {
      syncDialogState();
      var last = items[index];
      if (last && isVisible(last)) last.focus({ preventScroll: true });
    });

    dialog.addEventListener('click', function (event) {
      if (event.target === dialog && dialog.open) dialog.close();
    });

    dialog.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        show(index - 1);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        show(index + 1);
      }
    });

    var stage = $('[data-gallery-stage]', dialog);

    if (stage) {
      stage.addEventListener('pointerdown', function (event) {
        if (event.button !== 0) return;
        swipe = { id: event.pointerId, x: event.clientX, y: event.clientY };
      });

      stage.addEventListener('pointerup', function (event) {
        if (!swipe || swipe.id !== event.pointerId) return;

        var dx = event.clientX - swipe.x;
        var dy = event.clientY - swipe.y;
        swipe = null;

        if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy)) {
          show(index + (dx < 0 ? 1 : -1));
        }
      });

      stage.addEventListener('pointercancel', function () {
        swipe = null;
      });
    }
  }

  function initMotion() {
    var gsap = window.gsap;
    var ScrollTrigger = window.ScrollTrigger;

    if (motionContext) {
      motionContext.revert();
      motionContext = null;
    }

    stopSmoothScroll();

    if (!gsap || !ScrollTrigger || !allowsMotion()) {
      updateHeaderAndNav();
      return;
    }

    gsap.registerPlugin(ScrollTrigger);
    ScrollTrigger.config({ ignoreMobileResize: true });
    startSmoothScroll();

    motionContext = gsap.context(function () {
      var heroTimeline = gsap.timeline({
        defaults: { ease: 'power3.out' },
      });

      heroTimeline
        .from('.hero-eyebrow', { autoAlpha: 0, y: 12, duration: 0.55 })
        .from(
          '.hero-title__line > span',
          { autoAlpha: 0, yPercent: 110, duration: 0.9, stagger: 0.1 },
          '-=0.2'
        )
        .from(
          '[data-hero-item]',
          { autoAlpha: 0, y: 16, duration: 0.68, stagger: 0.075 },
          '-=0.55'
        );

      ScrollTrigger.batch('[data-reveal]', {
        start: 'top 88%',
        once: true,
        onEnter: function (batch) {
          gsap.from(batch, {
            autoAlpha: 0,
            y: 24,
            duration: 0.78,
            ease: 'power3.out',
            stagger: 0.055,
            overwrite: true,
          });
        },
      });

      gsap.fromTo(
        '.hero-bg',
        { scale: 1.08, yPercent: -1.5 },
        {
          yPercent: 2,
          ease: 'none',
          scrollTrigger: {
            trigger: '.hero',
            start: 'top top',
            end: 'bottom top',
            scrub: true,
          },
        }
      );
    });

    if (document.fonts && document.fonts.ready) {
      document.fonts.ready.then(function () {
        ScrollTrigger.refresh();
      });
    }

    updateHeaderAndNav();
  }

  function init() {
    bindUi();
    initMotion();

    if (reduceMotion.addEventListener) {
      reduceMotion.addEventListener('change', function () {
        initMotion();
      });
    } else if (reduceMotion.addListener) {
      reduceMotion.addListener(function () {
        initMotion();
      });
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else {
    init();
  }

  // Kept for a future Livewire integration and manual re-initialisation.
  window.SipSmoke = {
    refresh: function () {
      bindUi();
      initMotion();
      applyFilter('all', false);
    },
  };
})();
