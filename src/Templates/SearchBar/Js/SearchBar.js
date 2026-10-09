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
    this.timer = null;

    this.trigger.addEventListener('click', () => this.toggle());
    this.input.addEventListener('input', () => this.onInput());
    this.form.addEventListener('submit', (event) => {
      event.preventDefault();
      event.stopPropagation();
      window.clearTimeout(this.timer);
      this.search();
    });
    this.root.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        event.preventDefault();
        this.close();
        this.trigger.focus();
      }
    });
    document.addEventListener('pointerdown', (event) => {
      if (!this.root.contains(event.target)) this.close();
    });
  }

  toggle() {
    if (this.root.classList.contains('is-open')) {
      this.close();
    } else {
      this.open();
    }
  }

  open() {
    this.root.classList.add('is-open');
    this.trigger.setAttribute('aria-expanded', 'true');
    this.popover.setAttribute('aria-hidden', 'false');
    this.input.focus();
  }

  close() {
    this.root.classList.remove('is-open');
    this.trigger.setAttribute('aria-expanded', 'false');
    this.popover.setAttribute('aria-hidden', 'true');
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
        this.showMessage(this.root.dataset.webshopSearchLoading);
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
