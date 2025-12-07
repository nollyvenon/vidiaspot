@extends('admin.layout')

@section('title', 'Edit Subscription')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Edit Subscription Plan: {{ $subscription->name }}</h2>
        <a href="{{ route('admin.subscriptions.index') }}" class="admin-btn admin-btn-primary">← Back to Subscriptions</a>
    </div>
    
    <form id="subscription-form" method="POST" action="{{ route('admin.subscriptions.update', $subscription) }}">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $subscription->name) }}" required class="admin-form-input" placeholder="Plan name">
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Slug *</label>
                    <input type="text" name="slug" value="{{ old('slug', $subscription->slug) }}" required class="admin-form-input" placeholder="plan-slug">
                    @error('slug')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Price *</label>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $subscription->price) }}" required class="admin-form-input" placeholder="0.00">
                    @error('price')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Currency Code *</label>
                    <input type="text" name="currency_code" value="{{ old('currency_code', $subscription->currency_code) }}" required class="admin-form-input" placeholder="NGN">
                    @error('currency_code')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Billing Cycle *</label>
                    <select name="billing_cycle" class="admin-form-select" required>
                        <option value="monthly" {{ old('billing_cycle', $subscription->billing_cycle) === 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('billing_cycle', $subscription->billing_cycle) === 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="yearly" {{ old('billing_cycle', $subscription->billing_cycle) === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                    @error('billing_cycle')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Duration (Days) *</label>
                    <input type="number" name="duration_days" value="{{ old('duration_days', $subscription->duration_days) }}" required class="admin-form-input" placeholder="30">
                    @error('duration_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            
            <div>
                <div class="admin-form-group">
                    <label class="admin-form-label">Description</label>
                    <textarea name="description" class="admin-form-input" rows="4" placeholder="Plan description">{{ old('description', $subscription->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Ad Limit *</label>
                    <input type="number" name="ad_limit" value="{{ old('ad_limit', $subscription->ad_limit) }}" required class="admin-form-input" placeholder="0">
                    @error('ad_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Featured Ads Limit *</label>
                    <input type="number" name="featured_ads_limit" value="{{ old('featured_ads_limit', $subscription->featured_ads_limit) }}" required class="admin-form-input" placeholder="0">
                    @error('featured_ads_limit')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">
                        <input type="checkbox" name="has_priority_support" value="1" {{ old('has_priority_support', $subscription->has_priority_support) ? 'checked' : '' }} class="mr-2">
                        Priority Support
                    </label>
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }} class="mr-2">
                        Active
                    </label>
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $subscription->is_featured) ? 'checked' : '' }} class="mr-2">
                        Featured Plan
                    </label>
                </div>
                
                <div class="admin-form-group">
                    <label class="admin-form-label">Features</label>
                    <div id="features-container">
                        @if(old('features', $subscription->features))
                            @foreach(old('features', $subscription->features) as $feature)
                                <div class="flex mb-2">
                                    <input type="text" name="features[]" value="{{ $feature }}" class="admin-form-input flex-1 mr-2" placeholder="Feature">
                                    <button type="button" onclick="removeFeature(this)" class="admin-btn admin-btn-danger admin-btn-sm">Remove</button>
                                </div>
                            @endforeach
                        @else
                            <div class="flex mb-2">
                                <input type="text" name="features[]" class="admin-form-input flex-1 mr-2" placeholder="Feature">
                                <button type="button" onclick="removeFeature(this)" class="admin-btn admin-btn-danger admin-btn-sm">Remove</button>
                            </div>
                        @endif
                    </div>
                    <button type="button" onclick="addFeature()" class="admin-btn admin-btn-primary admin-btn-sm">Add Feature</button>
                    @error('features')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>
        
        <div class="mt-6">
            <button type="submit" class="admin-btn admin-btn-primary">Update Subscription</button>
        </div>
    </form>
</div>

<script>
function addFeature() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'flex mb-2';
    div.innerHTML = `
        <input type="text" name="features[]" class="admin-form-input flex-1 mr-2" placeholder="Feature">
        <button type="button" onclick="removeFeature(this)" class="admin-btn admin-btn-danger admin-btn-sm">Remove</button>
    `;
    container.appendChild(div);
}

function removeFeature(button) {
    const container = document.getElementById('features-container');
    if (container.children.length > 1) {
        button.parentElement.remove();
    }
}
</script>
@endsection