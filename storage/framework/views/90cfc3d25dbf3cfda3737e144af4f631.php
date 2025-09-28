<div
    <?php echo e($attributes
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)); ?>

>
    <?php echo e($getChildComponentContainer()); ?>

</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/filament/infolists/src/../resources/views/components/grid.blade.php ENDPATH**/ ?>