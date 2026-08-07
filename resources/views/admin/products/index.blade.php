@extends('admin.layouts.app')

@section('content')

<div class="page-heading d-flex justify-content-between align-items-center mb-4">

    <h2>Products</h2>

    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        Add Product
    </a>

</div>



<div class="card">

    <div class="card-body">

        <table class="desktop-data-table table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th width="60">ID</th>

                    <th width="100">Image</th>

                    <th>Product</th>

                    <th>Category</th>

                    <th width="120">Price</th>

                    <th width="100">Stock</th>

                    <th width="120">Status</th>

                    <th width="210">Actions</th>

                </tr>

            </thead>

            <tbody>

                @forelse($products as $product)

                <tr>

                    <td>{{ $product->id }}</td>

                    <td>

                        @if($product->image)

                            <img
                                src="{{ $product->image_url }}"
                                class="img-thumbnail"
                                width="70"
                                height="70">

                        @else

                            <span class="text-muted">
                                No Image
                            </span>

                        @endif

                    </td>

                    <td>

                        <strong>{{ $product->name }}</strong>

                        <br>

                        <small class="text-muted">

                            {{ $product->description }}

                        </small>

                    </td>

                    <td>

                        {{ $product->category->name }}

                    </td>

                    <td>

                        ₱ {{ number_format($product->price, 2) }}

                    </td>

                    <td>

                        {{ $product->stock }}

                    </td>

                    <td>

                        @if($product->is_available)

                            <span class="badge bg-success">

                                Available

                            </span>

                        @else

                            <span class="badge bg-danger">

                                Unavailable

                            </span>

                        @endif

                    </td>

                    <td>
                        <div class="table-actions">

                        <a
                            href="{{ route('admin.products.edit', $product) }}"
                            class="btn btn-warning btn-sm">

                            Edit

                        </a>

                        <form
    id="delete-product-{{ $product->id }}"
    action="{{ route('admin.products.destroy', $product) }}"
    method="POST"
    class="d-inline">

    @csrf
    @method('DELETE')

    <button
        type="button"
        class="btn btn-danger btn-sm"
        onclick="deleteProduct({{ $product->id }})">

        Delete

    </button>

</form>

                        </div>

                    </td>

                </tr>

                @empty

                <tr>

                    <td colspan="8" class="text-center">

                        No products found.

                    </td>

                </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mobile-data-list">
            @forelse($products as $product)
                <article class="mobile-data-card card border shadow-sm p-3">
                    <div class="d-flex gap-3">
                        @if($product->image)<img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="mobile-product-image">@else<div class="mobile-product-image d-grid place-items-center"><i class="bi bi-image text-muted"></i></div>@endif
                        <div class="flex-grow-1 min-w-0">
                            <div class="d-flex justify-content-between gap-2"><strong class="text-break">{{ $product->name }}</strong>@if($product->is_available)<span class="badge bg-success">Available</span>@else<span class="badge bg-danger">Unavailable</span>@endif</div>
                            <div class="small text-muted mt-1">{{ $product->category->name }}</div>
                            <div class="d-flex justify-content-between mt-2"><span class="fw-bold">₱ {{ number_format($product->price, 2) }}</span><span class="small text-muted">Stock: {{ $product->stock }}</span></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm flex-grow-1">Edit</a>
                        <button type="button" class="btn btn-danger btn-sm flex-grow-1" onclick="deleteProduct({{ $product->id }})">Delete</button>
                    </div>
                </article>
            @empty
                <p class="text-center text-muted py-3 mb-0">No products found.</p>
            @endforelse
        </div>

        <div class="mt-3">

            {{ $products->links() }}

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>

function deleteProduct(id)
{
    Swal.fire({

        title: 'Delete Product?',

        text: 'This action cannot be undone.',

        icon: 'warning',

        showCancelButton: true,

        confirmButtonColor: '#dc3545',

        cancelButtonColor: '#6c757d',

        confirmButtonText: 'Delete',

        cancelButtonText: 'Cancel'

    }).then((result)=>{

        if(result.isConfirmed){

            document.getElementById('delete-product-'+id).submit();

        }

    });
}

</script>

@endpush
