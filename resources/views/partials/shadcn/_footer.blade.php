<footer class="border-t border-border/40 bg-background py-4 px-4 sm:px-6 lg:px-8 ml-0 lg:ml-64 transition-all duration-300">
    <div class="container mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-sm text-muted-foreground">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
            </div>
            <div class="flex items-center space-x-4">
                <a href="mailto:{{ SettingData('app_content', 'contact_email') ?? 'support@gogo.delivery' }}" class="text-sm text-muted-foreground hover:text-foreground">
                    <i class="fas fa-envelope mr-1"></i>
                    {{ SettingData('app_content', 'contact_email') ?? 'support@gogo.delivery' }}
                </a>
                <a href="tel:{{ SettingData('app_content', 'contact_number') ?? '00961 03 900 270' }}" class="text-sm text-muted-foreground hover:text-foreground">
                    <i class="fas fa-phone mr-1"></i>
                    {{ SettingData('app_content', 'contact_number') ?? '00961 03 900 270' }}
                </a>
            </div>
        </div>
    </div>
</footer>
