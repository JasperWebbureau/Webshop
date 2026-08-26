class WebshopGroupHeroDescription {
  constructor(root) {

    this.root = root;
    this.description = root.querySelector('[data-webshop-group-hero-description]');
    this.excerpt = root.querySelector('[data-webshop-group-hero-description-excerpt]');
    this.full = root.querySelector('[data-webshop-group-hero-description-full]');
    this.toggle = root.querySelector('[data-webshop-group-hero-description-toggle]');

    if (!this.description || !this.excerpt || !this.full || !this.toggle) {
      return;
    }

    this.readMoreLabel = this.toggle.textContent.trim() || 'Lees meer';
    this.readLessLabel = this.toggle.getAttribute('data-read-less-label') || 'Lees minder';
    this.toggle.addEventListener('click', () => this.toggleDescription());
  }

  toggleDescription() {
    var isExpanded = this.description.classList.toggle('webshop-product-grid-intro__description--expanded');
    this.toggle.setAttribute('aria-expanded', isExpanded ? 'true' : 'false');
    this.toggle.textContent = isExpanded ? this.readLessLabel : this.readMoreLabel;
    this.excerpt.hidden = isExpanded;
    this.full.hidden = !isExpanded;
  }
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.webshop-product-grid-intro').forEach(function (hero) {
    new WebshopGroupHeroDescription(hero);
  });
});
