@extends('admin.layouts.app')

@section('content')

<div class="page-heading d-flex justify-content-between align-items-center mb-4">

    <h2>Categories</h2>

    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        Add Category
    </a>

</div>



<div class="card">

    <div class="card-body">

        <table class="desktop-data-table table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th width="80">ID</th>

                    <th>Name</th>

                    <th>Description</th>

                    <th width="120">Status</th>

                    <th width="210">Actions</th>

                </tr>

            </thead>

            <tbody>

                @forelse($categories as $category)

                <tr>

                    <td>{{ $category->id }}</td>

                    <td>{{ $category->name }}</td>

                    <td>{{ $category->description }}</td>

                    <td>

                        @if($category->is_active)

                            <span class="badge bg-success">
                                Active
                            </span>

                        @else

                            <span class="badge bg-danger">
                                Inactive
                            </span>

                        @endif

                    </td>

                    <td>
                        <div class="table-actions">

                        <a href="{{ route('admin.categories.edit', $category) }}"
                           class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form id="delete-category-{{ $category->id }}"
                              action="{{ route('admin.categories.destroy', $category) }}"
                              method="POST"
                              class="d-inline"
                              data-category-name="{{ $category->name }}"
                              data-product-count="{{ $category->products_count }}">

                            @csrf
                            @method('DELETE')

                            <button type="button" class="btn btn-danger btn-sm"
                                    onclick="confirmCategoryDeletion({{ $category->id }})">

                                Delete

                            </button>

                        </form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="5" class="text-center">

                        No categories found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mobile-data-list">
            @forelse($categories as $category)
                <article class="mobile-data-card card border shadow-sm p-3">
                    <div class="d-flex justify-content-between gap-3 mb-3">
                        <div><div class="data-card-label">Category</div><div class="data-card-value">{{ $category->name }}</div></div>
                        @if($category->is_active)<span class="badge bg-success align-self-start">Active</span>@else<span class="badge bg-danger align-self-start">Inactive</span>@endif
                    </div>
                    <p class="small text-muted mb-3">{{ $category->description ?: 'No description provided.' }}</p>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm flex-grow-1">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm flex-grow-1" onclick="confirmCategoryDeletion({{ $category->id }})">Delete</button>
                    </div>
                </article>
            @empty
                <p class="text-center text-muted py-3 mb-0">No categories found.</p>
            @endforelse
        </div>

        <div class="mt-3">

            {{ $categories->links() }}

        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
function confirmCategoryDeletion(id) {
    const form = document.getElementById(`delete-category-${id}`);
    const productCount = Number(form.dataset.productCount);
    const categoryName = form.dataset.categoryName;

    if (productCount === 0) {
        Swal.fire({
            title: 'Delete category?',
            text: `"${categoryName}" will be permanently deleted.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });

        return;
    }

    Swal.fire({
        title: 'This category contains products',
        text: `"${categoryName}" contains ${productCount} product${productCount === 1 ? '' : 's'}. Deleting it will permanently delete those products too.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Delete category and products',
        cancelButtonText: 'Cancel',
    }).then((result) => {
        if (result.isConfirmed) {
            const forceDelete = document.createElement('input');
            forceDelete.type = 'hidden';
            forceDelete.name = 'force_delete_products';
            forceDelete.value = '1';
            form.appendChild(forceDelete);
            form.submit();
        }
    });
}
</script>
@endpush
