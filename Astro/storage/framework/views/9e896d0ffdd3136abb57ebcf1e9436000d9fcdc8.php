

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><?php echo e(__('Admin Dashboard')); ?></div>

                <div class="card-body">
                    <h4>Categories</h4>
                    <form method="POST" action="<?php echo e(route('admin.categories.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="category_name" class="form-label">Category Name</label>
                            <input id="category_name" type="text" class="form-control" name="name" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </form>

                    <h4 class="mt-4">Subcategories</h4>
                    <form method="POST" action="<?php echo e(route('admin.subcategories.store')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label for="subcategory_name" class="form-label">Subcategory Name</label>
                            <input id="subcategory_name" type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category</label>
                            <select id="category_id" class="form-control" name="category_id" required>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category->id); ?>"><?php echo e($category->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Subcategory</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\nishantg\Downloads\Astro\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>