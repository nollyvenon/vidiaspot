@extends('layouts.app')

@section('title', 'Personalization Settings')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Personalization Settings</h1>
            
            <!-- Mood State Selector -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mood-Based Shopping</h5>
                </div>
                <div class="card-body">
                    <p>Adjust your shopping experience based on your current mood:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-outline-primary mood-btn" data-mood="normal">Normal</button>
                        <button class="btn btn-outline-success mood-btn" data-mood="excited">Excited</button>
                        <button class="btn btn-outline-info mood-btn" data-mood="home">At Home</button>
                        <button class="btn btn-outline-warning mood-btn" data-mood="luxury">Luxury</button>
                        <button class="btn btn-outline-secondary mood-btn" data-mood="practical">Practical</button>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Current mood: <span id="current-mood" class="fw-bold">{{ $moodState ?? 'normal' }}</span></small>
                    </div>
                </div>
            </div>
            
            <!-- Preference Categories -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Shopping Preferences</h5>
                </div>
                <div class="card-body">
                    <form id="preferences-form">
                        <div class="mb-3">
                            <label class="form-label">Preferred Categories</label>
                            <select multiple class="form-select" name="preferred_categories[]">
                                <option value="1" {{ in_array(1, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Electronics</option>
                                <option value="2" {{ in_array(2, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Vehicles</option>
                                <option value="3" {{ in_array(3, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Furniture</option>
                                <option value="4" {{ in_array(4, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Property</option>
                                <option value="5" {{ in_array(5, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Jobs</option>
                                <option value="6" {{ in_array(6, $preferences['preferred_categories'] ?? []) ? 'selected' : '' }}>Services</option>
                            </select>
                            <div class="form-text">Select categories you're most interested in</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Preferred Locations</label>
                            <select multiple class="form-select" name="preferred_locations[]">
                                <option value="Lagos" {{ in_array('Lagos', $preferences['preferred_locations'] ?? []) ? 'selected' : '' }}>Lagos</option>
                                <option value="Abuja" {{ in_array('Abuja', $preferences['preferred_locations'] ?? []) ? 'selected' : '' }}>Abuja</option>
                                <option value="Kano" {{ in_array('Kano', $preferences['preferred_locations'] ?? []) ? 'selected' : '' }}>Kano</option>
                                <option value="Ibadan" {{ in_array('Ibadan', $preferences['preferred_locations'] ?? []) ? 'selected' : '' }}>Ibadan</option>
                                <option value="Port Harcourt" {{ in_array('Port Harcourt', $preferences['preferred_locations'] ?? []) ? 'selected' : '' }}>Port Harcourt</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Min Price (₦)</label>
                                <input type="number" class="form-control" name="price_range[min]" value="{{ $preferences['price_range']['min'] ?? '' }}" placeholder="e.g., 10000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Max Price (₦)</label>
                                <input type="number" class="form-control" name="price_range[max]" value="{{ $preferences['price_range']['max'] ?? '' }}" placeholder="e.g., 500000">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Interface Theme</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="theme" id="theme-light" value="light" {{ ($preferences['theme'] ?? 'light') === 'light' ? 'checked' : '' }}>
                                <label class="form-check-label" for="theme-light">Light</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="theme" id="theme-dark" value="dark" {{ ($preferences['theme'] ?? 'light') === 'dark' ? 'checked' : '' }}>
                                <label class="form-check-label" for="theme-dark">Dark</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="theme" id="theme-auto" value="auto" {{ ($preferences['theme'] ?? 'light') === 'auto' ? 'checked' : '' }}>
                                <label class="form-check-label" for="theme-auto">Auto (System)</label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Layout Preference</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="layout" id="layout-default" value="default" {{ ($preferences['layout'] ?? 'default') === 'default' ? 'checked' : '' }}>
                                <label class="form-check-label" for="layout-default">Default</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="layout" id="layout-compact" value="compact" {{ ($preferences['layout'] ?? 'default') === 'compact' ? 'checked' : '' }}>
                                <label class="form-check-label" for="layout-compact">Compact</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="layout" id="layout-card" value="card" {{ ($preferences['layout'] ?? 'default') === 'card' ? 'checked' : '' }}>
                                <label class="form-check-label" for="layout-card">Card View</label>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">Save Preferences</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mood state selector
    const moodButtons = document.querySelectorAll('.mood-btn');
    const currentMoodSpan = document.getElementById('current-mood');
    
    moodButtons.forEach(button => {
        button.addEventListener('click', function() {
            const mood = this.getAttribute('data-mood');
            
            // Update current mood display
            currentMoodSpan.textContent = mood;
            currentMoodSpan.className = 'fw-bold text-' + (mood === 'normal' ? 'primary' : 
                mood === 'excited' ? 'success' : 
                mood === 'home' ? 'info' : 
                mood === 'luxury' ? 'warning' : 'secondary');
            
            // Send update to server
            fetch('/user/preferences', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    mood_state: mood
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success mt-3';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Mood updated successfully!';
                    document.querySelector('.card').insertAdjacentElement('afterend', alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                }
            })
            .catch(error => console.error('Error updating mood:', error));
        });
    });
    
    // Save preferences form
    const preferencesForm = document.getElementById('preferences-form');
    if (preferencesForm) {
        preferencesForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(preferencesForm);
            const preferences = {
                preferred_categories: Array.from(formData.getAll('preferred_categories[]')).filter(val => val !== ''),
                preferred_locations: Array.from(formData.getAll('preferred_locations[]')).filter(val => val !== ''),
                price_range: {
                    min: formData.get('price_range[min]'),
                    max: formData.get('price_range[max]')
                },
                theme: formData.get('theme'),
                layout: formData.get('layout')
            };
            
            fetch('/user/preferences', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(preferences)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success mt-3';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Preferences saved successfully!';
                    preferencesForm.insertAdjacentElement('afterend', alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                }
            })
            .catch(error => console.error('Error saving preferences:', error));
        });
    }
});
</script>
@endsection