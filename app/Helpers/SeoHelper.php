<?php

namespace App\Helpers;

class SeoHelper
{
    /**
     * Generate meta tags for a page
     */
    public static function generateMetaTags($data = [])
    {
        $defaults = [
            'title' => config('app.name') . ' - Quality Education Platform',
            'description' => 'Access comprehensive academic videos and study notes aligned with the Ugandan New Syllabus.',
            'keywords' => 'Uganda education, online learning, academic videos, study notes',
            'image' => asset('images/og-image.jpg'),
            'url' => url()->current(),
            'type' => 'website',
            'author' => 'Naf Academy',
            'robots' => 'index, follow',
        ];

        $meta = array_merge($defaults, $data);

        return $meta;
    }

    /**
     * Generate Open Graph tags
     */
    public static function generateOgTags($data = [])
    {
        $meta = self::generateMetaTags($data);

        $tags = [
            'og:type' => $meta['type'],
            'og:url' => $meta['url'],
            'og:title' => $meta['title'],
            'og:description' => $meta['description'],
            'og:image' => $meta['image'],
            'og:site_name' => config('app.name'),
        ];

        return $tags;
    }

    /**
     * Generate Twitter Card tags
     */
    public static function generateTwitterTags($data = [])
    {
        $meta = self::generateMetaTags($data);

        $tags = [
            'twitter:card' => 'summary_large_image',
            'twitter:url' => $meta['url'],
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image' => $meta['image'],
        ];

        return $tags;
    }

    /**
     * Generate Organization Schema
     */
    public static function generateOrganizationSchema($data = [])
    {
        $defaults = [
            'name' => 'Naf Academy',
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => 'Premier educational institution in Uganda',
            'email' => 'info@nafacademy.com',
            'phone' => '+256 706090021',
        ];

        $org = array_merge($defaults, $data);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'EducationalOrganization',
            'name' => $org['name'],
            'url' => $org['url'],
            'logo' => $org['logo'],
            'description' => $org['description'],
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'contactType' => 'Customer Service',
                'email' => $org['email'],
                'telephone' => $org['phone'],
            ],
        ];
    }

    /**
     * Generate Breadcrumb Schema
     */
    public static function generateBreadcrumbSchema($items = [])
    {
        $itemListElement = [];
        
        foreach ($items as $index => $item) {
            $itemListElement[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }

    /**
     * Generate Course Schema
     */
    public static function generateCourseSchema($course)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course['name'],
            'description' => $course['description'],
            'provider' => [
                '@type' => 'Organization',
                'name' => 'Naf Academy',
                'sameAs' => url('/'),
            ],
            'hasCourseInstance' => [
                '@type' => 'CourseInstance',
                'courseMode' => 'online',
                'duration' => $course['duration'] ?? 'P30D',
            ],
        ];
    }

    /**
     * Generate FAQ Schema
     */
    public static function generateFaqSchema($faqs = [])
    {
        $mainEntity = [];

        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Clean text for SEO (remove special characters, limit length)
     */
    public static function cleanText($text, $maxLength = 160)
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (strlen($text) > $maxLength) {
            $text = substr($text, 0, $maxLength - 3) . '...';
        }

        return $text;
    }

    /**
     * Generate alt text for images
     */
    public static function generateAltText($filename, $context = '')
    {
        // Remove file extension
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // Replace dashes and underscores with spaces
        $name = str_replace(['-', '_'], ' ', $name);
        
        // Capitalize words
        $name = ucwords($name);
        
        // Add context if provided
        if ($context) {
            $name = $context . ' - ' . $name;
        }

        return $name;
    }

    /**
     * Generate canonical URL
     */
    public static function generateCanonicalUrl($path = null)
    {
        if ($path) {
            return url($path);
        }

        return url()->current();
    }

    /**
     * Generate hreflang tags for multilingual sites
     */
    public static function generateHreflangTags($languages = [])
    {
        $tags = [];

        foreach ($languages as $lang => $url) {
            $tags[] = [
                'rel' => 'alternate',
                'hreflang' => $lang,
                'href' => $url,
            ];
        }

        return $tags;
    }
}
