<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'header'=>null,
    'footer'=>null
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'header'=>null,
    'footer'=>null
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php use \Filament\Support\Facades\FilamentAsset; ?>
<?php use \Filament\Support\Enums\MaxWidth; ?>

<?php
    $isClosedByClickingAway = $this->getConfigs()->isClosedByClickingAway();
    $isClosedByEscaping = $this->getConfigs()->isClosedByEscaping();
    $hasCloseButton=$this->getConfigs()->hasCloseButton();
    $isSwappableOnMobile= $this->getConfigs()->isSwappableOnMobile();
    $isSlideOver = $this->getConfigs()->isSlideOver();
    $maxWidth=$this->getConfigs()->getMaxWidth();
    $position = $this->getConfigs()->getPosition();
    $top = $position?->getTop() ?: ($isSlideOver ? '0px' : '100px');
    $left = $position?->getLeft() ?? '0';
    $right = $position?->getRight() ?? '0';
    $bottom = $position?->getBottom() ?? '0';
?>

<div 
    class="<?php echo \Illuminate\Support\Arr::toCssClasses(['flex justify-center ltr:text-left rtl:text-right']); ?>" 
>
    <div 
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'fixed inset-0 z-40 overflow-y-hidden',
            'sm:pt-0'=> !$isSlideOver
        ]); ?>" 
        role="dialog" 
        aria-modal="true" 
        style="display: none"
        x-show="$store.globalSearchModalStore.isOpen"
        
        <?php if($isClosedByEscaping): ?>
             x-on:keydown.escape.window="$store.globalSearchModalStore.hideModal()" 
        <?php endif; ?>
        x-id="['modal-title']" 
        x-bind:aria-labelledby="$id('modal-title')">

        <!-- Overlay -->
        <div 
        class="<?php echo \Illuminate\Support\Arr::toCssClasses([
          'global-search-modal-overlay fixed inset-0 bg-black bg-opacity-60 backdrop-blur-lg'
        ]); ?>"
        x-show="$store.globalSearchModalStore.isOpen"
        x-transition.opacity
        
        >
        </div>

        <!-- Panel -->
        <div class="global-search-modal-panel">
            <div 
                class="relative flex min-h-screen items-center justify-center p-4" 
                x-show="$store.globalSearchModalStore.isOpen"
                x-transition 
                
                <?php if($isClosedByClickingAway): ?> 
                    x-on:click="$store.globalSearchModalStore.hideModal()" 
                <?php endif; ?>
                >
                <div
                    <?php if(blank($position)): ?>
                        style="<?php echo \Illuminate\Support\Arr::toCssStyles([
                                "top: 100px;" => !$isSlideOver,
                                "top: 0;" => $isSlideOver,
                                "height:screen;"=>$isSlideOver
                            ]) ?>"
                    <?php else: ?>
                        style="
                            top: <?php echo e($top); ?>;
                            left: <?php echo e($left); ?>;
                            right: <?php echo e($right); ?>;
                            bottom: <?php echo e($bottom); ?>;
                            "
                    <?php endif; ?>
                    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        'absolute py-1 px-0.5 shadow-lg  dark:bg-gray-900  bg-white',
                        'inset-y-0 overflow-y-auto  rounded right-0 max-w-2xl w-full sm:w-1/2' => $isSlideOver,
                        'inset-x-0 w-full rounded-xl mx-auto mx-2' => !$isSlideOver,
                        match ($maxWidth) {
                            MaxWidth::ExtraSmall => 'max-w-xs',
                            MaxWidth::Small => 'max-w-sm',
                            MaxWidth::Medium => 'max-w-md',
                            MaxWidth::Large => 'max-w-lg',
                            MaxWidth::ExtraLarge => 'max-w-xl',
                            MaxWidth::TwoExtraLarge => 'max-w-2xl',
                            MaxWidth::ThreeExtraLarge => 'max-w-3xl',
                            MaxWidth::FourExtraLarge => 'max-w-4xl',
                            MaxWidth::FiveExtraLarge => 'max-w-5xl',
                            MaxWidth::SixExtraLarge => 'max-w-6xl',
                            MaxWidth::SevenExtraLarge => 'max-w-7xl',
                            MaxWidth::Full => 'max-w-full',
                            MaxWidth::MinContent => 'max-w-min',
                            MaxWidth::MaxContent => 'max-w-max',
                            MaxWidth::FitContent => 'max-w-fit',
                            MaxWidth::Prose => 'max-w-prose',
                            MaxWidth::ScreenSmall => 'max-w-screen-sm',
                            MaxWidth::ScreenMedium => 'max-w-screen-md',
                            MaxWidth::ScreenLarge => 'max-w-screen-lg',
                            MaxWidth::ScreenExtraLarge => 'max-w-screen-xl',
                            MaxWidth::ScreenTwoExtraLarge => 'max-w-screen-2xl',
                            MaxWidth::Screen => 'fixed inset-0',
                            default => "max-w-2xl",
                        },
                    ]); ?>" 
                    x-on:click.stop
                    x-trap.noscroll.inert="$store.globalSearchModalStore.isOpen"
                    >
                    <div
                        x-ignore
                        ax-load
                        ax-load-src="<?php echo e(FilamentAsset::getAlpineComponentSrc('global-search-modal-swappable', 'charrafimed/global-search-modal')); ?>"
                        x-data="swappable" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                        ' overflow-y-auto  px-1 py-1 text-center shadow-sm',
                        'rounded-xl mx-2' => !$isSlideOver,
                        'h-[90vh]' => $isSlideOver
                        ]); ?>">
                    <!--[if BLOCK]><![endif]--><?php if($isSwappableOnMobile): ?>
                        <div 
                            x-on:touchstart="handleMovingStart($event)"
                            x-on:touchmove="handleWhileMoving($event)"
                            x-on:touchend="handleMovingEnd()"                            
                            class="absolute sm:hidden top-[-10px] left-0 right-0 h-[50px]">
                            <div class="flex justify-center pt-[12px]">
                                <div class="bg-gray-400 rounded-full w-[10%] h-[5px]"></div>
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                

                        <!-- Content -->
                        <!--[if BLOCK]><![endif]--><?php if(filled($header)): ?>
                            <header class="global-search-modal-header flex sticky top-0 z-30  items-center border-b border-gray-100 dark:border-gray-700 px-2">
                                <?php echo e($header); ?>

                            </header>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <div 
                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'global-search-modal-drop-down',
                                'overflow-auto text-white',
                                'max-h-[60vh]'=>!$isSlideOver,
                                'max-h-full'=>$isSlideOver
                            ]); ?>"
                        >
                            <?php echo e($dropdown); ?>

                        </div>
                    </div>
                    <!--[if BLOCK]><![endif]--><?php if(filled($footer)): ?>
                        <footer
                            class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                                'global-search-modal-footer',
                                "z-30 hidden sm:flex  w-full select-none items-center px-2 py-2 text-center dark:border-gray-700",
                                'relative'=>!$isSlideOver,
                                'sticky bottom-2'=>$isSlideOver,
                                ]); ?>"
                            >
                            <?php echo e($footer); ?>

                        </footer>            
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/charrafimed/global-search-modal/src/../resources/views/components/modal/index.blade.php ENDPATH**/ ?>