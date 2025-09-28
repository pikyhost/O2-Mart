@php
    $settings = \App\Models\Setting::getAllSettings();
    $locale = app()->getLocale();

    // Use settings directly since they are returned as an array
    $siteName = $settings['site_name'] ?? env('APP_NAME', 'Default Site Name');

    // Translations for the footer
    $footerTexts = [
        'en' => [
            'rights' => 'All rights reserved © :name - :year',
            'made_with' => 'Made with ♥️ by',
        ],
        'ar' => [
            'rights' => 'جميع الحقوق محفوظة © :name - :year',
            'made_with' => 'مصنوع بحب ♥️ بواسطة',
        ],
    ];
@endphp

<div class="mx-auto max-w-7xl px-6 py-4 flex flex-col items-center md:items-start lg:px-8">
    <div class="text-center md:text-left">
        <p class="text-sm leading-5 footer-text">
            {{ __($footerTexts[$locale]['rights'], ['name' => $siteName, 'year' => now()->year]) }}
        </p>
        <p class="text-sm leading-5 footer-text">
            {{ __($footerTexts[$locale]['made_with']) }}
            <a class="footer-link magic-hover" target="_blank" href="https://www.pikyhost.com/">
                PikyHost
            </a>
        </p>
    </div>
</div>


<style>
    :root {
        --footer-text-color: #1e40af; /* Indigo */
        --footer-link-hover-color: #1e3a8a; /* Darker Indigo */
        --footer-divider-color: #64748b; /* Slate */
        --footer-link-gradient: linear-gradient(90deg, #1e40af, #1e3a8a, #64748b);
    }

    .footer-text, .footer-link, .footer-separator {
        color: var(--footer-text-color);
        font-size: 0.875rem;
    }

    .footer-link {
        text-decoration: none;
        font-weight: 500;
        position: relative;
        padding-bottom: 2px;
        transition: color 0.3s ease, border-bottom 0.3s ease;
    }

    .footer-link::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        left: 0;
        bottom: 0;
        background-color: var(--footer-link-hover-color);
        transition: width 0.3s ease;
    }

    .footer-link:hover {
        color: var(--footer-link-hover-color);
    }

    .footer-link:hover::after {
        width: 100%;
    }

    .magic-hover {
        background: var(--footer-link-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        transition: background-position 0.5s ease;
        background-size: 200% 200%;
        background-position: 0% 50%;
    }

    .magic-hover:hover {
        background-position: 100% 50%;
    }
</style>
