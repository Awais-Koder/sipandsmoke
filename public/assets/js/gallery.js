/*!
 * Sip & Smoke — gallery page
 * Paginates the full photograph set, opens a keyboard/swipe lightbox and runs
 * the header + mobile navigation. No framework or vendor dependency.
 */
(function () {
  'use strict';

  var $ = function (selector, scope) {
    return (scope || document).querySelector(selector);
  };

  var $$ = function (selector, scope) {
    return Array.prototype.slice.call((scope || document).querySelectorAll(selector));
  };

  var dialog = $('#gallery-dialog');
  var grid = $('[data-gallery-grid]');
  var items = grid ? $$('[data-gallery-item]', grid) : [];
  var pagination = $('[data-pagination]');
  var status = $('[data-gallery-status]');
  var mobileNav = $('#mobile-nav');
  var navToggle = $('[data-nav-toggle]');

  var perPage = 24;
  var pages = Math.max(1, Math.ceil(items.length / perPage));
  var currentPage = 1;
  var currentIndex = 0;

  function pad(value) {
    return value < 10 ? '0' + value : String(value);
  }

  function allowsMotion() {
    return !window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  function syncLock() {
    var open = !!((dialog && dialog.open) || (mobileNav && mobileNav.open));
    document.body.classList.toggle('is-locked', open);
  }

  function pageFromHash() {
    var match = window.location.hash.match(/^#page-(\d+)$/);
    var page = match ? parseInt(match[1], 10) : 1;
    return Math.min(pages, Math.max(1, page));
  }

  function pageOfIndex(index) {
    return Math.floor(index / perPage) + 1;
  }

  function renderPage(page, shouldScroll) {
    currentPage = Math.min(pages, Math.max(1, page || 1));

    var start = (currentPage - 1) * perPage;
    var end = Math.min(start + perPage, items.length);

    items.forEach(function (item, index) {
      var figure = item.closest('.gallery-item');
      if (figure) figure.hidden = index < start || index >= end;
    });

    $$('[data-page-number]', pagination).forEach(function (button) {
      if (parseInt(button.getAttribute('data-page-number'), 10) === currentPage) {
        button.setAttribute('aria-current', 'page');
      } else {
        button.removeAttribute('aria-current');
      }
    });

    var previous = $('[data-page-prev]', pagination);
    var following = $('[data-page-next]', pagination);

    if (previous) previous.disabled = currentPage === 1;
    if (following) following.disabled = currentPage === pages;

    if (status) {
      status.textContent =
        'Page ' + currentPage + ' of ' + pages +
        ' · showing ' + (start + 1) + '–' + end +
        ' of ' + items.length + ' photographs';
    }

    if (window.history && window.history.replaceState) {
      window.history.replaceState(null, '', '#page-' + currentPage);
    }

    if (shouldScroll) {
      var anchor = $('.gallery-page__toolbar');
      var header = $('.site-header');

      if (anchor) {
        var offset = header ? header.offsetHeight + 20 : 90;
        var top = anchor.getBoundingClientRect().top + window.pageYOffset - offset;

        window.scrollTo({
          top: Math.max(0, top),
          behavior: allowsMotion() ? 'smooth' : 'auto',
        });
      }
    }
  }

  function buildPagination() {
    if (!pagination) return;

    var previous = document.createElement('button');
    previous.type = 'button';
    previous.className = 'pagination__edge';
    previous.setAttribute('data-page-prev', '');
    previous.setAttribute('aria-label', 'Previous page');
    previous.textContent = '←';

    var numbers = document.createElement('div');
    numbers.className = 'pagination__pages';

    for (var page = 1; page <= pages; page += 1) {
      var button = document.createElement('button');
      button.type = 'button';
      button.className = 'pagination__page';
      button.setAttribute('data-page-number', String(page));
      button.setAttribute('aria-label', 'Page ' + page);
      button.textContent = pad(page);
      numbers.appendChild(button);
    }

    var following = document.createElement('button');
    following.type = 'button';
    following.className = 'pagination__edge';
    following.setAttribute('data-page-next', '');
    following.setAttribute('aria-label', 'Next page');
    following.textContent = '→';

    pagination.appendChild(previous);
    pagination.appendChild(numbers);
    pagination.appendChild(following);

    pagination.addEventListener('click', function (event) {
      var button = event.target.closest('button');
      if (!button || button.disabled) return;

      if (button.hasAttribute('data-page-prev')) {
        renderPage(currentPage - 1, true);
      } else if (button.hasAttribute('data-page-next')) {
        renderPage(currentPage + 1, true);
      } else if (button.hasAttribute('data-page-number')) {
        renderPage(parseInt(button.getAttribute('data-page-number'), 10), true);
      }
    });
  }

  // Pagination -------------------------------------------------------------
  if (items.length) {
    buildPagination();
    renderPage(pageFromHash(), false);

    window.addEventListener('hashchange', function () {
      if (dialog && dialog.open) return;
      renderPage(pageFromHash(), true);
    });
  }

  // Lightbox ---------------------------------------------------------------
  if (dialog && items.length) {
    var image = $('[data-gallery-img]', dialog);
    var counter = $('[data-gallery-count]', dialog);
    var caption = $('[data-gallery-caption]', dialog);
    var stage = $('[data-gallery-stage]', dialog);
    var swipe = null;

    var show = function (next) {
      currentIndex = (next + items.length) % items.length;

      var item = items[currentIndex];
      image.src = item.getAttribute('data-full') || '';
      image.alt = item.getAttribute('data-alt') || '';

      if (caption) caption.textContent = item.getAttribute('data-caption') || '';
      if (counter) counter.textContent = pad(currentIndex + 1) + ' / ' + items.length;
    };

    var open = function (index) {
      if (!items[index]) return;
      show(index);

      if (typeof dialog.showModal === 'function') {
        if (!dialog.open) dialog.showModal();
      } else {
        dialog.setAttribute('open', '');
      }

      syncLock();
    };

    items.forEach(function (item, index) {
      item.addEventListener('click', function () {
        open(index);
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
        show(currentIndex - 1);
      });
    }

    if (following) {
      following.addEventListener('click', function () {
        show(currentIndex + 1);
      });
    }

    dialog.addEventListener('click', function (event) {
      if (event.target === dialog && dialog.open) dialog.close();
    });

    dialog.addEventListener('keydown', function (event) {
      if (event.key === 'ArrowLeft') {
        event.preventDefault();
        show(currentIndex - 1);
      } else if (event.key === 'ArrowRight') {
        event.preventDefault();
        show(currentIndex + 1);
      }
    });

    dialog.addEventListener('close', function () {
      syncLock();

      var page = pageOfIndex(currentIndex);
      if (page !== currentPage) renderPage(page, false);

      var item = items[currentIndex];
      window.setTimeout(function () {
        if (item) item.focus({ preventScroll: true });
      }, 30);
    });

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
          show(currentIndex + (dx < 0 ? 1 : -1));
        }
      });

      stage.addEventListener('pointercancel', function () {
        swipe = null;
      });
    }

    syncLock();
  }

  // Header + mobile navigation --------------------------------------------
  function updateHeader() {
    var header = $('.site-header');
    if (header) header.classList.toggle('is-scrolled', window.scrollY > 18);
  }

  if (navToggle && mobileNav) {
    var openMobileNav = function () {
      if (typeof mobileNav.showModal === 'function') {
        if (!mobileNav.open) mobileNav.showModal();
      } else {
        mobileNav.setAttribute('open', '');
      }

      navToggle.setAttribute('aria-expanded', 'true');
      syncLock();
    };

    var closeMobileNav = function () {
      if (mobileNav.open) {
        mobileNav.close();
      } else {
        mobileNav.removeAttribute('open');
      }

      navToggle.setAttribute('aria-expanded', 'false');
      syncLock();
    };

    navToggle.addEventListener('click', openMobileNav);

    $$('[data-nav-close]').forEach(function (button) {
      button.addEventListener('click', closeMobileNav);
    });

    $$('[data-nav-link]').forEach(function (link) {
      link.addEventListener('click', function () {
        if (mobileNav.open) closeMobileNav();
      });
    });

    mobileNav.addEventListener('close', function () {
      navToggle.setAttribute('aria-expanded', 'false');
      syncLock();
    });

    mobileNav.addEventListener('click', function (event) {
      if (event.target === mobileNav) closeMobileNav();
    });
  }

  window.addEventListener('scroll', updateHeader, { passive: true });
  updateHeader();
})();
