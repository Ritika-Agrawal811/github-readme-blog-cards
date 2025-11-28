<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// load functions
require_once 'src/theme/Theme.php';

class OptionsTest extends TestCase
{
    /**
     * Test theme request parameters return colors for theme
     */
    public function testCorrectThemeValues()
    {
        $THEMES = include 'src/theme/themes_list.php';
        foreach ($THEMES as $theme => $colors) {
            $themeObj = new Theme($theme);
            $actualColors = $themeObj->getTheme();
            $expectedColors = $colors;
            $this->assertEquals($expectedColors, $actualColors);
        }
    }

    /**
     * Test fallback to default theme
     */
    public function testFallbackToDefaultTheme()
    {
        $themeObj = new Theme('invalid_theme');
        $actualColors = $themeObj->getTheme();

        $expectedThemeObj = new Theme('default');
        $expectedColors = $expectedThemeObj->getTheme();
        $this->assertEquals($expectedColors, $actualColors);
    }
}
