class WebshopMainGroupBannerDescription {
  constructor(root) {
    this.root = root;
    this.description = root.querySelector('[data-webshop-main-group-description]');
    this.toggle = root.querySelector('[data-webshop-main-group-description-toggle]');

    if (!this.description || !this.toggle) {
      return;
    }

    this.readMoreLabel = this.toggle.textContent.trim() || 'Lees meer';
    this.readLessLabel = this.toggle.getAttribute('data-read-less-label') || 'Lees minder';
    this.toggle.addEventListener('click', () => this.toggleDescription());
    this.updateVisibility();
    window.addEventListener('resize', () => this.updateVisibility());
  }

  updateVisibility() {
    this.description.classList.remove('webshop-main-group-banner__description--expanded');
    this.toggle.setAttribute('aria-expanded', 'false');
    this.toggle.textContent = this.readMoreLabel;

    var shouldCollapse = this.description.scrollHeight > 150;
    this.description.classList.toggle('webshop-main-group-banner__description--collapsible', shouldCollapse);
    this.toggle.hidden = !shouldCollapse;
  }

  toggleDescription() {
    var isExpanded = this.description.classList.toggle('webshop-main-group-banner__description--expanded');
    this.toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
    this.toggle.textContent = isExpanded ? this.readLessLabel : this.readMoreLabel;
  }
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.webshop-main-group-banner').forEach(function (banner) {
    new WebshopMainGroupBannerDescription(banner);
  });
});
