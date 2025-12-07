<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpeechRecognitionService
{
    protected $apiKey;
    protected $apiUrl;
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
        $this->apiKey = env('SPEECH_TO_TEXT_API_KEY'); // Could be Google Cloud Speech-to-Text, Azure Cognitive Services, etc.
        $this->apiUrl = env('SPEECH_TO_TEXT_API_URL', 'https://speech.googleapis.com/v1/speech:recognize');
    }

    /**
     * Transcribe audio file to text
     */
    public function transcribeAudio(string $audioPath): string
    {
        $cacheKey = "speech_transcription:" . sha1($audioPath);
        $cached = $this->redisService->get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            $audioFullPath = storage_path("app/public/{$audioPath}");
            $audioContent = file_get_contents($audioFullPath);

            // Encode audio content as base64
            $audioBase64 = base64_encode($audioContent);

            // Check if we're using Google Cloud Speech-to-Text
            if (strpos($this->apiUrl, 'googleapis') !== false) {
                $result = $this->transcribeWithGoogle($audioBase64);
            } elseif (strpos($this->apiUrl, 'azure') !== false) {
                $result = $this->transcribeWithAzure($audioBase64);
            } else {
                // Use a generic API approach
                $result = $this->transcribeWithGenericApi($audioBase64);
            }

            $transcription = $result['transcription'] ?? 'Transcription failed';

            // Cache the result for 1 hour
            $this->redisService->put($cacheKey, $transcription, 3600);

            return $transcription;
        } catch (\Exception $e) {
            Log::error('Speech recognition failed: ' . $e->getMessage());
            return 'Sorry, there was an issue transcribing your audio. Please try again.';
        }
    }

    /**
     * Transcribe audio with Google Cloud Speech-to-Text
     */
    protected function transcribeWithGoogle(string $audioBase64): array
    {
        $response = Http::withHeaders([
            'Authorization' => "Bearer {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post($this->apiUrl . '?key=' . $this->apiKey, [
            'config' => [
                'encoding' => 'MP3', // Could also be LINEAR16, FLAC, etc.
                'sampleRateHertz' => 16000,
                'languageCode' => 'en-US',
                'enableAutomaticPunctuation' => true,
            ],
            'audio' => [
                'content' => $audioBase64,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();

            if (isset($data['results']) && !empty($data['results'])) {
                $transcription = '';
                foreach ($data['results'] as $result) {
                    $transcription .= $result['alternatives'][0]['transcript'] . ' ';
                }
                return [
                    'transcription' => trim($transcription),
                    'confidence' => $data['results'][0]['alternatives'][0]['confidence'] ?? 0,
                ];
            }
        }

        return [
            'transcription' => 'Transcription failed',
            'error' => $response->body(),
        ];
    }

    /**
     * Transcribe audio with Azure Cognitive Services
     */
    protected function transcribeWithAzure(string $audioBase64): array
    {
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->apiKey,
            'Content-Type' => 'audio/wav', // Change based on actual format
            'Accept' => 'application/json',
        ])->post($this->apiUrl, [
            'config' => [
                'encoding' => 'MP3',
                'sampleRateHertz' => 16000,
                'languageCode' => 'en-US',
            ],
            'audio' => [
                'content' => $audioBase64,
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'transcription' => $data['transcription'] ?? 'Transcription failed',
                'confidence' => $data['confidence'] ?? 0,
            ];
        }

        return [
            'transcription' => 'Transcription failed',
            'error' => $response->body(),
        ];
    }

    /**
     * Transcribe with a generic API
     */
    protected function transcribeWithGenericApi(string $audioBase64): array
    {
        // For demonstration, return mock data
        // In a real implementation, you would connect to your chosen STT service
        return [
            'transcription' => 'This is a simulated transcription of the audio input. In a real implementation, this would connect to a speech recognition API.',
            'confidence' => 0.85,
        ];
    }

    /**
     * Transcribe audio chunk for real-time processing
     */
    public function transcribeAudioChunk(string $chunkPath, string $sessionId): string
    {
        $cacheKey = "chunk_transcription:{$sessionId}:" . sha1($chunkPath);
        $cached = $this->redisService->get($cacheKey);

        if ($cached) {
            return $cached;
        }

        try {
            $chunkFullPath = storage_path("app/public/{$chunkPath}");
            $audioContent = file_get_contents($chunkFullPath);
            $audioBase64 = base64_encode($audioContent);

            // For real-time, we might use a different API endpoint
            $result = $this->transcribeWithGenericApi($audioBase64);

            $transcription = $result['transcription'];

            // Cache for shorter period for real-time chunks
            $this->redisService->put($cacheKey, $transcription, 600); // 10 minutes

            return $transcription;
        } catch (\Exception $e) {
            Log::error('Audio chunk transcription failed: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Process voice command to extract search parameters
     */
    public function processVoiceCommand(string $command): array
    {
        $command = strtolower(trim($command));

        // Common voice commands for marketplace search
        $patterns = [
            // Search patterns
            '/find (.+?)(?: in (.+?))?$/i' => ['action' => 'search', 'extract' => ['query', 'location']],
            '/show me (.+?)(?: in (.+?))?$/i' => ['action' => 'search', 'extract' => ['query', 'location']],
            '/look for (.+?)(?: in (.+?))?$/i' => ['action' => 'search', 'extract' => ['query', 'location']],
            '/search for (.+?)(?: in (.+?))?$/i' => ['action' => 'search', 'extract' => ['query', 'location']],

            // Price filter patterns
            '/(.+?) under ([\d,]+) naira?/i' => ['action' => 'filter', 'filter_type' => 'price', 'operator' => 'under', 'extract' => ['query', 'max_price']],
            '/(.+?) over ([\d,]+) naira?/i' => ['action' => 'filter', 'filter_type' => 'price', 'operator' => 'over', 'extract' => ['query', 'min_price']],
            '/price under ([\d,]+) naira?/i' => ['action' => 'filter', 'filter_type' => 'max_price', 'extract' => ['max_price']],
            '/price over ([\d,]+) naira?/i' => ['action' => 'filter', 'filter_type' => 'min_price', 'extract' => ['min_price']],

            // Location patterns
            '/in (.+?)(?: near (.+?))?$/i' => ['action' => 'location', 'extract' => ['location', 'proximity']],
            '/near (.+?)$/i' => ['action' => 'location', 'extract' => ['location']],
        ];

        foreach ($patterns as $pattern => $config) {
            if (preg_match($pattern, $command, $matches)) {
                $params = ['action' => $config['action']];
                
                if (isset($config['extract'])) {
                    for ($i = 1; $i <= count($config['extract']); $i++) {
                        if (isset($matches[$i])) {
                            $params[$config['extract'][$i - 1]] = $matches[$i];
                        }
                    }
                }
                
                if (isset($config['filter_type'])) {
                    $params['filter_type'] = $config['filter_type'];
                }
                
                if (isset($config['operator'])) {
                    $params['operator'] = $config['operator'];
                }

                return $params;
            }
        }

        // If no pattern matches, return basic search
        return [
            'action' => 'search',
            'query' => $command,
        ];
    }

    /**
     * Validate audio file format and quality
     */
    public function validateAudioFile(string $audioPath): array
    {
        $validationResult = [
            'is_valid' => false,
            'format' => null,
            'duration' => 0,
            'sample_rate' => null,
            'bitrate' => null,
            'size_bytes' => 0,
            'issues' => [],
        ];

        $fullPath = storage_path("app/public/{$audioPath}");

        if (!file_exists($fullPath)) {
            $validationResult['issues'][] = 'File does not exist';
            return $validationResult;
        }

        $validationResult['size_bytes'] = filesize($fullPath);

        // Check file size (max 10MB for voice search)
        if ($validationResult['size_bytes'] > 10 * 1024 * 1024) {
            $validationResult['issues'][] = 'File too large (max 10MB)';
        }

        // Try to get audio info using FFmpeg if available
        if (extension_loaded('ffmpeg')) {
            try {
                $movie = new \FFMpeg\FFMpeg();
                $video = $movie->open($fullPath);
                $format = $video->getFormat();

                $validationResult['format'] = $format->get('format_name');
                $validationResult['duration'] = $format->get('duration');
                $validationResult['bitrate'] = $format->get('bit_rate');
            } catch (\Exception $e) {
                // FFmpeg not available or can't read file
                $validationResult['format'] = pathinfo($fullPath, PATHINFO_EXTENSION);
                $validationResult['issues'][] = 'Could not analyze audio properties with FFmpeg';
            }
        } else {
            // Just get basic info
            $validationResult['format'] = pathinfo($fullPath, PATHINFO_EXTENSION);
        }

        // Check if format is supported
        $supportedFormats = ['mp3', 'wav', 'm4a', 'ogg', 'flac'];
        if (!in_array(strtolower($validationResult['format']), $supportedFormats)) {
            $validationResult['issues'][] = 'Unsupported audio format';
        }

        $validationResult['is_valid'] = empty($validationResult['issues']);

        return $validationResult;
    }

    /**
     * Get supported languages for speech recognition
     */
    public function getSupportedLanguages(): array
    {
        return [
            ['code' => 'en-US', 'name' => 'English (United States)'],
            ['code' => 'en-GB', 'name' => 'English (United Kingdom)'],
            ['code' => 'fr-FR', 'name' => 'French (France)'],
            ['code' => 'es-ES', 'name' => 'Spanish (Spain)'],
            ['code' => 'ha-NG', 'name' => 'Hausa (Nigeria)'],
            ['code' => 'yo-NG', 'name' => 'Yoruba (Nigeria)'],
            ['code' => 'ig-NG', 'name' => 'Igbo (Nigeria)'],
            ['code' => 'pcm-NG', 'name' => 'Nigerian Pidgin'],
        ];
    }
}