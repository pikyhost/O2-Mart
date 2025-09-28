@php
    use Illuminate\Support\Facades\Storage;

    $settings = \App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();
    $lightLogoPath = $settings["logo"] ?? null;
    $darkLogoPath = $settings["dark_logo"] ?? null;

    $lightLogo = $lightLogoPath ? Storage::url($lightLogoPath) : null;
    $darkLogo = $darkLogoPath ? Storage::url($darkLogoPath) : null;
@endphp

<div
    x-data="{
        mode: localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'),
        lightLogo: '{{ $lightLogo }}',
        darkLogo: '{{ $darkLogo }}',
        updateMode() {
            this.mode = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
        }
    }"
    x-init="updateMode()"
    @theme-changed.window="updateMode()"
    class="flex items-center"
>
    <img
        :src="mode === 'dark' ? darkLogo : lightLogo"
        alt="Site Logo"
        class="h-10 w-auto"
        x-cloak
    >
</div>
