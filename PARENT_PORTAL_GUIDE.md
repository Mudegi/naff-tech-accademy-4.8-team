# Parent Portal Implementation Guide

## Overview
The Parent Portal allows parents/guardians to monitor their children's academic progress in real-time, demonstrating the value of their investment in education through continuous visibility into performance data.

## Features Implemented

### 1. Parent-Student Relationships
- **Database Table**: `parent_student` (many-to-many pivot table)
- **Fields**:
  - `parent_id`: User ID of the parent account
  - `student_id`: User ID of the student account
  - `relationship`: Type (parent, guardian, sponsor)
  - `is_primary`: Flag for primary contact
  - `receive_notifications`: Opt-in for email/SMS notifications
- **Model Relationships**:
  - `$parent->children()`: Get all students linked to a parent
  - `$student->parents()`: Get all parents/guardians for a student

### 2. Parent Dashboard (`/parent/dashboard`)
**What Parents See**:
- Overall summary across all children
  - Total children linked
  - Number performing well (70%+)
  - Number needing attention (<50%)
  - Total activities tracked
- Individual cards for each child showing:
  - Overall performance average and letter grade
  - Total assignments and exams
  - Pending assignments count
  - Recent activity timeline (last 3 activities)
  - Alerts (low grades, missing work, etc.)
  - Quick actions: View Full Performance, Contact Teacher, Download Reports

**Color Coding**:
- Green border: 70%+ (performing well)
- Yellow border: 50-69% (needs improvement)
- Red border: <50% (needs attention)

### 3. Individual Child Performance View (`/parent/children/{studentId}`)
**Detailed Performance Dashboard**:
- Comprehensive performance metrics (assignments, exams, group work)
- Subject-wise breakdown with progress bars
- Performance trends chart (last 6 months)
- Recent activity timeline (last 20 activities)
- Overall metrics with weighted averages

### 4. Performance Calculation
**Weighted Average Formula**:
- Assignments: 40% weight
- Exams: 60% weight
- Overall average = (Assignment_Avg * 0.4 + Exam_Avg * 0.6)

**Grade Conversion**:
- Numeric grades → direct percentage
- UCE/UACE grades (D1-F9) → percentage equivalents
- Letter grades (A-F) → percentage equivalents

## How to Link Parents to Students

### Method 1: Database Direct (Temporary - For Testing)
```sql
-- Link a parent to a student
INSERT INTO parent_student (parent_id, student_id, relationship, is_primary, receive_notifications, created_at, updated_at)
VALUES (
    (SELECT id FROM users WHERE phone_number = '0700000000' AND account_type = 'parent'),
    (SELECT id FROM users WHERE phone_number = '0711111111' AND account_type = 'student'),
    'parent',
    1,  -- is_primary: 1 = yes, 0 = no
    1,  -- receive_notifications: 1 = yes, 0 = no
    NOW(),
    NOW()
);
```

### Method 2: Laravel Tinker
```php
php artisan tinker

// Find parent and student
$parent = User::where('phone_number', '0700000000')->where('account_type', 'parent')->first();
$student = User::where('phone_number', '0711111111')->where('account_type', 'student')->first();

// Link them
$parent->children()->attach($student->id, [
    'relationship' => 'parent',
    'is_primary' => true,
    'receive_notifications' => true
]);

// Verify
$parent->children; // Should show the linked student
```

### Method 3: Admin Interface (RECOMMENDED - To Be Built)
Create an admin page where school administrators can:
1. Search for parent by phone/email
2. Search for student by name/ID/class
3. Select relationship type (parent/guardian/sponsor)
4. Set primary contact flag
5. Set notification preferences
6. Click "Link" button

## Testing the Parent Portal

### Step 1: Create Test Parent Account
```sql
INSERT INTO users (name, email, phone_number, account_type, password, created_at, updated_at)
VALUES ('Test Parent', 'parent@test.com', '0700000000', 'parent', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', NOW(), NOW());
```
**Login**: phone: 0700000000, password: password

### Step 2: Link to Existing Student
```sql
INSERT INTO parent_student (parent_id, student_id, relationship, is_primary, receive_notifications, created_at, updated_at)
VALUES (
    (SELECT id FROM users WHERE phone_number = '0700000000'),
    (SELECT id FROM users WHERE account_type = 'student' LIMIT 1),
    'parent',
    1,
    1,
    NOW(),
    NOW()
);
```

### Step 3: View Dashboard
1. Log in as parent account
2. Navigate to `/parent/dashboard`
3. Should see linked student(s) with performance data

## What Teachers Must Do

For the Parent Portal to show meaningful data, teachers MUST:

### 1. Grade Standalone Assignments
- Navigate to assignment submissions
- Enter numerical grade (e.g., 45/50)
- Add feedback (optional but recommended)
- Change status to "Graded"
- Without this: Assignment performance shows 0%

### 2. Upload Exam Marks
- Use "Upload Marks" feature
- Fill all required fields:
  - Subject name
  - Exam type
  - Grade (numeric, UCE/UACE, or letter)
  - Total marks
- Without this: Exam performance shows 0%

### 3. Grade Video Assignments
- Review video submissions
- Enter grade out of total_marks
- Provide feedback
- Mark as graded

### 4. Grade Group Work
- Access group submissions
- Enter grade for the group
- Individual members receive same grade

### 5. Set Clear Deadlines
- All assignments must have `due_date` set
- Used for calculating on-time submission rates

## Performance Data Flow

```
Teacher Actions → Database Updates → Real-time Dashboard Updates
├─ Grade Assignment → assignment_submissions.grade → Performance Dashboard
├─ Upload Marks → student_marks.grade → Performance Dashboard
├─ Grade Group Work → group_submissions.grade → Performance Dashboard
└─ All updates instant → Parent sees immediately on next page load
```

## Value Proposition for Parents

**What Parents Gain**:
1. **Continuous Visibility**: Real-time academic progress (not just term reports)
2. **Early Intervention**: Alerts when child is struggling
3. **Transparent Assessment**: See exactly what's being taught and assessed
4. **ROI Demonstration**: Clear evidence of educational investment value
5. **Data-Driven Decisions**: Make informed choices about child's education

**The ROI Message**:
> "Your school fees investment is working! See your child's progress across assignments, projects, and exams. This continuous assessment ensures your investment delivers visible, measurable results."

## Routes

### Parent Routes
- `GET /parent/dashboard` → Main dashboard with all children
- `GET /parent/children/{studentId}` → Detailed view of specific child

## Files Modified/Created

### Controllers
- ✅ `app/Http/Controllers/Parent/DashboardController.php` - Complete rewrite with multi-child support

### Views
- ✅ `resources/views/parent/parent-dashboard.blade.php` - New comprehensive dashboard
- ⏳ `resources/views/parent/child-performance.blade.php` - Detailed child view (needs creation)

### Models
- ✅ `app/Models/User.php` - Added `children()` and `parents()` relationships

### Migrations
- ✅ `database/migrations/2025_12_12_165726_create_parent_student_table.php` - Pivot table

### Routes
- ✅ `routes/web.php` - Added parent.children.show route

## Next Steps

### High Priority
1. **Create Admin Interface for Parent-Student Linking**
   - Admin page to search and link parents to students
   - Bulk import capability (CSV: parent_phone, student_id, relationship)
   - View/edit existing links

2. **Build Child Performance Detail View**
   - Create `resources/views/parent/child-performance.blade.php`
   - Reuse Student Performance Dashboard components
   - Add parent-specific messaging/actions

3. **Implement Email Notifications**
   - Weekly performance summaries
   - Alerts for poor grades
   - Alerts for missing assignments
   - Respect `receive_notifications` flag

### Medium Priority
4. **Parent-Teacher Communication**
   - Messaging system for parents to contact teachers
   - Teacher response interface
   - Message history/threading

5. **Report Generation**
   - PDF progress reports for parents
   - Downloadable grade sheets
   - Term-end comprehensive reports

6. **Teacher Grading Dashboard**
   - Queue of pending assignments
   - Quick grade entry interface
   - Reminders for ungraded work

### Low Priority
7. **Comparative Analytics**
   - Class averages (anonymous)
   - Performance rankings (optional, configurable)
   - Subject-wise class comparisons

8. **Attendance Integration**
   - Show attendance rates on parent dashboard
   - Alerts for excessive absences

## Technical Notes

### Database Relationships
- One parent can have multiple students (e.g., siblings)
- One student can have multiple parents/guardians
- Enforced at application level (no database foreign keys due to constraint issues)
- Unique constraint on (parent_id, student_id) prevents duplicates

### Performance Optimization
- Consider caching per-child performance data (refresh daily)
- Index on parent_id and student_id for fast lookups
- Eager load relationships to avoid N+1 queries

### Security
- Parents can ONLY view their linked children
- Middleware: CheckAccountType ensures only parent accounts access parent routes
- Child detail route verifies parent ownership before displaying data

## Support & Maintenance

### Common Issues

**Issue**: Parent sees "No Children Linked"
- **Cause**: No records in parent_student table
- **Fix**: Use Method 1 or 2 above to link parent to student(s)

**Issue**: Performance shows 0%
- **Cause**: Teachers haven't graded work
- **Fix**: Ensure teachers grade assignments and upload exam marks

**Issue**: Alerts not showing
- **Cause**: receive_notifications set to false
- **Fix**: Update parent_student.receive_notifications to true

**Issue**: Wrong child displayed
- **Cause**: Incorrect parent_student link
- **Fix**: Delete incorrect link, create correct one

### Monitoring

Track these metrics:
- Number of parents with linked accounts
- Average time between work grading and parent viewing
- Parent engagement rate (login frequency)
- Alert response rate

## Future Enhancements

- Mobile app for parents (React Native)
- Push notifications for alerts
- SMS notifications (via Africa's Talking, Twilio)
- Parent feedback on teaching effectiveness
- Student goal setting with parent involvement
- Integration with school payment system (fees → performance correlation)

---

**Implementation Date**: December 12, 2025
**Status**: Phase 1 Complete (Dashboard + Relationships)
**Next Phase**: Admin linking interface + Notifications
