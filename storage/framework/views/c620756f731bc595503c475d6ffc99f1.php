<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
   'placeholder'=>'Search for anything ...',
   'maxlength' => 64
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
   'placeholder'=>'Search for anything ...',
   'maxlength' => 64
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>
<input
    id="search-input"
    type="search"
    aria-autocomplete="both"
    aria-labelledby="search-label"
    aria-activedescendant="search-item-0"
    aria-controls="search-list"
    style="border:none; outline:none"
    wire:model.live.debounce.200ms="search"
    autocomplete="off"
    autocorrect="off"
    x-data="{}"
    x-on:keydown.down.prevent.stop="$dispatch('focus-first-element')"
    autocapitalize="none"
    enterkeyhint="go"
    spellcheck="false"
    placeholder="<?php echo e(__( $placeholder)); ?>"
    x-on:keydown.enter.prevent 
    autofocus="true"
    maxlength="<?php echo e($maxlength); ?>"
    class="<?php echo \Illuminate\Support\Arr::toCssClasses([
      'fi-input block w-full border-none bg-transparent py-1.5 text-base text-gray-950 transition duration-75 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6',
   ]); ?>"
/>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/charrafimed/global-search-modal/src/../resources/views/components/search/input.blade.php ENDPATH**/ ?>