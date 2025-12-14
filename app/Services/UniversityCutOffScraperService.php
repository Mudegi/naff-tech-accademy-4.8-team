<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\DomCrawler\Crawler;
use App\Models\UniversityCutOff;
use App\Models\University;

class UniversityCutOffScraperService
{
    /**
     * Scrape cut-off points from Kyambogo University website
     */
    public function scrapeKyambogo($academicYear = null)
    {
        $url = 'https://kyu.ac.ug/cut-off-point/';
        
        try {
            // Fetch the webpage
            // Note: verify(false) is used to bypass SSL certificate issues in development
            // In production, ensure proper SSL certificates are configured
            $response = Http::timeout(30)
                ->withoutVerifying() // Bypass SSL certificate verification for development
                ->withOptions([
                    'verify' => false, // Disable SSL verification
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->get($url);
            
            if (!$response->successful()) {
                throw new \Exception('Failed to fetch webpage. Status: ' . $response->status());
            }
            
            $html = $response->body();
            
            // Ensure the Crawler class is available
            if (!class_exists(Crawler::class)) {
                throw new \Exception('DOM Crawler class not found. Please run: composer require symfony/dom-crawler symfony/css-selector');
            }
            
            $crawler = new Crawler($html);
            
            // Find the table with cut-off points
            $table = $crawler->filter('table')->first();
            
            if ($table->count() === 0) {
                throw new \Exception('No table found on the webpage');
            }
            
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'skipped' => 0,
            ];
            
            $academicYear = $academicYear ?? date('Y');
            
            // Extract rows from the table (try both tbody tr and direct tr)
            $rows = $table->filter('tbody tr');
            if ($rows->count() === 0) {
                $rows = $table->filter('tr');
            }
            
            $rowIndex = 0;
            $rows->each(function ($row) use (&$results, &$rowIndex, $academicYear) {
                $rowIndex++;
                
                // Skip header row (first row)
                if ($rowIndex === 1) {
                    return;
                }
                
                $cells = $row->filter('td');
                
                if ($cells->count() < 4) {
                    return; // Skip rows without enough columns
                }
                
                try {
                    // Extract data: S/No, Programme Name, Prog. Code, Cut-off Points
                    $sno = trim($cells->eq(0)->text());
                    $programmeName = trim($cells->eq(1)->text());
                    $progCode = trim($cells->eq(2)->text());
                    $cutOffPoints = trim($cells->eq(3)->text());
                    
                    // Skip if programme name is empty
                    if (empty($programmeName)) {
                        $results['skipped']++;
                        return;
                    }
                    
                    // Handle empty or invalid cut-off points
                    if (empty($cutOffPoints) || $cutOffPoints === '-' || $cutOffPoints === '—' || strtolower($cutOffPoints) === 'n/a') {
                        $results['skipped']++;
                        return;
                    }
                    
                    // Convert cut-off points to float
                    $cutOffPoints = (float) str_replace(',', '', $cutOffPoints);
                    
                    if ($cutOffPoints <= 0) {
                        $results['skipped']++;
                        return;
                    }
                    
                    // Determine program category
                    $programCategory = $this->determineProgramCategory($programmeName);
                    
                    // Check if record already exists
                    $exists = UniversityCutOff::where('university_name', 'Kyambogo University')
                        ->where('course_name', $programmeName)
                        ->where('academic_year', $academicYear)
                        ->exists();
                    
                    if ($exists) {
                        // Update existing record
                        UniversityCutOff::where('university_name', 'Kyambogo University')
                            ->where('course_name', $programmeName)
                            ->where('academic_year', $academicYear)
                            ->update([
                                'course_code' => $progCode ?: null,
                                'cut_off_points' => $cutOffPoints,
                                'cut_off_format' => 'kyambogo',
                                'program_category' => $programCategory,
                                'is_active' => true,
                            ]);
                    } else {
                        // Create new record
                        UniversityCutOff::create([
                            'university_name' => 'Kyambogo University',
                            'university_code' => 'KyU',
                            'course_name' => $programmeName,
                            'course_code' => $progCode ?: null,
                            'cut_off_points' => $cutOffPoints,
                            'cut_off_format' => 'kyambogo',
                            'program_category' => $programCategory,
                            'academic_year' => $academicYear,
                            'minimum_principal_passes' => 2,
                            'degree_type' => $this->determineDegreeType($programmeName),
                            'is_active' => true,
                        ]);
                    }
                    
                    $results['success']++;
                } catch (\Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row " . $rowIndex . ": " . $e->getMessage();
                    Log::error('Error scraping Kyambogo cut-off row', [
                        'row' => $rowIndex,
                        'error' => $e->getMessage()
                    ]);
                }
            });
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Error scraping Kyambogo University cut-offs', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Scrape cut-off points from Makerere University PDF
     */
    public function scrapeMakerere($academicYear = null)
    {
        $academicYear = $academicYear ?? date('Y');
        $nextYear = $academicYear + 1;
        
        // Construct the PDF URL based on academic year
        // Format: https://mak.ac.ug/sites/default/files/2025-07/Cut-Off-Points-DIRECT_ENTRY-Private-Sponsorship-2025-2026.pdf
        $url = "https://mak.ac.ug/sites/default/files/{$academicYear}-07/Cut-Off-Points-DIRECT_ENTRY-Private-Sponsorship-{$academicYear}-{$nextYear}.pdf";
        
        try {
            // Check if PDF parser is available
            if (!class_exists('\Smalot\PdfParser\Parser')) {
                // Try to load the class manually if vendor directory exists
                $parserPath = base_path('vendor/smalot/pdfparser/src/Smalot/PdfParser/Parser.php');
                if (file_exists($parserPath)) {
                    require_once $parserPath;
                }
                
                // Also try loading via autoloader
                if (!class_exists('\Smalot\PdfParser\Parser')) {
                    $autoloadPath = base_path('vendor/autoload.php');
                    if (file_exists($autoloadPath)) {
                        require_once $autoloadPath;
                    }
                }
                
                if (!class_exists('\Smalot\PdfParser\Parser')) {
                    throw new \Exception(
                        'PDF Parser package (smalot/pdfparser) is not installed. ' .
                        'Please run the following command in your terminal: ' .
                        'composer require smalot/pdfparser ' .
                        'Then clear caches with: php artisan optimize:clear'
                    );
                }
            }
            
            // Download the PDF
            $response = Http::timeout(60)
                ->withoutVerifying()
                ->withOptions([
                    'verify' => false,
                    'curl' => [
                        CURLOPT_SSL_VERIFYPEER => false,
                        CURLOPT_SSL_VERIFYHOST => false,
                    ]
                ])
                ->get($url);
            
            if (!$response->successful()) {
                throw new \Exception("Failed to fetch PDF from {$url}. Status: {$response->status()}. Please check if the PDF URL is correct for academic year {$academicYear}.");
            }
            
            $pdfContent = $response->body();
            
            if (empty($pdfContent)) {
                throw new \Exception('Downloaded PDF is empty');
            }
            
            // Parse PDF
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseContent($pdfContent);
            $text = $pdf->getText();
            
            $results = [
                'success' => 0,
                'failed' => 0,
                'errors' => [],
                'skipped' => 0,
            ];
            
            // Parse the text content
            // Makerere PDF structure typically has: Program Name | Code | Male Cut-off | Female Cut-off (for STEM)
            // Or: Program Name | Code | Cut-off (for non-STEM)
            
            $lines = explode("\n", $text);
            $programCategory = 'other';
            
            foreach ($lines as $lineIndex => $line) {
                $line = trim($line);
                
                if (empty($line) || strlen($line) < 5) {
                    continue;
                }
                
                // Skip header rows
                if (stripos($line, 'cut-off') !== false && stripos($line, 'point') !== false) {
                    continue;
                }
                if (stripos($line, 'programme') !== false && (stripos($line, 'code') !== false || stripos($line, 'name') !== false)) {
                    continue;
                }
                if (stripos($line, 'male') !== false && stripos($line, 'female') !== false) {
                    continue;
                }
                if (stripos($line, 'direct entry') !== false || stripos($line, 'private sponsorship') !== false) {
                    continue;
                }
                
                // Extract program category (STEM vs Other)
                $programCategory = $this->determineProgramCategory($line);
                
                // Try to parse line - Makerere PDF may have different formats
                // Common formats:
                // 1. Program Name | Code | Male | Female
                // 2. Program Name | Code | Cut-off
                // 3. Program Name (long) | ... | Cut-off values
                
                // Split by multiple spaces, tabs, or common delimiters
                $parts = preg_split('/\s{2,}|\t|(?<=\d)\s+(?=\d)/', $line);
                $parts = array_filter(array_map('trim', $parts));
                $parts = array_values($parts);
                
                if (count($parts) >= 2) {
                    // First part is usually program name
                    $programName = $parts[0];
                    
                    // Skip if it's just a number or very short
                    if (is_numeric($programName) || strlen($programName) < 5) {
                        continue;
                    }
                    
                    $code = null;
                    $maleCutOff = null;
                    $femaleCutOff = null;
                    $generalCutOff = null;
                    
                    // Look for course code (usually short alphanumeric, 2-10 chars)
                    foreach ($parts as $idx => $part) {
                        if ($idx === 0) continue; // Skip program name
                        
                        // Check if it's a code (short alphanumeric)
                        if (strlen($part) >= 2 && strlen($part) <= 10 && preg_match('/^[A-Z0-9\-]+$/i', $part)) {
                            $code = $part;
                            continue;
                        }
                        
                        // Try to extract numbers (cut-off points)
                        $number = $this->extractNumber($part);
                        if ($number !== null) {
                            if ($maleCutOff === null) {
                                $maleCutOff = $number;
                            } elseif ($femaleCutOff === null) {
                                $femaleCutOff = $number;
                            } else {
                                $generalCutOff = $number;
                            }
                        }
                    }
                    
                    // If we have both male and female cut-offs and it's STEM, use them
                    if ($programCategory === 'stem' && $maleCutOff !== null && $femaleCutOff !== null) {
                        $this->saveCutOff('Makerere University', 'MAK', $programName, $code, $maleCutOff, $femaleCutOff, null, $programCategory, $academicYear, $results);
                    } elseif ($maleCutOff !== null || $femaleCutOff !== null || $generalCutOff !== null) {
                        // Use available cut-off
                        $cutOff = $generalCutOff ?? $maleCutOff ?? $femaleCutOff;
                        $this->saveCutOff('Makerere University', 'MAK', $programName, $code, null, null, $cutOff, $programCategory, $academicYear, $results);
                    }
                }
            }
            
            if ($results['success'] === 0 && $results['failed'] === 0 && $results['skipped'] === 0) {
                throw new \Exception('No valid cut-off data found in PDF. The PDF structure may have changed.');
            }
            
            return $results;
            
        } catch (\Exception $e) {
            Log::error('Error scraping Makerere University cut-offs', [
                'url' => $url,
                'academic_year' => $academicYear,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Extract numeric value from string
     */
    private function extractNumber($text)
    {
        // Remove non-numeric characters except decimal point
        $cleaned = preg_replace('/[^0-9.]/', '', $text);
        
        if (empty($cleaned)) {
            return null;
        }
        
        $number = (float) $cleaned;
        
        // Validate reasonable cut-off range (0-100)
        if ($number >= 0 && $number <= 100) {
            return $number;
        }
        
        return null;
    }
    
    /**
     * Save cut-off to database
     */
    private function saveCutOff($universityName, $universityCode, $courseName, $courseCode, $maleCutOff, $femaleCutOff, $generalCutOff, $programCategory, $academicYear, &$results)
    {
        if (empty($courseName) || strlen($courseName) < 5) {
            $results['skipped']++;
            return;
        }
        
        try {
            $data = [
                'university_name' => $universityName,
                'university_code' => $universityCode,
                'course_name' => $courseName,
                'course_code' => $courseCode ?: null,
                'program_category' => $programCategory,
                'cut_off_format' => 'makerere',
                'academic_year' => $academicYear,
                'minimum_principal_passes' => 2,
                'degree_type' => $this->determineDegreeType($courseName),
                'is_active' => true,
            ];
            
            if ($programCategory === 'stem' && $maleCutOff !== null && $femaleCutOff !== null) {
                $data['cut_off_points_male'] = $maleCutOff;
                $data['cut_off_points_female'] = $femaleCutOff;
            } else {
                $data['cut_off_points'] = $generalCutOff ?? $maleCutOff ?? $femaleCutOff;
            }
            
            $exists = UniversityCutOff::where('university_name', $universityName)
                ->where('course_name', $courseName)
                ->where('academic_year', $academicYear)
                ->exists();
            
            if ($exists) {
                UniversityCutOff::where('university_name', $universityName)
                    ->where('course_name', $courseName)
                    ->where('academic_year', $academicYear)
                    ->update($data);
            } else {
                UniversityCutOff::create($data);
            }
            
            $results['success']++;
        } catch (\Exception $e) {
            $results['failed']++;
            $results['errors'][] = "Error saving {$courseName}: " . $e->getMessage();
            Log::error('Error saving cut-off', [
                'course' => $courseName,
                'error' => $e->getMessage()
            ]);
        }
    }
    
    /**
     * Determine program category based on programme name
     */
    private function determineProgramCategory($programmeName)
    {
        $lowerName = strtolower($programmeName);
        
        // Check for STEM keywords
        $stemKeywords = [
            'engineering', 'science', 'technology', 'mathematics', 'physics', 
            'chemistry', 'biology', 'computer', 'it', 'information', 'statistics',
            'architecture', 'surveying', 'industrial', 'automotive', 'power'
        ];
        
        foreach ($stemKeywords as $keyword) {
            if (strpos($lowerName, $keyword) !== false) {
                return 'stem';
            }
        }
        
        return 'other';
    }
    
    /**
     * Determine degree type based on programme name
     */
    private function determineDegreeType($programmeName)
    {
        $lowerName = strtolower($programmeName);
        
        if (strpos($lowerName, 'diploma') !== false) {
            return 'diploma';
        }
        
        if (strpos($lowerName, 'certificate') !== false) {
            return 'certificate';
        }
        
        if (strpos($lowerName, 'masters') !== false || strpos($lowerName, 'master') !== false) {
            return 'masters';
        }
        
        if (strpos($lowerName, 'phd') !== false || strpos($lowerName, 'doctorate') !== false) {
            return 'phd';
        }
        
        return 'bachelor'; // Default
    }
    
    /**
     * Get list of supported universities (from database and hardcoded fallback)
     */
    public function getSupportedUniversities()
    {
        $universities = [];
        
        // First, try to get from database
        try {
            $dbUniversities = University::active()->get();
            foreach ($dbUniversities as $uni) {
                $universities['uni_' . $uni->id] = [
                    'name' => $uni->name,
                    'code' => $uni->code,
                    'url' => $uni->url_pattern ?? $uni->base_url,
                    'format' => $uni->cut_off_format,
                    'scraper_available' => $uni->hasUrlPattern(),
                    'from_db' => true,
                    'university_id' => $uni->id,
                ];
            }
        } catch (\Exception $e) {
            // Database table might not exist yet, continue with hardcoded
        }
        
        // Add hardcoded fallback universities (for backward compatibility)
        $hardcoded = [
            'kyambogo' => [
                'name' => 'Kyambogo University',
                'code' => 'KyU',
                'url' => 'https://kyu.ac.ug/cut-off-point/',
                'format' => 'kyambogo',
                'scraper_available' => true,
            ],
            'makerere' => [
                'name' => 'Makerere University',
                'code' => 'MAK',
                'url' => 'https://mak.ac.ug/sites/default/files/2025-07/Cut-Off-Points-DIRECT_ENTRY-Private-Sponsorship-2025-2026.pdf',
                'format' => 'makerere',
                'scraper_available' => true,
            ],
        ];
        
        return array_merge($universities, $hardcoded);
    }
}

