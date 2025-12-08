@extends('layouts.app')

@section('title', 'Edit Store')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Edit Your Store</h1>
        
        <form id="vendorStoreEditForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Store Name *</label>
                    <input type="text" id="store_name" name="store_name" required value="{{ old('store_name', $store->store_name) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">Select Theme *</label>
                    <select id="theme" name="theme" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Choose a theme</option>
                        @foreach($themes as $key => $theme)
                            <option value="{{ $key }}" {{ $store->theme == $key ? 'selected' : '' }}>{{ $theme['name'] }} - {{ $theme['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Store Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('description', $store->description) }}</textarea>
                </div>
                
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Store Logo</label>
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @if($store->logo_url)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current logo:</p>
                            <img src="{{ Storage::url($store->logo_url) }}" alt="Current logo" class="w-16 h-16 object-cover rounded mt-1">
                        </div>
                    @endif
                </div>
                
                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-1">Store Banner</label>
                    <input type="file" id="banner" name="banner" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    @if($store->banner_url)
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Current banner:</p>
                            <img src="{{ Storage::url($store->banner_url) }}" alt="Current banner" class="w-32 h-16 object-cover rounded mt-1">
                        </div>
                    @endif
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $store->contact_email) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="tel" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $store->contact_phone) }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="mt-6 flex space-x-4">
                <button type="submit"
                        class="flex justify-center py-3 px-6 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Store
                </button>
                <a href="{{ route('vendor.store.show', $store->store_slug) }}"
                   class="flex justify-center py-3 px-6 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('vendorStoreEditForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);

    try {
        const response = await fetch('{{ route("vendor.store.update", $store->id) }}', {
            method: 'PUT',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        const result = await response.json();

        if (result.success) {
            alert('Store updated successfully!');
            window.location.href = `/store/${result.store.store_slug}`;
        } else {
            alert('Error: ' + (result.message || 'Something went wrong'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while updating the store');
    }
});
</script>
@endsection