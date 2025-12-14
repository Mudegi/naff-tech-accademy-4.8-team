# Quick Testing Guide - School SaaS Features

## ✅ Step 1: Setup Complete
All migrations have been run successfully! You're ready to test.

## 🧪 Step 2: Test School Registration

### 2.1 Access Registration Page
Open your browser and go to:
```
http://localhost/naff-tech-accademy-4.8-team/public/school/register
```
(Adjust the URL based on your local setup)

### 2.2 Fill the Registration Form

**School Information:**
- School Name: `Green Valley High School`
- School Email: `info@greenvalley.edu`
- School Phone: `+256 700 123 456`
- School Address: `123 Education Road, Kampala`

**Administrator Information:**
- Administrator Name: `John Principal`
- Administrator Email: `principal@greenvalley.edu`
- Administrator Phone: `+256 700 654 321`
- Password: `password123`
- Confirm Password: `password123`

**Select Subscription Package:**
- Choose any available package (Basic, Standard, or Premium)
- Review the features and pricing

### 2.3 Submit Registration
- Click "Register School"
- **Expected**: Redirected to payment page with subscription details

## 💳 Step 3: Test Payment Processing

### 3.1 Complete Payment (Manual Method for Testing)
On the payment page:
1. Select Payment Method: **"Manual Payment (Bank Transfer/Cash)"**
2. Transaction ID: `TEST-TXN-001`
3. Payment Reference: `REF-001`
4. Notes: `Test payment for school registration`
5. Click "Complete Payment"

### 3.2 Expected Result
- ✅ Payment status: Completed
- ✅ Subscription: Active
- ✅ School status: Active
- ✅ Redirected to login page with success message

## 🔐 Step 4: Test School Admin Login

### 4.1 Login
1. Go to: `http://localhost/naff-tech-accademy-4.8-team/public/login`
2. Login with:
   - Email: `principal@greenvalley.edu`
   - Password: `password123`

### 4.2 Expected Result
- ✅ Redirected to School Admin Dashboard
- ✅ See school information
- ✅ See statistics (staff, students, subjects, classes)
- ✅ See quick action links

## 👥 Step 5: Test Staff Management

### 5.1 Create Director of Studies
1. Click "Manage Staff" from dashboard
2. Click "Add Staff Member"
3. Fill in:
   - Name: `Jane Director`
   - Email: `director@greenvalley.edu`
   - Phone: `+256 700 111 222`
   - Role: **Director of Studies**
   - Password: `password123`
   - Confirm: `password123`
4. Click "Create Staff Member"

**Expected**: Staff member created, appears in list

### 5.2 Create Department First
Before creating HOD, create a department:
1. Click "Manage Departments" from dashboard
2. Click "Add Department"
3. Fill in:
   - Name: `Mathematics Department`
   - Code: `MATH`
   - Description: `Mathematics and Statistics`
   - Head of Department: Leave empty for now
4. Click "Create Department"

### 5.3 Create Head of Department
1. Go to Staff Management
2. Click "Add Staff Member"
3. Fill in:
   - Name: `Bob Head`
   - Email: `hod@greenvalley.edu`
   - Phone: `+256 700 222 333`
   - Role: **Head of Department**
   - Department: Select "Mathematics Department"
   - Password: `password123`
4. Click "Create Staff Member"

**Expected**: HOD created and assigned to department

### 5.4 Update Department with HOD
1. Go to Departments
2. Edit "Mathematics Department"
3. Select "Bob Head" as Head of Department
4. Save

**Expected**: Department shows HOD, HOD's department_id is updated

### 5.5 Create Subject Teacher
1. Go to Staff Management
2. Click "Add Staff Member"
3. Fill in:
   - Name: `Alice Teacher`
   - Email: `teacher@greenvalley.edu`
   - Phone: `+256 700 333 444`
   - Role: **Subject Teacher**
   - Department: Select "Mathematics Department"
   - Password: `password123`
4. Click "Create Staff Member"

**Expected**: Teacher created and assigned to department

### 5.6 Test Staff Listing
- ✅ All staff appear in list
- ✅ Departments shown for HODs and Teachers
- ✅ Search works
- ✅ Filter by role works

## 🏢 Step 6: Test Department Management

### 6.1 Create More Departments
Create:
- **Science Department** (Code: SCI)
- **Languages Department** (Code: LANG)

### 6.2 Test Department Features
- ✅ View all departments
- ✅ See HOD for each department
- ✅ See teacher count
- ✅ Edit departments
- ✅ Delete department (teachers unassigned)

## 💰 Step 7: Test Subscription Management

### 7.1 View Subscriptions
1. Click "Subscriptions" from dashboard
2. **Expected**: See active subscription card with end date

### 7.2 Purchase New Subscription
1. Click "Purchase New Subscription"
2. Select a package
3. Complete payment (use manual method)
4. **Expected**: New subscription activated

### 7.3 Verify Subscription History
- ✅ All subscriptions appear in history
- ✅ Payment status shown
- ✅ Active subscription highlighted

## ⚙️ Step 8: Test School Settings

### 8.1 Access Settings
1. Click "School Settings" from dashboard
2. **Expected**: Only School Admin can access

### 8.2 Update Settings
1. Change school name
2. Update email
3. Upload logo (optional)
4. Save
5. **Expected**: Changes saved and reflected

## 🔒 Step 9: Test Role Hierarchy

### 9.1 Test Director of Studies
1. Logout
2. Login as: `director@greenvalley.edu` / `password123`
3. Try to create staff
   - **Expected**: Can only create HOD and Teacher
4. Try to access settings
   - **Expected**: Access denied

### 9.2 Test Head of Department
1. Logout
2. Login as: `hod@greenvalley.edu` / `password123`
3. Try to create staff
   - **Expected**: Can only create Teacher
4. Try to edit Director
   - **Expected**: Access denied (can't manage higher roles)

### 9.3 Test Subject Teacher
1. Logout
2. Login as: `teacher@greenvalley.edu` / `password123`
3. Try to access staff management
   - **Expected**: Access denied

## 🏫 Step 10: Test Data Isolation

### 10.1 Create Second School
1. Register another school:
   - School Name: `Blue Mountain Academy`
   - Admin Email: `admin@bluemountain.edu`
   - Complete payment
2. Login as second school's admin

### 10.2 Verify Isolation
- ✅ Cannot see School 1's staff
- ✅ Cannot see School 1's departments
- ✅ Cannot see School 1's subjects
- ✅ Each school's data is completely isolated

## 📋 Quick Testing Checklist

Use this checklist to ensure you've tested everything:

### Registration & Payment
- [ ] School registration form loads
- [ ] Can select subscription package
- [ ] School account created
- [ ] Payment page accessible
- [ ] Payment can be completed
- [ ] School activated after payment

### School Admin Features
- [ ] Can login as school admin
- [ ] Dashboard shows correct information
- [ ] Statistics display correctly
- [ ] Quick actions work

### Staff Management
- [ ] Can create Director of Studies
- [ ] Can create Head of Department
- [ ] Can create Subject Teacher
- [ ] Department assignment works
- [ ] Staff listing shows all staff
- [ ] Search and filter work
- [ ] Can edit staff
- [ ] Can delete staff (with permission checks)

### Department Management
- [ ] Can create departments
- [ ] Can assign HOD to department
- [ ] Can edit departments
- [ ] Can delete departments
- [ ] Teacher count displays correctly
- [ ] HOD assignment updates user's department_id

### Subscription Management
- [ ] Can view subscription history
- [ ] Active subscription displays correctly
- [ ] Can purchase new subscription
- [ ] Payment processing works
- [ ] Subscription activates after payment

### School Settings
- [ ] Only School Admin can access
- [ ] Can update school information
- [ ] Can upload logo
- [ ] Changes save correctly

### Role Hierarchy
- [ ] School Admin can create all roles
- [ ] Director can only create HOD and Teacher
- [ ] HOD can only create Teacher
- [ ] Teacher cannot create staff
- [ ] Permission checks work correctly

### Data Isolation
- [ ] Schools cannot see each other's data
- [ ] Staff isolated by school
- [ ] Departments isolated by school
- [ ] Subjects isolated by school

## 🐛 Troubleshooting

### Issue: Can't see subscription packages
**Solution**: Run seeder:
```bash
php artisan db:seed --class=SubscriptionPackageSeeder
```

### Issue: Foreign key errors
**Solution**: The migrations handle this. Relationships work at application level.

### Issue: Payment page not accessible
**Solution**: Check subscription exists and status is 'pending'

### Issue: Can't login after registration
**Solution**: Make sure payment was completed and school is activated

## 🎯 Next Steps After Testing

1. Integrate payment gateways (Flutterwave/EasyPay)
2. Add email notifications
3. Add subscription renewal reminders
4. Create automated expiration handling
5. Add more analytics and reporting

---

**Happy Testing! 🚀**

