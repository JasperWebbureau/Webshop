(() => {
  class ProductMainGroupNavigation {
    constructor(root) {
      this.active = null;
      this.hover = window.matchMedia('(any-hover: hover)');

      root.querySelectorAll('.webshop-category-navigation__item').forEach((item) => {
        const entry = {
          item,
          toggle: item.querySelector('.webshop-category-navigation__toggle'),
          dropdown: item.querySelector('.webshop-category-navigation__dropdown'),
        };

        entry.toggle.addEventListener('click', () => {
          if (this.active === entry && !this.openedByHover) this.close();
          else this.open(entry);
        });
        item.addEventListener('pointerenter', (event) => {
          if (event.pointerType !== 'mouse' || !this.hover.matches || this.active === entry) return;
          if (this.active?.dropdown.contains(document.activeElement)) return;
          this.open(entry, true);
        });
        item.addEventListener('pointerleave', () => {
          if (this.active === entry && this.openedByHover && !item.contains(document.activeElement)) this.close();
        });
        item.addEventListener('focusout', (event) => {
          if (this.active === entry && !item.contains(event.relatedTarget)) this.close();
        });
      });

      document.addEventListener('pointerdown', (event) => {
        if (this.active && !this.active.item.contains(event.target)) this.close();
      });
      document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !this.active) return;
        const { item, toggle } = this.active;
        if (item.contains(document.activeElement)) {
          event.preventDefault();
          toggle.focus();
        }
        this.close();
      });
      window.addEventListener('resize', () => this.positionDropdown());
    }

    open(entry, byHover = false) {
      if (this.active !== entry) this.close();
      this.active = entry;
      this.openedByHover = byHover;
      entry.dropdown.hidden = false;
      entry.toggle.setAttribute('aria-expanded', 'true');
      this.positionDropdown();
    }

    close() {
      if (!this.active) return;
      const { toggle, dropdown } = this.active;
      this.active = null;
      toggle.setAttribute('aria-expanded', 'false');
      dropdown.hidden = true;
    }

    positionDropdown() {
      if (!this.active) return;
      const { dropdown } = this.active;
      const property = '--webshop-category-navigation-offset';
      dropdown.style.removeProperty(property);
      const bounds = dropdown.getBoundingClientRect();
      const viewport = document.documentElement.clientWidth;
      const offset = Math.max(0, 12 - bounds.left) - Math.max(0, bounds.right - viewport + 12);
      if (offset) dropdown.style.setProperty(property, `${offset}px`);
    }
  }

  const initialize = () => {
    document.querySelectorAll('[data-webshop-category-navigation]:not([data-category-navigation-ready])').forEach((root) => {
      root.dataset.categoryNavigationReady = 'true';
      new ProductMainGroupNavigation(root);
    });
  };

  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initialize, {once: true});
  else initialize();
  document.addEventListener('fg:ajax-success', initialize);
})();
