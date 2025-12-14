# Uganda A-Level Subject Combinations Guide

## Overview
In Uganda's Advanced Level (A-Level) education system, students must take:
- **3 Principal Pass subjects** (full A-Level subjects)
- **1 Subsidiary subject** (Sub, lighter version)

## Common Science Combinations

### PCM/ICT
- **Principal subjects**: Physics, Chemistry, Mathematics
- **Subsidiary**: ICT (Information and Communication Technology)
- **Career paths**: Engineering, Computer Science, Architecture, Pure Sciences

### PCB/ICT
- **Principal subjects**: Physics, Chemistry, Biology
- **Subsidiary**: ICT
- **Career paths**: Medicine, Pharmacy, Dentistry, Veterinary, Biomedical Sciences

### BCM/ICT
- **Principal subjects**: Biology, Chemistry, Mathematics
- **Subsidiary**: ICT
- **Career paths**: Medicine, Pharmacy, Agricultural Sciences, Biotechnology

### PCM/Sub Maths
- **Principal subjects**: Physics, Chemistry, Mathematics
- **Subsidiary**: Sub Mathematics (additional math topics)
- **Career paths**: Pure Sciences, Physics, Chemistry, Mathematics

## Common Arts Combinations

### HEG/Sub ICT
- **Principal subjects**: History, Economics, Geography
- **Subsidiary**: Sub ICT
- **Career paths**: Law, Journalism, Social Work, Development Studies, International Relations

### HGL/Sub ICT
- **Principal subjects**: History, Geography, Literature
- **Subsidiary**: Sub ICT
- **Career paths**: Education, Journalism, Publishing, Social Sciences

### MEG/Sub ICT
- **Principal subjects**: Mathematics, Economics, Geography
- **Subsidiary**: Sub ICT
- **Career paths**: Economics, Business Administration, Accounting, Finance, Statistics

## Subject Code Abbreviations

### Principal Subjects
- **P** = Physics
- **C** = Chemistry
- **M** = Mathematics
- **B** = Biology
- **H** = History
- **E** = Economics
- **G** = Geography
- **L** = Literature in English
- **A** = Agriculture
- **K** = Kiswahili

### Subsidiary Subjects
- **Sub ICT** = Subsidiary ICT
- **Sub Maths** = Subsidiary Mathematics
- **Sub ICT** = Subsidiary ICT

## Grading System

### Principal Pass Subjects
Graded from A to F:
- **A** = Excellent (80-100)
- **B** = Very Good (70-79)
- **C** = Good (60-69)
- **D** = Credit (50-59)
- **E** = Pass (45-49)
- **F** = Fail (0-44)

### Subsidiary Subjects
Graded from:
- **O** = Ordinary Pass (subsidiary)
- **F** = Fail

**Note**: Universities typically require at least 2 principal passes (or sometimes 3) for admission to most programs.

## Database Implementation

### StudentMark Model
The `student_marks` table has:
- `is_principal_pass` (boolean): 
  - `true` for principal subjects
  - `false` for subsidiary subjects
- `grade`: The actual grade (A, B, C, D, E, F for principals; O, F for subsidiary)

### Combination Storage
In the `students` table:
- `combination` field stores the combination code (e.g., "PCM/ICT", "HEG/Sub ICT")

### Filtering Logic
Located in `app/Traits/FiltersByStudentCombination.php`:
- Parses combination strings (e.g., "PCM/ICT")
- Maps letters to full subject names
- Returns subject IDs for filtering resources

## Important Notes

1. **General Paper (GP)** is automatically included for all A-Level students
2. **Subsidiary subjects** are NOT full A-Level subjects - they're lighter versions
3. **Principal passes** are what universities primarily consider for admission
4. **ICT as subsidiary** is very common across most combinations
5. When uploading marks, teachers must correctly mark `is_principal_pass`:
   - Check the box for Physics, Chemistry, Mathematics in PCM/ICT
   - Uncheck the box for ICT in PCM/ICT

## Example Test Data Created

### Student with PCM/ICT:
```
Physics (A, 88) - Principal ✓
Mathematics (A, 90) - Principal ✓
Chemistry (A, 86) - Principal ✓
ICT (O, 75) - Subsidiary ✗
```

This correctly represents the Uganda A-Level system.
