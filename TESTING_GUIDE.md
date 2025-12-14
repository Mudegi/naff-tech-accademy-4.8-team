# School SaaS Testing Guide

This guide will walk you through testing all the new features for the multi-tenant school management system.

## Prerequisites

1. Ensure your database is set up and configured
2. Make sure you have a super admin account (or create one)

## Step 1: Run Migrations

First, run all the new migrations to create the necessary tables:

```bash
php artisan migrate
```

This will create:
- `schools` table
- `school_subscriptions` table
- `departments` table
- Add `school_id` to users, students, subjects, classes, resources, topics
- Add `department_id` to users
- Update `account_type` enum in users table

## Step 2: Seed Initial Data (Optional)

If you want to seed subscription packages:

```bash
php artisan db:seed --class=SubscriptionPackageSeeder
```

Or seed school roles:

```bash
php artisan db:seed --class=SchoolRolesSeeder
```

## Step 3: Test School Registration

### 3.1 Access Registration Page
1. Open your browser and navigate to: `http://localhost/naff-tech-accademy-4.8-team/public/school/register`
   (Adjust URL based on your setup)

### 3.2 Fill Registration Form
1. **School Information:**
   - School Name: "Test School"
   - School Email: "testschool@example.com"
   - School Phone: "+256 700 000 000"
   - School Address: "123 Test Street, Kampala"

2. **Administrator Information:**
   - Administrator Name: "John Admin"
   - Administrator Email: "admin@testschool.com"
   - Administrator Phone: "+256 700 111 111"
   - Password: "password123"
   - Confirm Password: "password123"

3. **Select Subscription Package:**
   - Choose one of the available packages
   - Review package details (price, duration, features)

4. Click "Register School"

### 3.3 Expected Result
- You should be redirected to a payment page
- A pending subscription should be created
- School status should be "inactive"
- School admin account should be created

## Step 4: Test Payment Processing

### 4.1 Complete Payment
1. On the payment page, you should see:
   - Subscription details (package name, amount, dates)
   - Payment method selection

2. **For Testing (Manual Payment):**
   - Select "Manual Payment (Bank Transfer/Cash)"
   - Enter a test Transaction ID: "TEST123456"
   - Enter Payment Reference: "REF789"
   - Add optional notes
   - Click "Complete Payment"

### 4.2 Expected Result
- Payment status changes to "completed"
- Subscription becomes active
- School status changes to "active"
- You're redirected to login page with success message

## Step 5: Test School Admin Login

### 5.1 Login as School Admin
1. Navigate to login page: `http://localhost/naff-tech-accademy-4.8-team/public/login`
2. Login with:
   - Email: "admin@testschool.com" (or phone number)
   - Password: "password123"

### 5.2 Expected Result
- You should be redirected to the School Admin Dashboard
- Dashboard should show:
  - School information
  - Statistics (staff, students, subjects, classes)
  - Quick action links
  - Recent staff members

## Step 6: Test Staff Management

### 6.1 Create Director of Studies
1. Navigate to: Staff Management (from dashboard or `/admin/school/staff`)
2. Click "Add Staff Member"
3. Fill in:
   - Name: "Jane Director"
   - Email: "director@testschool.com"
   - Phone: "+256 700 222 222"
   - Role: "Director of Studies"
   - Password: "password123"
   - Confirm Password: "password123"
4. Click "Create Staff Member"

### 6.2 Create Head of Department
1. First, create a department (see Step 7)
2. Go to Staff Management
3. Click "Add Staff Member"
4. Fill in:
   - Name: "Bob Head"
   - Email: "hod@testschool.com"
   - Phone: "+256 700 333 333"
   - Role: "Head of Department"
   - Department: Select the department you created
   - Password: "password123"
   - Confirm Password: "password123"
5. Click "Create Staff Member"

### 6.3 Create Subject Teacher
1. Go to Staff Management
2. Click "Add Staff Member"
3. Fill in:
   - Name: "Alice Teacher"
   - Email: "teacher@testschool.com"
   - Phone: "+256 700 444 444"
   - Role: "Subject Teacher"
   - Department: Select a department
   - Password: "password123"
   - Confirm Password: "password123"
4. Click "Create Staff Member"

### 6.4 Test Staff Listing
- Verify all staff members appear in the list
- Check that department is shown for HODs and Teachers
- Test search functionality
- Test filtering by role

### 6.5 Test Staff Editing
1. Click edit icon on any staff member
2. Update their information
3. Save changes
4. Verify changes are reflected

### 6.6 Test Role Hierarchy
1. Login as Director of Studies (jane@testschool.com)
2. Try to create staff - should only see HOD and Teacher options
3. Login as Head of Department (bob@testschool.com)
4. Try to create staff - should only see Teacher option
5. Login as Subject Teacher (alice@testschool.com)
6. Try to access staff management - should be denied

## Step 7: Test Department Management

### 7.1 Create Department
1. Navigate to: Departments (from dashboard or `/admin/school/departments`)
2. Click "Add Department"
3. Fill in:
   - Department Name: "Mathematics"
   - Code: "MATH" (optional)
   - Description: "Mathematics Department"
   - Head of Department: Select "Bob Head" (or leave empty)
   - Active: Checked
4. Click "Create Department"

### 7.2 Create More Departments
- Create "Science" department
- Create "Languages" department
- Assign different HODs to each

### 7.3 Test Department Listing
- Verify all departments appear
- Check that HOD is shown
- Check teacher count for each department
- Test search functionality

### 7.4 Test Department Editing
1. Click edit icon on a department
2. Change the HOD assignment
3. Update description
4. Save changes
5. Verify the HOD's department_id is updated automatically

### 7.5 Test Department Deletion
1. Try to delete a department
2. Verify all teachers are unassigned (department_id set to null)

## Step 8: Test Subscription Management

### 8.1 View Subscriptions
1. Navigate to: Subscriptions (from dashboard or `/admin/school/subscriptions`)
2. You should see:
   - Active subscription card (if payment was completed)
   - Subscription history table
   - All past subscriptions

### 8.2 Purchase New Subscription
1. Click "Purchase New Subscription"
2. Browse available packages
3. Select a package
4. You'll be redirected to payment page
5. Complete payment (use manual method for testing)
6. Verify subscription is activated

### 8.3 Test Subscription Status
- Check active subscription shows correct end date
- Verify "days remaining" calculation
- Test with expired subscription (manually set end_date in database)

## Step 9: Test School Settings

### 9.1 Access Settings
1. Navigate to: School Settings (from dashboard or `/admin/school/settings`)
2. Only School Admin should be able to access

### 9.2 Update School Information
1. Update school name
2. Update email
3. Upload school logo (optional)
4. Update address
5. Save changes
6. Verify changes are reflected

### 9.3 Test Access Control
1. Login as Director of Studies
2. Try to access settings - should be denied
3. Only School Admin can access settings

## Step 10: Test Data Isolation

### 10.1 Create Second School
1. Register another school with different details
2. Complete payment
3. Login as second school's admin

### 10.2 Verify Isolation
1. Create staff in School 1
2. Login to School 2
3. Verify School 2 cannot see School 1's staff
4. Create departments in both schools
5. Verify each school only sees their own departments
6. Create subjects in both schools
7. Verify data is properly isolated

## Step 11: Test Subscription Expiration

### 11.1 Simulate Expired Subscription
1. In database, update a school's subscription_end_date to past date
2. Login as that school's admin
3. Try to access dashboard
4. Should be logged out with expiration message

### 11.2 Renew Subscription
1. Purchase new subscription
2. Complete payment
3. Verify school is reactivated
4. Verify new subscription dates are set

## Step 12: Test Role Hierarchy Permissions

### 12.1 Test School Admin Permissions
- Can create all roles (Director, HOD, Teacher)
- Can manage all staff
- Can access all school features

### 12.2 Test Director of Studies Permissions
- Can create HODs and Teachers
- Cannot create another Director
- Can manage HODs and Teachers under them

### 12.3 Test Head of Department Permissions
- Can only create Teachers
- Can manage Teachers in their department
- Cannot manage other HODs

### 12.4 Test Subject Teacher Permissions
- Cannot create any staff
- Cannot access staff management
- Can access teaching features

## Common Issues & Solutions

### Issue: Foreign Key Constraint Errors
**Solution**: The migrations handle this gracefully. If you see errors, the relationships will still work at the application level.

### Issue: Can't see subscription packages during registration
**Solution**: Make sure subscription packages are seeded or created in the database with `is_active = true`

### Issue: Payment page not accessible
**Solution**: Check that the subscription ID exists and payment status is 'pending'

### Issue: School status not activating after payment
**Solution**: Check that `activateSubscription()` method is being called and school model is updated

## Testing Checklist

- [ ] School registration with subscription selection
- [ ] Payment processing (manual method)
- [ ] School activation after payment
- [ ] School admin login and dashboard
- [ ] Create Director of Studies
- [ ] Create Head of Department
- [ ] Create Subject Teacher
- [ ] Assign staff to departments
- [ ] Create departments
- [ ] Edit departments
- [ ] Delete departments
- [ ] View subscription history
- [ ] Purchase new subscription
- [ ] Update school settings
- [ ] Test role hierarchy permissions
- [ ] Test data isolation between schools
- [ ] Test subscription expiration handling

## Next Steps After Testing

1. Integrate payment gateways (Flutterwave/EasyPay) for automatic processing
2. Add email notifications for subscription events
3. Add subscription renewal reminders
4. Create automated subscription expiration handling
5. Add more detailed reporting and analytics

