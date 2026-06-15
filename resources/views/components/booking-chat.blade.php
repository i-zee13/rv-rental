@if(config('ai.enabled'))
<div id="mv-booking-chat" aria-live="polite">
    <div class="chat-backdrop" aria-hidden="true"></div>

    <aside class="chat-sidebar" role="dialog" aria-label="{{ __('chat.widget_title') }}" aria-modal="true">
        <header class="chat-header">
            <div class="chat-header-brand">
                <span class="chat-avatar" aria-hidden="true">
                    <i class="bi bi-stars"></i>
                </span>
                <div>
                    <h3>{{ __('chat.widget_title') }}</h3>
                    <p>{{ __('chat.widget_subtitle') }}</p>
                </div>
            </div>
            <button type="button" class="chat-close" aria-label="{{ __('chat.close_chat') }}">
                <i class="bi bi-x-lg"></i>
            </button>
        </header>

        <div class="chat-body">
            <div class="chat-messages"></div>
            <div class="chat-typing" aria-hidden="true">
                <span class="chat-typing-dots"><span></span><span></span><span></span></span>
                {{ __('chat.typing') }}
            </div>
            <div class="chat-options"></div>
            <div class="chat-actions"></div>
        </div>

        <footer class="chat-composer">
            <form class="chat-input-wrap">
                <input type="text" class="chat-input" placeholder="{{ __('chat.placeholder') }}" maxlength="2000" autocomplete="off">
                <button type="submit" class="chat-send" aria-label="{{ __('chat.send') }}">
                    <i class="bi bi-arrow-up-short"></i>
                </button>
            </form>
            <p class="chat-footer">{{ __('chat.powered_by') }}</p>
        </footer>
    </aside>

    <button type="button" class="chat-fab" aria-label="{{ __('chat.open_chat') }}" aria-expanded="false">
        <i class="bi bi-chat-dots-fill chat-fab-icon-open"></i>
        <i class="bi bi-x-lg chat-fab-icon-close"></i>
        <span class="chat-fab-label">{{ __('chat.open_chat') }}</span>
    </button>
</div>
@endif
