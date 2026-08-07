@extends('admin.layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Add Category</h2>

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

        <form action="{{ route('admin.categories.store') }}" method="POST"
              data-swal-confirm
              data-swal-title="Create category?"
              data-swal-text="The new category will be added to the menu."
              data-swal-confirm-text="Create category">

            @csrf

            <div class="mb-3">

                <label class="form-label">Category Name</label>

                <input
                    type="text"
                    name="name"
                    class="form-control"
                    value="{{ old('name') }}"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">Description</label>

                <textarea
                    name="description"
                    rows="4"
                    class="form-control">{{ old('description') }}</textarea>

            </div>

            <div class="form-check mb-4">

                <input
                    class="form-check-input"
                    type="checkbox"
                    name="is_active"
                    id="is_active"
                    checked>

                <label class="form-check-label" for="is_active">

                    Active

                </label>

            </div>

            <button class="btn btn-primary">

                Save Category

            </button>

        </form>

    </div>

</div>

@endsection
