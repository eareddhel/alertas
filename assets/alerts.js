(function () {
  class AlertSystem {
    constructor(containerId = 'alertsContainer') {
      this.container = document.getElementById(containerId);
      this.initialized = Boolean(this.container);
    }

    initFromServer(alerts) {
      if (!this.initialized || !Array.isArray(alerts)) {
        return;
      }

      alerts.forEach((alert, index) => {
        setTimeout(() => this.show(alert), index * 200);
      });
    }

    show(alertData) {
      if (!this.initialized) {
        return;
      }

      const {
        type = 'info',
        title = '',
        message = '',
        icon,
        duration = type === 'success' ? 4000 : 6000,
        dismissible = true,
      } = alertData || {};

      const alertId = `alert-${Date.now()}-${Math.random().toString(16).slice(2)}`;
      const alertElement = document.createElement('div');
      alertElement.id = alertId;
      alertElement.className = `alert-toast alert-${this.normalizeType(type)}`;
      alertElement.innerHTML = `
        <div class="alert-content">
          <i class="bi bi-${icon || this.getIcon(type)} alert-icon"></i>
          <div class="alert-body">
            ${title ? `<strong class="alert-title">${title}</strong>` : ''}
            <div class="alert-message">${message}</div>
          </div>
        </div>
        ${dismissible ? '<button type="button" class="alert-close" data-dismiss="true"><i class="bi bi-x"></i></button>' : ''}
      `;

      this.container.appendChild(alertElement);

      requestAnimationFrame(() => {
        alertElement.classList.add('show');
      });

      const closeBtn = alertElement.querySelector('.alert-close');
      if (closeBtn) {
        closeBtn.addEventListener('click', () => this.dismiss(alertId));
      }

      if (duration > 0) {
        setTimeout(() => this.dismiss(alertId), duration);
      }
    }

    dismiss(alertId) {
      const alert = document.getElementById(alertId);
      if (!alert) {
        return;
      }

      alert.classList.remove('show');
      setTimeout(() => alert.remove(), 300);
    }

    notify(message, type = 'info', title = '') {
      this.show({
        type,
        title,
        message,
        icon: this.getIcon(type),
      });
    }

    normalizeType(type) {
      if (String(type).toLowerCase() === 'error') {
        return 'danger';
      }

      const valid = ['success', 'danger', 'warning', 'info'];
      return valid.includes(String(type).toLowerCase()) ? String(type).toLowerCase() : 'info';
    }

    getIcon(type) {
      const icons = {
        success: 'check-circle-fill',
        danger: 'exclamation-circle-fill',
        warning: 'exclamation-triangle-fill',
        info: 'info-circle-fill',
      };

      const normalizedType = this.normalizeType(type);
      return icons[normalizedType] || icons.info;
    }
  }

  const system = new AlertSystem();
  window.AlertSystem = {
    notify: (message, type, title) => system.notify(message, type, title),
    show: (data) => system.show(data),
  };

  document.addEventListener('DOMContentLoaded', () => {
    const payload = document.getElementById('alertRepositoryData');
    if (!payload) {
      return;
    }

    try {
      const alerts = JSON.parse(payload.textContent || '[]');
      system.initFromServer(alerts);
    } catch (error) {
      system.initFromServer([]);
    }
  });
})();
