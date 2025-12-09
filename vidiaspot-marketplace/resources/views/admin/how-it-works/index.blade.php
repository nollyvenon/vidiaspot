@extends('admin.layout')

@section('title', 'Manage How It Works Steps')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">How It Works Steps</h4>
                    <a href="{{ route('admin.how-it-works.create') }}" class="btn btn-success">Add New Step</a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Step Order</th>
                                    <th>Title</th>
                                    <th>Icon</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($steps as $step)
                                <tr>
                                    <td>{{ $step->step_order }}</td>
                                    <td>{{ $step->title }}</td>
                                    <td><i class="{{ $step->icon_class }}"></i> {{ $step->icon_class }}</td>
                                    <td>
                                        <span class="badge bg-{{ $step->is_active ? 'success' : 'secondary' }}">
                                            {{ $step->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.how-it-works.edit', $step) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('admin.how-it-works.destroy', $step) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this step?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No steps found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection