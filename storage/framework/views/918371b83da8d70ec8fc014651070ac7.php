<?php
    use Filament\Support\Facades\FilamentView;
    use Filament\Support\Facades\FilamentAsset;

    $prefixLabel = $getPrefixLabel();
    $suffixLabel = $getSuffixLabel();
    $prefixIcon = $getPrefixIcon();
    $suffixIcon = $getSuffixIcon();
    $prefixActions = $getPrefixActions();
    $suffixActions = $getSuffixActions();
?>

<?php if (isset($component)) { $__componentOriginal511d4862ff04963c3c16115c05a86a9d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal511d4862ff04963c3c16115c05a86a9d = $attributes; } ?>
<?php $component = Illuminate\View\DynamicComponent::resolve(['component' => $getFieldWrapperView()] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dynamic-component'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\DynamicComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['field' => $field]); ?>
    <div
        wire:key="<?php echo e($getTreeKey()); ?>"
        wire:ignore
        x-ignore
        <?php if(FilamentView::hasSpaMode(url()->current())): ?>
            ax-load="visible"
        <?php else: ?>
            ax-load
        <?php endif; ?>
        ax-load-css="<?php echo e(FilamentAsset::getStyleHref('filament-select-tree-styles', package: 'codewithdennis/filament-select-tree')); ?>"
        ax-load-src="<?php echo e(FilamentAsset::getAlpineComponentSrc('filament-select-tree', package: 'codewithdennis/filament-select-tree')); ?>"
        x-data="selectTree({
            name: <?php echo \Illuminate\Support\Js::from($getName())->toHtml() ?>,
            state: $wire.<?php echo e($applyStateBindingModifiers("\$entangle('{$getStatePath()}')")); ?>,
            options: <?php echo \Illuminate\Support\Js::from($getTree())->toHtml() ?>,
            searchable: <?php echo \Illuminate\Support\Js::from($isSearchable())->toHtml() ?>,
            showCount: <?php echo \Illuminate\Support\Js::from($getWithCount())->toHtml() ?>,
            placeholder: <?php echo \Illuminate\Support\Js::from($getPlaceholder())->toHtml() ?>,
            disabledBranchNode: <?php echo \Illuminate\Support\Js::from(!$getEnableBranchNode())->toHtml() ?>,
            disabled: <?php echo \Illuminate\Support\Js::from($isDisabled())->toHtml() ?>,
            isSingleSelect: <?php echo \Illuminate\Support\Js::from(!$getMultiple())->toHtml() ?>,
            isIndependentNodes: <?php echo \Illuminate\Support\Js::from($getIndependent())->toHtml() ?>,
            showTags: <?php echo \Illuminate\Support\Js::from($getMultiple())->toHtml() ?>,
            alwaysOpen: <?php echo \Illuminate\Support\Js::from($getAlwaysOpen())->toHtml() ?>,
            clearable: <?php echo \Illuminate\Support\Js::from($getClearable())->toHtml() ?>,
            emptyText: <?php echo \Illuminate\Support\Js::from($getEmptyLabel())->toHtml() ?>,
            expandSelected: <?php echo \Illuminate\Support\Js::from($getExpandSelected())->toHtml() ?>,
            grouped: <?php echo \Illuminate\Support\Js::from($getGrouped())->toHtml() ?>,
            openLevel: <?php echo \Illuminate\Support\Js::from($getDefaultOpenLevel())->toHtml() ?>,
            direction: <?php echo \Illuminate\Support\Js::from($getDirection())->toHtml() ?>,
            rtl: <?php echo \Illuminate\Support\Js::from(__('filament-panels::layout.direction') === 'rtl')->toHtml() ?>,
        })"
    >
        <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['suffix' => $suffixLabel,'prefix' => $prefixLabel,'prefixIcon' => $prefixIcon,'suffixIcon' => $suffixIcon,'disabled' => $isDisabled(),'prefixActions' => $prefixActions,'suffixActions' => $suffixActions]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['suffix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($suffixLabel),'prefix' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($prefixLabel),'prefix-icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($prefixIcon),'suffix-icon' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($suffixIcon),'disabled' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($isDisabled()),'prefix-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($prefixActions),'suffix-actions' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($suffixActions)]); ?>
            <div x-ref="tree"></div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $attributes = $__attributesOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__attributesOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal505efd9768415fdb4543e8c564dad437)): ?>
<?php $component = $__componentOriginal505efd9768415fdb4543e8c564dad437; ?>
<?php unset($__componentOriginal505efd9768415fdb4543e8c564dad437); ?>
<?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $attributes = $__attributesOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__attributesOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal511d4862ff04963c3c16115c05a86a9d)): ?>
<?php $component = $__componentOriginal511d4862ff04963c3c16115c05a86a9d; ?>
<?php unset($__componentOriginal511d4862ff04963c3c16115c05a86a9d); ?>
<?php endif; ?>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/codewithdennis/filament-select-tree/src/../resources/views/select-tree.blade.php ENDPATH**/ ?>