// Accessibility Utilities
class AccessibilityManager {
    constructor() {
        this.isHighContrast = false;
        this.isLargeText = false;
        this.isReducedMotion = false;
        this.isScreenReaderMode = false;
        
        this.init();
    }
    
    init() {
        // Load saved preferences
        this.loadPreferences();
        
        // Apply saved settings
        this.applySettings();
        
        // Listen for changes to accessibility preferences
        this.listenForChanges();
    }
    
    loadPreferences() {
        const preferences = JSON.parse(sessionStorage.getItem('accessibilityPreferences') || '{}');
        
        this.isHighContrast = preferences.highContrast || false;
        this.isLargeText = preferences.largeText || false;
        this.isReducedMotion = preferences.reducedMotion || false;
        this.isScreenReaderMode = preferences.screenReaderMode || false;
    }
    
    savePreferences() {
        const preferences = {
            highContrast: this.isHighContrast,
            largeText: this.isLargeText,
            reducedMotion: this.isReducedMotion,
            screenReaderMode: this.isScreenReaderMode
        };
        
        sessionStorage.setItem('accessibilityPreferences', JSON.stringify(preferences));
    }
    
    applySettings() {
        // Apply high contrast
        if (this.isHighContrast) {
            document.body.classList.add('high-contrast-mode');
        } else {
            document.body.classList.remove('high-contrast-mode');
        }
        
        // Apply large text
        if (this.isLargeText) {
            document.body.classList.add('large-text-mode');
        } else {
            document.body.classList.remove('large-text-mode');
        }
        
        // Apply reduced motion
        if (this.isReducedMotion) {
            document.body.classList.add('reduced-motion-mode');
        } else {
            document.body.classList.remove('reduced-motion-mode');
        }
        
        this.savePreferences();
    }
    
    toggleHighContrast() {
        this.isHighContrast = !this.isHighContrast;
        this.applySettings();
        
        // Announce the change for screen readers
        this.announceForScreenReader(
            this.isHighContrast ? 'High contrast mode enabled' : 'High contrast mode disabled'
        );
    }
    
    toggleLargeText() {
        this.isLargeText = !this.isLargeText;
        this.applySettings();
        
        // Announce the change for screen readers
        this.announceForScreenReader(
            this.isLargeText ? 'Large text mode enabled' : 'Large text mode disabled'
        );
    }
    
    toggleReducedMotion() {
        this.isReducedMotion = !this.isReducedMotion;
        this.applySettings();
        
        // Announce the change for screen readers
        this.announceForScreenReader(
            this.isReducedMotion ? 'Reduced motion mode enabled' : 'Reduced motion mode disabled'
        );
    }
    
    toggleScreenReaderMode() {
        this.isScreenReaderMode = !this.isScreenReaderMode;
        
        // Announce the change for screen readers
        this.announceForScreenReader(
            this.isScreenReaderMode ? 'Screen reader mode enabled' : 'Screen reader mode disabled'
        );
    }
    
    listenForChanges() {
        // Listen for changes from the server or other components
        document.addEventListener('accessibility-settings-change', (e) => {
            const { highContrast, largeText, reducedMotion, screenReaderMode } = e.detail;
            
            if (typeof highContrast !== 'undefined') this.isHighContrast = highContrast;
            if (typeof largeText !== 'undefined') this.isLargeText = largeText;
            if (typeof reducedMotion !== 'undefined') this.isReducedMotion = reducedMotion;
            if (typeof screenReaderMode !== 'undefined') this.isScreenReaderMode = screenReaderMode;
            
            this.applySettings();
        });
    }
    
    // Announce messages for screen readers
    announceForScreenReader(message) {
        // Create a temporary element for screen readers
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.innerHTML = message;
        
        document.body.appendChild(announcement);
        
        // Remove the element after it's been announced
        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }
    
    // Get current accessibility state
    getState() {
        return {
            highContrast: this.isHighContrast,
            largeText: this.isLargeText,
            reducedMotion: this.isReducedMotion,
            screenReaderMode: this.isScreenReaderMode
        };
    }
    
    // Apply settings from server response
    setSettings(settings) {
        this.isHighContrast = settings.high_contrast || false;
        this.isLargeText = settings.large_text || false;
        this.isReducedMotion = settings.reduced_motion || false;
        this.isScreenReaderMode = settings.screen_reader_mode || false;
        
        this.applySettings();
    }
}

// Voice Navigation Manager
class VoiceNavigationManager {
    constructor() {
        this.isListening = false;
        this.recognition = null;
        this.accessibilityManager = new AccessibilityManager();
        
        this.init();
    }
    
    init() {
        // Check if browser supports speech recognition
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            this.recognition = new SpeechRecognition();
            
            this.recognition.continuous = false;
            this.recognition.interimResults = false;
            this.recognition.lang = 'en-US';
            
            this.recognition.onresult = (event) => {
                const command = event.results[0][0].transcript.toLowerCase().trim();
                this.processCommand(command);
            };
            
            this.recognition.onerror = (event) => {
                console.error('Speech recognition error', event.error);
                this.accessibilityManager.announceForScreenReader('Voice command failed. Please try again.');
            };
        } else {
            console.warn('Speech recognition not supported in this browser');
        }
    }
    
    startListening() {
        if (!this.recognition) {
            this.accessibilityManager.announceForScreenReader('Voice navigation not supported in this browser.');
            return;
        }
        
        try {
            this.recognition.start();
            this.isListening = true;
            this.accessibilityManager.announceForScreenReader('Voice navigation activated. Speak your command.');
        } catch (error) {
            console.error('Error starting speech recognition', error);
            this.accessibilityManager.announceForScreenReader('Could not start voice navigation. Please try again.');
        }
    }
    
    stopListening() {
        if (this.recognition && this.isListening) {
            this.recognition.stop();
            this.isListening = false;
            this.accessibilityManager.announceForScreenReader('Voice navigation deactivated.');
        }
    }
    
    processCommand(command) {
        // Send command to server for processing
        fetch('/voice-navigation/command', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                command: command,
                context: {
                    page_title: document.title,
                    url: window.location.href
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            // Announce the result
            this.accessibilityManager.announceForScreenReader(data.message || 'Command processed');
            
            // Handle navigation if needed
            if (data.action === 'navigate' && data.destination) {
                window.location.href = data.destination;
            }
            
            // Handle other actions as needed
            this.handleAction(data);
        })
        .catch(error => {
            console.error('Error processing voice command', error);
            this.accessibilityManager.announceForScreenReader('Error processing command. Please try again.');
        });
    }
    
    handleAction(data) {
        // Handle different types of actions returned from the server
        switch (data.action) {
            case 'read_content':
                this.readPageContent(data.content_type, data.items);
                break;
            case 'describe_page':
                this.accessibilityManager.announceForScreenReader(data.message);
                break;
            case 'click':
                this.simulateClick(data.element);
                break;
            case 'go_back':
                window.history.back();
                break;
            case 'go_forward':
                window.history.forward();
                break;
            case 'refresh':
                window.location.reload();
                break;
        }
    }
    
    readPageContent(contentType, items) {
        let message = '';
        
        switch (contentType) {
            case 'headings':
                message = 'Headings on this page: ' + Object.values(items).join(', ');
                break;
            case 'links':
                message = 'Links on this page: ' + Object.keys(items).join(', ');
                break;
            case 'buttons':
                message = 'Buttons on this page: ' + items.join(', ');
                break;
            case 'navigation':
                message = 'Navigation options: ' + items.join(', ');
                break;
            default:
                message = 'Content: ' + (items.title || items.description || 'Page content');
        }
        
        this.accessibilityManager.announceForScreenReader(message);
    }
    
    simulateClick(element) {
        // In a real implementation, this would simulate clicking the specified element
        // For now, we'll just announce that a click action was requested
        this.accessibilityManager.announceForScreenReader(`Click action requested for ${element}`);
    }
    
    getStatus() {
        return {
            isSupported: !!this.recognition,
            isListening: this.isListening
        };
    }
}

// Initialize accessibility managers when the page loads
document.addEventListener('DOMContentLoaded', function() {
    const accessibilityManager = new AccessibilityManager();
    const voiceNavigationManager = new VoiceNavigationManager();
    
    // Expose managers globally for other scripts to use
    window.accessibilityManager = accessibilityManager;
    window.voiceNavigationManager = voiceNavigationManager;
    
    // Add keyboard shortcuts for accessibility features
    document.addEventListener('keydown', function(e) {
        // Alt + H: Toggle high contrast
        if (e.altKey && e.key === 'h') {
            e.preventDefault();
            accessibilityManager.toggleHighContrast();
        }
        // Alt + L: Toggle large text
        if (e.altKey && e.key === 'l') {
            e.preventDefault();
            accessibilityManager.toggleLargeText();
        }
        // Alt + M: Toggle reduced motion
        if (e.altKey && e.key === 'm') {
            e.preventDefault();
            accessibilityManager.toggleReducedMotion();
        }
        // Alt + V: Toggle voice navigation
        if (e.altKey && e.key === 'v') {
            e.preventDefault();
            if (voiceNavigationManager.isListening) {
                voiceNavigationManager.stopListening();
            } else {
                voiceNavigationManager.startListening();
            }
        }
    });
    
    // Add touch and gesture support for mobile accessibility
    let touchStartX = 0;
    let touchStartY = 0;
    
    document.addEventListener('touchstart', function(e) {
        touchStartX = e.touches[0].clientX;
        touchStartY = e.touches[0].clientY;
    }, false);
    
    document.addEventListener('touchend', function(e) {
        const touchEndX = e.changedTouches[0].clientX;
        const touchEndY = e.changedTouches[0].clientY;
        
        const deltaX = touchEndX - touchStartX;
        const deltaY = touchEndY - touchStartY;
        
        // Swipe right: Next page/slide
        if (deltaX > 50 && Math.abs(deltaY) < 50) {
            // Handle swipe right gesture
            accessibilityManager.announceForScreenReader('Swipe right detected');
        }
        // Swipe left: Previous page/slide
        else if (deltaX < -50 && Math.abs(deltaY) < 50) {
            // Handle swipe left gesture
            accessibilityManager.announceForScreenReader('Swipe left detected');
        }
        // Swipe down: Show menu
        else if (deltaY > 50 && Math.abs(deltaX) < 50) {
            // Handle swipe down gesture
            accessibilityManager.announceForScreenReader('Swipe down detected');
        }
        // Swipe up: Close menu
        else if (deltaY < -50 && Math.abs(deltaX) < 50) {
            // Handle swipe up gesture
            accessibilityManager.announceForScreenReader('Swipe up detected');
        }
    }, false);
});