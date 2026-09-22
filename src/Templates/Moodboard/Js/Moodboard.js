class WebshopMoodboard {
  constructor(element) {
    this.element = element;
    this.hotspots = Array.from(element.querySelectorAll('[data-webshop-moodboard-hotspot]'));
    this.products = Array.from(element.querySelectorAll('[data-webshop-moodboard-product]'));
    this.bind();
  }

  bind() {
    this.hotspots.forEach((hotspot) => {
      hotspot.addEventListener('click', () => {
        this.setActive(hotspot.dataset.webshopMoodboardHotspot);
      });
    });

    this.products.forEach((product) => {
      product.addEventListener('mouseenter', () => {
        this.setActive(product.dataset.webshopMoodboardProduct);
      });

      product.addEventListener('focus', () => {
        this.setActive(product.dataset.webshopMoodboardProduct);
      });
    });
  }

  setActive(productId) {
    this.hotspots.forEach((hotspot) => {
      hotspot.classList.toggle('is-active', hotspot.dataset.webshopMoodboardHotspot === productId);
    });

    this.products.forEach((product) => {
      product.classList.toggle('is-active', product.dataset.webshopMoodboardProduct === productId);
    });
  }
}

document.querySelectorAll('[data-webshop-moodboard]').forEach((element) => {
  new WebshopMoodboard(element);
});
