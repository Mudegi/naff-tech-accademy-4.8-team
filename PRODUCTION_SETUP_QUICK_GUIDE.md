# Production Setup Quick Guide

## 🚀 Complete Production Setup (In Order)

### 1️⃣ Upload All Required Files

Upload these files to production:

```
✅ database/migrations/2025_12_06_032912_add_is_system_class_to_classes_table.php
✅ database/seeders/ProductionUniversityCutOffsSeeder.php
✅ app/Console/Commands/FixClassesTable.php
✅ app/Console/Commands/VerifyCutOffs.php
✅ app/Console/Commands/CompareCutOffs.php
```

### 2️⃣ SSH into Production

```bash
ssh your-user@your-server
cd /path/to/laravel/project
```

### 3️⃣ Backup Database

```bash
# Full backup
php artisan backup:run

# Or manual backup
mysqldump -u username -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 4️⃣ Clear Caches

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
composer dump-autoload
```

### 5️⃣ Fix Classes Table

```bash
# This adds the is_system_class column if missing
php artisan fix:classes-table
```

**Expected Output:**
```
🔍 Checking classes table structure...
⚠️  Column is_system_class is missing!
🔧 Adding the column now...
✅ Column added successfully!
✅ Verification passed - column exists now!
```

### 6️⃣ Run Migrations (Just in Case)

```bash
php artisan migrate
```

### 7️⃣ Seed Standard Classes

```bash
php artisan db:seed --class=ClassSeeder
```

**Expected Output:**
```
Creating standard Ugandan school classes (Form 1-6)...
📝 Updated: Form 1 (O Level)
📝 Updated: Form 2 (O Level)
...
🎉 Standard classes seeding completed!
```

### 8️⃣ Seed University Cut-Offs

```bash
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

**Expected Output:**
```
🔄 Seeding university cut-offs...
✅ Seeding completed successfully!
📊 Results:
   • Created: 307 new courses (or Updated: 307 if already existed)
   • Total processed: 307

University Breakdown:
   • Kyambogo University: 149 courses
   • Makerere University: 158 courses
```

### 9️⃣ Verify Everything

```bash
# Compare with expected
php artisan cutoffs:compare

# Detailed verification
php artisan cutoffs:verify
```

**Expected from compare:**
```
📊 Cut-Offs Comparison Report
Expected Courses: 307
Actual Courses:   307
Difference:       0 (Perfect match! ✅)
```

### 🔟 Test Application

1. ✅ Login to admin panel
2. ✅ Navigate to University Cut-Offs
3. ✅ Check 307 courses are visible
4. ✅ Test filtering (Makerere/Kyambogo)
5. ✅ Test Export to Excel
6. ✅ Test student course recommendations
7. ✅ Verify essential subjects showing

---

## ⚠️ Common Issues & Quick Fixes

### Error: Column 'is_system_class' not found
```bash
php artisan fix:classes-table
```

### Error: Seeder class not found
```bash
composer dump-autoload
php artisan cache:clear
```

### Error: Memory limit
```bash
php -d memory_limit=512M artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

### Wrong number of courses
```bash
# Check what's different
php artisan cutoffs:compare

# Re-run seeder (safe, uses updateOrCreate)
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

---

## 📋 Verification Checklist

After setup, verify:

- [ ] Classes table has `is_system_class` column
- [ ] Form 1-6 classes exist
- [ ] 307 university courses exist
- [ ] 158 Makerere courses
- [ ] 149 Kyambogo courses
- [ ] ~270 courses have essential subjects
- [ ] Admin can view cut-offs page
- [ ] Export button works
- [ ] Import form accessible
- [ ] Student recommendations work
- [ ] No errors in logs

---

## 🆘 Emergency Commands

### Check Database State
```bash
# Quick count
php artisan tinker --execute="echo App\Models\UniversityCutOff::count();"

# Detailed check
php artisan cutoffs:verify

# Compare
php artisan cutoffs:compare
```

### Reset and Re-seed (DESTRUCTIVE!)
```bash
# Backup first!
mysqldump -u user -p database > backup_emergency.sql

# Clear cut-offs only
php artisan tinker
>>> DB::table('university_cut_offs')->truncate();
>>> exit

# Re-seed
php artisan db:seed --class=ProductionUniversityCutOffsSeeder
```

### Rollback from Backup
```bash
mysql -u user -p database < backup_YYYYMMDD_HHMMSS.sql
```

---

## 📞 Support

If you encounter issues not covered here:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Check database: `php artisan tinker`
3. Re-run fix command: `php artisan fix:classes-table`
4. Verify files uploaded correctly
5. Check file permissions (755 for directories, 644 for files)

---

**Last Updated**: December 14, 2025  
**Total Courses**: 307 (Makerere: 158, Kyambogo: 149)  
**Version**: 2.0 (with ClassSeeder fix)
