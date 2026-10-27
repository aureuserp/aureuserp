(() => {
    window.crmFitKanbanBoard ??= (board) => {
        const row = board?.querySelector('.crm-kanban-columns');

        if (! row) {
            return;
        }

        const overflow = row.style.overflowX;
        row.style.overflowX = 'scroll';
        let below = row.offsetHeight - row.clientHeight;
        row.style.overflowX = overflow;

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

    const cardOnlyCalls = ['moveRecord', 'setKanbanCardPriority', 'setKanbanCardColor', 'mountAction', 'unmountAction', 'callMountedAction'];

    const reloadsBoard = (commit) => Object.keys(commit.updates ?? {}).length > 0
        || (commit.calls ?? []).some((call) => ! cardOnlyCalls.includes(call.method));

    const registerShimmer = () => {
        if (window.crmKanbanShimmerRegistered) {
            return;
        }

        window.crmKanbanShimmerRegistered = true;

        window.Livewire.hook('commit', ({ component, commit, succeed, fail }) => {
            if (! component.el.querySelector('.crm-kanban-board')) {
                return;
            }

            if (! (commit.calls ?? []).every((call) => ['mountAction', 'unmountAction'].includes(call.method)) || Object.keys(commit.updates ?? {}).length > 0) {
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

    window.Livewire ? registerShimmer() : document.addEventListener('livewire:init', registerShimmer);

    if (! window.crmKanbanFitListening) {
        window.crmKanbanFitListening = true;
        window.addEventListener('resize', fitAll);
        window.addEventListener('load', fitAll);
        document.addEventListener('livewire:navigated', fitAll);
    }

    document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fitAll) : fitAll();
})();
