<?php

require_once __DIR__ . '/text/TextFormatter.php';
require_once __DIR__ . '/theme/Theme.php';
require_once __DIR__ . '/error/ErrorCard.php';

class Card
{
    /**
     * necessary styling variables for the blog card :-
     * blogURL: url of the blog
     * cardWidth: width of the blog card
     * cardHeight: height of the blog card
     * padding: padding around the card
     * lineHeight: line height value for title and description
     * layout: horizontal or vertical layout
     * theme : color theme for card
     */
    private array $layoutData = [
        'horizontal' => [
            'width' => 385,
            'height' => 110,
            'padding' => 8,
            'titleFontSize' => 13,
            'desciptionFontSize' => 11,
            'tagFontSize' => 11,
            'faviconSize' => 15,
            'lineHeight' => 18,
        ],
        'vertical' => [
            'width' => 250,
            'height' => 300,
            'padding' => 8,
            'titleFontSize' => 14,
            'desciptionFontSize' => 12,
            'tagFontSize' => 12,
            'faviconSize' => 16,
            'lineHeight' => 20,
        ],
    ];

    private float $cardWidth;
    private float $cardHeight;
    private int $padding;
    private int $titleFontSize;
    private int $desciptionFontSize;
    private int $tagFontSize;
    private int $faviconSize;
    private int $lineHeight;

    // received via params
    private string $blogURL;
    private string $layout;
    private string $theme;

    /**
     * @param string blog URL
     */
    public function __construct(string $blogURL, string $layout, string $theme)
    {
        $this->blogURL = $blogURL;
        $this->layout = $layout;
        $this->theme = $theme;

        $dimensions = $this->layoutData[$layout];
        $this->cardWidth = $dimensions['width'];
        $this->cardHeight = $dimensions['height'];
        $this->padding = $dimensions['padding'];
        $this->titleFontSize = $dimensions['titleFontSize'];
        $this->desciptionFontSize = $dimensions['desciptionFontSize'];
        $this->tagFontSize = $dimensions['tagFontSize'];
        $this->faviconSize = $dimensions['faviconSize'];
        $this->lineHeight = $dimensions['lineHeight'];
    }

    /**
     * @return string the final blog card SVG
     */
    public function render(): string
    {
        $escapedURL = htmlspecialchars($this->blogURL);

        if ($escapedURL) {
            $html = $this->fetchHTML($escapedURL);

            if (!$html) {
                $errorCard = new ErrorCard('Failed to extra blog metadata', 400);
                $errorCard->render();
                exit();
            }

            $meta = $this->extractMetadata($html);
            return $this->compressSVG($meta);
        }
    }

    /**
     * Fetch the HTML content for the blog URL and enforce the encoding to UTF-8
     *
     * @param string blog URL
     * @return string HTML content of blog page
     */
    private function fetchHTML($url): ?string
    {
        $context = stream_context_create([
            'http' => [
                'header' => "User-Agent: PHP\r\n",
            ],
        ]);
        $html = @file_get_contents($url, false, $context);
        if ($html) {
            $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        return $html ?: null;
    }

    /**
     * extracts the necessary metadata - image, title and description from the HTML content fetched previously
     *
     * @param string HTML content of blog page
     * @return array<string,string> blog metadata mapping
     */
    private function extractMetadata($html): array
    {
        // Force UTF-8 for encoding
        if (stripos($html, '<meta charset=') === false) {
            $html = preg_replace('/<head[^>]*>/i', '$0<meta charset="UTF-8">', $html, 1);
        }

        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $tags = [];
        foreach ($doc->getElementsByTagName('meta') as $meta) {
            $property = $meta->getAttribute('property') ?: $meta->getAttribute('name');
            $content = $meta->getAttribute('content');
            if ($property && $content) {
                $tags[$property] = $content;
            }
        }

        $title = $doc->getElementsByTagName('title')->item(0)?->nodeValue;

        // Extract site name from og:site_name if available
        $siteName = $tags['og:site_name'] ?? parse_url($url, PHP_URL_HOST);

        // Extract favicon
        $favicon = null;
        foreach ($doc->getElementsByTagName('link') as $link) {
            $rel = strtolower($link->getAttribute('rel'));
            if (in_array($rel, ['icon', 'shortcut icon', 'apple-touch-icon'])) {
                $href = $link->getAttribute('href');
                if ($href) {
                    // Resolve relative URLs
                    $favicon =
                        str_starts_with($href, 'http') || str_starts_with($href, '//')
                            ? $href
                            : rtrim($url, '/') . '/' . ltrim($href, '/');
                    break;
                }
            }
        }

        // Fallback to root domain favicon
        if (!$favicon) {
            $parsed = parse_url($url);
            $favicon = $parsed['scheme'] . '://' . $parsed['host'] . '/favicon.ico';
        }

        return [
            'title' => $tags['og:title'] ?? ($title ?? 'No title found'),
            'description' => $tags['og:description'] ?? ($tags['description'] ?? 'No description'),
            'image' => $tags['og:image'] ?? null,
            'site_name' => $siteName,
            'favicon' => $favicon,
            'published_date' => $tags['article:published_time'] ?? ($tags['last-updated'] ?? 'No date'),
        ];
    }

    /**
     * Compress SVG
     *
     * @param array<string,string> blog metadata
     * @return string compressed svg
     */
    private function compressSVG($meta)
    {
        $svgContent = $this->generateSVG($meta);

        // Remove unnecessary whitespace but keep structure
        $svgContent = preg_replace('/>\s+</', '><', $svgContent);

        // Remove comments
        $svgContent = preg_replace('/<!--.*?-->/', '', $svgContent);

        // Optimize path data (round numbers to 1 decimal place)
        $svgContent = preg_replace_callback(
            '/(\d+\.\d{2,})/',
            function ($matches) {
                return round($matches[1], 1);
            },
            $svgContent,
        );

        // Remove unnecessary attributes
        $svgContent = str_replace(' fill-opacity="1"', '', $svgContent);
        $svgContent = str_replace(' stroke="none"', '', $svgContent);

        return $svgContent;
    }

    /**
     * Get the SVG for the blog card
     *
     * @param array<string,string> the metadata for the blog
     * @return string the SVG for card
     */
    private function generateSVG($meta): string
    {
        // blog image
        $imageSVG = $this->renderImage($meta);

        // blog title
        $titleData = $this->renderTitle($meta);
        $titleSVG = $titleData['svg'];
        $titleLines = $titleData['lineCount'];

        // blog description
        $descriptionSVG = $this->renderDescription($meta, $titleLines);

        // blog published date
        $publishedDateSVG = $this->renderPublishedDate($meta);

        // blog website tag
        $blogWebsiteTag = $this->renderBlogWebsiteTag($meta);

        // card theme
        $themeObj = new Theme($this->theme);
        $theme = $themeObj->getTheme();

        return "<svg xmlns='http://www.w3.org/2000/svg' xmlns:xlink='http://www.w3.org/1999/xlink' style='isolation: isolate;' viewBox='0 0 {$this->cardWidth} {$this->cardHeight}' width='{$this->cardWidth}px' height='{$this->cardHeight}px'>
            <style>

                .title {
                    font: bold {$this->titleFontSize}px sans-serif;
                    fill: {$theme['title']};
                }

                .description {
                    font: {$this->desciptionFontSize}px sans-serif;
                    fill: {$theme['description']};
                }

                .card-bg {
                    fill: {$theme['background']};
                    stroke: {$theme['stroke']};
                    stroke-width: 2;
                    rx: 10;
                    ry: 10;
                }

                .tag {
                  fill: {$theme['tagBackground']};
                  rx: 6; 
                  ry: 6; 
                }

                .tagTitle {
                    font: bold {$this->tagFontSize}px sans-serif;
                    fill: {$theme['tagTitle']};
                }

                .published_date {
                    font: 400 {$this->tagFontSize}px sans-serif;
                    fill: {$theme['description']};
                }
                
                .clock-icon {
                    stroke: {$theme['description']};
                    stroke-width: 1.2;
                }

            </style>

            <!-- Card background -->
            <rect class='card-bg' x='0' y='0' width='{$this->cardWidth}' height='{$this->cardHeight}' rx='10' ry='10'/>

            <!-- Blog image (full width, half height) -->
            {$imageSVG}

             <!-- Title -->
            {$titleSVG}

            <!-- Description -->
            {$descriptionSVG}

            <!-- Published Date -->
            {$publishedDateSVG} 

            <!-- Blog Site Icon and Name -->
            {$blogWebsiteTag}
        </svg>";
    }

    /**
     * Generate SVG for the blog image
     *
     * @param array<string,string> blog metadata
     * @return string image SVG
     */
    private function renderImage($meta): string
    {
        $image = $meta['image'] ?? '';
        $encodedImage = $this->fetchAndEncodeImage($image);
        if (!$encodedImage) {
            return '';
        }

        $imageDimensions = [
            'horizontal' => [
                'width' => $this->cardWidth * 0.2,
                'height' => $this->cardHeight - $this->padding * 2,
            ],
            'vertical' => [
                'width' => $this->cardWidth - $this->padding * 2,
                'height' => $this->cardHeight / 2,
            ],
        ];

        $width = $imageDimensions[$this->layout]['width'];
        $height = $imageDimensions[$this->layout]['height'];

        return "
            <defs>
                <clipPath id='rounded-image-clip'>
                    <rect x='{$this->padding}' y='{$this->padding}' width='{$width}' height='{$height}' rx='10' ry='10' />
                </clipPath>
            </defs>

            <image 
                x='{$this->padding}' 
                y='{$this->padding}' 
                width='{$width}' 
                height='{$height}' 
                href='{$encodedImage}' 
                preserveAspectRatio='xMidYMid slice'
                clip-path='url(#rounded-image-clip)'
            />
        ";
    }

    /**
     * Generate SVG for blog title
     *
     * @param array<string,string> blog metadata
     * @return array<string,string> title svg and lines count
     */
    private function renderTitle($meta): array
    {
        $title = $meta['title'] ?? 'No Title';

        $titlePositions = [
            'horizontal' => [
                'x' => $this->cardWidth * 0.2 + $this->padding * 2,
                'y' => $this->lineHeight * 1.25,
                'width' => $this->cardWidth * 0.8 - $this->padding,
                'lines' => 2,
            ],
            'vertical' => [
                'x' => $this->padding,
                'y' => $this->cardHeight / 2 + $this->lineHeight + $this->padding * 1.5,
                'width' => $this->cardWidth,
                'lines' => 3,
            ],
        ];

        $width = $titlePositions[$this->layout]['width'];
        $titleXPosition = $titlePositions[$this->layout]['x'];
        $titleYPosition = $titlePositions[$this->layout]['y'];
        $maxLines = $titlePositions[$this->layout]['lines'];

        // break title into multiple lines
        $titleData = $this->renderText($title, $this->titleFontSize, $maxLines, true, $width, $titleXPosition);
        $titleSpans = $titleData['svg'];
        $titleLines = $titleData['lineCount'];

        $titleSVG = "
            <text 
                x='{$titleXPosition}' 
                y='{$titleYPosition}' 
                class='title'>{$titleSpans}</text>
        ";

        return [
            'svg' => $titleSVG,
            'lineCount' => $titleLines,
        ];
    }

    /**
     * Generate SVG for blog description
     *
     * @param array<string,string> blog metadata
     * @param int number of lines for title
     * @return string SVG for description
     */
    private function renderDescription($meta, $titleLines): string
    {
        $description = $meta['description'] ?? 'No description available.';

        $descriptionPositions = [
            'horizontal' => [
                'x' => $this->cardWidth * 0.2 + $this->padding * 2,
                'y' => ($titleLines + 1) * $this->lineHeight + $this->padding,
                'width' => $this->cardWidth * 0.8 - $this->padding,
                'lines' => $titleLines == 1 ? 2 : 1,
            ],
            'vertical' => [
                'x' => $this->padding,
                'y' => $this->cardHeight / 2 + ($titleLines + 1) * $this->lineHeight + $this->padding * 2,
                'width' => $this->cardWidth,
                'lines' => max(4 - $titleLines, 1),
            ],
        ];

        $width = $descriptionPositions[$this->layout]['width'];
        $descriptionXPosition = $descriptionPositions[$this->layout]['x'];
        $descriptionYPosition = $descriptionPositions[$this->layout]['y'];
        $maxLines = $descriptionPositions[$this->layout]['lines'];

        // break description into multiple lines
        $descriptionData = $this->renderText(
            $description,
            $this->desciptionFontSize,
            $maxLines,
            false,
            $width,
            $descriptionXPosition,
        );
        $descriptionSpans = $descriptionData['svg'];

        return "
         <text 
            x='{$descriptionXPosition}' 
            y='{$descriptionYPosition}' 
            class='description'>{$descriptionSpans}</text> 
        ";
    }

    /**
     * Generate SVG for blog published date
     *
     * @param array<string,string> blog metadata
     * @return string SVG for published date
     */
    private function renderPublishedDate($meta): string
    {
        $dateString = $meta['published_date'] ?? null;

        if (!$dateString || $dateString === 'No date') {
            $formattedDate = null;
        } else {
            try {
                // Parse the ISO 8601 date
                $dateObj = new DateTime($dateString);

                // Format as "24 May, 2025"
                $formattedDate = $dateObj->format('d M, Y');
            } catch (Exception $e) {
                $formattedDate = null;
            }
        }

        if (!$formattedDate) {
            return '';
        }

        $dateDimensions = [
            'horizontal' => [
                'x' => $this->cardWidth * 0.2 + $this->padding * 2,
                'y' => $this->cardHeight - $this->padding * 2,
            ],
            'vertical' => [
                'x' => $this->padding,
                'y' => $this->cardHeight - $this->padding * 2,
            ],
        ];

        $x = $dateDimensions[$this->layout]['x'];
        $y = $dateDimensions[$this->layout]['y'];

        $iconSize = 14;
        $iconX = $x;
        $textX = $x + $iconSize + 3;

        return "
        <!-- Clock Icon -->
        <g transform='translate({$iconX}, " .
            ($y - 9.5) .
            ")'>
            <!-- Clock circle -->
            <circle cx='6' cy='6' r='6' class='clock-icon' fill='none'/>
            <!-- Hour hand -->
            <line x1='6' y1='6' x2='6' y2='3' class='clock-icon' stroke-linecap='round'/>
            <!-- Minute hand -->
            <line x1='6' y1='6' x2='9' y2='6' class='clock-icon' stroke-linecap='round'/>
        </g>
        
        <!-- Date Text -->
        <text 
            x='{$textX}'
            y='{$y}'
            class='published_date'>{$formattedDate}</text>
        ";
    }

    /**
     * Generate SVG for blog website tag
     *
     * @param array<string,string> blog metadata
     * @return string SVG for tag
     */
    private function renderBlogWebsiteTag($meta): string
    {
        $siteName = htmlspecialchars($meta['site_name'] ?? '');
        $favicon = $meta['favicon'] ?? '';
        $encodedImage = $this->fetchAndEncodeImage($favicon);
        if (!$encodedImage) {
            return '';
        }

        $fontSize = $this->tagFontSize;
        $padding = 6; // inner padding for the tag box
        $gap = 5; // space between favicon and text

        // Estimate text width
        $avgCharWidth = 0.6 * $fontSize;
        $textWidth = strlen($siteName) * $avgCharWidth;

        // Total tag box width = favicon + gap + text + 2*padding
        $boxWidth = $this->faviconSize + $gap + $textWidth + 2 * $padding;
        $boxHeight = $this->faviconSize + 2 * $padding;

        // Box position: bottom right
        $boxX = $this->cardWidth - $this->padding - $boxWidth;
        $boxY = $this->cardHeight - $this->padding - $boxHeight;

        // Position elements inside box
        $faviconX = $boxX + $padding;
        $faviconY = $boxY + $padding;

        $textX = $faviconX + $this->faviconSize + $gap;
        $textY = $faviconY + $fontSize * 1.05;

        return "
            <g>
                <rect 
                    x='{$boxX}' 
                    y='{$boxY}' 
                    width='{$boxWidth}' 
                    height='{$boxHeight}' 
                    class='tag' 
                />
                <image 
                    x='{$faviconX}' 
                    y='{$faviconY}' 
                    width='{$this->faviconSize}' 
                    height='{$this->faviconSize}' 
                    href='{$encodedImage}' 
                    preserveAspectRatio='xMidYMid slice'
                />
                <text 
                    x='{$textX}' 
                    y='{$textY}' 
                    class='tagTitle'
                >
                 {$siteName}
                </text>
            </g>
        ";
    }

    /**
     * Generate the tspans and number of lines for the wrapped text
     *
     * @param string text
     * @param int font size
     * @param int maximum number of lines
     * @param float width
     * @param float x position for each tspan tags
     * @return array<string,string> SVG and lines count
     */
    private function renderText(string $text, int $fontSize, int $maxLines, bool $isBold, float $width, float $x): array
    {
        $textObj = new TextFormatter($width, $this->cardHeight, $this->padding, $fontSize, $this->lineHeight, $isBold);
        $lines = $textObj->wrapTextByWidth($text, $maxLines);

        // generate final SVG with tspans for each line
        $svgText = '';
        foreach ($lines as $index => $line) {
            $dy = $index === 0 ? 0 : $this->lineHeight;
            $formattedLine = htmlspecialchars($line);
            $svgText .= "<tspan x=\"$x\" dy=\"$dy\">$formattedLine</tspan>";
        }

        return [
            'svg' => $svgText,
            'lineCount' => count($lines),
        ];
    }

    /**
     * Convert the image URL to base64
     *
     * @param string image URL
     * @return string base64 version of image
     */
    private function fetchAndEncodeImage($url)
    {
        try {
            $imageData = file_get_contents($url);
            if (!$imageData) {
                return null;
            }

            $image = imagecreatefromstring($imageData);
            if (!$image) {
                return null;
            }

            $maxWidth = 350;
            $originalWidth = imagesx($image);
            $originalHeight = imagesy($image);

            if ($originalWidth <= $maxWidth) {
                // trying with webp
                if (function_exists('imagewebp')) {
                    ob_start();
                    imagewebp($image, null, 85);
                    $compressedData = ob_get_contents();
                    ob_end_clean();
                    imagedestroy($image);
                    return 'data:image/webp;base64,' . base64_encode($compressedData);
                }
            }

            $ratio = $maxWidth / $originalWidth;
            $newHeight = (int) round($originalHeight * $ratio);

            $resized = imagecreatetruecolor($maxWidth, $newHeight);
            imageantialias($resized, true);

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $maxWidth, $newHeight, $originalWidth, $originalHeight);

            // fallback to JPEG
            if (function_exists('imagewebp')) {
                ob_start();
                imagewebp($resized, null, 80);
                $compressedData = ob_get_contents();
                ob_end_clean();
                $mimeType = 'image/webp';
            } else {
                ob_start();
                imagejpeg($resized, null, 85);
                $compressedData = ob_get_contents();
                ob_end_clean();
                $mimeType = 'image/jpeg';
            }

            imagedestroy($image);
            imagedestroy($resized);

            return "data:$mimeType;base64," . base64_encode($compressedData);
        } catch (Exception $e) {
            return null;
        }
    }
}
