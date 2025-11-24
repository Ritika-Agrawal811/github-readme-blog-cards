<?php

class TextFormatter
{
    /**
     * necessary styling rendering the text :-
     * cardWidth: width of the blog card
     * cardHeight: height of the blog card
     * padding: padding around the card
     * lineHeight: line height value for text
     * fontSize: font size for the text
     */
    private float $cardHeight;
    private float $cardWidth;
    private int $padding;
    private int $fontSize;
    private int $lineHeight;
    private bool $isBold;
    private $CHAR_WIDTHS;

    /**
     * @param int card width
     * @param int card height
     * @param int padding for text
     * @param int font size
     * @param int line height for text
     */
    public function __construct(
        float $cardWidth,
        float $cardHeight,
        int $padding,
        int $fontSize,
        int $lineHeight,
        bool $isBold,
    ) {
        $this->cardWidth = $cardWidth;
        $this->cardHeight = $cardHeight;
        $this->padding = $padding;
        $this->fontSize = $fontSize;
        $this->lineHeight = $lineHeight;
        $this->isBold = $isBold;

        $this->CHAR_WIDTHS = include __DIR__ . '/char_widths.php';
    }

    /**
     * Get the X position to center a text
     * @param string text
     * @param int width
     */
    public function calculateTextCenterX(string $text): float
    {
        $textWidth = $this->calculateTextWidth($text);

        $x = $this->cardWidth / 2 - $textWidth / 2 + 5;

        return $x;
    }

    /**
     * Wrap text into multiple lines according to their width
     *
     * @param string text
     * @param int maximum number of lines
     * @return array text split into lines
     */
    public function wrapTextByWidth(string $text, int $maxLines): array
    {
        $words = explode(' ', $text);
        $lines = [];
        $currentLine = '';
        $maxWidth = $this->cardWidth - $this->padding * 2;

        foreach ($words as $word) {
            $testLine = trim($currentLine . ' ' . $word);
            $testWidth = $this->calculateTextWidth($testLine);

            if ($testWidth <= $maxWidth) {
                $currentLine = $testLine;
            } else {
                if (empty($currentLine)) {
                    // single word is too long, hence we truncate it
                    $remainingWidth = $maxWidth - $this->calculateTextWidth('...');
                    $trucatedWord = $this->truncateToWidth($word, $remainingWidth);

                    $lines[] = $trucatedWord . '...';
                    break;
                } else {
                    // add current line and start new one
                    $lines[] = $currentLine;
                    $currentLine = $word;

                    // check if we have reached max lines
                    if (count($lines) >= $maxLines) {
                        break;
                    }
                }
            }
        }

        // handle the final line
        if (!empty($currentLine) && count($lines) < $maxLines) {
            $lines[] = $currentLine;
        }

        // handle overflow with ellipses
        if (count($lines) > $maxLines) {
            $lines = array_slice($lines, 0, $maxLines);
        }

        // check if we need ellipsis on the last line
        if (count($lines) == $maxLines) {
            $allProcessedText = implode(' ', $lines);

            if (strlen($text) > strlen($allProcessedText)) {
                $lastLine = $lines[$maxLines - 1];
                $adjustedWidth = $maxWidth - $this->calculateTextWidth('...');
                $truncatedLine = $this->truncateToWidth($lastLine, $adjustedWidth);

                $lines[$maxLines - 1] = $truncatedLine . '...';
            }
        }

        return $lines;
    }

    /**
     * Truncate text to fit within the width
     *
     * @param string the text
     * @param float maximum width for text
     * @return string the truncated text
     */
    private function truncateToWidth(string $text, float $maxWidth): string
    {
        $width = $this->calculateTextWidth($text);

        if ($width <= $maxWidth) {
            return $text;
        }

        $chars = mb_str_split($text, 1, 'UTF-8');
        $result = '';

        foreach ($chars as $char) {
            $testResult = $result . $char;
            $testWidth = $this->calculateTextWidth($testResult);

            if ($testWidth > $maxWidth) {
                break;
            }

            $result = $testResult;
        }

        return $result;
    }

    /**
     * Calculate the text width
     *
     * @param string the text
     * @return float text width
     */
    private function calculateTextWidth(string $text)
    {
        $width = 0;

        // Handle multibyte characters properly
        $chars = mb_str_split($text, 1, 'UTF-8');

        foreach ($chars as $char) {
            $charWidth = $this->CHAR_WIDTHS[$char] ?? 0.56;
            $width += $charWidth * $this->fontSize;
        }

        return $this->isBold ? $width * 1.048 : $width;
    }
}
