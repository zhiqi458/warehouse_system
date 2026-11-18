document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('a[href="#item-form"]').forEach((link) => {
    link.addEventListener('click', () => {
      const formPanel = document.querySelector('#item-form');
      if (formPanel instanceof HTMLDetailsElement) formPanel.open = true;
    });
  });
  document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
      if (!window.confirm(form.dataset.confirm || 'Continue?')) event.preventDefault();
    });
  });
  document.querySelectorAll('[data-menu-toggle]').forEach((button) => {
    button.addEventListener('click', () => document.body.classList.toggle('nav-open'));
  });
});
