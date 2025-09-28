<?php use \Filament\Support\Facades\FilamentAsset; ?>
<?php
    use function Filament\Support\prepare_inherited_attributes;
    $placeholder=$this->getConfigs()->getPlaceholder();
    $maxLength=$this->getConfigs()->getSearchInputMaxLength();
    $hasCloseButton=$this->getConfigs()->hasCloseButton();
    $isRetainRecentIfFavorite=$this->getConfigs()->isRetainRecentIfFavorite();
    $maxItemsAllowed = $this->getConfigs()->getMaxItemsAllowed() ?? 10;
    $hasFooterView=$this->getConfigs()->hasFooterView();
    $footerView=$this->getConfigs()->getFooterView();
    $EmptyQueryView=$this->getConfigs()->getEmptyQueryView();
?>
<div>
    <div 
        x-ignore 
        ax-load
        x-load-css="[<?php echo \Illuminate\Support\Js::from(FilamentAsset::getStyleHref('global-search-modal', 'charrafimed/global-search-modal'))->toHtml() ?>]" 
        ax-load-src="<?php echo e(FilamentAsset::getAlpineComponentSrc('global-search-modal-observer', 'charrafimed/global-search-modal')); ?>"
        x-data="observer"
    >
    <?php if (isset($component)) { $__componentOriginalde289b8d6bbe02223587aef5c2d32df3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalde289b8d6bbe02223587aef5c2d32df3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.modal.index','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('header', null, []); ?> 
            <form 
                class="relative flex w-full items-center px-1 py-0.5"
                >
                    <label 
                        class="flex h-4 w-4 items-center justify-center text-gray-300/40 dark:text-white/30"
                        id="search-label" 
                        for="search-input"
                        >
                          <?php if (isset($component)) { $__componentOriginal0fce8fd4f3f7937a542337cc2d58fc5e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0fce8fd4f3f7937a542337cc2d58fc5e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.icon.search','data' => ['wire:loading.class' => 'hidden']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::icon.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['wire:loading.class' => 'hidden']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0fce8fd4f3f7937a542337cc2d58fc5e)): ?>
<?php $attributes = $__attributesOriginal0fce8fd4f3f7937a542337cc2d58fc5e; ?>
<?php unset($__attributesOriginal0fce8fd4f3f7937a542337cc2d58fc5e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0fce8fd4f3f7937a542337cc2d58fc5e)): ?>
<?php $component = $__componentOriginal0fce8fd4f3f7937a542337cc2d58fc5e; ?>
<?php unset($__componentOriginal0fce8fd4f3f7937a542337cc2d58fc5e); ?>
<?php endif; ?>
                          <div class="hidden" wire:loading.class.remove="hidden">
                                <?php if (isset($component)) { $__componentOriginal54f8212977138fdba173b0b2ce1300ee = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal54f8212977138fdba173b0b2ce1300ee = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.icon.loading-indicator','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::icon.loading-indicator'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal54f8212977138fdba173b0b2ce1300ee)): ?>
<?php $attributes = $__attributesOriginal54f8212977138fdba173b0b2ce1300ee; ?>
<?php unset($__attributesOriginal54f8212977138fdba173b0b2ce1300ee); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal54f8212977138fdba173b0b2ce1300ee)): ?>
<?php $component = $__componentOriginal54f8212977138fdba173b0b2ce1300ee; ?>
<?php unset($__componentOriginal54f8212977138fdba173b0b2ce1300ee); ?>
<?php endif; ?>
                          </div>
                    </label>
                    <?php if (isset($component)) { $__componentOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.input','data' => ['placeholder' => $placeholder,'maxlength' => $maxLength]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($placeholder),'maxlength' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($maxLength)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16)): ?>
<?php $attributes = $__attributesOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16; ?>
<?php unset($__attributesOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16)): ?>
<?php $component = $__componentOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16; ?>
<?php unset($__componentOriginal5b47f450d5bc9cd8e895cd9fc7ecfd16); ?>
<?php endif; ?>
            </form>
            <!--[if BLOCK]><![endif]--><?php if($hasCloseButton): ?>
            <button
                type="button"
                x-on:click.stop="$store.globalSearchModalStore.hideModal()"
                class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                    // 'right-0 top-2' => ! $isSlideOver,
                    // 'end-6 top-6' => $isSlideOver,
                ]); ?>"
            >
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
        </button>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
         <?php $__env->endSlot(); ?>
         <?php $__env->slot('dropdown', null, []); ?> 
        <div     
            x-ignore
            ax-load
            ax-load-src="<?php echo e(FilamentAsset::getAlpineComponentSrc('global-search-modal-search', 'charrafimed/global-search-modal')); ?>"
            x-data="searchComponent({
                recentSearchesKey:  <?php echo \Illuminate\Support\Js::from($this->getPanelId() . "_recent_search")->toHtml() ?>,
                favoriteSearchesKey: <?php echo \Illuminate\Support\Js::from( $this->getPanelId() . "_favorites_search")->toHtml() ?>,
                maxItemsAllowed:  <?php echo \Illuminate\Support\Js::from( $maxItemsAllowed)->toHtml() ?>,
                retainRecentIfFavorite : <?php echo \Illuminate\Support\Js::from($isRetainRecentIfFavorite)->toHtml() ?>
            })"
            >
            <!--[if BLOCK]><![endif]--><?php if (! (empty($search))): ?>
                <?php if (isset($component)) { $__componentOriginale1c74538c624595cbacb616a9146e18f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginale1c74538c624595cbacb616a9146e18f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.results','data' => ['results' => $results]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.results'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['results' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($results)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginale1c74538c624595cbacb616a9146e18f)): ?>
<?php $attributes = $__attributesOriginale1c74538c624595cbacb616a9146e18f; ?>
<?php unset($__attributesOriginale1c74538c624595cbacb616a9146e18f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginale1c74538c624595cbacb616a9146e18f)): ?>
<?php $component = $__componentOriginale1c74538c624595cbacb616a9146e18f; ?>
<?php unset($__componentOriginale1c74538c624595cbacb616a9146e18f); ?>
<?php endif; ?>
            <?php else: ?>
                <div
                    class="w-full global-search-modal"
                    >
                    <!--[if BLOCK]><![endif]--><?php if (! (filled($EmptyQueryView))): ?>
                        <div>                            
                            <template x-if="search_history.length <=0 && favorite_items.length <=0">
                                <?php if (isset($component)) { $__componentOriginalca20025d3b3437193ec004b74ba46edd = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalca20025d3b3437193ec004b74ba46edd = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.empty-query-text','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.empty-query-text'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalca20025d3b3437193ec004b74ba46edd)): ?>
<?php $attributes = $__attributesOriginalca20025d3b3437193ec004b74ba46edd; ?>
<?php unset($__attributesOriginalca20025d3b3437193ec004b74ba46edd); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalca20025d3b3437193ec004b74ba46edd)): ?>
<?php $component = $__componentOriginalca20025d3b3437193ec004b74ba46edd; ?>
<?php unset($__componentOriginalca20025d3b3437193ec004b74ba46edd); ?>
<?php endif; ?>
                            </template>
                        </div>
                    <?php else: ?>
                        <div>
                            <template x-if="search_history.length <=0 && favorite_items.length <=0">
                                <div>     
                                    <?php echo $EmptyQueryView->render(); ?>

                                </div>
                            </template>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    <?php if (isset($component)) { $__componentOriginala5d720e82b9ef010351119d08e72fe81 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala5d720e82b9ef010351119d08e72fe81 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.summary.summary-wrapper','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.summary.summary-wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala5d720e82b9ef010351119d08e72fe81)): ?>
<?php $attributes = $__attributesOriginala5d720e82b9ef010351119d08e72fe81; ?>
<?php unset($__attributesOriginala5d720e82b9ef010351119d08e72fe81); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala5d720e82b9ef010351119d08e72fe81)): ?>
<?php $component = $__componentOriginala5d720e82b9ef010351119d08e72fe81; ?>
<?php unset($__componentOriginala5d720e82b9ef010351119d08e72fe81); ?>
<?php endif; ?>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->  
        </div>
         <?php $__env->endSlot(); ?>

        <!--[if BLOCK]><![endif]--><?php if($hasFooterView): ?>
             <?php $__env->slot('footer', null, []); ?> 
                <!--[if BLOCK]><![endif]--><?php if (! (filled($footerView))): ?>
                        <?php if (isset($component)) { $__componentOriginalf9b38aaae2e27749580d8de253289837 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf9b38aaae2e27749580d8de253289837 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'global-search-modal::components.search.footer','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('global-search-modal::search.footer'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf9b38aaae2e27749580d8de253289837)): ?>
<?php $attributes = $__attributesOriginalf9b38aaae2e27749580d8de253289837; ?>
<?php unset($__attributesOriginalf9b38aaae2e27749580d8de253289837); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf9b38aaae2e27749580d8de253289837)): ?>
<?php $component = $__componentOriginalf9b38aaae2e27749580d8de253289837; ?>
<?php unset($__componentOriginalf9b38aaae2e27749580d8de253289837); ?>
<?php endif; ?>    
                <?php else: ?>
                    <?php echo $footerView->render(); ?>

                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
             <?php $__env->endSlot(); ?>
          <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        

     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalde289b8d6bbe02223587aef5c2d32df3)): ?>
<?php $attributes = $__attributesOriginalde289b8d6bbe02223587aef5c2d32df3; ?>
<?php unset($__attributesOriginalde289b8d6bbe02223587aef5c2d32df3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalde289b8d6bbe02223587aef5c2d32df3)): ?>
<?php $component = $__componentOriginalde289b8d6bbe02223587aef5c2d32df3; ?>
<?php unset($__componentOriginalde289b8d6bbe02223587aef5c2d32df3); ?>
<?php endif; ?>    
</div>
</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/charrafimed/global-search-modal/src/../resources/views/components/dialog.blade.php ENDPATH**/ ?>