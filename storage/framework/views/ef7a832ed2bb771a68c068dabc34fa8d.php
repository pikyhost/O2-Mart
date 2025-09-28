<div 
x-on:keydown.up.prevent="$focus.wrap().previous()"
x-on:keydown.down.prevent="$focus.wrap().next()"
x-on:focus-first-element.window="($el.querySelector('.fi-global-search-result-link')?.focus())"
    >
    <div class="summary-wrapper">
        <div x-show="search_history.length > 0">
            <?php if (isset($component)) { $__componentOriginal83707d2ea1c571500fb4144b10d5603e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal83707d2ea1c571500fb4144b10d5603e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.summary.title','data' => ['title' => 'recent']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.summary.title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'recent']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal83707d2ea1c571500fb4144b10d5603e)): ?>
<?php $attributes = $__attributesOriginal83707d2ea1c571500fb4144b10d5603e; ?>
<?php unset($__attributesOriginal83707d2ea1c571500fb4144b10d5603e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal83707d2ea1c571500fb4144b10d5603e)): ?>
<?php $component = $__componentOriginal83707d2ea1c571500fb4144b10d5603e; ?>
<?php unset($__componentOriginal83707d2ea1c571500fb4144b10d5603e); ?>
<?php endif; ?>
            <ul x-animate>
                <template x-for="(result,index) in search_history ">
                    <?php if (isset($component)) { $__componentOriginal4f766de0c6d45c5aa9a658cb094647a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.summary.item','data' => ['xBind:key' => 'index']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.summary.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-bind:key' => 'index']); ?>
                         <?php $__env->slot('slot', null, []); ?> 
                            <span x-html="result.item">
                            </span>
                         <?php $__env->endSlot(); ?>
    
                         <?php $__env->slot('actions', null, []); ?> 
                            <?php if (isset($component)) { $__componentOriginal59a1b5e5a955254ded729f78ce0291a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.action-button','data' => ['title' => 'delete','clickFunction' => 'deleteFromHistory(result.item, result.group)','icon' => 'x']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'delete','clickFunction' => 'deleteFromHistory(result.item, result.group)','icon' => 'x']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $attributes = $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $component = $__componentOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
    
                            <?php if (isset($component)) { $__componentOriginal59a1b5e5a955254ded729f78ce0291a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.action-button','data' => ['title' => 'favorite this item','clickFunction' => 'addToFavorites(result.item, result.group, result.url)','icon' => 'favorite']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'favorite this item','clickFunction' => 'addToFavorites(result.item, result.group, result.url)','icon' => 'favorite']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $attributes = $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $component = $__componentOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
    
                         <?php $__env->endSlot(); ?>
    
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0)): ?>
<?php $attributes = $__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0; ?>
<?php unset($__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f766de0c6d45c5aa9a658cb094647a0)): ?>
<?php $component = $__componentOriginal4f766de0c6d45c5aa9a658cb094647a0; ?>
<?php unset($__componentOriginal4f766de0c6d45c5aa9a658cb094647a0); ?>
<?php endif; ?>
                </template>
            </ul>
        </div>
        <div x-show="favorite_items.length > 0">
            <?php if (isset($component)) { $__componentOriginal83707d2ea1c571500fb4144b10d5603e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal83707d2ea1c571500fb4144b10d5603e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.summary.title','data' => ['title' => 'favorites']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.summary.title'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'favorites']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal83707d2ea1c571500fb4144b10d5603e)): ?>
<?php $attributes = $__attributesOriginal83707d2ea1c571500fb4144b10d5603e; ?>
<?php unset($__attributesOriginal83707d2ea1c571500fb4144b10d5603e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal83707d2ea1c571500fb4144b10d5603e)): ?>
<?php $component = $__componentOriginal83707d2ea1c571500fb4144b10d5603e; ?>
<?php unset($__componentOriginal83707d2ea1c571500fb4144b10d5603e); ?>
<?php endif; ?>
            <ul x-animate>
                <template x-for="(result,index) in favorite_items ">
                    <?php if (isset($component)) { $__componentOriginal4f766de0c6d45c5aa9a658cb094647a0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.summary.item','data' => ['xBind:key' => 'index']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.summary.item'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['x-bind:key' => 'index']); ?>
                         <?php $__env->slot('slot', null, []); ?> 
                            <span x-html="result.item">
                            </span>
                         <?php $__env->endSlot(); ?>
    
                         <?php $__env->slot('actions', null, []); ?> 
                            <?php if (isset($component)) { $__componentOriginal59a1b5e5a955254ded729f78ce0291a1 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.action-button','data' => ['title' => 'delete','clickFunction' => 'deleteFromFavorites(
                                    result.item,
                                    result.group
                                    )','icon' => 'x']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.action-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'delete','clickFunction' => 'deleteFromFavorites(
                                    result.item,
                                    result.group
                                    )','icon' => 'x']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $attributes = $__attributesOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__attributesOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1)): ?>
<?php $component = $__componentOriginal59a1b5e5a955254ded729f78ce0291a1; ?>
<?php unset($__componentOriginal59a1b5e5a955254ded729f78ce0291a1); ?>
<?php endif; ?>
                         <?php $__env->endSlot(); ?>
    
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0)): ?>
<?php $attributes = $__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0; ?>
<?php unset($__attributesOriginal4f766de0c6d45c5aa9a658cb094647a0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4f766de0c6d45c5aa9a658cb094647a0)): ?>
<?php $component = $__componentOriginal4f766de0c6d45c5aa9a658cb094647a0; ?>
<?php unset($__componentOriginal4f766de0c6d45c5aa9a658cb094647a0); ?>
<?php endif; ?>
                </template>
            </ul>
        </div>
    </div>
</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/charrafimed/global-search-modal/src/../resources/views/components/search/summary/summary-wrapper.blade.php ENDPATH**/ ?>