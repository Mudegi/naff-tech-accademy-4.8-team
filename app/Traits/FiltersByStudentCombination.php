<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait FiltersByStudentCombination
{
    /**
     * Get subject IDs from a combination string like "PCM/ICT"
     * P = Physics, C = Chemistry, M = Mathematics, B = Biology
     * G = Geography, H = History, E = Economics, A = Agriculture
     * ICT = Information and Communication Technology
     * GP = General Paper (usually included by default for A-Level)
     */
    protected function getSubjectIdsFromCombination($combinationString)
    {
        if (empty($combinationString)) {
            return [];
        }

        // Subject abbreviation to name mapping
        $subjectMap = [
            'P' => 'Physics',
            'C' => 'Chemistry',
            'M' => 'Mathematics',
            'B' => 'Biology',
            'G' => 'Geography',
            'H' => 'History',
            'E' => 'Economics',
            'A' => 'Agriculture',
            'ICT' => 'Information and Communication Technology',
            'GP' => 'General Paper',
            'EL' => 'English Language',
            'L' => 'Literature in English',
            'K' => 'Kiswahili',
            'CRE' => 'Christian Religious Education',
            'IRE' => 'Islamic Religious Education',
            'ART' => 'Art and Design',
            'MUS' => 'Music',
            'PE' => 'Physical Education',
            'ENT' => 'Entrepreneurship',
            'TD' => 'Technology and Design',
            'NFT' => 'Nutrition and Food Technology',
        ];

        $subjectNames = [];
        
        // Split by / to get individual codes
        $codes = explode('/', strtoupper($combinationString));
        
        foreach ($codes as $code) {
            $code = trim($code);
            
            // Handle multi-letter codes first
            if (isset($subjectMap[$code])) {
                $subjectNames[] = $subjectMap[$code];
            } else {
                // Handle single letter codes
                foreach (str_split($code) as $letter) {
                    if (isset($subjectMap[$letter])) {
                        $subjectNames[] = $subjectMap[$letter];
                    }
                }
            }
        }

        // Add General Paper by default for A-Level students
        if (!in_array('General Paper', $subjectNames)) {
            $subjectNames[] = 'General Paper';
        }

        // Get subject IDs from names
        $subjectIds = DB::table('subjects')
            ->whereIn('name', $subjectNames)
            ->where('is_active', 1)
            ->pluck('id')
            ->toArray();

        return $subjectIds;
    }

    /**
     * Check if user is an A-Level student (Form 5 or Form 6)
     */
    protected function isALevelStudent($user)
    {
        // Check from students table
        $student = DB::table('students')
            ->where('user_id', $user->id)
            ->first(['level', 'class', 'combination']);

        if (!$student) {
            return false;
        }

        // Check if A-Level or Form 5/6
        return $student->level === 'A Level' || 
               in_array($student->class, ['Form 5', 'Form 6']);
    }

    /**
     * Get the combination subject IDs for the current user
     */
    protected function getStudentCombinationSubjects($user)
    {
        $student = DB::table('students')
            ->where('user_id', $user->id)
            ->first(['combination', 'level', 'class']);

        if (!$student) {
            return null;
        }

        // Only filter for A-Level students
        if ($student->level !== 'A Level' && !in_array($student->class, ['Form 5', 'Form 6'])) {
            return null;
        }

        return $this->getSubjectIdsFromCombination($student->combination);
    }
}
