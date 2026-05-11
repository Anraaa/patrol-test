<?php
    $statePaths = $getStatePath();
    $isDisabled = $isDisabled();
    $files = (array) $getState();
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

    <div class="space-y-4">
        
        <div class="flex flex-col sm:flex-row gap-2">
            
            <button
                type="button"
                onclick="document.getElementById('camera-input').click()"
                :disabled="<?php echo e($isDisabled ? 'true' : 'false'); ?>"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-blue-300 dark:border-blue-600 bg-blue-50 dark:bg-blue-950/30 text-blue-700 dark:text-blue-300 font-semibold hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <polyline points="9 22 9 12 15 12 15 22" />
                </svg>
                <span>📷 Ambil Foto Langsung</span>
            </button>

            
            <button
                type="button"
                onclick="document.getElementById('gallery-input').click()"
                :disabled="<?php echo e($isDisabled ? 'true' : 'false'); ?>"
                class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-lg border-2 border-emerald-300 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-950/30 text-emerald-700 dark:text-emerald-300 font-semibold hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 0 1 2.828 0L16 16m-2-2l1.586-1.586a2 2 0 0 1 2.828 0L20 14m-6-6h.01M6 20h12a2 2 0 0 0 2-2V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z" />
                </svg>
                <span>🖼️ Pilih dari Galery</span>
            </button>
        </div>

        
        <input
            id="camera-input"
            type="file"
            accept="image/*"
            capture="environment"
            <?php echo e($isDisabled ? 'disabled' : ''); ?>

            wire:model.live="<?php echo e($statePaths); ?>"
            class="hidden">

        <input
            id="gallery-input"
            type="file"
            accept="image/*"
            multiple
            <?php echo e($isDisabled ? 'disabled' : ''); ?>

            wire:model.live="<?php echo e($statePaths); ?>"
            class="hidden">

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(count($files) > 0): ?>
        <div class="space-y-2">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                Foto yang dipilih (<?php echo e(count($files)); ?>/5)
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $files; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $file): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative group">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(is_string($file)): ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(str_starts_with($file, 'http') || str_starts_with($file, '/')): ?>
                        <img
                            src="<?php echo e(str_starts_with($file, 'http') ? $file : \Storage::url($file)); ?>"
                            alt="Photo <?php echo e($index + 1); ?>"
                            class="w-full h-24 object-cover rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                        <?php else: ?>
                        <div class="w-full h-24 bg-gray-200 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm flex items-center justify-center">
                            <span class="text-xs text-gray-500"><?php echo e($file); ?></span>
                        </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php else: ?>
                    <div class="w-full h-24 bg-blue-100 dark:bg-blue-900 rounded-lg border border-blue-200 dark:border-blue-700 shadow-sm flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-6-6.5h.008v.008h-.008v-.008Zm0 6h.008v.008h-.008v-.008Zm6-11.25h.008v.008h-.008v-.008Zm0 6h.008v.008h-.008v-.008Z" />
                        </svg>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <button
                        type="button"
                        wire:click="$parent.$parent.deleteUploadedFile('<?php echo e($statePaths); ?>', <?php echo e($index); ?>)"
                        onclick="Livewire.dispatch('deleteUploadedFile', { statePath: '<?php echo e($statePaths); ?>', index: <?php echo e($index); ?> })"
                        class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full p-1 opacity-0 group-hover:opacity-100 transition-opacity"
                        <?php echo e($isDisabled ? 'disabled' : ''); ?>>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <p class="text-xs text-gray-500 dark:text-gray-400">
            💡 Klik <span class="font-semibold">📷 Ambil Foto Langsung</span> untuk menggunakan kamera perangkat, atau klik <span class="font-semibold">🖼️ Pilih dari Galery</span> untuk memilih dari galeri. Maksimal 5 foto.
        </p>
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


<?php /**PATH /root/gawe/PatrolHR/resources/views/filament/forms/components/photo-capture-field.blade.php ENDPATH**/ ?>