@extends('layouts.app')

@section('title', 'Notification Preferences')
@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <h1 class="mb-4">Notification Preferences</h1>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Manage Your Notifications</h5>
                </div>
                <div class="card-body">
                    <form id="notification-preferences-form">
                        <!-- Email Notifications -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-envelope me-2 text-primary"></i>Email Notifications</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="email-enabled" {{ $notificationPreferences['email']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            
                            <div id="email-settings" class="{{ $notificationPreferences['email']['enabled'] ? '' : 'd-none' }}">
                                <div class="mb-2">
                                    <label class="form-label">Frequency</label>
                                    <select class="form-select" id="email-frequency">
                                        <option value="immediate" {{ $notificationPreferences['email']['frequency'] === 'immediate' ? 'selected' : '' }}>Immediate</option>
                                        <option value="daily" {{ $notificationPreferences['email']['frequency'] === 'daily' ? 'selected' : '' }}>Daily Digest</option>
                                        <option value="weekly" {{ $notificationPreferences['email']['frequency'] === 'weekly' ? 'selected' : '' }}>Weekly Digest</option>
                                        <option value="never" {{ $notificationPreferences['email']['frequency'] === 'never' ? 'selected' : '' }}>Never</option>
                                    </select>
                                </div>
                                
                                <div class="mb-2">
                                    <label class="form-label">Notification Types</label>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="email-recommendations" 
                                            {{ ($notificationPreferences['email']['types']['recommendations'] ?? false) ? 'checked' : '' }} 
                                            data-channel="email" data-type="recommendations">
                                        <label class="form-check-label" for="email-recommendations">Personalized Recommendations</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="email-new-ads" 
                                            {{ ($notificationPreferences['email']['types']['new_ads_in_category'] ?? false) ? 'checked' : '' }} 
                                            data-channel="email" data-type="new_ads_in_category">
                                        <label class="form-check-label" for="email-new-ads">New Ads in My Categories</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="email-price-alerts" 
                                            {{ ($notificationPreferences['email']['types']['price_alerts'] ?? false) ? 'checked' : '' }} 
                                            data-channel="email" data-type="price_alerts">
                                        <label class="form-check-label" for="email-price-alerts">Price Drop Alerts</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="email-account" 
                                            {{ ($notificationPreferences['email']['types']['account_activity'] ?? false) ? 'checked' : '' }} 
                                            data-channel="email" data-type="account_activity">
                                        <label class="form-check-label" for="email-account">Account Activity</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Push Notifications -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-bell me-2 text-success"></i>Push Notifications</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="push-enabled" {{ $notificationPreferences['push']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            
                            <div id="push-settings" class="{{ $notificationPreferences['push']['enabled'] ? '' : 'd-none' }}">
                                <div class="mb-2">
                                    <label class="form-label">Notification Types</label>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="push-recommendations" 
                                            {{ ($notificationPreferences['push']['types']['recommendations'] ?? false) ? 'checked' : '' }} 
                                            data-channel="push" data-type="recommendations">
                                        <label class="form-check-label" for="push-recommendations">Personalized Recommendations</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="push-messages" 
                                            {{ ($notificationPreferences['push']['types']['messages'] ?? false) ? 'checked' : '' }} 
                                            data-channel="push" data-type="messages">
                                        <label class="form-check-label" for="push-messages">Messages & Chats</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="push-account" 
                                            {{ ($notificationPreferences['push']['types']['account_activity'] ?? false) ? 'checked' : '' }} 
                                            data-channel="push" data-type="account_activity">
                                        <label class="form-check-label" for="push-account">Account Activity</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SMS Notifications -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-0"><i class="fas fa-comment-sms me-2 text-info"></i>SMS Notifications</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="sms-enabled" {{ $notificationPreferences['sms']['enabled'] ? 'checked' : '' }}>
                                </div>
                            </div>
                            
                            <div id="sms-settings" class="{{ $notificationPreferences['sms']['enabled'] ? '' : 'd-none' }}">
                                <div class="mb-2">
                                    <label class="form-label">Notification Types</label>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="sms-important" 
                                            {{ ($notificationPreferences['sms']['types']['important'] ?? false) ? 'checked' : '' }} 
                                            data-channel="sms" data-type="important">
                                        <label class="form-check-label" for="sms-important">Important Notifications</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input notification-type" type="checkbox" id="sms-account" 
                                            {{ ($notificationPreferences['sms']['types']['account_activity'] ?? false) ? 'checked' : '' }} 
                                            data-channel="sms" data-type="account_activity">
                                        <label class="form-check-label" for="sms-account">Account Activity</label>
                                    </div>
                                </div>
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
    // Toggle notification channel settings visibility
    document.getElementById('email-enabled').addEventListener('change', function() {
        const settingsDiv = document.getElementById('email-settings');
        settingsDiv.classList.toggle('d-none', !this.checked);
    });
    
    document.getElementById('push-enabled').addEventListener('change', function() {
        const settingsDiv = document.getElementById('push-settings');
        settingsDiv.classList.toggle('d-none', !this.checked);
    });
    
    document.getElementById('sms-enabled').addEventListener('change', function() {
        const settingsDiv = document.getElementById('sms-settings');
        settingsDiv.classList.toggle('d-none', !this.checked);
    });
    
    // Save notification preferences form
    const notificationForm = document.getElementById('notification-preferences-form');
    if (notificationForm) {
        notificationForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const notificationPreferences = {
                email: {
                    enabled: document.getElementById('email-enabled').checked,
                    frequency: document.getElementById('email-frequency').value,
                    types: {
                        recommendations: document.getElementById('email-recommendations').checked,
                        'new_ads_in_category': document.getElementById('email-new-ads').checked,
                        'price_alerts': document.getElementById('email-price-alerts').checked,
                        'account_activity': document.getElementById('email-account').checked
                    }
                },
                push: {
                    enabled: document.getElementById('push-enabled').checked,
                    types: {
                        recommendations: document.getElementById('push-recommendations').checked,
                        messages: document.getElementById('push-messages').checked,
                        'account_activity': document.getElementById('push-account').checked
                    }
                },
                sms: {
                    enabled: document.getElementById('sms-enabled').checked,
                    types: {
                        important: document.getElementById('sms-important').checked,
                        'account_activity': document.getElementById('sms-account').checked
                    }
                }
            };
            
            fetch('/user/notifications', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(notificationPreferences)
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    // Show success message
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'alert alert-success mt-3';
                    alertDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> Notification preferences saved successfully!';
                    notificationForm.insertAdjacentElement('afterend', alertDiv);
                    
                    setTimeout(() => {
                        alertDiv.remove();
                    }, 3000);
                }
            })
            .catch(error => console.error('Error saving notification preferences:', error));
        });
    }
});
</script>
@endsection