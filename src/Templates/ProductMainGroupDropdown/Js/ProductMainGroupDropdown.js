(() => {
  class ProductMainGroupDropdown {
    constructor() {
      this.bindNavigation();
      this.positionMenus();
      window.addEventListener('resize', () => this.positionMenus());
      window.addEventListener('scroll', () => this.positionMenus(), {passive: true});
    }

    bindNavigation() {
      document.addEventListener('click', (event) => {
        const openButton = event.target.closest('[data-open-product-groups]');
        if (openButton) {
          this.openGroups(openButton);
          return;
        }

        const backButton = event.target.closest('[data-back-to-main-groups]');
        if (backButton) {
          this.showMainGroups(backButton);
        }
      });

      document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
          return;
        }

        const openMenu = document.querySelector('.webshop-product-main-group-dropdown.is-mobile-groups-open');
        if (openMenu && window.matchMedia('(max-width: 992px)').matches) {
          const backButton = openMenu.querySelector('[data-back-to-main-groups]');
          if (backButton) {
            this.showMainGroups(backButton);
          }
        }
      });
    }

    openGroups(button) {
      if (!window.matchMedia('(max-width: 992px)').matches) {
        return;
      }

      const menu = button.closest('.webshop-product-main-group-dropdown');
      const item = button.closest('.webshop-product-main-group-dropdown__item');
      if (!menu || !item) {
        return;
      }

      menu.querySelectorAll('.webshop-product-main-group-dropdown__item.is-mobile-active').forEach((activeItem) => {
        activeItem.classList.remove('is-mobile-active');
      });
      menu.querySelectorAll('[data-open-product-groups]').forEach((openButton) => {
        openButton.setAttribute('aria-expanded', 'false');
      });

      item.classList.add('is-mobile-active');
      button.setAttribute('aria-expanded', 'true');
      menu.classList.add('is-mobile-groups-open');

      const backButton = item.querySelector('[data-back-to-main-groups]');
      if (backButton) {
        backButton.focus();
      }
    }

    showMainGroups(button) {
      const menu = button.closest('.webshop-product-main-group-dropdown');
      if (!menu) {
        return;
      }

      const activeItem = menu.querySelector('.webshop-product-main-group-dropdown__item.is-mobile-active');
      menu.classList.remove('is-mobile-groups-open');
      menu.querySelectorAll('.webshop-product-main-group-dropdown__item.is-mobile-active').forEach((item) => {
        item.classList.remove('is-mobile-active');
      });
      menu.querySelectorAll('[data-open-product-groups]').forEach((openButton) => {
        openButton.setAttribute('aria-expanded', 'false');
      });

      const openButton = activeItem && activeItem.querySelector('[data-open-product-groups]');
      if (openButton) {
        openButton.focus();
      }
    }

    positionMenus() {
      const desktop = !window.matchMedia('(max-width: 992px)').matches;
      document.querySelectorAll('.webshop-product-main-group-dropdown').forEach((menu) => {
        if (!desktop) {
          menu.style.removeProperty('--webshop-product-main-group-dropdown-top');
          return;
        }

        const navigationItem = menu.parentElement ? menu.parentElement.closest('li') : null;
        const trigger = navigationItem ? navigationItem.querySelector(':scope > a') : null;
        if (!trigger) {
          return;
        }

        const top = Math.max(8, Math.round(trigger.getBoundingClientRect().bottom));
        menu.style.setProperty('--webshop-product-main-group-dropdown-top', `${top}px`);
      });
    }
  }

  const initialize = () => new ProductMainGroupDropdown();
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialize, {once: true});
  } else {
    initialize();
  }
})();
