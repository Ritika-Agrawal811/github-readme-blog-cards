<?php

class URLValidator
{
    private string|null $url;
    private string $error;

    // Private IP ranges to block
    private const BLOCKED_IP_PATTERNS = [
        '/^127\./', // Loopback
        '/^10\./', // Private Class A
        '/^172\.(1[6-9]|2[0-9]|3[0-1])\./', // Private Class B
        '/^192\.168\./', // Private Class C
        '/^169\.254\./', // Link-local
        '/^0\.0\.0\.0$/', // Invalid
        '/^::1$/', // IPv6 loopback
        '/^fe80:/i', // IPv6 link-local
        '/^fc00:/i', // IPv6 unique local
    ];

    private const MAX_URL_LENGTH = 2048;
    private const ALLOWED_SCHEMES = ['https'];

    /**
     * @param string blog URL
     */
    public function __construct(string|null $url)
    {
        $this->url = $url;
    }

    /**
     * Validate the blog URL
     * @return bool
     */
    public function validate(): bool
    {
        // Check if URL is empty
        if (!$this->url) {
            $this->error = 'URL cannot be empty.';
            return false;
        }

        // Check URL length
        if (strlen($this->url) > self::MAX_URL_LENGTH) {
            $this->error = 'URL is too long (max ' . self::MAX_URL_LENGTH . ' characters).';
            return false;
        }

        // Validate URL format'
        if (!filter_var($this->url, FILTER_VALIDATE_URL)) {
            $this->error = 'Invalid URL format.';
            return false;
        }

        // Parse URL components
        $parsed = parse_url($this->url);

        if ($parsed === false) {
            $this->error = 'Unable to parse URL.';
            return false;
        }

        // Check scheme
        if (!isset($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), self::ALLOWED_SCHEMES)) {
            $this->error = 'Only HTTPS protocols are allowed.';
            return false;
        }

        // Check host exists
        if (!isset($parsed['host']) || empty($parsed['host'])) {
            $this->error = 'URL must contain a valid host.';
            return false;
        }

        // Validate against private/internal IPs
        if (!$this->isPublicHost($parsed['host'])) {
            $this->error = 'Access to private/internal hosts is not allowed.';
            return false;
        }

        return true;
    }

    /**
     * Check if host is publicly accessible (not private/internal)
     * @param string $host
     * @return bool
     */
    private function isPublicHost($host): bool
    {
        // Get IP address from hostname
        $ip = gethostbyname($host);

        // If gethostbyname fails, it returns the hostname itself
        if ($ip === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            $this->error = 'Could not resolve hostname.';
            return false;
        }

        // Check against private/reserved IP ranges
        foreach (self::BLOCKED_IP_PATTERNS as $pattern) {
            if (preg_match($pattern, $ip)) {
                return false;
            }
        }

        // Use PHP's built-in filter for additional validation
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message
     * @return string|null
     */
    public function getError()
    {
        return $this->error;
    }
}
