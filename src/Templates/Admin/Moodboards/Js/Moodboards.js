class WebshopAdminMoodboard {
  constructor(element) {
    this.element = element;
    this.preview = element.querySelector('[data-webshop-moodboard-preview]');
    this.items = element.querySelector('[data-webshop-moodboard-items]');
    this.template = element.querySelector('[data-webshop-moodboard-row-template]');
    this.addButton = element.querySelector('[data-webshop-moodboard-add-row]');
    this.rowIndex = this.items ? this.items.children.length : 0;
    this.bind();
  }

  bind() {
    if (this.items) {
      this.items.addEventListener('focusin', (event) => {
        const row = event.target.closest('[data-webshop-moodboard-row]');
        if (row) {
          this.setActiveRow(row);
        }
      });

      this.items.addEventListener('click', (event) => {
        const row = event.target.closest('[data-webshop-moodboard-row]');
        if (row) {
          this.setActiveRow(row);
        }
      });
    }

    if (this.preview) {
      this.preview.addEventListener('click', (event) => {
        const pin = event.target.closest('[data-webshop-moodboard-pin]');
        if (pin) {
          const row = this.element.querySelector(`[data-webshop-moodboard-row="${pin.dataset.webshopMoodboardPin}"]`);
          if (row) {
            this.setActiveRow(row);
          }
          return;
        }

        this.applyClickPosition(event);
      });
    }

    if (this.addButton && this.template && this.items) {
      this.addButton.addEventListener('click', () => this.addRow());
    }
  }

  addRow() {
    const fragment = this.template.content.cloneNode(true);
    const row = fragment.querySelector('[data-webshop-moodboard-row]');
    const index = this.rowIndex++;

    row.querySelectorAll('[data-name]').forEach((input) => {
      input.name = `items[${index}][${input.dataset.name}]`;
      input.removeAttribute('data-name');
    });

    this.items.appendChild(fragment);
    this.setActiveRow(row);
    row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
  }

  setActiveRow(row) {
    this.element.querySelectorAll('[data-webshop-moodboard-row]').forEach((item) => {
      item.classList.toggle('is-active', item === row);
    });

    const rowId = row.dataset.webshopMoodboardRow;
    this.element.querySelectorAll('[data-webshop-moodboard-pin], [data-webshop-moodboard-draft-pin]').forEach((pin) => {
      const pinId = pin.dataset.webshopMoodboardPin || pin.dataset.webshopMoodboardDraftPin;
      pin.classList.toggle('is-active', pinId === rowId);
    });
  }

  getActiveRow() {
    return this.element.querySelector('[data-webshop-moodboard-row].is-active')
      || this.element.querySelector('[data-webshop-moodboard-row]');
  }

  applyClickPosition(event) {
    const row = this.getActiveRow();
    if (!row || !this.preview) {
      return;
    }

    const rect = this.preview.getBoundingClientRect();
    const x = Math.max(0, Math.min(100, ((event.clientX - rect.left) / rect.width) * 100));
    const y = Math.max(0, Math.min(100, ((event.clientY - rect.top) / rect.height) * 100));
    const xInput = row.querySelector('[data-webshop-moodboard-x]');
    const yInput = row.querySelector('[data-webshop-moodboard-y]');

    if (xInput) {
      xInput.value = x.toFixed(1);
    }

    if (yInput) {
      yInput.value = y.toFixed(1);
    }

    this.updateDraftPin(row, x, y);
  }

  updateDraftPin(row, x, y) {
    const rowId = row.dataset.webshopMoodboardRow || 'new';
    const selector = `[data-webshop-moodboard-draft-pin="${rowId}"]`;
    let pin = this.preview.querySelector(selector);

    if (!pin) {
      pin = document.createElement('button');
      pin.type = 'button';
      pin.className = 'webshop-admin-moodboard-edit__pin is-active';
      pin.dataset.webshopMoodboardDraftPin = rowId;
      pin.textContent = String(Array.from(this.element.querySelectorAll('[data-webshop-moodboard-row]')).indexOf(row) + 1).padStart(2, '0');
      this.preview.appendChild(pin);
    }

    this.preview.querySelectorAll('.webshop-admin-moodboard-edit__pin').forEach((item) => {
      item.classList.toggle('is-active', item === pin);
    });

    pin.style.setProperty('--pin-x', `${x.toFixed(1)}%`);
    pin.style.setProperty('--pin-y', `${y.toFixed(1)}%`);
  }
}

document.querySelectorAll('[data-webshop-admin-moodboard]').forEach((element) => {
  new WebshopAdminMoodboard(element);
});
