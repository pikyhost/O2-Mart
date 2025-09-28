<div
    x-data="{}"
    x-on:click="$dispatch('open-modal', { id: 'database-notifications' })"
    <?php echo e($attributes->class(['inline-block'])); ?>

>
    <?php echo e($slot); ?>

</div>
<?php /**PATH /home/mo/code/laravel/o2-mart-back/vendor/filament/notifications/src/../resources/views/components/database/trigger.blade.php ENDPATH**/ ?>