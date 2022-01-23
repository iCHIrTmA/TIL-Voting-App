<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Voting App</title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <livewire:styles />

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}" defer></script>
    </head>
    <body class="font-sans bg-gray-background text-gray-900 text-sm">
        <div class="header flex flex-col md:flex-row items-center justify-between px-8 py-4">
            <a href="#"><img src="{{ asset('logo-bg-gray.svg')}}" class="w-32" alt=""></a>
            <div class="flex items-center mt-2 md:mt-0">
                @if (Route::has('login'))
                    <div class="px-6 py-4">
                        @auth
                            <div class="flex item-center space-x-4">
                                <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                    <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                        this.closest('form').submit();">
                                        {{ __('Log Out') }}
                                    </a>
                                </form>
                                <div x-data="{ isOpen: false }">
                                    <button @click="isOpen = !isOpen" class="relative">
                                        <svg class="h-7 w-7 text-gray-400" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                        </svg>
                                        <div class="absolute rounded-full bg-red text-white text-xxs w-4 h-4 flex justify-center items-center top-0 right-0">
                                            8
                                        </div>
                                    </button>
                                    <ul
                                    class="absolute w-76 md:w-96 text-left text-gray-700 bg-white shadow-dialog rounded-xl max-h-104 overflow-y-auto z-10 right-21 md:right-12"
                                    x-cloak
                                    x-show.transition.origin.top="isOpen"
                                    @click.away = "isOpen = false"
                                    @keydown.escape.window="isOpen = false">
                                        <li>
                                            <a
                                                href="#"
                                                @click.prevent="$dispatch('edit-modal')"
                                                class="flex hover:bg-gray-100 transition duration-150 ease-in px-5 py-3"
                                            >
                                                <img src="{{ auth()->user()->getAvatar() }}" class="rounded-xl w-10 h-10" alt="avatar">
                                                <div class="ml-4">
                                                    <div class="line-clamp-6">
                                                        <span class="font-semibold">a user</span>
                                                        commented on
                                                        <span class="font-semibold">This is my idea</span>:
                                                        <span class="font-semibold">"Lauren Dimsum. Lauren Dimsum.
                                                            Lauren Dimsum. Lauren Dimsum. Lauren Dimsum"</span>
                                                    </div>
                                                    <div class="text-xs text-gray-500 mt-2">1 hour ago</div>
                                                </div>
                                            </a>
                                        </li>
                                        <li class="border-t border-gray-300 text-center">
                                            <button
                                                href="#"
                                                class="block w-full font-semibold hover:bg-gray-100 transition duration-150 ease-in px-5 py-3">
                                                Mark all as read
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Log in</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif
                <div>
                    <a href="#">
                        <img src="https://www.gravatar.com/avatar/00000000000000000000000000000000?d=mp" alt="avatar" class="w-10 h-10 rounded-full">
                    </a>
                </div>
            </div>
        </div>

        <main class="container mx-auto max-w-custom flex flex-col md:flex-row">
            <div class="w-70 md:w-1/5 mx-auto md:mx-0 md:mr-5">
                <div class="bg-white md:sticky md:top-8 border-2 border-blue rounded-xl mt-16" 
                    style="border-image-source: linear-gradient(to bottom, rgba(50, 138, 241, 0.22), rgba(99, 123, 255, 0));
                        border-image-slice: 1;
                        background-image: linear-gradient(to bottom, #ffffff, #ffffff), linear-gradient(to bottom, rgba(50, 138, 241, 0.22), rgba(99, 123, 255, 0));
                        background-origin: border-box;
                        background-clip: content-box, border-box;"
                >
                    <div class="text-center px-6 py-2 pt-6">
                        <h3 class="font-semibold text-base">Add an idea</h3>
                        @auth
                            <p class="text-xs mt-4">Let us know what you would like and we'll take a look over!</p>
                        @else
                            <p class="text-xs mt-4">Please login to create an idea.</p>
                        @endauth
                    </div>
                    @auth
                        <livewire:create-idea/>
                    @else
                        <div class="my-6 text-center">
                            <a
                               href="{{ route('login') }}"
                               class="inline-block items-center justify-center w-1/2 h-11 text-xs bg-blue text-white
                                    font-semibold rounded-xl border border-blue hover:bg-blue-hover transition
                                    duration-150 ease-in px-6 py-3"
                            >
                                Login
                            </a>
                            <a 
                                href="{{ route('register')}}"
                                class="inline-block items-center justify-center w-1/2 h-11 text-xs bg-gray-200
                                    font-semibold rounded-xl border border-gray-200 hover:border-gray-400
                                    transition duration-150 ease-in px-6 py-3 mt-4"
                            >
                                Register
                            </a>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="w-full px-2 md:px-0 md:w-175">
                <livewire:status-filters/>

                <div class="mt-8">
                    {{ $slot }}
                </div>

            </div>
        </main>
        @if (session('success_message'))
            <x-notification-success :redirect="true" messageToDisplay="{{ session('success_message') }}"/>
        @endif

        <livewire:scripts />
    </body>
</html>
