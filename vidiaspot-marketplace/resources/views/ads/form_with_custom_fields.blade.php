@extends('layouts.app')

@section('title', 'Create Ad with Custom Fields')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">Create Ad with Custom Fields</h1>
        
        <form id="adFormWithCustomFields">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select id="category" name="category" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select a category</option>
                        <option value="electronics">Electronics</option>
                        <option value="vehicles">Vehicles</option>
                        <option value="furniture">Furniture</option>
                        <option value="property">Property</option>
                    </select>
                </div>
                
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                    <input type="text" id="title" name="title" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price *</label>
                    <input type="number" id="price" name="price" required step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                
                <div>
                    <label for="condition" class="block text-sm font-medium text-gray-700 mb-1">Condition</label>
                    <select id="condition" name="condition"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="new">New</option>
                        <option value="used">Used</option>
                        <option value="like_new">Like New</option>
                        <option value="refurbished">Refurbished</option>
                    </select>
                </div>
            </div>
            
            <!-- Custom fields will be loaded here based on category -->
            <div id="customFieldsContainer" class="mt-6"></div>
            
            <div class="mt-6">
                <button type="submit"
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Ad with Custom Fields
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('category').addEventListener('change', async function() {
    const category = this.value;
    const container = document.getElementById('customFieldsContainer');
    
    if (!category) {
        container.innerHTML = '';
        return;
    }
    
    try {
        const response = await fetch(`/api/vendor-store/custom-field-templates?category=${category}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });
        
        const result = await response.json();
        const templates = result.templates;
        
        if (templates && templates.length > 0) {
            let html = '<div class="border-t border-gray-200 pt-6"><h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3><div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
            
            templates.forEach(field => {
                html += `<div>`;
                html += `<label class="block text-sm font-medium text-gray-700 mb-1">${field.label}</label>`;
                
                switch(field.type) {
                    case 'select':
                        html += `<select name="custom_fields[${field.key}]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">`;
                        field.options.forEach(option => {
                            html += `<option value="${option}">${option}</option>`;
                        });
                        html += `</select>`;
                        break;
                    case 'checkbox':
                        html += `<input type="checkbox" name="custom_fields[${field.key}]" value="1" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">`;
                        break;
                    case 'number':
                        html += `<input type="number" name="custom_fields[${field.key}]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">`;
                        break;
                    default:
                        html += `<input type="text" name="custom_fields[${field.key}]" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">`;
                }
                
                html += `</div>`;
            });
            
            html += '</div></div>';
            container.innerHTML = html;
        } else {
            container.innerHTML = '';
        }
    } catch (error) {
        console.error('Error loading custom fields:', error);
        container.innerHTML = '<p class="text-red-500">Error loading custom fields</p>';
    }
});

document.getElementById('adFormWithCustomFields').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const customFieldsContainer = document.getElementById('customFieldsContainer');
    
    // Collect custom fields data
    const customFields = [];
    if (customFieldsContainer.innerHTML.trim() !== '') {
        const customFieldInputs = customFieldsContainer.querySelectorAll('input, select, textarea');
        customFieldInputs.forEach(input => {
            if (input.name.startsWith('custom_fields[')) {
                const key = input.name.replace('custom_fields[', '').replace(']', '');
                let value;
                
                if (input.type === 'checkbox') {
                    value = input.checked ? '1' : '0';
                } else {
                    value = input.value;
                }
                
                customFields.push({
                    key: key,
                    label: input.previousElementSibling.textContent,
                    type: input.type === 'checkbox' ? 'checkbox' : input.type,
                    value: value
                });
            }
        });
    }
    
    // Prepare the data to send
    const data = {
        title: document.getElementById('title').value,
        description: document.getElementById('description').value,
        price: document.getElementById('price').value,
        condition: document.getElementById('condition').value,
        category: document.getElementById('category').value,
        fields: customFields
    };
    
    try {
        // First, create the ad
        const adResponse = await fetch('/api/ads', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title: data.title,
                description: data.description,
                price: data.price,
                condition: data.condition,
                category_id: 1 // This would be the actual category ID in a real implementation
            })
        });
        
        const adResult = await adResponse.json();
        
        if (adResult.id && customFields.length > 0) {
            // Now add custom fields to the ad
            const customFieldsResponse = await fetch(`/api/ads/${adResult.id}/custom-fields`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fields: data.fields
                })
            });
            
            const customFieldsResult = await customFieldsResponse.json();
            
            if (customFieldsResult.success) {
                alert('Ad created successfully with custom fields!');
                window.location.href = `/ads/${adResult.id}`;
            } else {
                alert('Ad created but custom fields failed: ' + (customFieldsResult.message || 'Unknown error'));
            }
        } else {
            alert('Ad created successfully!');
            window.location.href = `/ads/${adResult.id}`;
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while creating the ad');
    }
});
</script>
@endsection