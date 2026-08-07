@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add Product</h2>

    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        Back
    </a>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
              data-swal-confirm
              data-swal-title="Create product?"
              data-swal-text="The new product will be added to the menu."
              data-swal-confirm-text="Create product">
            @csrf

            <div class="mb-3">
                <label for="image" class="form-label">Product Image</label>
                <input id="image" type="file" name="image" class="form-control" accept="image/jpeg,image/png">
            </div>

            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label for="category_id" class="form-label">Category</label>
                <select id="category_id" name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="4" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input id="price" type="number" step="0.01" min="0" name="price" class="form-control" value="{{ old('price') }}" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input id="stock" type="number" min="0" name="stock" class="form-control" value="{{ old('stock') }}" required>
                    </div>
                </div>
            </div>

            <div class="form-check mb-4">
                <input id="is_available" class="form-check-input" type="checkbox" name="is_available" value="1" @checked(old('is_available', true))>
                <label class="form-check-label" for="is_available">Available</label>
            </div>

            <button class="btn btn-primary">Save Product</button>
        </form>
    </div>
</div>

@endsection
