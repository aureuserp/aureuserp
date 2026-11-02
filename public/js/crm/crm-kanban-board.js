(() => {
    window.crmFitKanbanBoard ??= (board) => {
        const row = board?.querySelector('.crm-kanban-columns');

        if (! row) {
            return;
        }

        if (window.crmKanbanScrollbarHeight === undefined) {
            const overflow = row.style.overflowX;
            row.style.overflowX = 'scroll';
            window.crmKanbanScrollbarHeight = row.offsetHeight - row.clientHeight;
            row.style.overflowX = overflow;
        }

        let below = window.crmKanbanScrollbarHeight;

        for (let element = row; element && element !== document.body; element = element.parentElement) {
            const style = getComputedStyle(element);

            below += parseFloat(style.paddingBottom) + parseFloat(style.marginBottom) + parseFloat(style.borderBottomWidth);
        }

        document.documentElement.style.setProperty('--crm-board-offset', (row.getBoundingClientRect().top + window.scrollY + below) + 'px');
        document.documentElement.dataset.crmBoardFitted = 'true';
    };

    let fitFrame = null;

    const fitAll = () => {
        if (fitFrame !== null) {
            return;
        }

        fitFrame = requestAnimationFrame(() => {
            fitFrame = null;
            document.querySelectorAll('.crm-kanban-board').forEach((board) => window.crmFitKanbanBoard(board));
        });
    };

    window.crmScheduleKanbanFit = fitAll;

    window.crmHighlightKanbanColumn ??= (value) => setTimeout(() => {
        const column = document.querySelector(`.crm-kanban-board [data-kanban-column][data-column-value="${CSS.escape(String(value))}"]`)?.closest('.fi-kanban-column');

        if (! column) {
            return;
        }

        column.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
        column.classList.remove('crm-kanban-column-new');
        void column.offsetWidth;
        column.classList.add('crm-kanban-column-new');
        setTimeout(() => column.classList.remove('crm-kanban-column-new'), 2800);
    }, 50);

    const cardOnlyCalls = ['moveRecord', 'setKanbanCardPriority', 'mountAction', 'unmountAction', 'callMountedAction'];

    const layoutNeutralCalls = ['moveRecord', 'setKanbanCardPriority', 'mountAction', 'unmountAction'];

    const reloadsBoard = (commit) => ! (commit.calls ?? []).some((call) => ['switchViewLayout', 'moveRecord'].includes(call.method))
        && (Object.keys(commit.updates ?? {}).length > 0
            || (commit.calls ?? []).some((call) => ! cardOnlyCalls.includes(call.method)));

    let dragSourceColumn = null;

    let draggedCard = null;

    let shiftedRecord = null;

    const columnSelector = (value) => `[data-kanban-column][data-column-value="${CSS.escape(String(value))}"]`;

    const shiftColumnCount = (board, value, delta) => {
        const badge = board.querySelector(columnSelector(value))?.closest('.fi-kanban-column')?.querySelector('[data-kanban-count]');

        if (! badge) {
            return;
        }

        const count = Math.max(0, parseInt(badge.dataset.kanbanCount, 10) + delta);

        badge.dataset.kanbanCount = count;
        badge.textContent = badge.dataset.kanbanWipLimit ? `${count} / ${badge.dataset.kanbanWipLimit}` : String(count);
    };

    const showMove = (boards, commit, succeed, fail) => {
        const move = (commit.calls ?? []).find((call) => call.method === 'moveRecord');

        if (! move) {
            return;
        }

        const [recordId, targetColumn] = move.params;
        const cardSelector = `[data-kanban-card][data-record-id="${CSS.escape(String(recordId))}"]`;
        const board = boards.find((board) => board.querySelector(cardSelector));
        const card = board?.querySelector(cardSelector);

        if (! card) {
            return;
        }

        if (shiftedRecord !== String(recordId)
            && dragSourceColumn !== null
            && dragSourceColumn !== String(targetColumn)) {
            shiftColumnCount(board, dragSourceColumn, -1);
            shiftColumnCount(board, targetColumn, 1);
        }

        dragSourceColumn = null;
        shiftedRecord = null;

        card.classList.add('crm-kanban-card-saving');

        const done = () => card.classList.remove('crm-kanban-card-saving');

        succeed(done);
        fail(done);
    };

    const registerShimmer = () => {
        if (window.crmKanbanShimmerRegistered) {
            return;
        }

        window.crmKanbanShimmerRegistered = true;

        document.addEventListener('pointerdown', (event) => {
            draggedCard = event.target.closest?.('.crm-kanban-board [data-kanban-card]') ?? null;
            dragSourceColumn = draggedCard?.closest('[data-kanban-column]')?.dataset.columnValue ?? null;
            shiftedRecord = null;
        }, true);

        const shiftOnRelease = () => setTimeout(() => {
            const targetColumn = draggedCard?.closest('[data-kanban-column]')?.dataset.columnValue ?? null;
            const board = draggedCard?.closest('.crm-kanban-board');

            if (board
                && dragSourceColumn !== null
                && targetColumn !== null
                && targetColumn !== dragSourceColumn
                && shiftedRecord !== draggedCard.dataset.recordId) {
                shiftColumnCount(board, dragSourceColumn, -1);
                shiftColumnCount(board, targetColumn, 1);
                shiftedRecord = draggedCard.dataset.recordId;
            }

            draggedCard = null;
        }, 0);

        document.addEventListener('pointerup', shiftOnRelease, true);

        window.Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
            if (! component.el.querySelector('.crm-kanban-board')) {
                return;
            }

            if (! (commit.calls ?? []).every((call) => layoutNeutralCalls.includes(call.method)) || Object.keys(commit.updates ?? {}).length > 0) {
                succeed(fitAll);
            }

            const boards = [...component.el.querySelectorAll('.crm-kanban-board')];

            showMove(boards, commit, succeed, fail);

            if (! reloadsBoard(commit)) {
                return;
            }

            boards.forEach((board) => {
                const column = board.querySelector('.crm-kanban-columns .fi-kanban-column');

                if (column) {
                    board.style.setProperty('--crm-shimmer-height', column.offsetHeight + 'px');
                }

                board.classList.add('crm-kanban-loading');
            });

            const done = () => boards.forEach((board) => board.classList.remove('crm-kanban-loading'));

            succeed(done);
            fail(done);
        });
    };

    let hoveredColumn = null;

    const highlightColumn = (column) => {
        if (column === hoveredColumn) {
            return;
        }

        hoveredColumn?.classList.remove('crm-kanban-column-hover');
        column?.classList.add('crm-kanban-column-hover');
        hoveredColumn = column;
    };

    const followPointer = (x, y) => {
        const ghost = document.querySelector('.crm-kanban-board .fi-kanban-card-ghost');

        if (! ghost) {
            return;
        }

        const element = document.elementFromPoint(x, y);
        const column = element?.closest('.crm-kanban-board .fi-kanban-column') ?? null;
        const list = column?.querySelector('[data-kanban-column]');

        highlightColumn(column);

        if (! list
            || element.closest('[data-kanban-card]')
            || ghost.parentElement === list && ghost === list.lastElementChild) {
            return;
        }

        const below = [...list.querySelectorAll('[data-kanban-card]')].find((card) => card !== ghost && card.getBoundingClientRect().top > y);

        below ? list.insertBefore(ghost, below) : list.appendChild(ghost);
    };

    let followFrame = null;

    const scheduleFollow = (event) => {
        if (! event.buttons) {
            return;
        }

        const floating = document.body.querySelector(':scope > .crm-kanban-card-floating');

        if (floating) {
            document.documentElement.appendChild(floating);
        }

        if (followFrame !== null) {
            return;
        }

        followFrame = requestAnimationFrame(() => {
            followFrame = null;
            followPointer(event.clientX, event.clientY);
        });
    };

    const prepareDrag = (event) => {
        if (! event.target.closest?.('.crm-kanban-board [data-kanban-card]')) {
            return;
        }

        event.target.closest('.crm-kanban-board').querySelectorAll('[data-kanban-column]').forEach((list) => {
            const sortable = list[Object.keys(list).find((name) => name.startsWith('Sortable'))];

            if (! sortable) {
                return;
            }

            sortable.nativeDraggable = false;
            sortable.option('animation', 0);
            sortable.option('forceFallback', true);
            sortable.option('fallbackOnBody', true);
            sortable.option('fallbackClass', 'crm-kanban-card-floating');
            sortable.option('fallbackTolerance', 3);
            sortable.option('emptyInsertThreshold', 24);
        });
    };

    if (! window.crmKanbanDragListening) {
        window.crmKanbanDragListening = true;
        document.addEventListener('pointerdown', prepareDrag, true);
        document.addEventListener('pointermove', scheduleFollow);
        document.addEventListener('pointerup', () => {
            cancelAnimationFrame(followFrame);
            followFrame = null;
            highlightColumn(null);
        }, true);
    }

    window.Livewire ? registerShimmer() : document.addEventListener('livewire:init', registerShimmer);

    if (! window.crmKanbanFitListening) {
        window.crmKanbanFitListening = true;
        window.addEventListener('resize', fitAll);
        window.addEventListener('load', fitAll);
        document.addEventListener('livewire:navigated', fitAll);
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fitAll) : fitAll();
})();
