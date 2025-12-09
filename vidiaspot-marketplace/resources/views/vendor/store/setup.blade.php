@extends('layouts.app')

@section('title', 'Setup Your Store')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Your Online Store</h1>
        
        <form id="vendorStoreForm" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1">Store Name *</label>
                    <input type="text" id="store_name" name="store_name" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="theme" class="block text-sm font-medium text-gray-700 mb-1">Select Theme *</label>
                    <select id="theme" name="theme" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Choose a theme</option>
                        @foreach($themes as $key => $theme)
                            <option value="{{ $key }}">{{ $theme['name'] }} - {{ $theme['description'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Store Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div>
                    <label for="logo" class="block text-sm font-medium text-gray-700 mb-1">Store Logo</label>
                    <input type="file" id="logo" name="logo" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="banner" class="block text-sm font-medium text-gray-700 mb-1">Store Banner</label>
                    <input type="file" id="banner" name="banner" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" id="contact_email" name="contact_email"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="tel" id="contact_phone" name="contact_phone"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
            
            <div class="mt-6">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Store
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('vendorStoreForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    try {
        const response = await fetch('{{ route("vendor.store.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Store created successfully!');
            window.location.href = `/store/${result.store.store_slug}`;
        } else {
            alert('Error: ' + (result.message || 'Something went wrong'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while creating the store');
    }
});
</script>
@endsection