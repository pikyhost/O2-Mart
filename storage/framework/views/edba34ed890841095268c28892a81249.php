<div

    <?php if(filament()->isSidebarCollapsibleOnDesktop()): ?>

        x-bind:class="$store.sidebar.isOpen ? 'block' : 'hidden'"

    <?php endif; ?>

>

    <?php if (isset($component)) { $__componentOriginal505efd9768415fdb4543e8c564dad437 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal505efd9768415fdb4543e8c564dad437 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.wrapper','data' => ['class' => 'relative','inlinePrefix' => true,'suffixIcon' => 'heroicon-o-magnifying-glass']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input.wrapper'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'relative','inline-prefix' => true,'suffix-icon' => 'heroicon-o-magnifying-glass']); ?>

        <?php if (isset($component)) { $__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'filament::components.input.index','data' => ['type' => 'text','placeholder' => __('Search'),'xData' => 'sidebarSearch()','xRef' => 'search','xOn:input.debounce.300ms' => 'filterItems($event.target.value)','xOn:keydown.escape' => 'clearSearch','xOn:keydown.meta.j.prevent.document' => '$refs.search.focus()']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('filament::input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'text','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(__('Search')),'x-data' => 'sidebarSearch()','x-ref' => 'search','x-on:input.debounce.300ms' => 'filterItems($event.target.value)','x-on:keydown.escape' => 'clearSearch','x-on:keydown.meta.j.prevent.document' => '$refs.search.focus()']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e)): ?>
<?php $attributes = $__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e; ?>
<?php unset($__attributesOriginal9ad6b66c56a2379ee0ba04e1e358c61e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e)): ?>
<?php $component = $__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e; ?>
<?php unset($__componentOriginal9ad6b66c56a2379ee0ba04e1e358c61e); ?>
<?php endif; ?>




        </kbd>

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



    <script>

        document.addEventListener('alpine:init', () => {

            Alpine.data('sidebarSearch', () => ({

                init() {

                    this.$refs.search.value = ''

                },



                filterItems(searchTerm) {

                    const groups = document.querySelectorAll('.fi-sidebar-nav-groups .fi-sidebar-group')

                    searchTerm = searchTerm.toLowerCase()



                    groups.forEach(group => {

                        const groupButton = group.querySelector('.fi-sidebar-group-button')

                        const groupText = groupButton?.textContent.toLowerCase() || ''

                        const items = group.querySelectorAll('.fi-sidebar-item')

                        let hasVisibleItems = false



                        const groupMatches = groupText.includes(searchTerm)



                        items.forEach(item => {

                            const itemText = item.textContent.toLowerCase()

                            const isVisible = itemText.includes(searchTerm) || groupMatches



                            item.style.display = isVisible ? '' : 'none'

                            if (isVisible) hasVisibleItems = true

                        })



                        group.style.display = (hasVisibleItems || groupMatches) ? '' : 'none'

                    })

                },



                clearSearch() {

                    this.$refs.search.value = ''

                    this.filterItems('')

                }

            }))

        })

    </script>

</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/resources/views/navigation-filter.blade.php ENDPATH**/ ?>