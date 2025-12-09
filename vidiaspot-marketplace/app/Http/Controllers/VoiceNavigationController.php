<?php

namespace App\Http\Controllers;

use App\Services\VoiceNavigationService;
use Illuminate\Http\Request;

class VoiceNavigationController extends Controller
{
    private VoiceNavigationService $voiceNavigationService;

    public function __construct()
    {
        $this->voiceNavigationService = new VoiceNavigationService();
    }

    /**
     * Process a voice command.
     */
    public function processCommand(Request $request)
    {
        $request->validate([
            'command' => 'required|string|max:500',
            'context' => 'array',
        ]);

        $result = $this->voiceNavigationService->processVoiceCommand(
            $request->command,
            $request->context ?? []
        );

        return response()->json($result);
    }

    /**
     * Transcribe speech to text.
     */
    public function transcribeSpeech(Request $request)
    {
        $request->validate([
            'audio_data' => 'required|string', // Base64 encoded audio data
            'format' => 'string|in:wav,mp3,ogg,flac',
        ]);

        // In a real implementation, this would send the audio to a speech-to-text service
        $transcription = $this->voiceNavigationService->transcribeSpeech($request->audio_data);

        return response()->json($transcription);
    }

    /**
     * Get all available voice commands.
     */
    public function getCommands()
    {
        $commands = $this->voiceNavigationService->getAllCommands();

        // Group commands by category for better organization
        $categorizedCommands = [
            'navigation' => [],
            'content' => [],
            'actions' => [],
            'listings' => [],
            'common' => [],
        ];

        foreach ($commands as $command => $action) {
            if (strpos($action, 'navigate') === 0) {
                $categorizedCommands['navigation'][] = $command;
            } elseif (strpos($action, 'read') === 0 || strpos($action, 'describe') === 0) {
                $categorizedCommands['content'][] = $command;
            } elseif (strpos($action, 'click') === 0 || strpos($action, 'go_') === 0 || strpos($action, 'refresh') === 0) {
                $categorizedCommands['actions'][] = $command;
            } elseif (strpos($action, 'post') === 0 || strpos($action, 'browse') === 0 || strpos($action, 'view') === 0) {
                $categorizedCommands['listings'][] = $command;
            } else {
                $categorizedCommands['common'][] = $command;
            }
        }

        return response()->json([
            'commands' => $categorizedCommands,
            'total_count' => count($commands),
        ]);
    }

    /**
     * Start voice navigation session.
     */
    public function startSession(Request $request)
    {
        // Initialize a voice navigation session
        $sessionId = 'voice_session_' . uniqid();
        
        session(['voice_session_id' => $sessionId]);
        
        return response()->json([
            'session_id' => $sessionId,
            'message' => 'Voice navigation session started',
            'commands' => $this->voiceNavigationService->getAllCommands(),
        ]);
    }

    /**
     * End voice navigation session.
     */
    public function endSession(Request $request)
    {
        $sessionId = session('voice_session_id');
        
        session()->forget('voice_session_id');
        
        return response()->json([
            'session_id' => $sessionId,
            'message' => 'Voice navigation session ended',
        ]);
    }

    /**
     * Get voice navigation status.
     */
    public function getStatus()
    {
        $sessionId = session('voice_session_id');
        
        return response()->json([
            'active' => !empty($sessionId),
            'session_id' => $sessionId,
            'message' => $sessionId ? 'Voice navigation active' : 'Voice navigation inactive',
        ]);
    }
}