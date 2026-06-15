@if(config('ai.enabled'))
<script>
    window.bookingChatConfig = {
        startUrl: @json(route('booking-chat.start')),
        messageUrl: @json(route('booking-chat.message')),
        actionUrl: @json(route('booking-chat.action')),
        resetUrl: @json(route('booking-chat.reset')),
        locale: @json(app()->getLocale()),
    };
</script>
<script src="{{ asset('js/booking-chat.js') }}?v=3"></script>
@endif
