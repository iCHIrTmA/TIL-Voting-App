<div class="comment-container relative bg-white rounded-xl flex transition duration-500 ease-in mt-4">
    <div class="flex flex-col md:flex-row flex-1 px-4 py-6">
        <div class="flex-none">
            <a href="#">
                <img src="{{ $comment->user->getAvatar() }}" alt="avatar" class="w-14 h-14 rounded-xl">
            </a>
        </div>
        <div class="w-full mx-0 md:mx-4">
            <div class="text-gray-600">
                {{ $comment->body }}
            </div>
            <div x-data="{ isOpen: false }"class="flex items-center justify-between mt-6">
                <div class="flex items-center text-xs text-gray-400 font-semibold space-x-2">
                    <div class="font-semibold text-gray-900">{{ $comment->user->name }}</div>
                    <div>&bull;</div>
                    @if($comment->user_id == $ideaUserId)
                        <div class="rounded-full border bg-gray-100 px-3 py-1">OP</div>
                    @endif
                    <div>&bull;</div>
                    <div>{{ $comment->created_at->diffForHumans() }}</div>
                </div>
                @auth
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <button @click="isOpen = !isOpen"class="relative bg-gray-100 hover:bg-gray-200 border rounded-full h-7 transition duration-150 ease-in py-2 px-3">
                                <svg fill="currentColor" width="24" height="6">
                                    <path d="M2.97.061A2.969 2.969 0 000 3.031 2.968 2.968 0 002.97 6a2.97 2.97 0 100-5.94zm9.184 0a2.97 2.97 0 100 5.939 2.97 2.97 0 100-5.939zm8.877 0a2.97 2.97 0 10-.003 5.94A2.97 2.97 0 0021.03.06z" style="color: rgba(163, 163, 163, .5)">
                                </svg>
                            </button>
                            <ul
                                x-show.transition.origin.top.left="isOpen"
                                @click.away = "isOpen = false"
                                x-cloak
                                @keydown.escape.window="isOpen = false"
                                class="absolute w-36 z-10 text-left font-semibold bg-white shadow-dialog rounded-xl py-3 ml-8 right-0 md:left-0">
                                @can('update', $comment)
                                    <li>
                                        <a
                                            href="#"
                                            @click.prevent=
                                                "isOpen = false
                                                Livewire.emit('setEditComment', {{ $comment->id }})"
                                            class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3"
                                        >
                                            Edit Comment
                                        </a>
                                    </li>
                                @endcan
                                <li><a href="#" class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3">Mark as Spam</a></li>
                                <li><a href="#" class="hover:bg-gray-100 block transition duration-150 ease-in px-5 py-3">Delete Comment</a></li>
                            </ul>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</div> <!-- end comment-container -->