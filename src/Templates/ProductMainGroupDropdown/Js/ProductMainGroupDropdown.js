(() => {
  class ProductMainGroupDropdown {
    constructor() {
      this.mobile = window.matchMedia('(max-width: 992px)');
      this.desktopDropdown = null;
      this.desktopOwner = null;
      this.mobileDropdown = null;
      this.hideTimer = null;

      document.addEventListener('pointerover', (event) => this.onPointerOver(event));
      document.addEventListener('pointerout', (event) => this.onPointerOut(event));
      document.addEventListener('focusin', (event) => this.onFocusIn(event));
      document.addEventListener('focusout', () => window.setTimeout(() => this.checkFocus(), 0));
      document.addEventListener('click', (event) => this.onClick(event), true);
      document.addEventListener('keydown', (event) => this.onKeyDown(event));
      window.addEventListener('scroll', () => this.positionDesktop(), {passive: true});
      window.addEventListener('resize', () => this.onResize());
    }

    dropdownForOwner(owner) {
      return owner?.querySelector(':scope > .webshop-product-main-group-dropdown') || null;
    }

    onPointerOver(event) {
      if (this.mobile.matches) return;
      if (this.desktopDropdown?.contains(event.target)) {
        window.clearTimeout(this.hideTimer);
        return;
      }

      const owner = event.target.closest('li');
      if (!owner || owner.closest('.off-canvas')) return;
      const dropdown = this.dropdownForOwner(owner);
      if (!dropdown || (event.relatedTarget && owner.contains(event.relatedTarget))) return;
      this.openDesktop(owner, dropdown);
    }

    onPointerOut(event) {
      return;
      if (!this.desktopDropdown || this.mobile.matches) return;
      if (!this.desktopOwner?.contains(event.target) && !this.desktopDropdown.contains(event.target)) return;
      const next = event.relatedTarget;
      if (next && (this.desktopOwner?.contains(next) || this.desktopDropdown.contains(next))) return;
      this.scheduleDesktopClose();
    }

    onFocusIn(event) {
      if (this.mobile.matches) return;
      const owner = event.target.closest('li');
      const dropdown = owner && !owner.closest('.off-canvas') ? this.dropdownForOwner(owner) : null;
      if (dropdown) {
        this.openDesktop(owner, dropdown);
      } else if (this.desktopDropdown?.contains(event.target)) {
        window.clearTimeout(this.hideTimer);
      }
    }

    checkFocus() {
      if (!this.desktopDropdown || this.mobile.matches) return;
      const focused = document.activeElement;
      if (!this.desktopOwner?.contains(focused) && !this.desktopDropdown.contains(focused)) {
        this.scheduleDesktopClose();
      }
    }

    openDesktop(owner, dropdown) {
      window.clearTimeout(this.hideTimer);
      if (this.desktopDropdown && this.desktopDropdown !== dropdown) this.closeDesktop();
      this.desktopOwner = owner;
      this.desktopDropdown = dropdown;
      if (dropdown.parentElement !== document.body) document.body.appendChild(dropdown);
      this.positionDesktop();
      dropdown.classList.add('is-open');
    }

    positionDesktop() {
      if (!this.desktopDropdown || !this.desktopOwner || this.mobile.matches) return;
      const bottom = this.desktopOwner.getBoundingClientRect().bottom;
      this.desktopDropdown.style.setProperty('--webshop-product-main-group-dropdown-top', Math.max(0, bottom + 8) + 'px');
    }

    scheduleDesktopClose() {
      window.clearTimeout(this.hideTimer);
      this.hideTimer = window.setTimeout(() => this.closeDesktop(), 180);
    }

    closeDesktop() {
      window.clearTimeout(this.hideTimer);
      if (this.desktopDropdown && this.desktopOwner) {
        this.desktopDropdown.classList.remove('is-open');
        this.desktopDropdown.style.removeProperty('--webshop-product-main-group-dropdown-top');
        this.desktopOwner.appendChild(this.desktopDropdown);
      }
      this.desktopDropdown = null;
      this.desktopOwner = null;
    }

    onClick(event) {
      if (!this.mobile.matches) return;
      const canvas = event.target.closest('.off-canvas-menu');
      if (!canvas) return;

      if (event.target.closest('.main-menu-close')) {
        this.resetMobile(canvas);
        return;
      }

      const menuLink = event.target.closest('.nav__content > ul > li > a');
      const menuItem = menuLink?.parentElement;
      const dropdown = menuItem && this.dropdownForOwner(menuItem);
      if (dropdown) {
        event.preventDefault();
        event.stopImmediatePropagation();
        canvas.classList.add('is-webshop-menu-open');
        menuItem.classList.add('is-webshop-active');
        this.mobileDropdown = dropdown;
        dropdown.querySelector('[data-back-to-menu]')?.focus();
        return;
      }

      const backMenu = event.target.closest('[data-back-to-menu]');
      if (backMenu) {
        event.preventDefault();
        this.backToMenu(canvas, backMenu.closest('.webshop-product-main-group-dropdown'));
        return;
      }

      const backGroups = event.target.closest('[data-back-to-main-groups]');
      if (backGroups) {
        event.preventDefault();
        this.backToMainGroups(backGroups.closest('.webshop-product-main-group-dropdown'));
        return;
      }

      const openGroups = event.target.closest('[data-open-product-groups], .webshop-product-main-group-dropdown__main-link');
      const groupDropdown = openGroups?.closest('.webshop-product-main-group-dropdown');
      if (groupDropdown) {
        event.preventDefault();
        event.stopImmediatePropagation();
        this.openGroups(groupDropdown, openGroups.closest('.webshop-product-main-group-dropdown__item'));
      }
    }

    openGroups(dropdown, item) {
      if (!item) return;
      dropdown.querySelectorAll('.is-mobile-active').forEach((active) => active.classList.remove('is-mobile-active'));
      dropdown.querySelectorAll('[data-open-product-groups]').forEach((button) => button.setAttribute('aria-expanded', 'false'));
      item.classList.add('is-mobile-active');
      item.querySelector('[data-open-product-groups]')?.setAttribute('aria-expanded', 'true');
      dropdown.classList.add('is-mobile-groups-open');
      item.querySelector('[data-back-to-main-groups]')?.focus();
    }

    backToMainGroups(dropdown) {
      if (!dropdown) return;
      const active = dropdown.querySelector('.is-mobile-active');
      dropdown.classList.remove('is-mobile-groups-open');
      active?.classList.remove('is-mobile-active');
      active?.querySelector('[data-open-product-groups]')?.setAttribute('aria-expanded', 'false');
      active?.querySelector('.webshop-product-main-group-dropdown__main-link')?.focus();
    }

    backToMenu(canvas, dropdown) {
      if (!dropdown) return;
      if (dropdown.classList.contains('is-mobile-groups-open')) {
        this.backToMainGroups(dropdown);
        return;
      }
      const item = dropdown.closest('li.is-webshop-active');
      item?.classList.remove('is-webshop-active');
      canvas.classList.remove('is-webshop-menu-open');
      this.mobileDropdown = null;
      item?.querySelector(':scope > a')?.focus();
    }

    resetMobile(canvas) {
      canvas.classList.remove('is-webshop-menu-open');
      canvas.querySelectorAll('.is-webshop-active, .is-mobile-active, .is-mobile-groups-open').forEach((element) => {
        element.classList.remove('is-webshop-active', 'is-mobile-active', 'is-mobile-groups-open');
      });
      canvas.querySelectorAll('[data-open-product-groups]').forEach((button) => button.setAttribute('aria-expanded', 'false'));
      this.mobileDropdown = null;
    }

    onKeyDown(event) {
      if (event.key !== 'Escape') return;
      if (this.mobile.matches) {
        const canvas = document.querySelector('.off-canvas-menu.is-webshop-menu-open');
        if (canvas && this.mobileDropdown) {
          event.preventDefault();
          this.backToMenu(canvas, this.mobileDropdown);
        }
      } else if (this.desktopDropdown) {
        this.closeDesktop();
      }
    }

    onResize() {
      if (this.mobile.matches) {
        this.closeDesktop();
      } else {
        document.querySelectorAll('.off-canvas-menu.is-webshop-menu-open').forEach((canvas) => this.resetMobile(canvas));
      }
    }
  }

  const initialize = () => new ProductMainGroupDropdown();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, {once: true});
  } else {
    initialize();
  }
})();
