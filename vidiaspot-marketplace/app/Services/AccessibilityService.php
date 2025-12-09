<?php

namespace App\Services;

use Illuminate\Http\Request;

class AccessibilityService
{
    /**
     * Generate ARIA attributes for enhanced screen reader compatibility
     */
    public function generateAriaAttributes(array $config = []): array
    {
        $ariaAttributes = [];
        
        // Add role if specified
        if (isset($config['role'])) {
            $ariaAttributes['role'] = $config['role'];
        }
        
        // Add label if specified
        if (isset($config['label'])) {
            $ariaAttributes['aria-label'] = $config['label'];
        }
        
        // Add describedby if specified
        if (isset($config['describedby'])) {
            $ariaAttributes['aria-describedby'] = $config['describedby'];
        }
        
        // Add labelledby if specified
        if (isset($config['labelledby'])) {
            $ariaAttributes['aria-labelledby'] = $config['labelledby'];
        }
        
        // Add live region for dynamic content
        if (isset($config['live'])) {
            $ariaAttributes['aria-live'] = $config['live']; // 'polite', 'assertive', or 'off'
        }
        
        // Add busy state if specified
        if (isset($config['busy'])) {
            $ariaAttributes['aria-busy'] = $config['busy'] ? 'true' : 'false';
        }
        
        // Add hidden state if specified
        if (isset($config['hidden'])) {
            $ariaAttributes['aria-hidden'] = $config['hidden'] ? 'true' : 'false';
        }
        
        // Add expanded/collapsed state for collapsible elements
        if (isset($config['expanded'])) {
            $ariaAttributes['aria-expanded'] = $config['expanded'] ? 'true' : 'false';
        }
        
        // Add controls for elements that control other elements
        if (isset($config['controls'])) {
            $ariaAttributes['aria-controls'] = $config['controls'];
        }
        
        return $ariaAttributes;
    }

    /**
     * Generate accessibility-compliant HTML attributes
     */
    public function generateAccessibilityAttributes(string $elementType, array $content = []): array
    {
        $attributes = [];
        
        switch ($elementType) {
            case 'button':
                $attributes['role'] = 'button';
                $attributes['tabindex'] = '0';
                if (!isset($content['aria-label']) && isset($content['text'])) {
                    $attributes['aria-label'] = $content['text'];
                }
                break;
                
            case 'link':
                $attributes['role'] = 'link';
                $attributes['tabindex'] = '0';
                if (!isset($content['aria-label']) && isset($content['text'])) {
                    $attributes['aria-label'] = $content['text'];
                }
                break;
                
            case 'form':
                $attributes['role'] = 'form';
                if (isset($content['title'])) {
                    $attributes['aria-label'] = $content['title'];
                }
                break;
                
            case 'dialog':
                $attributes['role'] = 'dialog';
                $attributes['aria-modal'] = 'true';
                if (isset($content['title'])) {
                    $attributes['aria-label'] = $content['title'];
                }
                break;
                
            case 'navigation':
                $attributes['role'] = 'navigation';
                if (isset($content['label'])) {
                    $attributes['aria-label'] = $content['label'];
                }
                break;
                
            case 'search':
                $attributes['role'] = 'search';
                break;
                
            case 'main':
                $attributes['role'] = 'main';
                break;
                
            case 'banner':
                $attributes['role'] = 'banner';
                break;
                
            case 'contentinfo':
                $attributes['role'] = 'contentinfo';
                break;
                
            case 'complementary':
                $attributes['role'] = 'complementary';
                break;
                
            case 'region':
                $attributes['role'] = 'region';
                if (isset($content['title'])) {
                    $attributes['aria-label'] = $content['title'];
                }
                break;
        }
        
        return $attributes;
    }

    /**
     * Generate skip link for screen readers
     */
    public function generateSkipLink(string $target = '#main-content', string $text = 'Skip to main content'): string
    {
        return '<a href="' . $target . '" class="skip-link visually-hidden visually-hidden-focusable">' . $text . '</a>';
    }

    /**
     * Format content for screen readers (with visual hiding but screen reader access)
     */
    public function screenReaderOnly(string $text): string
    {
        return '<span class="sr-only">' . $text . '</span>';
    }

    /**
     * Create accessible table markup
     */
    public function createAccessibleTable(array $headers, array $rows, string $caption = ''): array
    {
        $tableData = [
            'caption' => $caption,
            'headers' => [],
            'rows' => []
        ];
        
        // Process headers with proper scope attributes
        foreach ($headers as $index => $header) {
            $tableData['headers'][] = [
                'content' => $header,
                'id' => 'th-' . uniqid() . '-' . $index,
                'scope' => 'col'
            ];
        }
        
        // Process rows with proper associations
        foreach ($rows as $rowIndex => $row) {
            $rowData = [];
            foreach ($row as $cellIndex => $cell) {
                $rowData[] = [
                    'content' => $cell,
                    'headers' => isset($tableData['headers'][$cellIndex]) ? $tableData['headers'][$cellIndex]['id'] : null
                ];
            }
            $tableData['rows'][] = $rowData;
        }
        
        return $tableData;
    }

    /**
     * Generate accessible form field attributes
     */
    public function generateAccessibleFormField(string $type, string $name, array $options = []): array
    {
        $attributes = [
            'id' => $name . '-' . uniqid(),
            'name' => $name,
            'type' => $type,
        ];
        
        if (isset($options['label'])) {
            $attributes['aria-labelledby'] = 'label-' . $name . '-' . uniqid();
        }
        
        if (isset($options['required'])) {
            $attributes['aria-required'] = $options['required'] ? 'true' : 'false';
        }
        
        if (isset($options['describedby'])) {
            $attributes['aria-describedby'] = $options['describedby'];
        }
        
        if (isset($options['invalid'])) {
            $attributes['aria-invalid'] = $options['invalid'] ? 'true' : 'false';
        }
        
        if (isset($options['autocomplete'])) {
            $attributes['autocomplete'] = $options['autocomplete'];
        }
        
        return $attributes;
    }

    /**
     * Generate focus management attributes
     */
    public function generateFocusAttributes(array $options = []): array
    {
        $attributes = [];
        
        if (isset($options['trap']) && $options['trap']) {
            $attributes['data-focus-trap'] = 'true';
            $attributes['tabindex'] = '-1';
        }
        
        if (isset($options['autoFocus']) && $options['autoFocus']) {
            $attributes['autofocus'] = 'true';
        }
        
        if (isset($options['tabIndex'])) {
            $attributes['tabindex'] = $options['tabIndex'];
        }
        
        return $attributes;
    }

    /**
     * Generate language-specific accessibility content
     */
    public function generateLocalizedAccessibilityContent(string $locale = 'en'): array
    {
        $accessibilityLabels = [
            'en' => [
                'skip_to_content' => 'Skip to main content',
                'skip_to_navigation' => 'Skip to navigation',
                'close_dialog' => 'Close dialog',
                'expand_section' => 'Expand section',
                'collapse_section' => 'Collapse section',
                'required_field' => 'Required field',
                'optional_field' => 'Optional field',
                'error_occurred' => 'An error occurred',
                'success_message' => 'Action completed successfully',
            ],
            'es' => [
                'skip_to_content' => 'Saltar al contenido principal',
                'skip_to_navigation' => 'Saltar a la navegación',
                'close_dialog' => 'Cerrar diálogo',
                'expand_section' => 'Expandir sección',
                'collapse_section' => 'Contraer sección',
                'required_field' => 'Campo requerido',
                'optional_field' => 'Campo opcional',
                'error_occurred' => 'Ocurrió un error',
                'success_message' => 'Acción completada exitosamente',
            ],
            // Add more languages as needed
        ];
        
        return $accessibilityLabels[$locale] ?? $accessibilityLabels['en'];
    }
}