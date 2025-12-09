@extends('layouts.app')

@section('title', 'Accessibility Settings')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Accessibility Settings</h1>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-800 mb-6">Customize Accessibility Features</h2>
            
            <form id="accessibility-form" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- High Contrast Mode -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">High Contrast Mode</h3>
                            <p class="text-sm text-gray-600 mt-1">Increase contrast between text and background</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="high-contrast" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Large Text -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Large Text</h3>
                            <p class="text-sm text-gray-600 mt-1">Increase text size for better readability</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="large-text" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Reduced Motion -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Reduced Motion</h3>
                            <p class="text-sm text-gray-600 mt-1">Minimize animations and transitions</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="reduced-motion" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Screen Reader Mode -->
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg">
                        <div>
                            <h3 class="font-medium text-gray-900">Screen Reader Mode</h3>
                            <p class="text-sm text-gray-600 mt-1">Optimize for screen reader navigation</p>
                        </div>
                        <div class="flex items-center">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="screen-reader-mode" class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Language Selection -->
                <div class="p-4 border border-gray-200 rounded-lg">
                    <h3 class="font-medium text-gray-900 mb-3">Language for Accessibility Labels</h3>
                    <select id="accessibility-language" class="w-full md:w-64 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <option value="fr">French</option>
                        <option value="de">German</option>
                        <option value="pt">Portuguese</option>
                        <option value="ar">Arabic</option>
                        <option value="yo">Yoruba</option>
                        <option value="ig">Igbo</option>
                        <option value="ha">Hausa</option>
                    </select>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>

        <!-- Accessibility Information -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4">About Accessibility Features</h2>
            
            <div class="prose max-w-none text-gray-700 space-y-4">
                <p>The accessibility features on this platform are designed to make our services usable by everyone, including people with disabilities such as visual, auditory, motor, and cognitive impairments.</p>
                
                <h3 class="font-semibold text-gray-800">Available Features:</h3>
                <ul class="list-disc list-inside space-y-2">
                    <li><strong>High Contrast Mode:</strong> Increases the contrast between text and background colors to make content more readable</li>
                    <li><strong>Large Text:</strong> Increases the size of text throughout the interface</li>
                    <li><strong>Reduced Motion:</strong> Minimizes animations and transitions that could cause discomfort to users with vestibular disorders</li>
                    <li><strong>Screen Reader Mode:</strong> Optimizes the interface for navigation using screen readers</li>
                    <li><strong>Keyboard Navigation:</strong> Full operation via keyboard for users who cannot use a mouse</li>
                    <li><strong>Alternative Text:</strong> Descriptive text for images and non-text content</li>
                </ul>
                
                <p>These settings are saved to your account and will be applied automatically when you log in from any device.</p>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load saved preferences
    loadAccessibilityPreferences();
    
    // Form submission handler
    document.getElementById('accessibility-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const preferences = {
            high_contrast: document.getElementById('high-contrast').checked,
            large_text: document.getElementById('large-text').checked,
            reduced_motion: document.getElementById('reduced-motion').checked,
            screen_reader_mode: document.getElementById('screen-reader-mode').checked,
            language: document.getElementById('accessibility-language').value
        };
        
        // Save to server
        fetch('/accessibility/settings', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(preferences)
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                alert('Accessibility settings updated successfully!');
            }
        })
        .catch(error => {
            console.error('Error saving accessibility settings:', error);
            alert('Error saving settings. Please try again.');
        });
    });
    
    function loadAccessibilityPreferences() {
        fetch('/accessibility/settings')
        .then(response => response.json())
        .then(data => {
            const prefs = data.preferences;
            document.getElementById('high-contrast').checked = prefs.high_contrast;
            document.getElementById('large-text').checked = prefs.large_text;
            document.getElementById('reduced-motion').checked = prefs.reduced_motion;
            document.getElementById('screen-reader-mode').checked = prefs.screen_reader_mode;
            document.getElementById('accessibility-language').value = prefs.language;
            
            // Apply settings to the current page
            applyAccessibilitySettings(prefs);
        })
        .catch(error => {
            console.error('Error loading accessibility preferences:', error);
        });
    }
    
    function applyAccessibilitySettings(settings) {
        // Apply high contrast
        if (settings.high_contrast) {
            document.body.classList.add('high-contrast-mode');
        } else {
            document.body.classList.remove('high-contrast-mode');
        }
        
        // Apply large text
        if (settings.large_text) {
            document.body.classList.add('large-text-mode');
        } else {
            document.body.classList.remove('large-text-mode');
        }
        
        // Apply reduced motion
        if (settings.reduced_motion) {
            document.body.classList.add('reduced-motion-mode');
        } else {
            document.body.classList.remove('reduced-motion-mode');
        }
    }
});
</script>
@endsection