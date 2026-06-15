(function () {
    function initBookingChat() {
        const root = document.getElementById('mv-booking-chat');
        if (!root || !window.bookingChatConfig) return;

    const cfg = window.bookingChatConfig;
    const backdrop = root.querySelector('.chat-backdrop');
    const fab = root.querySelector('.chat-fab');
    const closeBtn = root.querySelector('.chat-close');
    const messagesEl = root.querySelector('.chat-messages');
    const optionsEl = root.querySelector('.chat-options');
    const actionsEl = root.querySelector('.chat-actions');
    const inputEl = root.querySelector('.chat-input');
    const sendBtn = root.querySelector('.chat-send');
    const formEl = root.querySelector('.chat-input-wrap');

    let started = false;
    let savedScrollY = 0;

    function csrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }

    async function request(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(body || {}),
        });

        if (!response.ok) {
            const errBody = await response.json().catch(function () { return {}; });
            throw new Error(errBody.message || 'Request failed');
        }

        return response.json();
    }

    function setLoading(loading) {
        root.classList.toggle('is-loading', loading);
        sendBtn.disabled = loading;
        inputEl.disabled = loading;
    }

    function appendMessage(role, text) {
        const bubble = document.createElement('div');
        bubble.className = 'chat-message ' + role;
        bubble.textContent = text;
        messagesEl.appendChild(bubble);
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    function renderOptions(options) {
        optionsEl.innerHTML = '';
        (options || []).forEach(function (option) {
            const chip = document.createElement('button');
            chip.type = 'button';
            chip.className = 'chat-chip';
            chip.innerHTML = option.label + (option.meta ? '<small>' + option.meta + '</small>' : '');
            chip.addEventListener('click', function () {
                if (option.action === 'choose_vehicle') {
                    handleAction('choose_vehicle', {});
                } else if (option.action === 'choose_property') {
                    handleAction('choose_property', {});
                } else if (option.action === 'select_vehicle') {
                    handleAction('select_vehicle', { vehicle_id: option.vehicle_id });
                } else if (option.action === 'select_property') {
                    handleAction('select_property', { property_id: option.property_id });
                } else if (option.action === 'toggle_addon') {
                    handleAction('toggle_addon', { addon_id: option.addon_id });
                } else if (option.action === 'message' && option.value) {
                    handleMessage(option.value);
                }
            });
            optionsEl.appendChild(chip);
        });
    }

    function renderActions(actions) {
        actionsEl.innerHTML = '';
        (actions || []).forEach(function (action) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'chat-action-btn' + (action.action === 'cancel_confirm' ? ' secondary' : '');
            btn.textContent = action.label;
            btn.addEventListener('click', function () {
                if (action.action === 'confirm' || action.action === 'cancel_confirm' || action.action === 'skip_addons' || action.action === 'skip_phone' || action.action === 'choose_vehicle' || action.action === 'choose_property') {
                    handleAction(action.action, {});
                }
            });
            actionsEl.appendChild(btn);
        });
    }

    function renderPayload(payload) {
        (payload.messages || []).forEach(function (message) {
            if (message.role === 'assistant') {
                appendMessage('assistant', message.text);
            }
        });
        renderOptions(payload.options);
        renderActions(payload.actions);
    }

    async function handleMessage(text) {
        if (!text || !text.trim()) return;
        appendMessage('user', text.trim());
        inputEl.value = '';
        setLoading(true);
        optionsEl.innerHTML = '';
        actionsEl.innerHTML = '';

        try {
            const payload = await request(cfg.messageUrl, { message: text.trim() });
            renderPayload(payload);
        } catch (e) {
            appendMessage('assistant', e.message && e.message !== 'Request failed'
                ? e.message
                : 'Sorry, something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    async function handleAction(action, payload) {
        setLoading(true);
        optionsEl.innerHTML = '';
        actionsEl.innerHTML = '';

        try {
            const result = await request(cfg.actionUrl, { action: action, payload: payload });
            renderPayload(result);
        } catch (e) {
            appendMessage('assistant', e.message && e.message !== 'Request failed'
                ? e.message
                : 'Sorry, something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    async function startChat() {
        if (started) return;
        started = true;
        setLoading(true);

        try {
            const payload = await request(cfg.startUrl, {});
            renderPayload(payload);
        } catch (e) {
            appendMessage('assistant', 'Chat is temporarily unavailable.');
        } finally {
            setLoading(false);
        }
    }

    function openChat() {
        savedScrollY = window.scrollY || window.pageYOffset || 0;
        root.classList.add('is-open');
        document.body.classList.add('chat-sidebar-open');
        if (window.matchMedia('(max-width: 768px)').matches) {
            document.body.style.top = '-' + savedScrollY + 'px';
        }
        fab.setAttribute('aria-expanded', 'true');
        startChat();
        setTimeout(function () { inputEl.focus({ preventScroll: true }); }, 380);
    }

    function closeChat() {
        root.classList.remove('is-open');
        document.body.classList.remove('chat-sidebar-open');
        document.body.style.top = '';
        window.scrollTo(0, savedScrollY);
        fab.setAttribute('aria-expanded', 'false');
    }

    function toggleChat() {
        if (root.classList.contains('is-open')) {
            closeChat();
        } else {
            openChat();
        }
    }

    fab.addEventListener('click', toggleChat);
    closeBtn.addEventListener('click', closeChat);
    backdrop.addEventListener('click', closeChat);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && root.classList.contains('is-open')) {
            closeChat();
        }
    });

    formEl.addEventListener('submit', function (e) {
        e.preventDefault();
        handleMessage(inputEl.value);
    });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBookingChat);
    } else {
        initBookingChat();
    }
})();
