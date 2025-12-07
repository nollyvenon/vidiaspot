@extends('admin.layout')

@section('title', 'Social Login Settings')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Social Login Settings</h2>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Provider Configuration -->
        <div class="lg:col-span-2 admin-card">
            <h3 class="text-md font-semibold mb-4">Social Login Providers</h3>
            
            <div class="space-y-4">
                <!-- Google -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-google text-red-500 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium">Google</h4>
                                <p class="text-sm text-gray-600">Allow users to sign in with Google</p>
                            </div>
                        </div>
                        <div>
                            <label class="switch">
                                <input type="checkbox" id="google-enabled" @if(env('GOOGLE_CLIENT_ID')) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-3" id="google-config" @unless(env('GOOGLE_CLIENT_ID')) class="hidden" @endunless>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="admin-form-label">Client ID</label>
                                <input type="text" id="google-client-id" value="{{ env('GOOGLE_CLIENT_ID', '') }}" class="admin-form-input" placeholder="Google Client ID">
                            </div>
                            <div>
                                <label class="admin-form-label">Client Secret</label>
                                <input type="password" id="google-client-secret" value="{{ env('GOOGLE_CLIENT_SECRET', '') }}" class="admin-form-input" placeholder="Google Client Secret">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="admin-form-label">Redirect URI</label>
                            <input type="text" value="{{ url('/auth/google/callback') }}" readonly class="admin-form-input bg-gray-100">
                            <p class="text-sm text-gray-500 mt-1">Add this to your Google OAuth configuration</p>
                        </div>
                    </div>
                </div>
                
                <!-- Facebook -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-facebook text-blue-600 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium">Facebook</h4>
                                <p class="text-sm text-gray-600">Allow users to sign in with Facebook</p>
                            </div>
                        </div>
                        <div>
                            <label class="switch">
                                <input type="checkbox" id="facebook-enabled" @if(env('FACEBOOK_CLIENT_ID')) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-3" id="facebook-config" @unless(env('FACEBOOK_CLIENT_ID')) class="hidden" @endunless>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="admin-form-label">App ID</label>
                                <input type="text" id="facebook-app-id" value="{{ env('FACEBOOK_CLIENT_ID', '') }}" class="admin-form-input" placeholder="Facebook App ID">
                            </div>
                            <div>
                                <label class="admin-form-label">App Secret</label>
                                <input type="password" id="facebook-app-secret" value="{{ env('FACEBOOK_CLIENT_SECRET', '') }}" class="admin-form-input" placeholder="Facebook App Secret">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="admin-form-label">Redirect URI</label>
                            <input type="text" value="{{ url('/auth/facebook/callback') }}" readonly class="admin-form-input bg-gray-100">
                            <p class="text-sm text-gray-500 mt-1">Add this to your Facebook OAuth configuration</p>
                        </div>
                    </div>
                </div>
                
                <!-- Twitter -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-twitter text-blue-400 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium">Twitter</h4>
                                <p class="text-sm text-gray-600">Allow users to sign in with Twitter</p>
                            </div>
                        </div>
                        <div>
                            <label class="switch">
                                <input type="checkbox" id="twitter-enabled" @if(env('TWITTER_CLIENT_ID')) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-3" id="twitter-config" @unless(env('TWITTER_CLIENT_ID')) class="hidden" @endunless>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="admin-form-label">API Key</label>
                                <input type="text" id="twitter-api-key" value="{{ env('TWITTER_CLIENT_ID', '') }}" class="admin-form-input" placeholder="Twitter API Key">
                            </div>
                            <div>
                                <label class="admin-form-label">API Secret</label>
                                <input type="password" id="twitter-api-secret" value="{{ env('TWITTER_CLIENT_SECRET', '') }}" class="admin-form-input" placeholder="Twitter API Secret">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="admin-form-label">Redirect URI</label>
                            <input type="text" value="{{ url('/auth/twitter/callback') }}" readonly class="admin-form-input bg-gray-100">
                            <p class="text-sm text-gray-500 mt-1">Add this to your Twitter OAuth configuration</p>
                        </div>
                    </div>
                </div>
                
                <!-- LinkedIn -->
                <div class="p-4 border rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fab fa-linkedin text-blue-700 text-xl mr-3"></i>
                            <div>
                                <h4 class="font-medium">LinkedIn</h4>
                                <p class="text-sm text-gray-600">Allow users to sign in with LinkedIn</p>
                            </div>
                        </div>
                        <div>
                            <label class="switch">
                                <input type="checkbox" id="linkedin-enabled" @if(env('LINKEDIN_CLIENT_ID')) checked @endif>
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="mt-3" id="linkedin-config" @unless(env('LINKEDIN_CLIENT_ID')) class="hidden" @endunless>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="admin-form-label">Client ID</label>
                                <input type="text" id="linkedin-client-id" value="{{ env('LINKEDIN_CLIENT_ID', '') }}" class="admin-form-input" placeholder="LinkedIn Client ID">
                            </div>
                            <div>
                                <label class="admin-form-label">Client Secret</label>
                                <input type="password" id="linkedin-client-secret" value="{{ env('LINKEDIN_CLIENT_SECRET', '') }}" class="admin-form-input" placeholder="LinkedIn Client Secret">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="admin-form-label">Redirect URI</label>
                            <input type="text" value="{{ url('/auth/linkedin/callback') }}" readonly class="admin-form-input bg-gray-100">
                            <p class="text-sm text-gray-500 mt-1">Add this to your LinkedIn OAuth configuration</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button id="save-settings" class="admin-btn admin-btn-primary">Save Configuration</button>
            </div>
        </div>
        
        <!-- Stats and Info -->
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-4">Social Login Stats</h3>
            
            <div class="space-y-4">
                <div class="p-4 bg-blue-50 rounded-lg border border-blue-100">
                    <h4 class="font-medium text-blue-800">Total Social Sign-ins</h4>
                    <p class="text-2xl font-bold text-blue-600">1,245</p>
                    <p class="text-sm text-blue-700 mt-1">This month: +18%</p>
                </div>
                
                <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                    <h4 class="font-medium text-green-800">Active Providers</h4>
                    <p class="text-2xl font-bold text-green-600">{{ $activeProviders ?? 3 }}</p>
                    <p class="text-sm text-green-700 mt-1">Out of 6 available</p>
                </div>
                
                <div class="p-4 bg-purple-50 rounded-lg border border-purple-100">
                    <h4 class="font-medium text-purple-800">Popular Provider</h4>
                    <p class="text-2xl font-bold text-purple-600">Google</p>
                    <p class="text-sm text-purple-700 mt-1">45% of social sign-ins</p>
                </div>
            </div>
            
            <div class="mt-6">
                <h3 class="text-md font-semibold mb-3">Setup Instructions</h3>
                <ul class="text-sm space-y-2 text-gray-700">
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                        <span>Go to the provider's developer console</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                        <span>Create a new application/project</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                        <span>Add your redirect URI to authorized redirect URIs</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                        <span>Copy Client ID and Secret to the fields</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-green-500 mt-1 mr-2"></i>
                        <span>Save the configuration</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Test Social Login -->
    <div class="admin-card mt-6">
        <h3 class="text-md font-semibold mb-4">Test Social Login</h3>
        
        <div class="flex flex-wrap gap-4">
            @if(env('GOOGLE_CLIENT_ID'))
            <a href="{{ route('auth.social.redirect', ['provider' => 'google']) }}" class="admin-btn admin-btn-danger flex items-center">
                <i class="fab fa-google mr-2"></i>
                Google
            </a>
            @endif
            
            @if(env('FACEBOOK_CLIENT_ID'))
            <a href="{{ route('auth.social.redirect', ['provider' => 'facebook']) }}" class="admin-btn admin-btn-primary flex items-center">
                <i class="fab fa-facebook-f mr-2"></i>
                Facebook
            </a>
            @endif
            
            @if(env('TWITTER_CLIENT_ID'))
            <a href="{{ route('auth.social.redirect', ['provider' => 'twitter']) }}" class="admin-btn admin-btn-primary flex items-center" style="background-color: #1da1f2;">
                <i class="fab fa-twitter mr-2"></i>
                Twitter
            </a>
            @endif
            
            @if(env('LINKEDIN_CLIENT_ID'))
            <a href="{{ route('auth.social.redirect', ['provider' => 'linkedin']) }}" class="admin-btn admin-btn-primary flex items-center" style="background-color: #0077b5;">
                <i class="fab fa-linkedin mr-2"></i>
                LinkedIn
            </a>
            @endif
        </div>
        
        <div class="mt-4 text-sm text-gray-600">
            <p><strong>Note:</strong> Test buttons only appear for providers that are configured.</p>
        </div>
    </div>
</div>

<style>
.switch {
  position: relative;
  display: inline-block;
  width: 50px;
  height: 24px;
}

.switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: #ccc;
  transition: .4s;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 4px;
  bottom: 4px;
  background-color: white;
  transition: .4s;
}

input:checked + .slider {
  background-color: #2196F3;
}

input:checked + .slider:before {
  transform: translateX(26px);
}

.slider.round {
  border-radius: 24px;
}

.slider.round:before {
  border-radius: 50%;
}
</style>

<script>
// Toggle visibility of configuration sections
document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const provider = this.id.replace('-enabled', '');
        const configDiv = document.getElementById(provider + '-config');
        
        if (this.checked) {
            configDiv.classList.remove('hidden');
        } else {
            configDiv.classList.add('hidden');
        }
    });
});

// Save settings
document.getElementById('save-settings').addEventListener('click', function() {
    // Collect all configuration values
    const config = {};
    
    // Google
    if (document.getElementById('google-enabled').checked) {
        config.google_client_id = document.getElementById('google-client-id').value;
        config.google_client_secret = document.getElementById('google-client-secret').value;
    }
    
    // Facebook
    if (document.getElementById('facebook-enabled').checked) {
        config.facebook_client_id = document.getElementById('facebook-app-id').value;
        config.facebook_client_secret = document.getElementById('facebook-app-secret').value;
    }
    
    // Twitter
    if (document.getElementById('twitter-enabled').checked) {
        config.twitter_client_id = document.getElementById('twitter-api-key').value;
        config.twitter_client_secret = document.getElementById('twitter-api-secret').value;
    }
    
    // LinkedIn
    if (document.getElementById('linkedin-enabled').checked) {
        config.linkedin_client_id = document.getElementById('linkedin-client-id').value;
        config.linkedin_client_secret = document.getElementById('linkedin-client-secret').value;
    }
    
    // In a real implementation, this would save to a config file or database
    // For demo purposes, we'll just show an alert
    alert('Configuration would be saved in a real implementation');
    
    // Here you would make an API call to save the configuration
    /* 
    fetch('/admin/social-login/save-config', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(config)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully');
        } else {
            alert('Error saving settings: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error saving settings');
    });
    */
});
</script>
@endsection