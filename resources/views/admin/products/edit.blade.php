@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Edit Product</h2>

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

        <form action="{{ route('admin.products.update', $product) }}"
              method="POST"
              enctype="multipart/form-data"
              data-swal-confirm
              data-swal-title="Save product changes?"
              data-swal-text="Your product changes will be applied."
              data-swal-confirm-text="Save changes">

            @csrf
            @method('PUT')

            @if($product->image)

            <div class="mb-3">

                <label class="form-label">Current Image</label>
                <br>

                <img src="{{ $product->image_url }}"
                     width="150"
                     class="img-thumbnail">

            </div>

            @endif

            <div class="mb-3">

                <label class="form-label">Product Image</label>

                <input
                    type="file"
                    name="image"
                    class="form-control">

            </div>

            <div class="mb-3">

                <label class="form-label">Product Name</label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $product->name) }}"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">Category</label>

                <select
                    name="category_id"
                    class="form-select"
                    required>

                    <option value="">Select Category</option>

                    @foreach($categories as $category)

                        <option
                            value="{{ $category->id }}"
                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>

                            {{ $category->name }}

                        </option>

                    @endforeach

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">Description</label>

                <textarea
                    name="description"
                    rows="4"
                    class="form-control">{{ old('description', $product->description) }}</textarea>

            </div>

            <div class="row">

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label">Price</label>

                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="price"
                            class="form-control"
                            value="{{ old('price', $product->price) }}"
                            required>

                    </div>

                </div>

                <div class="col-md-6">

                    <div class="mb-3">

                        <label class="form-label">Stock</label>

                        <input
                            type="number"
                            min="0"
                            name="stock"
                            class="form-control"
                            value="{{ old('stock', $product->stock) }}"
                            required>

                    </div>

                </div>

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="is_available"
                    id="is_available"
                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}>

                <label
                    class="form-check-label"
                    for="is_available">

                    Available

                </label>

            </div>

            <button class="btn btn-success">

                Update Product

            </button>

        </form>

    </div>

</div>

@endsection
