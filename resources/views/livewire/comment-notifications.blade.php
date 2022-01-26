<div 
    wire:poll="getNotificationCount"
    x-data="{ isOpen: false }">
    <button
        @click=
        "isOpen = !isOpen
        if(isOpen) {
            Livewire.emit('getNotifications')
        }"
        class="relative">
        <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        @if($notificationCount)
            <div class="absolute rounded-full bg-red text-white text-xxs w-4 h-4 flex justify-center items-center top-0 right-0">
                {{ $notificationCount }}
            </div>
        @endif
    </button>
    <ul
        class="absolute w-76 md:w-96 text-left text-gray-700 bg-white shadow-dialog rounded-xl max-h-104 overflow-y-auto z-10 right-21 md:right-12"
        x-cloak
        x-show.transition.origin.top="isOpen"
        @click.away = "isOpen = false"
        @keydown.escape.window="isOpen = false">
        @if($notifications->isNotEmpty() && ! $isLoading)
            @foreach($notifications as $notification)
                <li>
                    <a
                        class="flex hover:bg-gray-100 transition duration-150 ease-in px-5 py-3"
                        wire:click.prevent="markAsRead('{{ $notification->id }}')"
                    >
                        <img src="{{ $notification->data['user_avatar'] }}" class="rounded-xl w-10 h-10" alt="avatar">
                        <div class="ml-4">
                            <div class="line-clamp-6">
                                <span class="font-semibold">{{ $notification->data['user_name'] }}</span>
                                commented on
                                <span class="font-semibold">{{ $notification->data['idea_title'] }}"</span>:
                                <span class="font-semibold">"{{ $notification->data['comment_body'] }}""</span>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">{{ $notification->created_at->diffForHumans() }}"</div>
                        </div>
                    </a>
                </li>
            @endforeach
            <li class="border-t border-gray-300 text-center">
                <button
                    wire:click="markAllAsRead"
                    @click="isOpen = false"
                    href="#"
                    class="block w-full font-semibold hover:bg-gray-100 transition duration-150 ease-in px-5 py-3">
                    Mark all as read
                </button>
            </li>
        @elseif ($isLoading)
            @foreach(range(1,3) as $i)
                <li class="animate-pulse flex items-center transition duration-150 ease-in px-5 py-3">
                    <div class="bg-gray-200 rounded-xl w-10 h-10"></div>
                    <div class="flex-1 ml-4 space-y-2">
                        <div class="bg-gray-200 w-full rounded h-4"></div>
                        <div class="bg-gray-200 w-full rounded h-4"></div>
                        <div class="bg-gray-200 w-1/2 rounded h-4"></div>
                    </div>
                </li>
            @endforeach
        @else
            <li class="mx-auto w-45">
                <img src="{{ asset('img/no-ideas.svg') }}" alt="No Notifications" class="mx-auto mix-blend-luminosity">
                <div class="text-gray-400 text-center font-bold">No new notifications yet....</div>
            </li>
        @endif
    </ul>
</div>