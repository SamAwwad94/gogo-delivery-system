<header class="sticky top-0 z-40 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-backdrop-blur:bg-background/60">
    <div class="container flex h-14 items-center">
        <!-- Mobile menu button -->
        <button type="button" class="lg:hidden mr-2 flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground" id="mobile-menu-button" aria-label="Menu">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2 mr-4">
            <img src="{{ \App\Helpers\LogoHelper::getLogo('site_logo') }}" alt="{{ config('app.name') }}" class="h-8 w-auto hidden dark:block">
            <img src="{{ \App\Helpers\LogoHelper::getLogo('site_dark_logo') }}" alt="{{ config('app.name') }}" class="h-8 w-auto block dark:hidden">
            <span class="hidden font-bold sm:inline-block">{{ config('app.name') }}</span>
        </a>
        
        <!-- Search -->
        <div class="flex-1 flex items-center">
            <form action="#" method="GET" class="hidden md:flex">
                <div class="relative">
                    <i class="fas fa-search absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground"></i>
                    <input type="search" placeholder="Search..." class="h-9 w-[300px] rounded-md border border-input bg-background pl-8 pr-4 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                </div>
            </form>
        </div>
        
        <!-- Right side navigation -->
        <div class="flex items-center space-x-1">
            <!-- Language selector -->
            <div class="relative" data-dropdown>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground" data-dropdown-trigger aria-label="Select language">
                    <i class="fas fa-globe"></i>
                </button>
                <div class="absolute right-0 top-full mt-1 w-48 rounded-md border border-border bg-popover p-1 shadow-md" data-dropdown-content>
                    @foreach(getLanguageList() as $language)
                        <a href="{{ route('switch-language', ['locale' => $language->code]) }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ app()->getLocale() == $language->code ? 'bg-accent text-accent-foreground' : '' }}">
                            <span>{{ $language->name }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="relative" data-dropdown>
                <button type="button" class="flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground" data-dropdown-trigger aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <span class="absolute top-1 right-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-medium text-destructive-foreground">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @endif
                </button>
                <div class="absolute right-0 top-full mt-1 w-80 rounded-md border border-border bg-popover p-1 shadow-md" data-dropdown-content>
                    <div class="flex items-center justify-between px-2 py-1.5">
                        <h3 class="text-sm font-medium">Notifications</h3>
                        @if(auth()->user()->unreadNotifications->count() > 0)
                            <a href="{{ route('notification.read-all') }}" class="text-xs text-primary hover:underline">Mark all as read</a>
                        @endif
                    </div>
                    <div class="max-h-[300px] overflow-y-auto">
                        @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                            <a href="{{ route('notification.read', ['id' => $notification->id]) }}" class="flex items-start space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground {{ $notification->read_at ? '' : 'bg-muted' }}">
                                <div class="flex-shrink-0 mt-0.5">
                                    <i class="fas fa-{{ $notification->data['icon'] ?? 'bell' }} text-{{ $notification->data['color'] ?? 'primary' }}"></i>
                                </div>
                                <div class="flex-1 space-y-1">
                                    <p class="text-sm font-medium">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $notification->data['message'] ?? '' }}</p>
                                    <p class="text-xs text-muted-foreground">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="px-2 py-4 text-center text-sm text-muted-foreground">
                                No notifications
                            </div>
                        @endforelse
                    </div>
                    <div class="border-t border-border px-2 py-1.5">
                        <a href="{{ route('notification.index') }}" class="block text-center text-xs text-primary hover:underline">View all notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- Theme toggle -->
            <button type="button" class="flex h-9 w-9 items-center justify-center rounded-md border border-input bg-background text-muted-foreground hover:bg-accent hover:text-accent-foreground" data-theme-toggle aria-label="Toggle theme">
                <i class="fas fa-sun block dark:hidden"></i>
                <i class="fas fa-moon hidden dark:block"></i>
            </button>
            
            <!-- User menu -->
            <div class="relative" data-dropdown>
                <button type="button" class="flex h-9 items-center space-x-2 rounded-md border border-input bg-background px-2 text-muted-foreground hover:bg-accent hover:text-accent-foreground" data-dropdown-trigger>
                    <img src="{{ auth()->user()->profile_image ?? asset('images/user/user.png') }}" alt="{{ auth()->user()->name }}" class="h-6 w-6 rounded-full">
                    <span class="hidden text-sm font-medium md:inline-block">{{ auth()->user()->name }}</span>
                </button>
                <div class="absolute right-0 top-full mt-1 w-56 rounded-md border border-border bg-popover p-1 shadow-md" data-dropdown-content>
                    <div class="px-2 py-1.5 text-sm font-medium">
                        {{ auth()->user()->name }}
                        <div class="text-xs font-normal text-muted-foreground">{{ auth()->user()->email }}</div>
                    </div>
                    <div class="border-t border-border"></div>
                    <a href="{{ route('user.profile') }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                        <i class="fas fa-user w-4 h-4"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('setting.index') }}" class="flex items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                        <i class="fas fa-cog w-4 h-4"></i>
                        <span>Settings</span>
                    </a>
                    <div class="border-t border-border"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center space-x-2 rounded-sm px-2 py-1.5 text-sm hover:bg-accent hover:text-accent-foreground">
                            <i class="fas fa-sign-out-alt w-4 h-4"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
