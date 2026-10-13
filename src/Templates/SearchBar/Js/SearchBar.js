class WebshopSearchBar {
  static initialize() {
    document.querySelectorAll('[data-webshop-search]:not([data-webshop-search-ready])').forEach((root) => {
      root.dataset.webshopSearchReady = 'true';
      new WebshopSearchBar(root);
    });
  }

  constructor(root) {
    this.root = root;
    this.trigger = root.querySelector('[data-webshop-search-trigger]');
    this.popover = root.querySelector('.webshop-search__popover');
    this.form = root.querySelector('[data-webshop-search-form]');
    this.input = root.querySelector('[data-webshop-search-input]');
    this.results = root.querySelector('[data-webshop-search-results]');
    this.backdrop = document.createElement('div');
    this.backdrop.className = 'webshop-search__backdrop';
    this.backdrop.setAttribute('aria-hidden', 'true');
    this.timer = null;

    this.backdrop.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();
      this.close();
      this.trigger.focus();
    });
    this.trigger.addEventListener('click', () => this.open());
    this.popover.querySelector('[data-webshop-search-close]').addEventListener('click', () => {
      this.close();
      this.trigger.focus();
    });
    this.input.addEventListener('input', () => this.onInput());
    this.form.addEventListener('submit', (event) => {
      event.preventDefault();
      event.stopPropagation();
      window.clearTimeout(this.timer);
      this.search();
    });
    document.addEventListener('click', (event) => {
      if (!this.contains(event.target)) this.close();
    });
    window.addEventListener('scroll', () => this.position(), {passive: true});
    window.addEventListener('resize', () => this.position());
  }

  contains(element) {
    return this.root.contains(element) || this.popover.contains(element);
  }

  open() {
    if (this.root.classList.contains('is-open')) {
      this.input.focus();
      return;
    }
    document.body.appendChild(this.backdrop);
    document.body.appendChild(this.popover);
    this.root.classList.add('is-open');
    this.popover.classList.add('is-open');
    this.position();
    this.trigger.setAttribute('aria-expanded', 'true');
    this.popover.setAttribute('aria-hidden', 'false');
    this.input.focus();
    if (Array.from(this.input.value.trim()).length >= 3) this.onInput();
  }

  close() {
    window.clearTimeout(this.timer);
    this.backdrop.remove();
    this.root.classList.remove('is-open');
    this.popover.classList.remove('is-open');
    this.popover.style.removeProperty('--webshop-search-top');
    this.trigger.setAttribute('aria-expanded', 'false');
    this.popover.setAttribute('aria-hidden', 'true');
    if (this.popover.parentElement !== this.root) this.root.appendChild(this.popover);
  }

  position() {
    if (!this.root.classList.contains('is-open')) return;
    const bounds = this.trigger.getBoundingClientRect();
    this.popover.style.setProperty('--webshop-search-top', Math.max(8, bounds.bottom + 8) + 'px');
  }

  onInput() {
    window.clearTimeout(this.timer);
    const query = this.input.value.trim();
    if (Array.from(query).length < 3) {
      this.showMessage(this.root.dataset.webshopSearchHint);
      return;
    }
    this.showMessage(this.root.dataset.webshopSearchLoading);
    this.timer = window.setTimeout(() => this.search(), 500);
  }

  search() {
    const query = this.input.value.trim();
    if (Array.from(query).length < 3) {
      this.showMessage(this.root.dataset.webshopSearchHint);
      return;
    }

    const ajax = new Ajax(this.form);
    ajax.setCallback((response) => {
      if ((response.query || '') !== this.input.value.trim()) {
        this.showMessage(Array.from(this.input.value.trim()).length < 3
          ? this.root.dataset.webshopSearchHint
          : this.root.dataset.webshopSearchLoading);
      }
    });
    ajax.go();
  }

  showMessage(message) {
    const paragraph = document.createElement('p');
    paragraph.className = 'webshop-search__message';
    paragraph.textContent = message || '';
    this.results.replaceChildren(paragraph);
  }
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', () => WebshopSearchBar.initialize(), {once: true});
} else {
  WebshopSearchBar.initialize();
}
document.addEventListener('fg:ajax-success', () => WebshopSearchBar.initialize());
