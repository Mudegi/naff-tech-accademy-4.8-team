# O-Level Course Recommendations System

## Overview
The O-Level Course Recommendations system allows Form 1-4 students (UCE level) to select at least 3 subjects from their marks and see which university courses they qualify for based on their aggregate points.

## Key Features

### 1. Customizable Grading Scale
- **Default Scale (Uganda UCE Standard):**
  - A: 80-100% = 6 points
  - B: 70-79% = 5 points
  - C: 60-69% = 4 points
  - D: 50-59% = 3 points
  - E: 40-49% = 2 points
  - O: 35-39% = 1 point
  - F: 0-34% = 0 points

- **School-Specific Scales:**
  - School admins/Directors of Studies can create custom grading scales
  - Override default scales to match school standards
  - Separate scales for O-Level and A-Level
  - Easy reset to default scales

### 2. Subject Selection Interface
- Students view all their O-Level marks
- Select minimum 3 subjects to calculate aggregate
- Real-time aggregate points preview
- Visual feedback on selected subjects
- Displays current grading scale for reference

### 3. Course Recommendations
- Calculates aggregate points using school's grading scale
- Filters qualifying university courses by:
  - Cut-off points (aggregate must meet or exceed cut-off)
  - Gender-specific cut-offs (where applicable)
  - Essential subject requirements
  - Relevant subject preferences

### 4. Results Display
- **Summary Cards:** Aggregate points, qualifying courses count, universities count
- **Selected Subjects Breakdown:** Shows grade, percentage, and points for each
- **Courses Grouped by University:**
  - Course name and description
  - Cut-off points vs your points
  - Points above cut-off
  - Match score (based on subject alignment)
  - Essential and relevant subjects badges
  - Match quality indicators (Excellent/Good match)

### 5. PDF Export
- Professional PDF report generation
- Includes all course recommendations
- Student information and summary
- Subject breakdown table
- Downloadable for records

## Database Schema

### `grade_scales` Table
```sql
- id
- grade (A, B, C, D, E, O, F)
- min_percentage (decimal)
- max_percentage (decimal)
- points (integer)
- academic_level (O-Level/A-Level)
- school_id (nullable - null = default system-wide)
- is_active (boolean)
- timestamps
```

### `university_cut_offs` Table (Updated)
```sql
- ... existing fields ...
- academic_level (O-Level/A-Level) - NEW FIELD
- ... existing fields ...
```

## File Structure

### Controllers
- `app/Http/Controllers/Student/OLevelCourseRecommendationController.php`
  - `selectSubjects()` - Display subject selection form
  - `showRecommendations()` - Calculate and display recommendations
  - `downloadPdf()` - Generate PDF report

- `app/Http/Controllers/Admin/GradeScaleController.php`
  - `index()` - View grade scales
  - `create()` - Create custom grade scale
  - `store()` - Save custom grade scale
  - `destroy()` - Reset to default scale

### Models
- `app/Models/GradeScale.php`
  - `getGradeAndPoints()` - Get grade and points for a percentage
  - `getPointsForPercentage()` - Get just points
  - `getGradeForPercentage()` - Get just grade
  - `getScalesForSchool()` - Get school-specific or default scales
  - Scopes: `active()`, `forLevel()`, `forSchool()`

- `app/Models/UniversityCutOff.php` (Updated)
  - Added `academic_level` to fillable

### Views
- `resources/views/student/o-level-recommendations/`
  - `select-subjects.blade.php` - Subject selection form
  - `results.blade.php` - Recommendations display
  - `pdf.blade.php` - PDF template

- `resources/views/admin/grade-scales/`
  - `index.blade.php` - View and manage scales
  - `create.blade.php` - Create custom scale

### Migrations
- `2025_12_11_100000_create_grade_scales_table.php`
- `2025_12_11_110000_add_academic_level_to_university_cut_offs_table.php`

### Seeders
- `database/seeders/GradeScaleSeeder.php` - Seeds default O-Level and A-Level scales

## Routes

### Student Routes
```php
// O-Level Course Recommendations
GET  /student/o-level-recommendations           → selectSubjects
POST /student/o-level-recommendations           → showRecommendations
POST /student/o-level-recommendations/download-pdf → downloadPdf
```

### Admin Routes
```php
// Grade Scale Management
GET    /admin/school/grade-scales         → index
GET    /admin/school/grade-scales/create  → create
POST   /admin/school/grade-scales         → store
DELETE /admin/school/grade-scales/{level} → destroy
```

## Usage Guide

### For School Administrators

1. **View Current Grading Scale:**
   - Navigate to `/admin/school/grade-scales`
   - See default O-Level and A-Level scales
   - Check if custom scales are active

2. **Create Custom Grading Scale:**
   - Click "Create Custom O-Level Scale" or "Create Custom A-Level Scale"
   - Adjust percentage ranges and points for each grade (A-F)
   - Save custom scale
   - Custom scale applies to all students in your school

3. **Reset to Default:**
   - Click "Reset to Default" button
   - Removes custom scale
   - Reverts to Uganda standard grading

### For Students

1. **Access O-Level Recommendations:**
   - Navigate to `/student/o-level-recommendations`
   - View your O-Level marks

2. **Select Subjects:**
   - Check at least 3 subjects from your marks
   - See real-time aggregate points preview
   - Click "Find Qualifying Courses"

3. **View Recommendations:**
   - See your aggregate points
   - View qualifying courses grouped by university
   - Check match scores and subject requirements
   - Download PDF report for records

## Calculation Logic

### Aggregate Points Calculation
1. Student selects 3+ subjects
2. For each subject:
   - If numeric mark exists: Use grading scale to convert percentage to points
   - If only letter grade exists: Look up points from grading scale
3. Sum all points = Aggregate Points

### Course Qualification
1. **Cut-off Check:** Student aggregate ≥ Course cut-off
2. **Gender-specific Cut-offs:** Apply male/female specific cut-offs if defined
3. **Essential Subjects:** Student must have ALL essential subjects (if course requires them)
4. **Match Score Calculation:**
   - Essential subject match: +10 points each
   - Relevant subject match: +5 points each
   - Desirable subject match: +2 points each
5. Sort by match score (highest first)

## Customization Options

### School-Specific Grading
Schools can customize:
- Percentage ranges for each grade
- Points awarded for each grade
- Separate scales for O-Level and A-Level

### Use Cases for Custom Scales
- **High-performing schools:** Stricter grading (e.g., A = 85-100%)
- **Special programs:** Different point systems
- **International standards:** Align with other systems

## Technical Notes

### Grading Scale Priority
1. Check for school-specific scale (`school_id` = student's school)
2. Fall back to default scale (`school_id` = NULL)

### Performance Optimizations
- Indexed fields: `academic_level`, `school_id`, `grade`
- Cached grade lookups in GradeScale model
- Efficient filtering with Eloquent scopes

### Error Handling
- Validates minimum 3 subject selection
- Gracefully handles missing marks
- Falls back to default grading if custom scale missing
- User-friendly error messages

## Future Enhancements

Potential improvements:
1. Allow selecting 3 best subjects automatically
2. Compare different subject combinations
3. Show why courses didn't qualify
4. Subject combination recommendations
5. Historical cut-off trends
6. Course popularity indicators
7. Integration with university application systems

## Testing Checklist

- [ ] Default grading scale seeds correctly
- [ ] Admin can create custom O-Level scale
- [ ] Admin can create custom A-Level scale
- [ ] Admin can reset to default scale
- [ ] Student sees correct grading scale (school-specific or default)
- [ ] Student can select 3+ subjects
- [ ] Aggregate points calculate correctly
- [ ] Course filtering works (cut-offs, gender, subjects)
- [ ] Match scores calculate correctly
- [ ] PDF generates successfully
- [ ] Results display properly by university
- [ ] No qualifying courses message shows when appropriate

## Support

For issues or questions:
- Check that migrations have run: `php artisan migrate`
- Verify seeder ran: `php artisan db:seed --class=GradeScaleSeeder`
- Check student has O-Level marks uploaded
- Verify university cut-offs have `academic_level` = 'O-Level'
- Ensure student's school_id is set correctly
