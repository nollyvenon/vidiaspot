@extends('admin.layout')

@section('title', 'Add Payment Setting')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Add New Payment Setting</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payment-settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_key" class="form-label">Feature Key</label>
                                    <input type="text" class="form-control @error('feature_key') is-invalid @enderror" 
                                        id="feature_key" name="feature_key" value="{{ old('feature_key') }}" 
                                        placeholder="e.g., cryptocurrency_payments" required>
                                    @error('feature_key')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">Use lowercase letters, numbers, and underscores only (e.g., cryptocurrency_payments)</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_name" class="form-label">Feature Name</label>
                                    <input type="text" class="form-control @error('feature_name') is-invalid @enderror" 
                                        id="feature_name" name="feature_name" value="{{ old('feature_name') }}" 
                                        placeholder="e.g., Cryptocurrency Payments" required>
                                    @error('feature_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="feature_type" class="form-label">Feature Type</label>
                                    <select class="form-select @error('feature_type') is-invalid @enderror" 
                                        id="feature_type" name="feature_type" required>
                                        <option value="">Select Type</option>
                                        <option value="payment_method" {{ old('feature_type') == 'payment_method' ? 'selected' : '' }}>
                                            Payment Method
                                        </option>
                                        <option value="service" {{ old('feature_type') == 'service' ? 'selected' : '' }}>
                                            Service
                                        </option>
                                        <option value="integration" {{ old('feature_type') == 'integration' ? 'selected' : '' }}>
                                            Integration
                                        </option>
                                    </select>
                                    @error('feature_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                            id="is_enabled" name="is_enabled" value="1" 
                                            {{ old('is_enabled') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_enabled">
                                            Enable this feature
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                id="description" name="description" rows="3" 
                                placeholder="Brief description of this payment feature">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="available_countries" class="form-label">Available Countries</label>
                            <select multiple class="form-select @error('available_countries') is-invalid @enderror" 
                                id="available_countries" name="available_countries[]" size="10">
                                <option value="" disabled>Select Countries</option>
                                <option value="NG" {{ collect(old('available_countries', []))->contains('NG') ? 'selected' : '' }}>Nigeria (NG)</option>
                                <option value="GH" {{ collect(old('available_countries', []))->contains('GH') ? 'selected' : '' }}>Ghana (GH)</option>
                                <option value="KE" {{ collect(old('available_countries', []))->contains('KE') ? 'selected' : '' }}>Kenya (KE)</option>
                                <option value="ZA" {{ collect(old('available_countries', []))->contains('ZA') ? 'selected' : '' }}>South Africa (ZA)</option>
                                <option value="UG" {{ collect(old('available_countries', []))->contains('UG') ? 'selected' : '' }}>Uganda (UG)</option>
                                <option value="RW" {{ collect(old('available_countries', []))->contains('RW') ? 'selected' : '' }}>Rwanda (RW)</option>
                                <option value="CM" {{ collect(old('available_countries', []))->contains('CM') ? 'selected' : '' }}>Cameroon (CM)</option>
                                <option value="TZ" {{ collect(old('available_countries', []))->contains('TZ') ? 'selected' : '' }}>Tanzania (TZ)</option>
                            </select>
                            @error('available_countries')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Hold Ctrl/Cmd to select multiple countries. Leave empty for global availability.</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" min="0">
                            @error('sort_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="configuration" class="form-label">Configuration (JSON)</label>
                            <textarea class="form-control @error('configuration') is-invalid @enderror" 
                                id="configuration" name="configuration" rows="5" 
                                placeholder='{"max_amount": 1000000, "supported_coins": ["BTC", "ETH"]}'>{{ old('configuration') ? json_encode(old('configuration'), JSON_PRETTY_PRINT) : '' }}</textarea>
                            @error('configuration')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Configuration parameters in JSON format</div>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-2"></i>Save Setting
                            </button>
                            <a href="{{ route('admin.payment-settings.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection