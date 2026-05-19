import './bootstrap';

const AppUI = {
  init() {
    this.initSidebar();
    this.initProfileMenu();
    this.initConfirmModal();
    this.initToasts();
    this.initLucide();
  },

  initSidebar() {
    const open = () => document.body.classList.add('sidebar-open');
    const close = () => document.body.classList.remove('sidebar-open');

    document.querySelectorAll('[data-sidebar-open]').forEach((btn) => btn.addEventListener('click', open));
    document.querySelectorAll('[data-sidebar-close]').forEach((btn) => btn.addEventListener('click', close));

    const backdrop = document.querySelector('.sidebar-backdrop');
    if (backdrop) backdrop.addEventListener('click', close);

    const panel = document.querySelector('.sidebar-panel');
    if (panel) {
      panel.querySelectorAll('a[href]').forEach((link) => {
        link.addEventListener('click', () => {
          if (window.innerWidth < 1024) close();
        });
      });
    }
  },

  initProfileMenu() {
    const btn = document.getElementById('profile-menu-btn');
    const popup = document.getElementById('profile-menu-popup');
    if (!btn || !popup) return;

    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      popup.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!popup.classList.contains('hidden') && !popup.contains(e.target) && e.target !== btn) {
        popup.classList.add('hidden');
      }
    });
  },

  initLucide() {
    if (window.lucide) {
      window.lucide.createIcons();
    }
  },

  initToasts() {
    const toast = document.querySelector('[data-toast]');
    if (!toast) return;

    const close = () => {
      toast.classList.add('opacity-0', 'translate-y-2');
      setTimeout(() => toast.remove(), 250);
    };

    toast.querySelectorAll('[data-toast-close]').forEach((button) => {
      button.addEventListener('click', close);
    });

    this.playToastSound(toast.dataset.toastType || 'info');
    setTimeout(close, 3200);
  },

  playToastSound(type) {
    if (!window.AudioContext && !window.webkitAudioContext) return;

    try {
      const audioContext = new (window.AudioContext || window.webkitAudioContext)();
      const oscillator = audioContext.createOscillator();
      const gainNode = audioContext.createGain();
      const configs = {
        success: [740, 0.025],
        error: [280, 0.04],
        warning: [460, 0.03],
        info: [520, 0.025],
      };
      const [frequency, gain] = configs[type] || configs.info;
      oscillator.type = 'sine';
      oscillator.frequency.value = frequency;
      gainNode.gain.value = gain;
      oscillator.connect(gainNode);
      gainNode.connect(audioContext.destination);
      oscillator.start();
      oscillator.stop(audioContext.currentTime + 0.12);
    } catch (error) {
      // no-op
    }
  },

  initConfirmModal() {
    document.addEventListener('submit', (event) => {
      const form = event.target;
      if (!(form instanceof HTMLFormElement)) return;
      const message = form.dataset.confirm;
      if (!message || form.dataset.confirmed === 'true') return;
      event.preventDefault();
      this.openConfirm(message, () => {
        form.dataset.confirmed = 'true';
        form.requestSubmit();
      });
    });
  },

  openConfirm(message, onConfirm) {
    const existing = document.getElementById('confirm-overlay');
    if (existing) existing.remove();
    const overlay = document.createElement('div');
    overlay.id = 'confirm-overlay';
    overlay.className = 'fixed inset-0 z-[80] flex items-center justify-center bg-slate-950/50 p-4';
    overlay.innerHTML = `
      <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-2xl">
        <div class="flex items-start gap-3">
          <div class="rounded-xl bg-amber-50 p-3 text-amber-700"><i data-lucide="triangle-alert" class="h-5 w-5"></i></div>
          <div>
            <h3 class="text-lg font-semibold text-slate-900">Konfirmasi tindakan</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">${message}</p>
          </div>
        </div>
        <div class="mt-6 flex justify-end gap-3">
          <button type="button" data-cancel class="btn-secondary">Batal</button>
          <button type="button" data-confirm-button class="btn-danger">Lanjutkan</button>
        </div>
      </div>`;

    document.body.appendChild(overlay);
    this.initLucide();

    overlay.querySelector('[data-cancel]').addEventListener('click', () => overlay.remove());
    overlay.addEventListener('click', (event) => {
      if (event.target === overlay) overlay.remove();
    });
    overlay.querySelector('[data-confirm-button]').addEventListener('click', () => {
      overlay.remove();
      onConfirm();
    });
  },
};

window.AppUI = AppUI;
document.addEventListener('DOMContentLoaded', () => AppUI.init());
