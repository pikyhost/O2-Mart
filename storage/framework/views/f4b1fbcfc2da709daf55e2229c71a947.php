<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'type' => 'button',
    'icon' => null,
    'title' => null, // this is just the native button's title (for accessibilty)
    'clickFunction' => null,
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
    'type' => 'button',
    'icon' => null,
    'title' => null, // this is just the native button's title (for accessibilty)
    'clickFunction' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<button
    type="<?php echo e($type); ?>"
    title="<?php echo e(__($title)); ?>"
    x-on:click.stop="<?php echo e($clickFunction); ?>"
    class="action-button  hover:bg-black/5 rounded-full cursor-pointer p-2 dark:hover:bg-white/10 duration transition"
    >
    <!--[if BLOCK]><![endif]--><?php switch($icon):
        case ('x'): ?>
            <?php if (isset($component)) { $__componentOriginalc601bb33052398e012f7ae630d9eac43 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc601bb33052398e012f7ae630d9eac43 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.icon.x','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::icon.x'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc601bb33052398e012f7ae630d9eac43)): ?>
<?php $attributes = $__attributesOriginalc601bb33052398e012f7ae630d9eac43; ?>
<?php unset($__attributesOriginalc601bb33052398e012f7ae630d9eac43); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc601bb33052398e012f7ae630d9eac43)): ?>
<?php $component = $__componentOriginalc601bb33052398e012f7ae630d9eac43; ?>
<?php unset($__componentOriginalc601bb33052398e012f7ae630d9eac43); ?>
<?php endif; ?>
        <?php break; ?>

        <?php case ('favorite'): ?>
            <?php if (isset($component)) { $__componentOriginalfd09d61a0561a67ffef7433be6fda4d6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalfd09d61a0561a67ffef7433be6fda4d6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.icon.favorite','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::icon.favorite'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalfd09d61a0561a67ffef7433be6fda4d6)): ?>
<?php $attributes = $__attributesOriginalfd09d61a0561a67ffef7433be6fda4d6; ?>
<?php unset($__attributesOriginalfd09d61a0561a67ffef7433be6fda4d6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalfd09d61a0561a67ffef7433be6fda4d6)): ?>
<?php $component = $__componentOriginalfd09d61a0561a67ffef7433be6fda4d6; ?>
<?php unset($__componentOriginalfd09d61a0561a67ffef7433be6fda4d6); ?>
<?php endif; ?>
        <?php break; ?>
    <?php endswitch; ?><!--[if ENDBLOCK]><![endif]-->
    
</button>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/charrafimed/global-search-modal/src/../resources/views/components/search/action-button.blade.php ENDPATH**/ ?>