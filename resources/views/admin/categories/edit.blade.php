@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Edit Category</h2>

    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
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

        <form action="{{ route('admin.categories.update', $category) }}" method="POST"
              data-swal-confirm
              data-swal-title="Save category changes?"
              data-swal-text="Your category changes will be applied."
              data-swal-confirm-text="Save changes">

            @csrf
            @method('PUT')

            <div class="mb-3">

                <label class="form-label">

                    Category Name

                </label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name', $category->name) }}"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">

                    Description

                </label>

                <textarea
                    name="description"
                    rows="4"
                    class="form-control">{{ old('description', $category->description) }}</textarea>

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    {{ old('is_active', $category->is_active) ? 'checked' : '' }}>

                <label class="form-check-label" for="is_active">

                    Active

                </label>

            </div>

            <button class="btn btn-success">

                Update Category

            </button>

        </form>

    </div>

</div>

@endsection
