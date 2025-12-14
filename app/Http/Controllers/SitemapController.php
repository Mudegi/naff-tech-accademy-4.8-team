<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Topic;
use App\Models\Resource;
use Illuminate\Http\Response;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        // Homepage
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily', Carbon::now());

        // Static pages
        $staticPages = [
            ['url' => route('about'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('contact'), 'priority' => '0.8', 'changefreq' => 'monthly'],
            ['url' => route('pricing'), 'priority' => '0.9', 'changefreq' => 'weekly'],
            ['url' => route('subjects'), 'priority' => '0.9', 'changefreq' => 'daily'],
            ['url' => route('team.members'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $sitemap .= $this->addUrl($page['url'], $page['priority'], $page['changefreq']);
        }

        // Subjects
        $subjects = Subject::where('is_active', true)->get();
        foreach ($subjects as $subject) {
            $sitemap .= $this->addUrl(
                route('subjects.show', $subject->slug),
                '0.8',
                'weekly',
                $subject->updated_at
            );
        }

        // Topics (if you have public topic pages)
        $topics = Topic::where('is_active', true)->limit(500)->get();
        foreach ($topics as $topic) {
            if (isset($topic->slug)) {
                $sitemap .= $this->addUrl(
                    url("/topics/{$topic->slug}"),
                    '0.6',
                    'weekly',
                    $topic->updated_at
                );
            }
        }

        $sitemap .= '</urlset>';

        return response($sitemap, 200)
            ->header('Content-Type', 'application/xml');
    }

    private function addUrl($loc, $priority = '0.5', $changefreq = 'weekly', $lastmod = null)
    {
        $url = '<url>';
        $url .= '<loc>' . htmlspecialchars($loc) . '</loc>';
        
        if ($lastmod) {
            $url .= '<lastmod>' . $lastmod->toAtomString() . '</lastmod>';
        }
        
        $url .= '<changefreq>' . $changefreq . '</changefreq>';
        $url .= '<priority>' . $priority . '</priority>';
        $url .= '</url>';

        return $url;
    }
}
