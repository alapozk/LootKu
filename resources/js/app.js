import './bootstrap';
import './echo';

document.querySelectorAll('[data-workspace]').forEach((workspace) => {
    const triggers = Array.from(workspace.querySelectorAll('[data-workspace-trigger]'));
    const panels = Array.from(workspace.querySelectorAll('[data-workspace-panel]'));
    const currentLabel = workspace.querySelector('[data-workspace-current]');
    const currentKicker = workspace.querySelector('[data-workspace-kicker]');
    const currentTitle = workspace.querySelector('[data-workspace-title]');
    const currentCopy = workspace.querySelector('[data-workspace-copy]');

    if (triggers.length === 0 || panels.length === 0) {
        return;
    }

    const availableTabs = new Set(panels.map((panel) => panel.dataset.workspacePanel));

    const updateTriggerState = (trigger, isActive) => {
        if (trigger.classList.contains('softdash-nav-link')) {
            trigger.classList.toggle('softdash-nav-link-active', isActive);
        }

        if (trigger.classList.contains('softdash-mobile-link')) {
            trigger.classList.toggle('softdash-mobile-link-active', isActive);
        }

        if (trigger.classList.contains('softdash-pill')) {
            trigger.classList.toggle('softdash-pill-active', isActive);
        }

        trigger.setAttribute('aria-selected', isActive ? 'true' : 'false');

        const badge = trigger.querySelector('[data-workspace-badge]');
        if (badge) {
            badge.hidden = !isActive;
        }
    };

    const setActiveTab = (tab) => {
        if (!availableTabs.has(tab)) {
            return;
        }

        triggers.forEach((trigger) => {
            const isActive = trigger.dataset.workspaceTrigger === tab;
            updateTriggerState(trigger, isActive);
        });

        panels.forEach((panel) => {
            const isActive = panel.dataset.workspacePanel === tab;
            panel.hidden = !isActive;
        });

        const activePanel = panels.find((panel) => panel.dataset.workspacePanel === tab);
        const activeTrigger = triggers.find((trigger) => trigger.dataset.workspaceTrigger === tab);

        if (activeTrigger && currentLabel) {
            currentLabel.textContent = activeTrigger.dataset.workspaceLabel || activeTrigger.textContent.trim();
        }

        if (activePanel && currentKicker) {
            currentKicker.textContent = activePanel.dataset.panelKicker || currentKicker.textContent;
        }

        if (activePanel && currentTitle) {
            currentTitle.textContent = activePanel.dataset.panelTitle || currentTitle.textContent;
        }

        if (activePanel && currentCopy) {
            currentCopy.textContent = activePanel.dataset.panelCopy || currentCopy.textContent;
        }

        const nextHash = `#${tab}`;
        if (window.location.hash !== nextHash) {
            window.history.replaceState(null, '', `${window.location.pathname}${window.location.search}${nextHash}`);
        }
    };

    triggers.forEach((trigger) => {
        trigger.addEventListener('click', (event) => {
            event.preventDefault();
            setActiveTab(trigger.dataset.workspaceTrigger);
        });
    });

    const hashTab = window.location.hash.replace('#', '');
    const defaultTab = availableTabs.has(hashTab)
        ? hashTab
        : workspace.dataset.workspaceDefault || panels[0].dataset.workspacePanel;

    setActiveTab(defaultTab);
});
