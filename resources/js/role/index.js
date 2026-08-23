/**
 * Roles & Permissions module — self-contained, follows the same IIFE
 * pattern as the other simple modules (faculty, shift, etc.).
 */
(() => {
    'use strict';

    const CONFIG = {
        ROLES_API: '/api/v1/roles',
        USERS_API: '/api/v1/users',
        CATALOG_API: '/api/v1/roles/permission-catalog',
    };

    const ACTIONS = ['view', 'create', 'edit', 'delete'];
    const ACTION_LABELS = { view: 'View', create: 'Create', edit: 'Edit', delete: 'Delete' };

    const DOM = {
        rolesGrid: document.getElementById('roles-grid'),
        usersBody: document.getElementById('users-table-body'),
        loader: document.getElementById('loading-overlay'),
        modal: document.getElementById('roleModal'),
        modalCard: document.getElementById('modalCard'),
        modalTitle: document.getElementById('modalTitle'),
        form: document.getElementById('roleForm'),
        permissionGrid: document.getElementById('permission-grid'),
        submitBtn: document.getElementById('roleSubmitBtn'),
    };

    const Toast = typeof Swal !== 'undefined' ? Swal.mixin({
        toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
    }) : { fire: console.log };

    const state = {
        catalog: {},
        roles: [],
        editingRoleId: null,
    };

    async function request(url, options = {}) {
        DOM.loader?.classList.remove('hidden');
        try {
            const { headers, method = 'GET', body, ...rest } = options;
            const response = await fetch(url, {
                method,
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', ...headers },
                body,
                ...rest,
            });
            const contentType = response.headers.get('content-type');
            const isJson = contentType && contentType.includes('application/json');
            const data = isJson ? await response.json() : null;
            return { error: !response.ok, status: response.status, data };
        } catch (err) {
            console.error('[Roles API Error]', err);
            return { error: true, status: 500, data: null };
        } finally {
            DOM.loader?.classList.add('hidden');
        }
    }

    // ---- Data loading ----

    async function loadCatalog() {
        const { error, data } = await request(CONFIG.CATALOG_API);
        if (!error) state.catalog = data?.data ?? {};
    }

    async function loadRoles() {
        const { error, data } = await request(CONFIG.ROLES_API);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load roles' });
            return;
        }
        state.roles = data?.data ?? [];
        renderRoles();
        renderUserRoleOptions();
    }

    async function loadUsers() {
        const { error, data } = await request(`${CONFIG.USERS_API}?per_page=100`);
        if (error) {
            Toast.fire({ icon: 'error', title: 'Failed to load users' });
            return;
        }
        const records = Array.isArray(data?.data) ? data.data : [];
        renderUsers(records);
    }

    // ---- Rendering ----

    function renderRoles() {
        if (!DOM.rolesGrid) return;
        if (state.roles.length === 0) {
            DOM.rolesGrid.innerHTML = '<p class="text-sm text-neutral-400 col-span-full">No roles yet.</p>';
            return;
        }

        DOM.rolesGrid.innerHTML = state.roles.map((role) => {
            const modules = [...new Set(role.permissions.map((p) => p.split('.')[0]))];
            const isAdmin = role.name === 'Admin';
            return `
                <div class="p-5 bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-white/10 rounded-2xl shadow-sm">
                    <div class="flex items-start justify-between mb-2">
                        <h4 class="font-bold text-neutral-900 dark:text-white">${escapeHtml(role.name)}</h4>
                        <div class="flex gap-1">
                            <button data-action="edit-role" data-id="${role.id}" class="p-1.5 text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            ${isAdmin ? '' : `
                            <button data-action="delete-role" data-id="${role.id}" class="p-1.5 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-lg transition-colors" title="Delete">
                                <svg class="w-4 h-4 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>`}
                        </div>
                    </div>
                    <p class="text-xs text-neutral-400 mb-3">${role.users_count} staff assigned</p>
                    <div class="flex flex-wrap gap-1.5">
                        ${isAdmin
                            ? '<span class="px-2 py-0.5 text-[11px] font-semibold bg-indigo-600/10 text-indigo-600 dark:text-indigo-400 rounded-md">All modules</span>'
                            : modules.map((m) => `<span class="px-2 py-0.5 text-[11px] font-semibold bg-neutral-100 dark:bg-white/5 text-neutral-600 dark:text-neutral-300 rounded-md">${escapeHtml(m)}</span>`).join('')
                        }
                    </div>
                </div>`;
        }).join('');
    }

    function renderUsers(users) {
        if (!DOM.usersBody) return;
        if (users.length === 0) {
            DOM.usersBody.innerHTML = '<tr><td colspan="3" class="text-center py-8 text-neutral-400">No users found.</td></tr>';
            return;
        }

        DOM.usersBody.innerHTML = users.map((user) => `
            <tr>
                <td class="px-6 py-3 font-medium text-neutral-900 dark:text-white">${escapeHtml(user.name)}</td>
                <td class="px-6 py-3">${escapeHtml(user.email)}</td>
                <td class="px-6 py-3">
                    <select data-user-id="${user.id}" class="user-role-select px-3 py-1.5 text-sm bg-neutral-50 dark:bg-neutral-950 border border-neutral-200 dark:border-white/10 rounded-lg text-neutral-900 dark:text-white outline-none focus:ring-4 focus:ring-indigo-500/10">
                        <option value="">— No role —</option>
                        ${state.roles.map((r) => `<option value="${escapeHtml(r.name)}" ${(user.roles ?? []).includes(r.name) ? 'selected' : ''}>${escapeHtml(r.name)}</option>`).join('')}
                    </select>
                </td>
            </tr>`).join('');
    }

    function renderUserRoleOptions() {
        document.querySelectorAll('.user-role-select').forEach((select) => {
            const current = select.value;
            select.innerHTML = `<option value="">— No role —</option>` +
                state.roles.map((r) => `<option value="${escapeHtml(r.name)}">${escapeHtml(r.name)}</option>`).join('');
            select.value = current;
        });
    }

    function renderPermissionGrid(checkedPermissions = []) {
        if (!DOM.permissionGrid) return;
        const modules = Object.keys(state.catalog).sort();

        DOM.permissionGrid.innerHTML = modules.map((module) => {
            const perms = state.catalog[module];
            return `
                <div class="flex items-center justify-between gap-3 py-1.5">
                    <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 capitalize">${escapeHtml(module.replace('-', ' '))}</span>
                    <div class="flex gap-3">
                        ${ACTIONS.map((action) => {
                            const permName = `${module}.${action}`;
                            const exists = perms.includes(permName);
                            if (!exists) return '';
                            const checked = checkedPermissions.includes(permName) ? 'checked' : '';
                            return `
                                <label class="inline-flex items-center gap-1.5 cursor-pointer select-none">
                                    <input type="checkbox" value="${permName}" ${checked}
                                        class="permission-checkbox w-4 h-4 rounded border-neutral-300 dark:border-white/20 text-indigo-600 focus:ring-4 focus:ring-indigo-500/10 cursor-pointer">
                                    <span class="text-xs text-neutral-500 dark:text-neutral-400">${ACTION_LABELS[action]}</span>
                                </label>`;
                        }).join('')}
                    </div>
                </div>`;
        }).join('');
    }

    // ---- Modal ----

    function toggleModal(forceOpen = null) {
        if (!DOM.modal || !DOM.modalCard) return;
        const isOpen = DOM.modal.classList.contains('flex');
        const makeOpen = forceOpen !== null ? forceOpen : !isOpen;

        if (makeOpen) {
            DOM.modal.classList.remove('invisible');
            DOM.modal.classList.add('flex');
            requestAnimationFrame(() => {
                DOM.modal.classList.remove('opacity-0');
                DOM.modalCard.classList.remove('scale-90', 'opacity-0');
                DOM.modalCard.classList.add('scale-100', 'opacity-100');
            });
        } else {
            DOM.modal.classList.add('opacity-0');
            DOM.modalCard.classList.remove('scale-100', 'opacity-100');
            DOM.modalCard.classList.add('scale-90', 'opacity-0');
            setTimeout(() => {
                DOM.modal.classList.add('invisible');
                DOM.modal.classList.remove('flex');
                DOM.form?.reset();
                state.editingRoleId = null;
            }, 300);
        }
    }

    function openCreate() {
        state.editingRoleId = null;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'បង្កើតតួនាទីថ្មី (Create Role)';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'រក្សាទុក (Save Role)';
        DOM.form?.reset();
        renderPermissionGrid([]);
        toggleModal(true);
    }

    function openEdit(roleId) {
        const role = state.roles.find((r) => String(r.id) === String(roleId));
        if (!role) return;

        state.editingRoleId = role.id;
        if (DOM.modalTitle) DOM.modalTitle.textContent = 'កែប្រែតួនាទី (Edit Role)';
        if (DOM.submitBtn) DOM.submitBtn.textContent = 'ធ្វើបច្ចុប្បន្នភាព (Update)';
        DOM.form?.reset();
        const nameInput = DOM.form?.querySelector('[name="name"]');
        if (nameInput) nameInput.value = role.name;
        renderPermissionGrid(role.permissions);
        toggleModal(true);
    }

    async function handleDeleteRole(roleId) {
        const confirmation = await Swal.fire({
            title: 'តើអ្នកប្រាកដជាចង់លុបមែនទេ?',
            text: 'Users assigned to this role must be reassigned first.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#ef4444',
            confirmButtonText: 'បាទ/ចាស លុបវា!',
            cancelButtonText: 'បោះបង់',
        });
        if (!confirmation.isConfirmed) return;

        const { error, data } = await request(`${CONFIG.ROLES_API}/${roleId}`, { method: 'DELETE' });
        if (!error) {
            Toast.fire({ icon: 'success', title: 'Role deleted' });
            await loadRoles();
        } else {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to delete role' });
        }
    }

    async function handleFormSubmit(e) {
        e.preventDefault();
        if (!DOM.form || !DOM.submitBtn) return;

        DOM.submitBtn.disabled = true;

        const name = DOM.form.querySelector('[name="name"]')?.value.trim();
        const permissions = [...document.querySelectorAll('.permission-checkbox:checked')].map((el) => el.value);

        const url = state.editingRoleId ? `${CONFIG.ROLES_API}/${state.editingRoleId}` : CONFIG.ROLES_API;
        const method = state.editingRoleId ? 'PUT' : 'POST';

        const { error, status, data } = await request(url, {
            method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ name, permissions }),
        });

        if (!error) {
            Toast.fire({ icon: 'success', title: state.editingRoleId ? 'Role updated' : 'Role created' });
            toggleModal(false);
            await loadRoles();
        } else if (status === 422 && data?.errors) {
            const messages = Object.values(data.errors).flat();
            Toast.fire({ icon: 'warning', title: messages[0] || 'Validation failed' });
        } else {
            Toast.fire({ icon: 'error', title: data?.message || 'Something went wrong' });
        }

        DOM.submitBtn.disabled = false;
    }

    async function handleRoleAssignment(userId, roleName) {
        const { error, data } = await request(`${CONFIG.USERS_API}/${userId}/roles`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ roles: roleName ? [roleName] : [] }),
        });

        if (!error) {
            Toast.fire({ icon: 'success', title: 'Role updated' });
        } else {
            Toast.fire({ icon: 'error', title: data?.message || 'Failed to update role' });
        }
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    // ---- Events ----

    function initEvents() {
        window.AppModal = { toggle: (open) => toggleModal(open) };
        window.RolesModule = { openCreate };

        DOM.form?.addEventListener('submit', handleFormSubmit);

        DOM.rolesGrid?.addEventListener('click', (e) => {
            const btn = e.target.closest('button[data-action]');
            if (!btn) return;
            const id = btn.getAttribute('data-id');
            if (btn.getAttribute('data-action') === 'edit-role') openEdit(id);
            if (btn.getAttribute('data-action') === 'delete-role') handleDeleteRole(id);
        });

        DOM.usersBody?.addEventListener('change', (e) => {
            const select = e.target.closest('.user-role-select');
            if (!select) return;
            handleRoleAssignment(select.getAttribute('data-user-id'), select.value);
        });
    }

    document.addEventListener('DOMContentLoaded', async () => {
        initEvents();
        await loadCatalog();
        await loadRoles();
        await loadUsers();
    });
})();
