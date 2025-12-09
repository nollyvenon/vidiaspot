@extends('admin.layout')

@section('title', 'Create How It Works Step')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create New How It Works Step</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.how-it-works.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="icon_class" class="form-label">Icon Class</label>
                            <input type="text" class="form-control @error('icon_class') is-invalid @enderror" id="icon_class" name="icon_class" value="{{ old('icon_class', 'fas fa-star') }}" required>
                            <div class="form-text">Enter Font Awesome icon class (e.g., fas fa-user-plus, fas fa-plus, fas fa-handshake)</div>
                            @error('icon_class')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="step_order" class="form-label">Step Order</label>
                            <input type="number" class="form-control @error('step_order') is-invalid @enderror" id="step_order" name="step_order" value="{{ old('step_order', 1) }}" min="1" required>
                            @error('step_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">Create Step</button>
                            <a href="{{ route('admin.how-it-works.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection