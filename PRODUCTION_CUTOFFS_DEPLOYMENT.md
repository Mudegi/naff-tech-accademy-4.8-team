# University Cut-Offs Production Deployment Guide

## Overview
This guide helps you deploy all 307 university courses (Makerere & Kyambogo) to your production server using a database seeder.

## What's Included
- **Total Courses**: 307
  - Makerere University: 158 courses
  - Kyambogo University: 149 courses
- **Essential Subjects**: 270 courses (87.9%) have essential subjects populated
- **Academic Year**: 2025

## Files Generated

### 1. ProductionUniversityCutOffsSeeder.php
Location: `database/seeders/ProductionUniversityCutOffsSeeder.php`
- Contains all 307 courses with complete data
- Uses `updateOrCreate` to prevent duplicates
- Can be run multiple times safely
- Shows detailed progress and results

### 2. ExportCutOffsToSeeder.php (Command)
Location: `app/Console/Commands/ExportCutOffsToSeeder.php`
- Exports current database to seeder file
- Useful for future updates

### 3. VerifyCutOffs.php (Command)
Location: `app/Console/Commands/VerifyCutOffs.php`
- Verifies database state
- Shows statistics and breakdowns

---

## Production Deployment Steps

### Step 1: Upload Files to Production

Upload these files to your production server:
```
database/seeders/ProductionUniversityCutOffsSeeder.php
app/Console/Commands/ExportCutOffsToSeeder.php (optional)
app/Console/Commands/VerifyCutOffs.php (optional)
```

### Step 2: SSH into Production Server
```bash
ssh your-user@your-production-server
cd /path/to/your/laravel/project
```

### Step 3: Backup Current Database (IMPORTANT!)
```bash
# Option 1: Full database backup
php artisan backup:run

# Option 2: Manual backup
mysqldump -u your_user -p your_database > backup_before_cutoffs_$(date +%Y%m%d_%H%M%S).sql

# Option 3: Just the cut-offs table
mysqldump -u your_user -p your_database university_cut_offs > cutoffs_backup_$(date +%Y%m%d_%H%M%S).sql
```

### Step 4: Check Current State (Optional)
```bash
# If you uploaded VerifyCutOffs command
php artisan cutoffs:verify
```

### Step 5: Fix Classes Table (If Needed)
```bash
# This ensures the is_system_class column exists
php artisan fix:classes-table
```

### Step 6: Run the Seeder
```bash
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

**Expected Output:**
```
INFO  Seeding database.

🔄 Seeding university cut-offs...

✅ Seeding completed successfully!
📊 Results:
   • Created: X new courses
   • Updated: Y existing courses
   • Total processed: 307

University Breakdown:
   • Kyambogo University: 149 courses
   • Makerere University: 158 courses
```

### Step 7: Verify the Import
```bash
php artisan cutoffs:verify
```

**Expected Output:**
```
📊 Total Courses: 307

University Breakdown:
  • Makerere University: 158 courses
  • Kyambogo University: 149 courses

✅ Courses with Essential Subjects: 270 (87.9%)
```

### Step 8: Test the Application
1. Login to admin panel
2. Navigate to University Cut-Offs page
3. Verify courses are displayed correctly
4. Test filtering by university (Makerere/Kyambogo)
5. Check a few course details to ensure essential subjects are showing

---

## Troubleshooting

### Issue: Column 'is_system_class' Not Found
**Error**: `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'is_system_class'`

**Solution:**
```bash
# Option 1: Run the fix command (Recommended)
php artisan fix:classes-table

# Option 2: Run the migration
php artisan migrate

# Option 3: Add manually via MySQL
mysql -u your_user -p your_database
ALTER TABLE classes ADD COLUMN is_system_class TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;
exit;
```

After fixing, run the seeder again:
```bash
php artisan db:seed --class=ClassSeeder
# or
php artisan db:seed
```

### Issue: Seeder File Not Found
**Error**: `Class "ProductionUniversityCutOffsSeeder" not found`

**Solution:**
```bash
# Clear caches
php artisan cache:clear
php artisan config:clear

# Dump autoload
composer dump-autoload

# Try again
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

### Issue: Memory Limit Error
**Error**: `Allowed memory size exhausted`

**Solution:**
```bash
php -d memory_limit=512M artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

### Issue: Duplicate Entries
**Solution:**
The seeder uses `updateOrCreate`, so duplicates shouldn't occur. It matches on:
- university_name
- course_name
- academic_year

If you have duplicates with different academic years, that's intentional.

### Issue: Some Courses Missing Essential Subjects
**Note:** This is expected. Currently, 37 courses (12.1%) don't have essential subjects defined. You can:

1. **Add them manually** via the admin panel
2. **Export and edit** in Excel:
   ```bash
   # In admin panel, click "Export to Excel"
   # Edit essential subjects in Excel
   # Click "Import from CSV/Excel" to update
   ```
3. **Populate using the seeder** (run on development first):
   ```bash
   php artisan db:seed --class=PopulateEssentialSubjectsSeeder
   ```

---

## Updating Cut-Offs in Future

### Method 1: Re-export from Development
When you update courses in development:

```bash
# On development server
php artisan cutoffs:export-seeder

# Copy the generated file to production
# Run the seeder on production
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

### Method 2: Excel Import/Export Workflow
1. Export current cut-offs to Excel (Admin Panel)
2. Edit in Excel (add/update courses, essential subjects)
3. Import updated Excel file (Admin Panel)

### Method 3: Manual Addition
- Use the "Add New Cut-Off" button in admin panel
- Fill in course details manually

---

## Important Notes

### Safe to Run Multiple Times
The seeder uses `updateOrCreate`, which means:
- ✅ Running it again won't create duplicates
- ✅ It will update existing courses with new data
- ✅ It will add any new courses

### What Gets Updated
When you run the seeder again, it updates:
- Cut-off points
- Essential subjects
- Faculty/Department info
- All other course fields

It matches courses by:
- University name
- Course name
- Academic year

### Data Loss Prevention
- Always backup before running seeders
- Test on staging environment first
- Keep the generated seeder file in version control

---

## Verification Checklist

After running the seeder, verify:

- [ ] Total courses = 307
- [ ] Makerere courses = 158
- [ ] Kyambogo courses = 149
- [ ] Essential subjects showing for ~88% of courses
- [ ] All courses are active
- [ ] Academic year = 2025
- [ ] Course recommendations working for students
- [ ] Export/Import functionality working
- [ ] Admin can view and edit courses

---

## Emergency Rollback

If something goes wrong:

### Option 1: Restore from Backup
```bash
# Restore full database
mysql -u your_user -p your_database < backup_before_cutoffs_YYYYMMDD_HHMMSS.sql

# Or just the cut-offs table
mysql -u your_user -p your_database < cutoffs_backup_YYYYMMDD_HHMMSS.sql
```

### Option 2: Truncate and Re-seed
```bash
# Clear the table
php artisan tinker
>>> DB::table('university_cut_offs')->truncate();
>>> exit

# Run the seeder again
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

---

## Support Commands

### Check Database Statistics
```bash
php artisan cutoffs:verify
```

### Export Current Database to New Seeder
```bash
php artisan cutoffs:export-seeder
```

### Check Specific University
```bash
php artisan tinker
>>> App\Models\UniversityCutOff::where('university_name', 'LIKE', '%Makerere%')->count();
>>> App\Models\UniversityCutOff::where('university_name', 'LIKE', '%Kyambogo%')->count();
```

### Find Courses Without Essential Subjects
```bash
php artisan tinker
>>> $missing = App\Models\UniversityCutOff::whereNull('essential_subjects')->orWhereRaw('JSON_LENGTH(essential_subjects) = 0')->get(['id', 'course_name', 'university_name']);
>>> $missing->each(fn($c) => print($c->university_name . ' - ' . $c->course_name . "\n"));
```

---

## Contact & Support

If you encounter any issues:
1. Check this guide's troubleshooting section
2. Verify your backup exists before making changes
3. Test changes on staging environment first
4. Keep the generated seeder files in version control

**Generated**: December 14, 2025  
**Total Courses**: 307  
**Version**: 1.0
