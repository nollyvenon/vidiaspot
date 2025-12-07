@extends('admin.layout')

@section('title', 'Voice Search Management')

@section('content')
<div class="admin-card">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Voice Search Management</h2>
    </div>
    
    <!-- Voice Search Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <h3 class="text-sm font-medium text-blue-800">Total Voice Searches</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['total_voice_searches'] ?? 0 }}</p>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <h3 class="text-sm font-medium text-green-800">Success Rate</h3>
            <p class="text-2xl font-bold text-green-600">{{ number_format(($stats['accuracy_rate'] ?? 0) * 100, 1) }}%</p>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
            <h3 class="text-sm font-medium text-yellow-800">Avg Duration</h3>
            <p class="text-2xl font-bold text-yellow-600">4.2s</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
            <h3 class="text-sm font-medium text-purple-800">Most Used Device</h3>
            <p class="text-2xl font-bold text-purple-600">Mobile (68%)</p>
        </div>
    </div>
    
    <!-- Voice Search Dashboard -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Voice Queries -->
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Popular Voice Queries</h3>
            <div class="space-y-3">
                @foreach($stats['most_popular_queries'] ?? [] as $queryData)
                <div class="flex justify-between items-center p-2 border-b">
                    <span class="font-medium">{{ $queryData['query'] }}</span>
                    <span class="text-sm bg-blue-100 text-blue-800 px-2 py-1 rounded">{{ $queryData['count'] }} searches</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- Peak Usage Times -->
        <div class="admin-card">
            <h3 class="text-md font-semibold mb-3">Peak Usage Hours</h3>
            <div class="h-64">
                <canvas id="usage-chart"></canvas>
            </div>
        </div>
        
        <!-- Voice Search Demo -->
        <div class="admin-card lg:col-span-2">
            <h3 class="text-md font-semibold mb-3">Voice Search Demo</h3>
            
            <div class="border rounded-lg p-6 bg-gray-50">
                <div class="flex flex-col items-center">
                    <div class="mb-4">
                        @if($stats['accuracy_rate'] && $stats['accuracy_rate'] >= 0.9)
                            <div class="text-green-500 text-6xl">🎤</div>
                        @elseif($stats['accuracy_rate'] && $stats['accuracy_rate'] >= 0.75)
                            <div class="text-yellow-500 text-6xl">🎤</div>
                        @else
                            <div class="text-red-500 text-6xl">🎤</div>
                        @endif
                    </div>
                    
                    <div class="text-center mb-4">
                        <p class="text-gray-600 mb-2">Click the microphone to start speaking</p>
                        <div id="transcription-display" class="min-h-[60px] border rounded p-3 bg-white">
                            <p class="text-gray-500">Your transcription will appear here...</p>
                        </div>
                    </div>
                    
                    <button id="start-voice-search" class="admin-btn admin-btn-primary px-8 py-3 text-lg">
                        <i class="fas fa-microphone mr-2"></i>
                        Start Voice Search
                    </button>
                    
                    <div class="mt-4 w-full max-w-md">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Or try a sample command:</label>
                        <select id="sample-commands" class="admin-form-select w-full">
                            <option value="">Select a sample command</option>
                            <option value="find iphone for sale in lagos">Find iPhone for sale in Lagos</option>
                            <option value="show me cars under five million naira">Show me cars under five million naira</option>
                            <option value="search for laptops with good cameras">Search for laptops with good cameras</option>
                            <option value="find furniture near me">Find furniture near me</option>
                            <option value="show me used phones under ten thousand naira">Show me used phones under ten thousand naira</option>
                        </select>
                        <button id="execute-sample" class="admin-btn admin-btn-success mt-2 w-full">Execute Command</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Voice Search Configuration -->
    <div class="admin-card mt-6">
        <h3 class="text-md font-semibold mb-3">Voice Search Configuration</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="font-medium mb-2">Language Settings</h4>
                <div class="space-y-2">
                    <label class="flex items-center">
                        <input type="radio" name="language" value="en-US" checked class="mr-2">
                        <span>English (United States)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="language" value="en-GB" class="mr-2">
                        <span>English (United Kingdom)</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="language" value="pcm-NG" class="mr-2">
                        <span>Nigerian Pidgin</span>
                    </label>
                    <label class="flex items-center">
                        <input type="radio" name="language" value="ha-NG" class="mr-2">
                        <span>Hausa (Nigeria)</span>
                    </label>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium mb-2">Accuracy Settings</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimum Confidence Threshold</label>
                        <input type="range" min="0" max="100" value="75" class="w-full">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Low</span>
                            <span>Medium</span>
                            <span>High</span>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Auto-correction Level</label>
                        <select class="admin-form-select w-full">
                            <option value="off">Off</option>
                            <option value="conservative" selected>Conservative</option>
                            <option value="moderate">Moderate</option>
                            <option value="aggressive">Aggressive</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex justify-end">
            <button class="admin-btn admin-btn-primary">Save Configuration</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let recognition;
let isRecording = false;

// Initialize speech recognition (using Web Speech API)
if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    recognition = new SpeechRecognition();
    recognition.continuous = false;
    recognition.interimResults = false;
    recognition.lang = 'en-US'; // This would be set from the config
    
    recognition.onresult = function(event) {
        const transcript = event.results[0][0].transcript;
        document.getElementById('transcription-display').innerHTML = `<p class="text-blue-600 font-medium">${transcript}</p>`;
        
        // Automatically perform search after transcription
        performVoiceSearch(transcript);
    };
    
    recognition.onerror = function(event) {
        console.error('Speech recognition error', event.error);
        document.getElementById('transcription-display').innerHTML = `<p class="text-red-600">Error: ${event.error}</p>`;
        isRecording = false;
        updateRecordingButton();
    };
    
    recognition.onend = function() {
        isRecording = false;
        updateRecordingButton();
    };
}

function updateRecordingButton() {
    const button = document.getElementById('start-voice-search');
    if (isRecording) {
        button.innerHTML = '<i class="fas fa-stop mr-2"></i>Stop Recording';
        button.classList.remove('admin-btn-primary');
        button.classList.add('admin-btn-danger');
    } else {
        button.innerHTML = '<i class="fas fa-microphone mr-2"></i>Start Voice Search';
        button.classList.remove('admin-btn-danger');
        button.classList.add('admin-btn-primary');
    }
}

// Start voice recognition
document.getElementById('start-voice-search').addEventListener('click', function() {
    if (!recognition) {
        alert('Your browser does not support speech recognition. Please use Chrome or Edge.');
        return;
    }
    
    if (isRecording) {
        recognition.stop();
    } else {
        document.getElementById('transcription-display').innerHTML = '<p class="text-gray-500">Listening...</p>';
        recognition.start();
        isRecording = true;
    }
    
    updateRecordingButton();
});

// Execute sample command
document.getElementById('execute-sample').addEventListener('click', function() {
    const selectedCommand = document.getElementById('sample-commands').value;
    if (selectedCommand) {
        document.getElementById('transcription-display').innerHTML = `<p class="text-blue-600 font-medium">${selectedCommand}</p>`;
        performVoiceSearch(selectedCommand);
    }
});

// Perform voice search
function performVoiceSearch(query) {
    fetch('/admin/voice-search', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            query: query
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Voice search results:', data);
            // In a real implementation, you would display results
            alert(`Search performed: ${query}\nFound ${data.results.total ?? 0} results`);
        } else {
            alert('Search failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error performing voice search:', error);
        alert('Error performing voice search');
    });
}

// Draw usage chart
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('usage-chart').getContext('2d');
    
    // Mock data from the server
    const peakTimes = {!! json_encode($stats['peak_usage_times'] ?? []) !!};
    
    if (peakTimes.length > 0) {
        const hours = peakTimes.map(d => d.hour);
        const percentages = peakTimes.map(d => d.percentage);
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: hours,
                datasets: [{
                    label: 'Usage Percentage',
                    data: percentages,
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 25
                    }
                }
            }
        });
    }
});

// Language selection
document.querySelectorAll('input[name="language"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (recognition) {
            recognition.lang = this.value;
        }
    });
});
</script>
@endsection