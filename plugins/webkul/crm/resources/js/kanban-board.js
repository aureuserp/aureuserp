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

    const cardOnlyCalls = ['moveRecord', 'moveKanbanRecords', 'setKanbanCardPriority', 'mountAction', 'unmountAction', 'callMountedAction'];

    const layoutNeutralCalls = ['moveRecord', 'moveKanbanRecords', 'setKanbanCardPriority', 'mountAction', 'unmountAction'];

    const reloadsBoard = (commit) => ! (commit.calls ?? []).some((call) => ['switchViewLayout', 'moveRecord', 'moveKanbanRecords'].includes(call.method))
        && (Object.keys(commit.updates ?? {}).length > 0
            || (commit.calls ?? []).some((call) => ! cardOnlyCalls.includes(call.method)));

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

    const pendingMoves = [];

    let queuedMoves = [];

    let flushingMoves = [];

    let flushTimer = null;

    const scheduleFlush = () => {
        clearTimeout(flushTimer);
        flushTimer = queuedMoves.length ? setTimeout(flushMoves, 600) : null;
    };

    const forgetMoves = (moves) => moves.forEach((move) => {
        const index = pendingMoves.indexOf(move);

        if (index !== -1) {
            pendingMoves.splice(index, 1);
        }
    });

    const flushMoves = () => {
        clearTimeout(flushTimer);
        flushTimer = null;

        if (! queuedMoves.length) {
            return;
        }

        const moves = queuedMoves;
        const latest = new Map();

        queuedMoves = [];

        moves.forEach((move) => {
            latest.delete(move.recordId);
            latest.set(move.recordId, move);
        });

        const changes = [...latest.values()].filter((move) => move.to !== moves.find((first) => first.recordId === move.recordId).from);

        if (! changes.length) {
            forgetMoves(moves);

            return;
        }

        flushingMoves = moves;
        moves[0].wire.moveKanbanRecords(changes.map((move) => [move.recordId, move.to, move.orderedIds]));
    };

    const queueDroppedMove = (event) => {
        const board = event.to.closest('.crm-kanban-board');
        const from = event.from.dataset.columnValue;
        const to = event.to.dataset.columnValue;

        setTimeout(() => {
            window.Alpine.$data(board).dragStarted = false;
        }, 0);

        if (from === to) {
            return;
        }

        const move = {
            recordId: event.item.dataset.recordId,
            from,
            to,
            orderedIds: [...event.to.querySelectorAll('[data-kanban-card]')].map((card) => card.dataset.recordId),
            wire: window.Livewire.find(board.closest('[wire\\:id]').getAttribute('wire:id')),
            action: null,
        };

        pendingMoves.push(move);
        queuedMoves.push(move);
        scheduleFlush();
    };

    const registerShimmer = () => {
        if (window.crmKanbanShimmerRegistered) {
            return;
        }

        window.crmKanbanShimmerRegistered = true;

        window.Livewire.interceptAction(({ action, onFinish }) => {
            if (action.name !== 'moveKanbanRecords') {
                return;
            }

            const moves = flushingMoves;

            flushingMoves = [];
            moves.forEach((move) => {
                move.action = action;
            });

            onFinish(() => forgetMoves(moves));
        });

        window.Livewire.interceptMessage(({ message, onSuccess }) => {
            onSuccess(({ payload }) => {
                const partials = payload.effects?.partials;

                if (! partials) {
                    return;
                }

                pendingMoves
                    .filter((move) => ! message.actions.has(move.action))
                    .forEach((move) => {
                        delete partials[`kanban-column-${move.from}`];
                        delete partials[`kanban-column-${move.to}`];
                    });
            });
        });

        window.Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
            if (! component.el.querySelector('.crm-kanban-board')) {
                return;
            }

            if (! (commit.calls ?? []).every((call) => layoutNeutralCalls.includes(call.method)) || Object.keys(commit.updates ?? {}).length > 0) {
                succeed(fitAll);
            }

            const boards = [...component.el.querySelectorAll('.crm-kanban-board')];

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
        if (column === hoveredColumn
            && (! column || column.classList.contains('crm-kanban-column-hover'))) {
            return;
        }

        hoveredColumn?.classList.remove('crm-kanban-column-hover');
        column?.classList.add('crm-kanban-column-hover');
        hoveredColumn = column;
    };

    const draggedGhost = () => document.querySelector('.crm-kanban-board .fi-kanban-card-ghost');

    const columnAt = (x, y) => document.elementFromPoint(x, y)?.closest('.crm-kanban-board .fi-kanban-column') ?? null;

    let followFrame = null;

    const scheduleFollow = (event) => {
        if (! event.buttons) {
            return;
        }

        const floating = document.body.querySelector(':scope > .crm-kanban-card-floating');

        if (floating) {
            const ghost = draggedGhost();

            document.documentElement.appendChild(floating);
            ghost?.classList.add('crm-kanban-card-lifted');

            if (ghost) {
                ghost.__livewire_ignore = true;
            }
            ghost?.closest('.crm-kanban-columns')?.classList.add('crm-kanban-dragging');
        }

        if (followFrame !== null) {
            return;
        }

        followFrame = requestAnimationFrame(() => {
            followFrame = null;
            highlightColumn(draggedGhost() ? columnAt(event.clientX, event.clientY) : null);
        });
    };

    const endFollow = () => {
        cancelAnimationFrame(followFrame);
        followFrame = null;
        highlightColumn(null);
        document.querySelectorAll('.crm-kanban-dragging').forEach((columns) => columns.classList.remove('crm-kanban-dragging'));
    };

    const placeDroppedCard = (event) => {
        endFollow();
        scheduleFlush();

        const ghost = draggedGhost();

        if (! ghost) {
            return;
        }

        const list = columnAt(event.clientX, event.clientY)?.querySelector('[data-kanban-column]');
        const before = list && [...list.querySelectorAll('[data-kanban-card]')].find((card) => {
            const rect = card.getBoundingClientRect();

            return card !== ghost && rect.top + rect.height / 2 > event.clientY;
        });

        ghost.classList.remove('crm-kanban-card-lifted');
        ghost.__livewire_ignore = false;

        if (list
            && ghost.parentElement !== list) {
            const board = list.closest('.crm-kanban-board');
            const from = ghost.parentElement.dataset.columnValue;

            shiftColumnCount(board, from, -1);
            shiftColumnCount(board, list.dataset.columnValue, 1);

            document.querySelectorAll('.crm-kanban-card-moved').forEach((card) => card.classList.remove('crm-kanban-card-moved'));
            ghost.classList.add('crm-kanban-card-moved');
        }

        if (list
            && (ghost.parentElement !== list || ghost.nextElementSibling !== (before ?? null))) {
            list.insertBefore(ghost, before ?? null);
        }
    };

    const prepareDrag = (event) => {
        if (! event.target.closest?.('.crm-kanban-board [data-kanban-card]')) {
            return;
        }

        const board = event.target.closest('.crm-kanban-board');

        clearTimeout(flushTimer);

        board.querySelectorAll('[data-kanban-column]').forEach((list) => {
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
            sortable.option('onMove', () => false);

            if (board.hasAttribute('data-crm-batch-moves')) {
                sortable.option('onEnd', queueDroppedMove);
            }
        });
    };

    if (! window.crmKanbanDragListening) {
        window.crmKanbanDragListening = true;
        document.addEventListener('pointerdown', prepareDrag, true);
        document.addEventListener('pointermove', scheduleFollow);
        document.addEventListener('pointerup', placeDroppedCard, true);
        window.addEventListener('pagehide', flushMoves);
        window.addEventListener('beforeunload', flushMoves);
        document.addEventListener('pointercancel', () => {
            const ghost = draggedGhost();

            endFollow();
            scheduleFlush();
            ghost?.classList.remove('crm-kanban-card-lifted');

            if (ghost) {
                ghost.__livewire_ignore = false;
            }
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
