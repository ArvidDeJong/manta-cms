<?php

namespace Manta\Services;

use DOMDocument;
use Exception;

class Sitemap
{
    /**
     * Generate a sitemap file from a list of URLs.
     *
     * @param string $name The name of the sitemap file.
     * @param array $urls Array of URLs to include in the sitemap.
     * @return string The path to the generated sitemap file.
     */
    public function sitemap_generator(string $name, array $urls): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $urlSet = $dom->createElement('urlset');
        $urlSet->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $urlSet->setAttribute('xmlns:image', 'http://www.google.com/schemas/sitemap-image/1.1');

        foreach ($urls as $url) {
            $urlElement = $dom->createElement('url');
            $loc = $dom->createElement('loc', htmlspecialchars($url));
            $urlElement->appendChild($loc);
            $urlSet->appendChild($urlElement);
        }

        $dom->appendChild($urlSet);
        $dom->formatOutput = true; // Pretty print the XML

        try {
            $path = public_path("$name.xml");
            $dom->save($path);
            return $path;
        } catch (Exception $e) {
            // Handle exception or log error
            return '';
        }
    }

    /**
     * Generate a main sitemap index file from a list of sitemap files.
     *
     * @param array $sitemaps Array of sitemap file paths.
     * @return void
     */
    public function main_sitemap_generator(array $sitemaps): void
    {
        $dom = new DOMDocument('1.0', 'UTF-8');
        $sitemapIndex = $dom->createElement('sitemapindex');
        $sitemapIndex->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($sitemaps as $sitemap) {
            $sitemapElement = $dom->createElement('sitemap');
            $loc = $dom->createElement('loc', htmlspecialchars($sitemap));
            $lastmod = $dom->createElement('lastmod', date('c'));
            $sitemapElement->appendChild($loc);
            $sitemapElement->appendChild($lastmod);
            $sitemapIndex->appendChild($sitemapElement);
        }

        $dom->appendChild($sitemapIndex);
        $dom->formatOutput = true; // Pretty print the XML

        try {
            $path = public_path('sitemap.xml');
            $dom->save($path);
        } catch (Exception $e) {
            // Handle exception or log error
        }
    }
}
