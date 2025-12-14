<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UniversityCutOff;
use Illuminate\Support\Facades\DB;

class PopulateEssentialSubjectsSeeder extends Seeder
{
    /**
     * Populate essential subjects for all university courses based on course names.
     * This uses pattern matching to assign appropriate A-Level subject requirements.
     */
    public function run()
    {
        echo "Starting to populate essential subjects for university courses...\n";
        
        $courses = UniversityCutOff::all();
        $updated = 0;
        
        foreach ($courses as $course) {
            $essentialSubjects = $this->determineEssentialSubjects($course->course_name);
            
            if (!empty($essentialSubjects)) {
                $course->essential_subjects = $essentialSubjects;
                $course->save();
                $updated++;
                echo ".";
            }
        }
        
        echo "\n✅ Updated {$updated} courses with essential subjects.\n";
        echo "📊 Summary by category:\n";
        $this->showSummary();
    }
    
    /**
     * Determine essential subjects based on course name patterns.
     */
    private function determineEssentialSubjects($courseName)
    {
        $name = strtolower($courseName);
        
        // ENGINEERING COURSES
        if (preg_match('/\b(engineering|engineer)\b/i', $name)) {
            // Civil, Mechanical, Electrical, etc. Engineering
            if (preg_match('/\b(civil|mechanical|electrical|power|automotive|structural|telecommunication)\b/i', $name)) {
                return ['Physics', 'Mathematics'];
            }
            // Computer/Software Engineering
            if (preg_match('/\b(computer|software|computing|information tech|ict)\b/i', $name)) {
                return ['Mathematics', 'Physics'];
            }
            // Biomedical/Biosystems Engineering
            if (preg_match('/\b(biomedical|biosystems|bio)\b/i', $name)) {
                return ['Biology', 'Chemistry', 'Mathematics'];
            }
            // Chemical/Industrial Engineering
            if (preg_match('/\b(chemical|industrial)\b/i', $name)) {
                return ['Chemistry', 'Mathematics', 'Physics'];
            }
            // General Engineering
            return ['Physics', 'Mathematics'];
        }
        
        // MEDICAL & HEALTH SCIENCES
        if (preg_match('/\b(medicine|medical|surgery|doctor|physician|clinical)\b/i', $name)) {
            return ['Biology', 'Chemistry', 'Physics'];
        }
        if (preg_match('/\b(nursing|midwifery|public health|health science)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(pharmacy|pharmaceutical|pharmacology)\b/i', $name)) {
            return ['Chemistry', 'Biology', 'Mathematics'];
        }
        if (preg_match('/\b(dentistry|dental)\b/i', $name)) {
            return ['Biology', 'Chemistry', 'Physics'];
        }
        if (preg_match('/\b(veterinary|animal health)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(radiography|medical imaging|laboratory technology|biomedical laboratory)\b/i', $name)) {
            return ['Biology', 'Chemistry', 'Physics'];
        }
        if (preg_match('/\b(optometry|ophthalmic)\b/i', $name)) {
            return ['Biology', 'Chemistry', 'Physics'];
        }
        if (preg_match('/\b(nutrition|dietetics)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(environmental health)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        
        // PURE SCIENCES
        if (preg_match('/\b(biochemistry|biotechnology|microbiology|molecular biology)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(chemistry|industrial chemistry)\b/i', $name)) {
            return ['Chemistry', 'Mathematics'];
        }
        if (preg_match('/\b(physics|applied physics)\b/i', $name)) {
            return ['Physics', 'Mathematics'];
        }
        if (preg_match('/\b(biology|botany|zoology|conservation biology)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(mathematics|statistics|actuarial)\b/i', $name)) {
            return ['Mathematics', 'Physics'];
        }
        if (preg_match('/\b(geology|petroleum geoscience|earth science)\b/i', $name)) {
            return ['Physics', 'Chemistry', 'Mathematics'];
        }
        if (preg_match('/\b(environmental science|environmental management)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        
        // AGRICULTURE & FOOD SCIENCES
        if (preg_match('/\b(agriculture|agribusiness|agronomy|crop|soil)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(animal production|animal science|livestock)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(fisheries|aquaculture)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        if (preg_match('/\b(food science|food technology)\b/i', $name)) {
            return ['Chemistry', 'Biology'];
        }
        if (preg_match('/\b(forestry|forest)\b/i', $name)) {
            return ['Biology', 'Chemistry'];
        }
        
        // COMPUTING & IT
        if (preg_match('/\b(computer science|computing|software|information systems|information technology)\b/i', $name)) {
            return ['Mathematics', 'Physics'];
        }
        if (preg_match('/\b(business computing|information management)\b/i', $name)) {
            return ['Mathematics', 'Economics'];
        }
        if (preg_match('/\b(data science|artificial intelligence)\b/i', $name)) {
            return ['Mathematics', 'Physics'];
        }
        
        // BUSINESS, ECONOMICS & COMMERCE
        if (preg_match('/\b(business administration|business management|mba)\b/i', $name)) {
            return ['Economics', 'Mathematics'];
        }
        if (preg_match('/\b(accounting|finance)\b/i', $name)) {
            return ['Mathematics', 'Economics'];
        }
        if (preg_match('/\b(economics)\b/i', $name)) {
            return ['Economics', 'Mathematics'];
        }
        if (preg_match('/\b(commerce)\b/i', $name)) {
            return ['Economics', 'Mathematics'];
        }
        if (preg_match('/\b(marketing|sales)\b/i', $name)) {
            return ['Economics', 'Entrepreneurship'];
        }
        if (preg_match('/\b(entrepreneurship)\b/i', $name)) {
            return ['Economics', 'Entrepreneurship'];
        }
        if (preg_match('/\b(procurement|supply chain|logistics)\b/i', $name)) {
            return ['Economics', 'Mathematics'];
        }
        if (preg_match('/\b(human resource|hr management)\b/i', $name)) {
            return ['Economics', 'Entrepreneurship'];
        }
        if (preg_match('/\b(international business|trade)\b/i', $name)) {
            return ['Economics', 'Geography'];
        }
        if (preg_match('/\b(tourism|hospitality|hotel)\b/i', $name)) {
            return ['Economics', 'Geography'];
        }
        if (preg_match('/\b(land economy|real estate|valuation)\b/i', $name)) {
            return ['Economics', 'Geography'];
        }
        if (preg_match('/\b(building economics|quantity surveying)\b/i', $name)) {
            return ['Mathematics', 'Physics'];
        }
        
        // ARCHITECTURE & DESIGN
        if (preg_match('/\b(architecture|architectural)\b/i', $name)) {
            return ['Mathematics', 'Physics'];
        }
        if (preg_match('/\b(art and design|industrial design|graphic design)\b/i', $name)) {
            return ['Art', 'Mathematics'];
        }
        if (preg_match('/\b(industrial art|applied design)\b/i', $name)) {
            return ['Art', 'Mathematics'];
        }
        
        // LAW & LEGAL STUDIES
        if (preg_match('/\b(law|legal|jurisprudence)\b/i', $name)) {
            return ['History', 'Literature'];
        }
        
        // EDUCATION PROGRAMS (WITH EDUCATION)
        if (preg_match('/\bwith education\b/i', $name) || preg_match('/\beducation\b.*\bday\b/i', $name)) {
            // Science Education
            if (preg_match('/\b(physics|chemistry|biology|mathematics|computer)\b/i', $name)) {
                if (preg_match('/\bphysics\b/i', $name)) return ['Physics', 'Mathematics'];
                if (preg_match('/\bchemistry\b/i', $name)) return ['Chemistry', 'Mathematics'];
                if (preg_match('/\bbiology|botany|zoology\b/i', $name)) return ['Biology', 'Chemistry'];
                if (preg_match('/\bmathematics\b/i', $name)) return ['Mathematics', 'Physics'];
                if (preg_match('/\bcomputer\b/i', $name)) return ['Mathematics', 'Physics'];
            }
            // Arts Education
            if (preg_match('/\b(arts|languages|literature|history|geography)\b/i', $name)) {
                if (preg_match('/\bgeography\b/i', $name)) return ['Geography', 'Economics'];
                if (preg_match('/\bhistory\b/i', $name)) return ['History', 'Literature'];
                return ['Literature', 'History'];
            }
            // Business/Economics Education
            if (preg_match('/\b(economics|commerce|entrepreneurship)\b/i', $name)) {
                return ['Economics', 'Mathematics'];
            }
            // Agriculture/Vocational Education
            if (preg_match('/\b(agriculture|vocational)\b/i', $name)) {
                return ['Biology', 'Chemistry'];
            }
            // Art & Design Education
            if (preg_match('/\b(art|design|music|performing)\b/i', $name)) {
                return ['Art', 'Literature'];
            }
            // General Education subjects
            return ['Any 2 teaching subjects'];
        }
        
        // GENERAL EDUCATION (not "with education")
        if (preg_match('/\beducation\b/i', $name)) {
            // Adult Education, Community Education, Early Childhood
            if (preg_match('/\b(adult|community|early childhood|guidance|counselling|special needs)\b/i', $name)) {
                return ['Any 2 subjects'];
            }
            return ['Any 2 subjects'];
        }
        
        // SOCIAL SCIENCES & HUMANITIES
        if (preg_match('/\b(social work|social services|social admin)\b/i', $name)) {
            return ['History', 'Literature'];
        }
        if (preg_match('/\b(psychology|applied psychology)\b/i', $name)) {
            return ['Biology', 'Mathematics'];
        }
        if (preg_match('/\b(sociology|anthropology)\b/i', $name)) {
            return ['History', 'Geography'];
        }
        if (preg_match('/\b(political science|politics|governance|public administration)\b/i', $name)) {
            return ['History', 'Economics'];
        }
        if (preg_match('/\b(development studies|community development)\b/i', $name)) {
            return ['Economics', 'Geography'];
        }
        if (preg_match('/\b(gender studies|women studies)\b/i', $name)) {
            return ['History', 'Literature'];
        }
        
        // ARTS & HUMANITIES
        if (preg_match('/\b(journalism|mass communication|media|communication)\b/i', $name)) {
            return ['Literature', 'History'];
        }
        if (preg_match('/\b(literature|english|language)\b/i', $name)) {
            return ['Literature', 'History'];
        }
        if (preg_match('/\b(history|archeology|heritage)\b/i', $name)) {
            return ['History', 'Literature'];
        }
        if (preg_match('/\b(geography|geo-informatics|gis)\b/i', $name)) {
            return ['Geography', 'Mathematics'];
        }
        if (preg_match('/\b(music|performing arts|drama|dance|theatre)\b/i', $name)) {
            return ['Music', 'Literature'];
        }
        if (preg_match('/\b(philosophy|ethics|religious studies)\b/i', $name)) {
            return ['History', 'Literature'];
        }
        if (preg_match('/\b(library|information science|records)\b/i', $name)) {
            return ['Any 2 subjects'];
        }
        if (preg_match('/\b(office management|secretarial)\b/i', $name)) {
            return ['Economics', 'Entrepreneurship'];
        }
        
        // GENERAL ARTS/HUMANITIES/SOCIAL SCIENCES
        if (preg_match('/\b(arts|humanities|social sciences)\b/i', $name)) {
            return ['Any 2 Arts subjects'];
        }
        
        // SPORTS & RECREATION
        if (preg_match('/\b(sports|physical education|recreation|leisure|events)\b/i', $name)) {
            return ['Any 2 subjects'];
        }
        
        // DEFAULT - General admission
        return []; // No specific requirements
    }
    
    /**
     * Show summary of updated courses by category.
     */
    private function showSummary()
    {
        $categories = [
            'Engineering' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where('course_name', 'like', '%engineering%')->count(),
            'Medical/Health' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where(function($q) {
                    $q->where('course_name', 'like', '%medicine%')
                      ->orWhere('course_name', 'like', '%nursing%')
                      ->orWhere('course_name', 'like', '%pharmacy%')
                      ->orWhere('course_name', 'like', '%health%');
                })->count(),
            'Business/Commerce' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where(function($q) {
                    $q->where('course_name', 'like', '%business%')
                      ->orWhere('course_name', 'like', '%commerce%')
                      ->orWhere('course_name', 'like', '%accounting%')
                      ->orWhere('course_name', 'like', '%economics%');
                })->count(),
            'Computing/IT' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where(function($q) {
                    $q->where('course_name', 'like', '%computer%')
                      ->orWhere('course_name', 'like', '%computing%')
                      ->orWhere('course_name', 'like', '%information%');
                })->count(),
            'Education' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where('course_name', 'like', '%education%')->count(),
            'Sciences' => UniversityCutOff::whereNotNull('essential_subjects')
                ->where(function($q) {
                    $q->where('course_name', 'like', '%biology%')
                      ->orWhere('course_name', 'like', '%chemistry%')
                      ->orWhere('course_name', 'like', '%physics%')
                      ->orWhere('course_name', 'like', '%mathematics%');
                })->count(),
        ];
        
        foreach ($categories as $category => $count) {
            echo "   • {$category}: {$count} courses\n";
        }
    }
}
