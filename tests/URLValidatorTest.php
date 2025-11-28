<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

// load functions
require_once 'src/error/URLValidator.php';

class URLValidatorTest extends TestCase
{
    /**
     * Test valid blog URLs
     */
    public function testValidHttpsUrl()
    {
        $validator = new URLValidator('https://example.com/blog');
        $this->assertTrue($validator->validate());
    }

    /**
     * Test empty blog URLs
     */
    public function testRejectsEmptyUrl()
    {
        $validator = new URLValidator('');
        $this->assertFalse($validator->validate());
        $this->assertEquals('URL cannot be empty.', $validator->getError());
    }

    /**
     * Test that private IPs are rejected
     */
    public function testRejectsPrivateIPs()
    {
        $validator = new URLValidator('https://localhost/admin');
        $this->assertFalse($validator->validate());
        $this->assertStringContainsString('private', $validator->getError());
    }

    /**
     * Test that invalid protocols are rejected
     */
    public function testRejectsInvalidProtocol()
    {
        $validator = new URLValidator('ftp://example.com');
        $this->assertFalse($validator->validate());
    }

    /**
     * Test that long IPs are rejected
     */
    public function testRejectsTooLongUrl()
    {
        $longUrl = 'https://example.com/' . str_repeat('a', 3000);
        $validator = new URLValidator($longUrl);
        $this->assertFalse($validator->validate());
    }

    /**
     * Test that internal IPs are rejected
     */
    public function testRejectsInternalIPs()
    {
        $testCases = ['http://127.0.0.1', 'http://192.168.1.1', 'http://10.0.0.1', 'http://172.16.0.1'];

        foreach ($testCases as $url) {
            $validator = new URLValidator($url);
            $this->assertFalse($validator->validate(), "Should reject: $url");
        }
    }
}
