<?php

namespace App\Services;

use Illuminate\Support\Str;

class CognitiveAccessibilityService
{
    /**
     * Simplify text content for users with cognitive disabilities
     */
    public function simplifyText(string $text, array $options = []): array
    {
        $simplified = $text;
        
        // Default options
        $opts = array_merge([
            'reading_level' => 'elementary', // elementary, middle, high_school
            'remove_complex_words' => true,
            'add_visual_cues' => true,
            'break_down_sentences' => true,
            'highlight_keywords' => false,
        ], $options);
        
        // Break down complex sentences
        if ($opts['break_down_sentences']) {
            $simplified = $this->breakDownComplexSentences($simplified);
        }
        
        // Replace complex words with simpler alternatives
        if ($opts['remove_complex_words']) {
            $simplified = $this->replaceComplexWords($simplified, $opts['reading_level']);
        }
        
        // Generate summary of the text
        $summary = $this->generateSummary($text, $opts['reading_level']);
        
        return [
            'original' => $text,
            'simplified' => $simplified,
            'summary' => $summary,
            'reading_level' => $opts['reading_level'],
            'simplified_at' => now()->toISOString(),
        ];
    }

    /**
     * Break down complex sentences into simpler ones
     */
    private function breakDownComplexSentences(string $text): string
    {
        // Split text into sentences
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        $simplifiedSentences = [];
        
        foreach ($sentences as $sentence) {
            if (str_word_count($sentence) > 20) { // If sentence has more than 20 words
                // Try to break down using conjunctions
                $subSentences = preg_split('/\b(and|or|but|so|because|since|when|while)\b/i', $sentence, -1, PREG_SPLIT_DELIM_CAPTURE);
                
                if (count($subSentences) > 1) {
                    $restructured = [];
                    for ($i = 0; $i < count($subSentences); $i += 2) {
                        $clause = trim($subSentences[$i]);
                        $connector = isset($subSentences[$i + 1]) ? ' ' . trim($subSentences[$i + 1]) . ' ' : '';
                        
                        if (!empty($clause)) {
                            if ($i === 0) {
                                $restructured[] = $clause . ($connector ? $connector : '. ');
                            } else {
                                $restructured[] = 'Also, ' . lcfirst($clause) . '. ';
                            }
                        }
                    }
                    $simplifiedSentences = array_merge($simplifiedSentences, $restructured);
                } else {
                    $simplifiedSentences[] = $sentence;
                }
            } else {
                $simplifiedSentences[] = $sentence;
            }
        }
        
        return implode(' ', $simplifiedSentences);
    }

    /**
     * Replace complex words with simpler alternatives
     */
    private function replaceComplexWords(string $text, string $readingLevel): string
    {
        // Define word replacement dictionaries based on reading level
        $wordReplacements = [
            'elementary' => [
                'utilize' => 'use',
                'facilitate' => 'help',
                'implement' => 'use',
                'establish' => 'set up',
                'demonstrate' => 'show',
                'analyze' => 'look at',
                'obtain' => 'get',
                'acquire' => 'get',
                'commence' => 'start',
                'terminate' => 'end',
                'initiate' => 'start',
                'conclude' => 'end',
                'ascertain' => 'find out',
                'examine' => 'look at',
                'endeavor' => 'try',
                'procure' => 'get',
                'commence' => 'begin',
                'terminate' => 'stop',
                'subsequently' => 'then',
                'nevertheless' => 'but',
                'moreover' => 'also',
                'furthermore' => 'also',
                'consequently' => 'so',
                'approximately' => 'about',
                'frequently' => 'often',
                'occasionally' => 'sometimes',
                'constantly' => 'always',
                'immediately' => 'right away',
                'subsequently' => 'next',
                'initially' => 'first',
                'ultimately' => 'finally',
                'significant' => 'important',
                'substantial' => 'big',
                'adequate' => 'enough',
                'sufficient' => 'enough',
                'essential' => 'important',
                'critical' => 'very important',
                'complex' => 'complicated',
                'sophisticated' => 'fancy',
                'comprehensive' => 'complete',
                'extensive' => 'big',
                'detailed' => 'thorough',
                'thorough' => 'complete',
                'precise' => 'exact',
                'accurate' => 'right',
                'efficient' => 'fast',
                'effective' => 'good',
                'optimal' => 'best',
                'desirable' => 'good',
                'preferable' => 'better',
                'advantageous' => 'good',
                'beneficial' => 'helpful',
                'detrimental' => 'bad',
                'adverse' => 'bad',
                'negative' => 'bad',
                'positive' => 'good',
                'satisfactory' => 'good',
                'unsatisfactory' => 'not good',
                'inadequate' => 'not enough',
                'insufficient' => 'not enough',
            ],
            'middle' => [
                'utilize' => 'use',
                'facilitate' => 'help',
                'implement' => 'put in place',
                'establish' => 'set up',
                'demonstrate' => 'show',
                'analyze' => 'analyze',
                'obtain' => 'obtain',
                'acquire' => 'acquire',
                'commence' => 'commence',
                'terminate' => 'terminate',
                'consequently' => 'consequently',
                'approximately' => 'approximately',
                'subsequently' => 'subsequently',
                'nevertheless' => 'nevertheless',
                'moreover' => 'moreover',
                'furthermore' => 'furthermore',
            ],
        ];
        
        $replacements = $wordReplacements[$readingLevel] ?? $wordReplacements['elementary'];
        
        foreach ($replacements as $complex => $simple) {
            $pattern = '/\b' . preg_quote($complex, '/') . '\b/i';
            $text = preg_replace($pattern, $simple, $text);
        }
        
        return $text;
    }

    /**
     * Generate a summary of the text
     */
    private function generateSummary(string $text, string $readingLevel): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $text);
        
        // Get the first few sentences as summary (or extract key sentences)
        $summarySentences = array_slice($sentences, 0, min(3, count($sentences)));
        $summary = implode(' ', $summarySentences);
        
        // Ensure the summary is appropriate for the reading level
        if ($readingLevel === 'elementary') {
            $summary = $this->replaceComplexWords($summary, $readingLevel);
        }
        
        return $summary;
    }

    /**
     * Generate alternative content formats for cognitive accessibility
     */
    public function generateAlternativeFormats(string $content): array
    {
        return [
            'simplified_text' => $this->simplifyText($content, ['reading_level' => 'elementary']),
            'bulleted_list' => $this->convertToBulletedList($content),
            'step_by_step' => $this->convertToStepByStep($content),
            'visual_cues' => $this->extractVisualCues($content),
            'key_points' => $this->extractKeyPoints($content),
        ];
    }

    /**
     * Convert content to bulleted list
     */
    private function convertToBulletedList(string $content): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $content);
        $items = [];
        
        foreach ($sentences as $sentence) {
            $sentence = trim($sentence);
            if (!empty($sentence)) {
                $items[] = $sentence;
            }
        }
        
        return $items;
    }

    /**
     * Convert content to step-by-step format
     */
    private function convertToStepByStep(string $content): array
    {
        // Look for action words to identify steps
        $actionWords = ['first', 'second', 'next', 'then', 'finally', 'step', 'next', 'after'];
        
        $paragraphs = explode("\n", $content);
        $steps = [];
        $stepNumber = 1;
        
        foreach ($paragraphs as $paragraph) {
            $paragraph = trim($paragraph);
            if (!empty($paragraph)) {
                $steps[] = [
                    'step' => $stepNumber++,
                    'content' => $paragraph
                ];
            }
        }
        
        return $steps;
    }

    /**
     * Extract visual cues from content
     */
    private function extractVisualCues(string $content): array
    {
        // Identify potential visual cues in the text
        $visualCuePatterns = [
            '/(image|picture|photo|diagram|chart|graph|illustration)/i',
            '/(show|display|appear|look|see)/i',
            '/(color|shape|size|position)/i',
        ];
        
        $cues = [];
        foreach ($visualCuePatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $cues = array_merge($cues, $matches[0]);
            }
        }
        
        return array_unique($cues);
    }

    /**
     * Extract key points from content
     */
    private function extractKeyPoints(string $content): array
    {
        $sentences = preg_split('/(?<=[.!?])\s+/', $content);
        $keyPoints = [];
        
        foreach ($sentences as $sentence) {
            // Look for sentences with important keywords
            $sentence = trim($sentence);
            if (strlen($sentence) > 10) { // Filter out very short sentences
                $keyPoints[] = $sentence;
            }
        }
        
        // Return top 5 key points
        return array_slice($keyPoints, 0, 5);
    }

    /**
     * Create a simplified interface structure
     */
    public function createSimplifiedInterface(array $originalComponents, array $options = []): array
    {
        $simplified = [
            'layout' => $this->simplifyLayout($originalComponents, $options),
            'navigation' => $this->simplifyNavigation($originalComponents, $options),
            'content' => $this->simplifyContentStructure($originalComponents, $options),
            'controls' => $this->simplifyControls($originalComponents, $options),
        ];
        
        return $simplified;
    }

    /**
     * Simplify the layout
     */
    private function simplifyLayout(array $components, array $options): array
    {
        return [
            'columns' => 1, // Single column layout
            'spacing' => 'increased', // More white space
            'colors' => $this->getSimplifiedColorScheme($options),
            'fonts' => $this->getSimplifiedFontSettings($options),
        ];
    }

    /**
     * Simplify navigation
     */
    private function simplifyNavigation(array $components, array $options): array
    {
        // Extract main navigation items
        $mainNavItems = [];
        foreach ($components as $component) {
            if (isset($component['type']) && $component['type'] === 'navigation') {
                $items = $component['items'] ?? [];
                foreach ($items as $item) {
                    $mainNavItems[] = [
                        'label' => $item['label'],
                        'url' => $item['url'],
                        'icon' => $item['icon'] ?? null, // Icons can help recognition
                    ];
                }
                break;
            }
        }
        
        return [
            'items' => $mainNavItems,
            'max_depth' => 1, // Flatten navigation
            'highlighted_items' => array_slice($mainNavItems, 0, 3), // Highlight top 3
        ];
    }

    /**
     * Simplify content structure
     */
    private function simplifyContentStructure(array $components, array $options): array
    {
        $contentItems = [];
        
        foreach ($components as $component) {
            if (isset($component['type']) && $component['type'] === 'content') {
                $contentItems[] = [
                    'title' => $component['title'] ?? 'Content',
                    'body' => $this->simplifyText($component['body'] ?? '', $options)['simplified'],
                    'type' => $component['contentType'] ?? 'text',
                ];
            }
        }
        
        return [
            'items' => $contentItems,
            'format' => 'linear', // Linear, sequential presentation
        ];
    }

    /**
     * Simplify controls
     */
    private function simplifyControls(array $components, array $options): array
    {
        $simplifiedControls = [];
        
        foreach ($components as $component) {
            if (isset($component['type']) && $component['type'] === 'form-control') {
                $simplifiedControls[] = [
                    'label' => $component['label'],
                    'type' => $component['inputType'] ?? 'text',
                    'required' => $component['required'] ?? false,
                    'simplified' => true,
                    'clear_instructions' => $this->generateClearInstructions($component),
                ];
            }
        }
        
        return [
            'controls' => $simplifiedControls,
            'layout' => 'vertical', // Stack controls vertically
            'spacing' => 'increased',
        ];
    }

    /**
     * Generate clear instructions for a control
     */
    private function generateClearInstructions(array $control): string
    {
        $label = $control['label'] ?? 'Field';
        $type = $control['inputType'] ?? 'text';
        $required = $control['required'] ?? false;
        
        $instructions = "Enter your {$label} in the box below. ";
        
        if ($required) {
            $instructions .= "This field is required. ";
        }
        
        if ($type === 'email') {
            $instructions .= "Make sure to use the format name@website.com";
        } elseif ($type === 'tel') {
            $instructions .= "Use numbers only, like 1234567890";
        } elseif ($type === 'date') {
            $instructions .= "Select a date from the calendar";
        }
        
        return $instructions;
    }

    /**
     * Get simplified color scheme
     */
    private function getSimplifiedColorScheme(array $options): array
    {
        return [
            'primary' => '#0066cc', // Standard blue
            'secondary' => '#ffffff', // White background
            'text' => '#000000', // Black text
            'highlight' => '#ffff00', // Yellow highlight for important items
        ];
    }

    /**
     * Get simplified font settings
     */
    private function getSimplifiedFontSettings(array $options): array
    {
        return [
            'family' => 'Arial, sans-serif', // Simple, readable font
            'size' => 'medium', // Default size
            'weight' => 'normal', // Avoid thin fonts
            'spacing' => 'normal', // Normal character spacing
        ];
    }

    /**
     * Get cognitive accessibility preferences for a user
     */
    public function getUserPreferences(string $userId): array
    {
        // In a real implementation, this would fetch from the database
        // For now, return default preferences
        $defaultPreferences = [
            'reading_level' => 'elementary',
            'high_contrast' => false,
            'large_text' => false,
            'simplified_navigation' => true,
            'visual_aids' => true,
            'step_by_step_guides' => true,
            'extra_time_prompts' => true,
            'distraction_free_mode' => false,
        ];
        
        $cachedPreferences = \Cache::get("cognitive_prefs_{$userId}", $defaultPreferences);
        
        return $cachedPreferences;
    }

    /**
     * Set cognitive accessibility preferences for a user
     */
    public function setUserPreferences(string $userId, array $preferences): void
    {
        // Validate preferences
        $validPreferences = [
            'reading_level' => in_array($preferences['reading_level'] ?? '', ['elementary', 'middle', 'high_school']) ? $preferences['reading_level'] : 'elementary',
            'high_contrast' => (bool)($preferences['high_contrast'] ?? false),
            'large_text' => (bool)($preferences['large_text'] ?? false),
            'simplified_navigation' => (bool)($preferences['simplified_navigation'] ?? true),
            'visual_aids' => (bool)($preferences['visual_aids'] ?? true),
            'step_by_step_guides' => (bool)($preferences['step_by_step_guides'] ?? true),
            'extra_time_prompts' => (bool)($preferences['extra_time_prompts'] ?? true),
            'distraction_free_mode' => (bool)($preferences['distraction_free_mode'] ?? false),
        ];
        
        \Cache::put("cognitive_prefs_{$userId}", $validPreferences, now()->addDays(30));
    }
}