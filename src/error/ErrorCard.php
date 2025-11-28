<?php

require_once __DIR__ . '/../text/TextFormatter.php';

class ErrorCard
{
    private string $message;
    private int $statusCode;

    /**
     * @param string error message
     * @param int error status code
     */
    public function __construct($message, $statusCode = 400)
    {
        $this->message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
        $this->statusCode = $statusCode;
    }

    /**
     * Render the error SVG
     */
    public function render()
    {
        http_response_code($this->statusCode);
        header('Content-Type: image/svg+xml');
        header('Cache-Control: no-cache, no-store, must-revalidate');

        echo $this->generateSVG();
    }

    /**
     * Generate the SVG content for the error
     * @return string
     */
    private function generateSVG()
    {
        $width = 250;
        $height = 300;

        $labelSVG = $this->renderLabel($width, $height);
        $titleSVG = $this->renderErrorTitle($width, $height);
        $messageSVG = $this->renderErrorMessage($width, $height);

        return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='isolation: isolate;' viewBox='0 0 {$width} {$height}' width='{$width}px' height='{$height}px'>
         <style>
            .error-card {
                fill: #FDFDFF;
                stroke: #E4E2E2;
                stroke-width: 3;
                rx: 10;
                ry: 10;
            }
            
            .error-icon-bg {
                fill: #fcf2f2ff;
            }

            .error-icon {
                stroke: #e74c3c;
                stroke-width: 3;
                fill: #e74c3c;
            }

            .error-title {
                font: bold 22px sans-serif;
                fill: #121212;
            }

            .error-message {
                font: 400 14px sans-serif;
                fill: #555555;
            }
        </style>
        <!-- Background -->
        <rect class='error-card' x='0' y='0' width='{$width}' height='{$height}' rx='10' ry='10'/>
        
        <!-- Error Icon (Circle with X) -->
        <circle class='error-icon-bg' cx='125' cy='80' r='40'/> 
        <circle class='error-icon' cx='125' cy='80' r='20'/>
        <line stroke='#fff' stroke-width='3' stroke-linecap='round' x1='115' y1='70' x2='135' y2='90'/>
        <line stroke='#fff' stroke-width='3' stroke-linecap='round' x1='135' y1='70' x2='115' y2='90'/> 
        
        <!-- Error Label -->
        {$labelSVG}

        <!-- Error Title -->
        {$titleSVG}

        <!-- Error Message -->
        {$messageSVG}
       
        </svg>";
    }

    /**
     * Generate SVG for the label
     *
     * @param int width
     * @param int height
     * @return string label SVG
     */
    private function renderLabel(int $width, int $height): string
    {
        $text = 'Uh oh.';

        $textObj = new TextFormatter($width, $height, 10, 24, 20, true);
        $labelX = $textObj->calculateTextCenterX($text);

        return "<text class='error-title' x='{$labelX}' y='170'>{$text}</text>";
    }

    /**
     * Generate SVG for the title
     *
     * @param int width
     * @param int height
     * @return string title SVG
     */
    private function renderErrorTitle(int $width, int $height): string
    {
        $text = 'Error loading blog card.';

        $textObj = new TextFormatter($width, $height, 10, 15, 20, false);
        $titleX = $textObj->calculateTextCenterX($text);

        return "<text class='error-message' x='{$titleX}' y='210'>{$text}</text>";
    }

    /** Generate SVG for the error message
     *
     * @param int width
     * @param int height
     * @return string message SVG
     */
    private function renderErrorMessage(int $width, int $height): string
    {
        $textObj = new TextFormatter($width, $height, 10, 15, 20, false);
        $messageX = $textObj->calculateTextCenterX($this->message);

        return "<text class='error-message' x='{$messageX}' y='232'>{$this->message}</text>";
    }
}
