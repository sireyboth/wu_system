import { buildDom } from './core.js';
import { initAlertModal, openEditModal, bindFormSubmit, bindQuickActions, loadDashboard, loadSearch } from './action.js';
import { loadDoneTable } from './done-table.js';

document.addEventListener('DOMContentLoaded', () => {
    const dom = buildDom();

    let currentBoardList = [];
    let currentDoneList = [];
    let currentSearchList = [];
    let searchTimer = null;

    initAlertModal(dom);

    async function refresh() {
        if (dom.searchInput?.value.trim()) {
            currentSearchList = await loadSearch(dom, dom.searchInput.value.trim());
        } else {
            currentBoardList = await loadDashboard(dom);
        }
        currentDoneList = await loadDoneTable();
    }

    bindFormSubmit(dom, refresh);
    bindQuickActions(dom, {
        onEdit: (id) => openEditModal(dom, id, [...currentBoardList, ...currentDoneList, ...currentSearchList]),
        onChange: refresh,
    });

    dom.searchInput?.addEventListener('input', (e) => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(async () => {
            const keyword = e.target.value.trim();
            const isSearching = !!keyword;

            dom.boardView?.classList.toggle('hidden', isSearching);
            dom.searchView?.classList.toggle('hidden', !isSearching);

            if (isSearching) {
                currentSearchList = await loadSearch(dom, keyword);
            } else {
                currentBoardList = await loadDashboard(dom);
            }
        }, 300);
    });

    loadDashboard(dom).then((list) => { currentBoardList = list; });
    loadDoneTable().then((list) => { currentDoneList = list; });
});
