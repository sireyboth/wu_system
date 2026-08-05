/**
 * ui.js — everything about controlling what's visible on screen:
 * modal open/close animations, the close-button registry, and tab switching.
 * Merged from separate modal.js/tabs.js — they were small and always
 * changed together, so one file is easier to navigate than two.
 */
import { state } from './core.js';

/**
 * Opens/closes the main add-edit student modal with the fade+scale transition.
 */
export function toggleModal(dom, forceOpen = null) {
    if (!dom.modal || !dom.modalCard) return;

    const isOpen = dom.modal.classList.contains('flex');
    const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

    if (makeOpen) {
        dom.modal.classList.remove('invisible');
        dom.modal.classList.add('flex');

        requestAnimationFrame(() => {
            dom.modal.classList.remove('opacity-0');
            dom.modalCard.classList.remove('scale-90', 'opacity-0');
            dom.modalCard.classList.add('scale-100', 'opacity-100');
        });

        setTimeout(() => {
            dom.form?.querySelector('[name="name_kh"]')?.focus();
        }, 250);
    } else {
        dom.modal.classList.add('opacity-0');
        dom.modalCard.classList.remove('scale-100', 'opacity-100');
        dom.modalCard.classList.add('scale-90', 'opacity-0');

        setTimeout(() => {
            dom.modal.classList.add('invisible');
            dom.modal.classList.remove('flex');
            resetFormState(dom);
        }, 300);
    }
}

/**
 * Opens/closes the read-only preview modal.
 */
export function togglePreviewModal(show) {
    const el = document.getElementById('previewModal');
    if (!el) return;
    if (show) {
        el.classList.remove('hidden');
        setTimeout(() => el.firstElementChild.classList.remove('scale-95'), 10);
    } else {
        el.firstElementChild.classList.add('scale-95');
        setTimeout(() => el.classList.add('hidden'), 150);
    }
}

/**
 * Registry so ANY button, anywhere in the DOM, can close a modal by name
 * without needing its own onclick="somethingSpecific()" global.
 * Usage in Blade: <button data-close-modal="student">Close</button>
 */
const closers = {};

export function registerModalCloser(name, closeFn) {
    closers[name] = closeFn;
}

export function closeModalByName(name) {
    closers[name]?.();
}

/**
 * Resets the form back to "create" mode: clears fields, clears edit state,
 * restores default title/button text, hides smart-hints.
 */
export function resetFormState(dom) {
    if (dom.form) dom.form.reset();
    state.isEditMode = false;
    state.editingStudentId = null;

    if (dom.modalTitle) dom.modalTitle.textContent = 'បន្ថែមនិស្សិតថ្មី';
    if (dom.submitBtn) dom.submitBtn.textContent = 'រក្សាទុក';

    document.querySelectorAll('.smart-hint').forEach((hint) => {
        hint.classList.add('opacity-0', 'scale-95', 'translate-y-1');
    });
}

/**
 * Switches the active tab panel + button styling within the student form modal.
 */
export function switchTab(tabId) {
    document.querySelectorAll('.tab-panel').forEach((panel) => {
        panel.classList.add('hidden');
    });

    document.querySelectorAll('[id^="tab-"]').forEach((button) => {
        button.classList.remove('bg-white', 'dark:bg-neutral-900', 'text-indigo-600', 'dark:text-white', 'shadow-sm', 'font-bold');
        button.classList.add('text-neutral-500', 'dark:text-neutral-400', 'font-medium');
    });

    const targetPanel = document.getElementById(`panel-${tabId}`);
    const targetTab = document.getElementById(`tab-${tabId}`);

    if (targetPanel && targetTab) {
        targetPanel.classList.remove('hidden');
        targetTab.classList.remove('text-neutral-500', 'dark:text-neutral-400', 'font-medium');
        targetTab.classList.add('bg-white', 'dark:bg-neutral-900', 'text-indigo-600', 'dark:text-white', 'shadow-sm', 'font-bold');
    }
}
