# NAF Tech Academy - Complete User Guide

**Version:** 1.0  
**Last Updated:** December 14, 2025

---

## Table of Contents

1. [System Overview](#system-overview)
2. [System Administrator Guide](#1-system-administrator-guide)
3. [School Administrator Guide](#2-school-administrator-guide)
4. [Teacher Guide](#3-teacher-guide)
5. [Student Guide](#4-student-guide)
6. [Parent Guide](#5-parent-guide)
7. [Troubleshooting](#troubleshooting)
8. [Support](#support)

---

## System Overview

NAF Tech Academy is a comprehensive Learning Management System (LMS) designed for secondary schools in Uganda. The system supports:

- **Multi-tenant architecture**: Multiple schools on one platform
- **Academic management**: Subjects, classes, topics, and resources
- **Assessment tracking**: Student marks and performance analytics
- **Career guidance**: A-Level subject combinations and university course recommendations
- **Parent engagement**: Real-time student progress monitoring
- **Content delivery**: Video lessons, notes, and assignments

### User Roles

1. **System Administrator** - Manages the entire platform, all schools, and global settings
2. **School Administrator** - Manages individual school operations, staff, and students
3. **Teacher** - Creates content, uploads resources, and manages marks
4. **Student** - Accesses learning materials and tracks academic progress
5. **Parent** - Monitors child's academic performance

---

## 1. System Administrator Guide

### 1.1 Getting Started

#### Login
1. Navigate to: `https://yourplatform.com/login`
2. Enter your admin credentials
3. Click "Sign In"

**Default Admin Account:**
- Email: admin@naftechacademy.com
- Password: (Set during installation)

#### Dashboard Overview
After login, you'll see:
- Total schools registered
- Active users count
- System-wide statistics
- Recent activities

### 1.2 Managing Schools

#### Adding a New School

1. Go to **Dashboard** → **Schools** → **Add New School**
2. Fill in school details:
   - School Name (required)
   - School Code (unique identifier)
   - Email Address
   - Phone Number
   - Physical Address
   - District/Region
   - School Type (Government/Private)
3. Click **"Create School"**

#### Editing School Information

1. Navigate to **Schools** list
2. Click the **"Edit"** icon next to the school
3. Update the required fields
4. Click **"Save Changes"**

#### Activating/Deactivating Schools

1. Go to **Schools** list
2. Find the school
3. Toggle the **"Active/Inactive"** switch
4. Confirm the action

### 1.3 Academic Settings

#### Managing Subjects

**Adding Subjects:**
1. Go to **Academic Settings** → **Subjects**
2. Click **"Add New Subject"**
3. Enter:
   - Subject Name (e.g., Mathematics, Physics)
   - Subject Code (e.g., MATH, PHY)
   - Description
   - Level (O-Level/A-Level/Both)
4. Click **"Create Subject"**

**Editing Subjects:**
1. Find the subject in the list
2. Click **"Edit"**
3. Modify details
4. Click **"Update"**

#### Managing Classes

**System Classes (Form 1-6):**
The system comes with pre-configured classes:
- **O-Level**: Form 1, Form 2, Form 3, Form 4
- **A-Level**: Form 5, Form 6

These are read-only and available to all schools.

**Viewing Classes:**
1. Go to **Academic Settings** → **Classes**
2. View all available classes

#### Managing Topics

**Adding Topics to Subjects:**
1. Go to **Academic Settings** → **Topics**
2. Click **"Add New Topic"**
3. Select:
   - Subject
   - Class Level
   - Topic Name
   - Description
4. Click **"Create Topic"**

### 1.4 University Cut-Offs Management

#### Viewing University Cut-Offs

1. Go to **University Cut-Offs**
2. Use filters:
   - University Name
   - Academic Year
   - Status (Active/Inactive)
3. View courses with:
   - Cut-off points
   - Essential subjects (A-Level requirements)
   - Minimum principal passes

#### Exporting Cut-Offs

1. Click **"Export to Excel"** button
2. Select filters (optional)
3. Download the Excel file
4. File includes:
   - University name
   - Course name
   - Cut-off points (general, male, female)
   - Essential subjects
   - Academic year
   - All other course details

#### Importing Cut-Offs

1. Click **"Import from Excel/CSV"**
2. Select university from dropdown
3. Choose your Excel/CSV file
4. Click **"Import File"**
5. Review import results:
   - Successfully imported
   - Skipped rows
   - Errors (if any)

**Excel File Format:**
- Must include columns:
  - Course Name
  - Degree Type
  - Minimum Principal Passes
  - Academic Year
  - Essential Subjects (comma-separated)

#### Adding Manual Cut-Offs

1. Click **"Add New Cut-Off"**
2. Fill in:
   - University Name
   - Course Name
   - Course Code
   - Faculty/Department
   - Degree Type
   - Cut-off Points
   - Essential Subjects (select from list)
   - Academic Year
3. Click **"Create"**

#### Editing Cut-Offs

1. Find the course in the list
2. Click **"Edit"** icon
3. Modify details (including essential subjects)
4. Click **"Update"**

### 1.5 User Management

#### Managing System Users

1. Go to **Users** → **All Users**
2. Filter by:
   - Account Type
   - School
   - Status

#### Creating Admin Accounts

**For Schools:**
1. Go to **Users** → **Add User**
2. Select Account Type: **"School Admin"**
3. Fill in:
   - Full Name
   - Email
   - Phone
   - Select School
4. Set temporary password
5. Click **"Create User"**
6. User will receive email to set their password

**For System:**
1. Select Account Type: **"Super Admin"**
2. Leave School field empty
3. Complete other fields
4. Click **"Create User"**

#### Impersonating Users

**For Testing/Support:**
1. Find user in list
2. Click **"Impersonate"** button
3. System logs you in as that user
4. Test features/troubleshoot
5. Click **"Stop Impersonating"** to return

### 1.6 Subscription Management

#### School Subscriptions

1. Go to **Subscriptions**
2. View all school subscriptions:
   - Plan type
   - Start/End dates
   - Payment status
   - Features included

#### Creating Subscription Plans

1. Go to **Subscription Plans**
2. Click **"Add New Plan"**
3. Define:
   - Plan Name (Basic, Premium, Enterprise)
   - Price
   - Duration
   - Features included
   - Student limit
4. Click **"Create"**

### 1.7 System Settings

#### Platform Configuration

1. Go to **Settings** → **System Settings**
2. Configure:
   - Platform Name
   - Support Email
   - Maximum Upload Size
   - Allowed File Types
   - Email Settings (SMTP)
   - Payment Gateway

#### Academic Year Settings

1. Go to **Settings** → **Academic Year**
2. Set:
   - Current Academic Year
   - Term Start/End Dates
   - School Calendar

### 1.8 Reports & Analytics

#### System Reports

1. Go to **Reports**
2. Generate:
   - **User Activity Report**: Login frequency, active users
   - **School Performance**: Top performing schools
   - **Resource Usage**: Most accessed materials
   - **Revenue Report**: Subscription payments
   - **System Health**: Performance metrics

#### Exporting Reports

1. Select report type
2. Choose date range
3. Apply filters
4. Click **"Export"** (Excel/PDF)
5. Download file

### 1.9 Content Moderation

#### Reviewing Uploaded Content

1. Go to **Content** → **Pending Review**
2. Review resources uploaded by teachers
3. Actions:
   - **Approve**: Make available to students
   - **Reject**: Send back with reason
   - **Edit**: Modify before approval

### 1.10 Security & Maintenance

#### User Access Logs

1. Go to **Security** → **Access Logs**
2. View:
   - Login attempts
   - Failed logins
   - IP addresses
   - Session times

#### System Backup

1. Go to **Maintenance** → **Backup**
2. Create manual backup:
   - Database backup
   - File storage backup
3. Schedule automatic backups
4. Download backup files

#### Database Maintenance

1. Go to **Maintenance** → **Database**
2. Run:
   - **Optimize Tables**
   - **Clear Cache**
   - **Rebuild Indexes**

---

## 2. School Administrator Guide

### 2.1 Getting Started

#### First Login

1. Receive credentials from System Admin or registration
2. Go to: `https://yourplatform.com/login`
3. Enter email and temporary password
4. Set new secure password
5. Complete profile setup

#### Dashboard Overview

Your dashboard shows:
- Total students enrolled
- Active teachers
- Recent activities
- Class statistics
- Upcoming events

### 2.2 School Profile Setup

#### Updating School Information

1. Go to **Settings** → **School Profile**
2. Update:
   - School Logo (upload image)
   - School Motto
   - Contact Information
   - Physical Address
   - Website URL
   - Social Media Links
3. Click **"Save Changes"**

#### Setting School Preferences

1. Go to **Settings** → **Preferences**
2. Configure:
   - Academic year
   - Current term
   - Grading system
   - Report card format
   - Parent access permissions
3. Save settings

### 2.3 Staff Management

#### Adding Teachers

1. Go to **Staff** → **Add Teacher**
2. Fill in:
   - **Personal Information:**
     - Full Name
     - Email Address
     - Phone Number
     - Gender
     - Date of Birth
   - **Professional Details:**
     - Employee ID
     - Qualification
     - Specialization
     - Date of Joining
   - **Account Details:**
     - Username
     - Temporary Password
3. Click **"Create Teacher Account"**
4. Teacher receives welcome email

#### Assigning Subjects to Teachers

1. Go to **Staff** → select teacher
2. Click **"Assign Subjects"**
3. Select:
   - Subject(s)
   - Class(es)
4. Click **"Save Assignments"**

#### Managing Teacher Classes

1. Navigate to **Staff** → **Class Assignments**
2. View grid of teachers and their classes
3. Add/remove assignments as needed
4. Set class teachers (form masters/mistresses)

#### Teacher Permissions

1. Select teacher
2. Click **"Permissions"**
3. Grant/revoke:
   - Upload resources
   - Enter marks
   - View reports
   - Communicate with parents
4. Save changes

### 2.4 Student Management

#### Bulk Student Registration

**Using Excel Import:**
1. Go to **Students** → **Bulk Import**
2. Click **"Download Template"**
3. Fill Excel template:
   - Student Name
   - Registration Number
   - Class
   - Date of Birth
   - Gender
   - Parent Information
4. Upload completed file
5. Review import summary
6. Confirm import

#### Adding Individual Students

1. Go to **Students** → **Add Student**
2. Fill in:
   - **Personal Details:**
     - Full Name
     - Registration Number (unique)
     - Date of Birth
     - Gender
     - National ID (if applicable)
   - **Academic Information:**
     - Current Class
     - Admission Date
     - Previous School (optional)
   - **Contact Information:**
     - Student Email (optional)
     - Student Phone (optional)
3. Click **"Create Student"**

#### Student Profile Management

1. Go to **Students** list
2. Search for student
3. Click on student name
4. View/edit:
   - Personal information
   - Academic records
   - Parent information
   - Disciplinary records
   - Medical information
4. Click **"Update"** to save

#### Managing Student Classes

**Class Promotion:**
1. Go to **Students** → **Class Promotion**
2. Select:
   - Current class
   - Academic year
3. Choose promotion method:
   - **Promote All**: Move entire class
   - **Selective**: Choose individual students
4. Select destination class
5. Click **"Promote Students"**

**Class Transfer:**
1. Find student in list
2. Click **"Transfer Class"**
3. Select new class
4. Add reason (optional)
5. Confirm transfer

### 2.5 Parent Portal Management

#### Linking Parents to Students

**Method 1 - Automatic (Recommended):**
1. Go to **Parent-Student Links** → **Bulk Import**
2. Download template
3. Fill in:
   - Parent Name
   - Parent Phone Number
   - Student Registration Number
4. Upload file
5. System automatically:
   - Creates parent account if new
   - Links to student
   - Sends SMS with credentials

**Method 2 - Manual:**
1. Go to **Parent-Student Links** → **Add Link**
2. Search for parent (or create new)
3. Search for student
4. Select relationship type:
   - Father
   - Mother
   - Guardian
5. Click **"Link"**

#### Managing Parent Access

1. Go to **Parents** list
2. Select parent
3. Configure access:
   - View marks: Yes/No
   - View attendance: Yes/No
   - View assignments: Yes/No
   - Receive notifications: Yes/No
4. Save settings

#### Parent Communications

**Sending Bulk Messages:**
1. Go to **Communications** → **Parent Messages**
2. Select recipients:
   - All parents
   - By class
   - Custom selection
3. Compose message
4. Choose delivery:
   - SMS
   - Email
   - In-app notification
5. Schedule or send immediately

### 2.6 Department Management

#### Creating Departments

1. Go to **Departments** → **Add Department**
2. Enter:
   - Department Name (Sciences, Arts, Languages)
   - Head of Department
   - Description
3. Assign subjects to department
4. Click **"Create"**

#### Assigning Department Heads

1. Go to **Departments** list
2. Select department
3. Click **"Assign Head"**
4. Select teacher from dropdown
5. Save assignment

### 2.7 Marks Management

#### Monitoring Marks Entry

1. Go to **Marks** → **Marks Overview**
2. View:
   - Which classes have marks entered
   - Missing marks by subject/class
   - Entry progress by teacher
3. Filter by:
   - Term
   - Class
   - Subject
   - Exam Type

#### Approving/Locking Marks

1. Go to **Marks** → **Approval Queue**
2. Review marks entered by teachers
3. Actions:
   - **Approve**: Makes marks visible to students/parents
   - **Reject**: Send back to teacher with comments
   - **Lock**: Prevent further editing
4. Bulk approve by class/term

#### Generating Report Cards

1. Go to **Reports** → **Report Cards**
2. Select:
   - Class
   - Term
   - Academic Year
3. Choose format:
   - Individual PDF
   - Bulk PDF (all students)
   - Excel summary
4. Click **"Generate"**
5. Download or print

### 2.8 Attendance Management

#### Recording Attendance

1. Go to **Attendance**
2. Select:
   - Date
   - Class
3. Mark students:
   - **P** - Present
   - **A** - Absent
   - **L** - Late
   - **E** - Excused
4. Add notes (optional)
5. Click **"Save Attendance"**

#### Attendance Reports

1. Go to **Attendance** → **Reports**
2. Generate:
   - Daily attendance
   - Weekly summary
   - Monthly report
   - Student attendance history
3. Export to Excel/PDF

### 2.9 School Calendar

#### Creating Events

1. Go to **Calendar** → **Add Event**
2. Fill in:
   - Event Title
   - Event Type (Holiday, Exam, Sports Day)
   - Start Date/Time
   - End Date/Time
   - Description
   - Notify (students, parents, teachers)
3. Click **"Create Event"**

#### Managing Academic Terms

1. Go to **Calendar** → **Term Settings**
2. Define:
   - Term 1: Dates
   - Term 2: Dates
   - Term 3: Dates
   - Holidays between terms
3. Save calendar

### 2.10 School Reports

#### Academic Performance Reports

1. Go to **Reports** → **Academic Performance**
2. Generate:
   - **Class Performance**: Average by class
   - **Subject Analysis**: Performance by subject
   - **Top Performers**: Best students per class
   - **At-Risk Students**: Below average performers
3. Select:
   - Date range
   - Classes
   - Subjects
4. Export report

#### Custom Reports

1. Go to **Reports** → **Custom Reports**
2. Select report type
3. Choose fields to include
4. Apply filters
5. Generate and export

---

## 3. Teacher Guide

### 3.1 Getting Started

#### First Login

1. Receive credentials from School Admin
2. Go to login page
3. Enter username and password
4. Complete profile setup:
   - Upload profile photo
   - Add bio
   - Set preferences
5. Explore dashboard

#### Teacher Dashboard

Your dashboard displays:
- **My Classes**: Classes you teach
- **My Subjects**: Subjects assigned
- **Pending Tasks**: Marks to enter, assignments to grade
- **Recent Activities**: Latest student submissions
- **Calendar**: Upcoming lessons and events

### 3.2 Profile Management

#### Updating Your Profile

1. Click profile icon → **My Profile**
2. Update:
   - Personal information
   - Profile photo
   - Bio/About me
   - Contact details
   - Qualifications
3. Click **"Save Changes"**

#### Setting Preferences

1. Go to **Settings**
2. Configure:
   - Email notifications
   - SMS alerts
   - Default subject view
   - Dashboard layout
3. Save preferences

### 3.3 Managing Resources

#### Uploading Learning Materials

**Video Lessons:**
1. Go to **Resources** → **Upload Resource**
2. Click **"Upload Video"**
3. Fill in:
   - Title
   - Subject
   - Class/Level
   - Topic
   - Description
   - Tags
4. Choose file:
   - Upload from computer (MP4, AVI, MOV)
   - Or paste YouTube/Vimeo link
5. Click **"Upload"**
6. Wait for processing
7. Click **"Publish"** when ready

**Notes/Documents:**
1. Click **"Upload Document"**
2. Fill in details:
   - Title
   - Subject
   - Class
   - Topic
   - Description
3. Upload file:
   - PDF, Word, PowerPoint
   - Maximum 50MB
4. Click **"Upload & Publish"**

**Past Papers:**
1. Click **"Upload Past Paper"**
2. Select:
   - Subject
   - Exam type (UCE, UACE, Mock)
   - Year
   - Paper number
3. Upload PDF file
4. Add marking guide (optional)
5. Publish

#### Organizing Resources

**Creating Folders:**
1. Go to **My Resources**
2. Click **"New Folder"**
3. Name folder (e.g., "Form 3 Physics - Term 1")
4. Drag and drop resources into folder

**Tagging Resources:**
1. Open resource
2. Click **"Edit"**
3. Add tags:
   - Topic names
   - Difficulty level
   - Exam relevance
4. Save tags

#### Making Resources Private/Public

1. Go to **My Resources**
2. Select resource
3. Click settings icon
4. Choose visibility:
   - **Public**: All students can access
   - **Class-Specific**: Only selected classes
   - **Private**: Only you can see
5. Save settings

### 3.4 Entering Student Marks

#### Uploading Marks (Bulk Entry)

1. Go to **Marks** → **Enter Marks**
2. Select:
   - **Subject** you teach
   - **Class**
   - **Exam Type**:
     - Beginning of Term
     - Mid Term
     - End of Term
     - Mock Exams
     - Other (specify)
3. Click **"Download Excel Template"**
4. Fill in template:
   - Student names auto-populated
   - Enter marks for each student
   - Add comments (optional)
5. Upload completed file
6. Review preview
7. Click **"Submit Marks"**

#### Entering Marks (Individual Student)

1. Go to **Marks** → **Single Entry**
2. Select:
   - Subject
   - Class
   - Exam Type
3. Search for student
4. Enter:
   - Marks obtained
   - Total marks
   - Grade (auto-calculated)
   - Comments
5. Click **"Save"**

#### Editing Marks

1. Go to **Marks** → **My Marks**
2. Find marks entry
3. Click **"Edit"**
4. Modify marks
5. Add reason for change
6. Click **"Update"**

**Note:** Once marks are approved by admin, you may need permission to edit.

### 3.5 Creating Assignments

#### Creating New Assignment

1. Go to **Assignments** → **Create Assignment**
2. Fill in:
   - **Title**
   - **Subject**
   - **Class(es)**
   - **Description/Instructions**
   - **Attachment** (PDF/Word - optional)
   - **Due Date**
   - **Total Marks**
   - **Submission Type**:
     - File upload
     - Text entry
     - Link submission
3. Click **"Create & Assign"**
4. Students receive notification

#### Managing Submissions

1. Go to **Assignments** → **View Submissions**
2. Select assignment
3. View all submissions:
   - Submitted on time
   - Late submissions
   - Not submitted
4. For each submission:
   - Download file
   - Review work
   - Enter marks
   - Add feedback
   - Click **"Save Grade"**

#### Providing Feedback

1. Open student submission
2. Add comments:
   - General feedback
   - Specific line comments
3. Attach files (optional):
   - Corrected version
   - Additional notes
4. Select emoji reaction:
   - Excellent work 🌟
   - Good effort 👍
   - Needs improvement 📝
5. Click **"Send Feedback"**

### 3.6 Managing Group Projects

#### Creating Project Groups

1. Go to **Projects** → **Create Project**
2. Enter project details:
   - Project title
   - Subject
   - Class
   - Description
   - Deadline
3. Click **"Create Groups"**
4. Choose grouping method:
   - **Auto**: System assigns students
   - **Manual**: You select members
   - **Student Choice**: Students form groups
5. Set group size (2-6 members)
6. Click **"Create Groups"**

#### Monitoring Group Progress

1. Go to **Projects** → **View Project**
2. See all groups
3. For each group:
   - View members
   - Check submission status
   - View progress updates
   - Send messages
4. Grade group work:
   - Enter group mark (same for all)
   - Or individual marks per member
5. Save grades

### 3.7 Classroom Communication

#### Messaging Students

**Individual Message:**
1. Go to **Messages**
2. Click **"New Message"**
3. Select recipient (student)
4. Type message
5. Click **"Send"**

**Class Announcement:**
1. Go to **Messages** → **Announcements**
2. Select class
3. Type message
4. Attach file (optional)
5. Click **"Post to Class"**

#### Communicating with Parents

1. Go to **Messages** → **Parent Messages**
2. Select student's parent
3. Compose message about:
   - Academic performance
   - Behavior
   - Attendance
4. Send message
5. Parent receives via SMS/email

### 3.8 Managing Classes

#### Viewing Class Lists

1. Go to **My Classes**
2. Select class
3. View:
   - Student names
   - Registration numbers
   - Contact information
4. Export to Excel

#### Taking Attendance

1. Go to **My Classes** → select class
2. Click **"Take Attendance"**
3. Select date (default: today)
4. Mark each student:
   - ✓ Present
   - ✗ Absent
   - L Late
5. Add notes for absent students
6. Click **"Submit Attendance"**

### 3.9 Viewing Reports

#### Student Performance Reports

1. Go to **Reports** → **Student Performance**
2. Select:
   - Class
   - Subject
   - Date range
3. View:
   - Individual student progress
   - Class average
   - Grade distribution
4. Export to PDF/Excel

#### My Teaching Reports

1. Go to **Reports** → **My Reports**
2. View:
   - Classes taught
   - Resources uploaded
   - Marks entered
   - Assignment completion rates
3. Download summary

### 3.10 Professional Development

#### Accessing Training Materials

1. Go to **Resources** → **Teacher Resources**
2. Browse:
   - Training videos
   - Best practices guides
   - Subject-specific materials
3. Download or view online

#### Sharing Resources with Colleagues

1. Go to **My Resources**
2. Select resource
3. Click **"Share"**
4. Choose recipients:
   - Specific teachers
   - All teachers in school
   - Public (all teachers on platform)
5. Click **"Share"**

---

## 4. Student Guide

### 4.1 Getting Started

#### First Login

1. Receive credentials from school
2. Go to login page
3. Enter:
   - Registration Number OR Email
   - Password
4. Click **"Sign In"**
5. Complete profile setup:
   - Upload photo
   - Verify contact information
   - Set preferences

#### Student Dashboard

Your dashboard shows:
- **My Classes**: Current subjects
- **Pending Assignments**: Due soon
- **Recent Grades**: Latest marks
- **Announcements**: From teachers
- **Career Guidance**: Recommended courses
- **Learning Progress**: Completion status

### 4.2 Profile Management

#### Updating Your Profile

1. Click on your name → **My Profile**
2. Update:
   - Profile photo
   - Bio
   - Email address
   - Phone number
   - Emergency contact
3. Click **"Save"**

#### Setting Study Preferences

1. Go to **Settings**
2. Configure:
   - Notification preferences
   - Email alerts
   - Study reminders
   - Dashboard layout
3. Save preferences

### 4.3 Accessing Learning Resources

#### Browsing Resources

**By Subject:**
1. Go to **Resources** → **Browse by Subject**
2. Select your subject
3. View:
   - Video lessons
   - Notes/Documents
   - Past papers
4. Filter by:
   - Topic
   - Resource type
   - Date added

**By Topic:**
1. Go to **Resources** → **Browse by Topic**
2. Navigate folder structure:
   - Subject → Class → Topic
3. View all resources for that topic

#### Watching Video Lessons

1. Select video from resources
2. Click to play
3. Features available:
   - Pause/Resume
   - Playback speed control
   - Full screen mode
   - Take notes while watching
   - Bookmark important moments
4. Mark as complete when done

#### Downloading Materials

1. Find resource (note, past paper, etc.)
2. Click **"Download"** button
3. File saves to your device
4. Open with appropriate app:
   - PDF Reader
   - Word Processor
   - Spreadsheet app

#### Searching for Resources

1. Use search bar at top
2. Enter keywords:
   - Topic name
   - Subject
   - Resource type
3. Apply filters:
   - Subject
   - Class level
   - Date range
4. Click on result to open

### 4.4 Completing Assignments

#### Viewing Assignments

1. Go to **Assignments**
2. See tabs:
   - **To Do**: Not yet submitted
   - **Completed**: Submitted
   - **Graded**: Marked by teacher
3. Each assignment shows:
   - Subject
   - Due date
   - Total marks
   - Status

#### Submitting Assignments

**File Upload Method:**
1. Click on assignment
2. Read instructions carefully
3. Click **"Add Submission"**
4. Choose file from device:
   - PDF, Word, Images
   - Maximum 10MB
5. Add description (optional)
6. Click **"Submit Assignment"**
7. Confirmation message appears

**Text Entry Method:**
1. Click **"Add Submission"**
2. Type answer in text box
3. Format using tools:
   - Bold, Italic
   - Lists
   - Links
4. Click **"Submit"**

**Late Submissions:**
- Possible if enabled by teacher
- Marked as "Late" in teacher's view
- May have penalty (check syllabus)

#### Viewing Grades & Feedback

1. Go to **Assignments** → **Graded**
2. Click on graded assignment
3. View:
   - Marks obtained
   - Teacher's comments
   - Corrected file (if attached)
4. Read feedback carefully
5. Ask teacher for clarification if needed

### 4.5 Viewing Your Marks

#### Marks Overview

1. Go to **My Marks**
2. View marks by:
   - Subject
   - Exam type
   - Term
3. See:
   - Marks obtained
   - Grade
   - Class average
   - Your position

#### Detailed Marks Report

1. Click on subject
2. View breakdown:
   - Different exam types
   - Progress over time
   - Strengths and weaknesses
3. Download report as PDF

#### Comparing Performance

1. Go to **My Marks** → **Performance Analysis**
2. View charts:
   - Your marks vs. class average
   - Progress over terms
   - Subject comparison
3. Identify areas needing improvement

### 4.6 Group Projects

#### Viewing Your Groups

1. Go to **Projects**
2. See all group projects
3. View:
   - Group members
   - Project deadline
   - Submission status

#### Collaborating with Group

1. Click on project
2. Access:
   - **Group Chat**: Discuss with members
   - **Shared Files**: Upload/download resources
   - **Task List**: See who's doing what
   - **Progress Updates**: Track completion
3. Add your contributions

#### Submitting Group Work

1. As designated group leader, or with consensus
2. Click **"Submit Project"**
3. Upload final files
4. Add project description
5. List each member's contribution
6. Click **"Submit"**

### 4.7 Career Guidance & Recommendations

#### A-Level Subject Combinations (For Form 4 Students)

1. Go to **Career Guidance** → **Subject Combinations**
2. View your O-Level results
3. System suggests A-Level combinations:
   - Based on your best subjects
   - Minimum entry requirements
   - Career path alignment
4. Explore combinations:
   - MCB (Maths, Chemistry, Biology)
   - PCM (Physics, Chemistry, Maths)
   - HEL (History, Economics, Literature)
   - And more...
5. Click on combination to see:
   - Required O-Level grades
   - University courses you can pursue
   - Career opportunities
6. Download recommendation PDF

#### University Course Recommendations (For A-Level Students)

1. Go to **Career Guidance** → **Course Recommendations**
2. View your UACE results
3. **Select Exam Type:**
   - Choose which exam's marks to use
   - Options: Mid Term, End of Term, Mock
4. System calculates your:
   - Aggregate points
   - Principal passes
   - Subsidiary passes
5. **View Qualifying Courses:**
   - Organized by university:
     - Makerere University
     - Kyambogo University
     - Other universities
   - Each course shows:
     - Program name
     - Required points
     - Your points
     - Essential subjects match
     - Admission probability
6. **Filter Results:**
   - By university
   - By program type
   - By your subject combination
7. Download recommendations as PDF

#### Performance Comparison

**Tracking Your Progress:**
1. View **Performance Comparison** section
2. See:
   - Current exam results
   - Previous exam results
   - Subject-by-subject comparison
   - Improved/declined subjects
   - Aggregate points change
3. Use insights to:
   - Focus on weak subjects
   - Maintain strong subjects
   - Set improvement goals

### 4.8 Notifications & Alerts

#### Managing Notifications

1. Click bell icon (🔔) at top
2. View notifications:
   - New assignments posted
   - Grades released
   - Announcements from teachers
   - Upcoming deadlines
3. Click notification to view details
4. Mark as read

#### Setting Notification Preferences

1. Go to **Settings** → **Notifications**
2. Enable/disable:
   - Email notifications
   - SMS alerts
   - In-app notifications
3. Choose what to be notified about:
   - New assignments
   - Grades
   - Messages
   - Deadlines (24hrs, 1 week before)
4. Save preferences

### 4.9 Communicating with Teachers

#### Sending Messages

1. Go to **Messages**
2. Click **"New Message"**
3. Select teacher
4. Choose subject (optional)
5. Type your message
6. Attach file if needed
7. Click **"Send"**

#### Class Discussions

1. Go to **Discussions** or subject page
2. View discussion threads
3. To participate:
   - Read the question/topic
   - Click **"Reply"**
   - Type your response
   - Submit
4. Be respectful and academic

### 4.10 Study Tools

#### Creating Study Notes

1. While viewing resources
2. Click **"Take Notes"**
3. Type notes in editor
4. Notes auto-save
5. Access later from **My Notes**

#### Using Bookmarks

1. While watching video or reading
2. Click **"Bookmark"** at important part
3. Add description (optional)
4. View all bookmarks from **My Bookmarks**
5. Click to return to that point

#### Study Schedule

1. Go to **Study Tools** → **My Schedule**
2. Add study sessions:
   - Subject
   - Topic
   - Duration
   - Date/Time
3. Receive reminders
4. Track completion

---

## 5. Parent Guide

### 5.1 Getting Started

#### Receiving Account Credentials

**Via SMS:**
- School sends SMS with:
  - Login link
  - Username (your phone number)
  - Temporary password
- First login: Change password

**Via Email:**
- Check email for welcome message
- Click activation link
- Set your password

#### First Login

1. Go to login page
2. Enter:
   - Phone number OR Email
   - Password
3. Click **"Sign In"**
4. You'll see your child's dashboard

#### Parent Dashboard

After login, you see:
- **Child's Information**: Name, class, registration number
- **Recent Grades**: Latest marks
- **Attendance Summary**: Present/Absent days
- **Pending Assignments**: Due soon
- **Teacher Messages**: Communications from school
- **Academic Progress**: Overall performance chart

### 5.2 Managing Multiple Children

#### Switching Between Children

If you have multiple children in the same school:

1. Look for dropdown menu with child's name
2. Click dropdown
3. Select another child
4. Dashboard updates to show that child's information

#### Adding Another Child

If you have a child not yet linked:

1. Contact school administrator
2. Provide:
   - Your details (name, phone)
   - Child's registration number
3. School will link accounts
4. You'll receive confirmation

### 5.3 Viewing Academic Performance

#### Checking Marks

**Overall View:**
1. From dashboard, click **"View Marks"**
2. See all subjects
3. View by:
   - Term
   - Exam Type
   - Subject
4. Each subject shows:
   - Marks obtained
   - Grade
   - Class average
   - Position in class

**Detailed Subject View:**
1. Click on specific subject
2. See breakdown:
   - Different assessments
   - Progress over time
   - Teacher's comments
   - Comparison with peers
3. Download report

**Performance Trends:**
1. Go to **Performance** → **Trends**
2. View graphs:
   - Progress over terms
   - Subject comparison
   - Strengths and weaknesses
3. Identify patterns

#### Report Cards

1. Go to **Reports** → **Report Cards**
2. Select term
3. View digital report card:
   - All subjects
   - Grades
   - Overall performance
   - Teacher comments
   - Class teacher's remarks
   - Head teacher's comments
4. Download PDF
5. Print if needed

### 5.4 Monitoring Assignments

#### Viewing Pending Assignments

1. Go to **Assignments**
2. See list of:
   - Current assignments
   - Due dates
   - Subjects
   - Submission status:
     - ✓ Submitted
     - ⏰ Pending
     - ⚠️ Overdue
3. Click for details

#### Checking Submission Status

1. Click on assignment
2. View:
   - When it was assigned
   - Due date
   - Submission status
   - If graded: mark and feedback
3. Encourage child to complete pending work

#### Monitoring Completion Rate

1. Go to **Assignments** → **Overview**
2. View statistics:
   - Total assignments
   - Completed on time
   - Late submissions
   - Not submitted
   - Average grade
3. Discuss with child if issues

### 5.5 Attendance Tracking

#### Daily Attendance

1. Go to **Attendance**
2. View:
   - Today's status (if updated)
   - Current week summary
   - Monthly overview
3. See color-coded calendar:
   - Green: Present
   - Red: Absent
   - Yellow: Late
   - Blue: Excused

#### Attendance Alerts

- Receive automatic SMS/email when:
  - Child is marked absent
  - Multiple consecutive absences
  - Attendance falls below threshold
- Contact school immediately if discrepancy

#### Attendance Reports

1. Go to **Attendance** → **Reports**
2. Generate:
   - Monthly summary
   - Term report
   - Yearly overview
3. View statistics:
   - Attendance percentage
   - Total days present
   - Total days absent
   - Late arrivals
4. Download report

### 5.6 Communication with School

#### Receiving Messages from Teachers

1. Check **Messages** section
2. View messages about:
   - Academic performance
   - Behavior
   - School events
   - Fee reminders
3. Mark important messages
4. Reply if needed

#### Sending Messages to Teachers

1. Click **"New Message"**
2. Select:
   - Subject teacher
   - Class teacher
   - School administrator
3. Type your message/concern
4. Send
5. Receive response in Messages

#### School Announcements

1. Go to **Announcements**
2. View:
   - General school news
   - Upcoming events
   - Holiday notices
   - Meeting schedules
3. Receive notifications for important announcements

### 5.7 Fee Management

#### Viewing Fee Balance

1. Go to **Fees** → **My Balance**
2. See:
   - Total fees for term
   - Amount paid
   - Outstanding balance
   - Payment due date
3. View transaction history

#### Making Payments

**Online Payment:**
1. Click **"Pay Now"**
2. Enter amount
3. Choose payment method:
   - Mobile Money
   - Bank Card
   - Bank Transfer
4. Complete payment
5. Receive confirmation

**Recording Offline Payment:**
1. If paid at school/bank
2. Go to **Fees** → **Record Payment**
3. Enter:
   - Amount paid
   - Payment method
   - Transaction reference
4. Upload receipt (optional)
5. Submit for verification

#### Payment Receipts

1. Go to **Fees** → **Receipts**
2. View all payments made
3. Download/print receipts
4. Keep for records

### 5.8 Monitoring Study Materials Access

#### Resource Usage

1. Go to **Resources** → **Usage Report**
2. View what child has accessed:
   - Videos watched
   - Documents downloaded
   - Study time
   - Most accessed subjects
3. Monitor engagement level
4. Encourage more usage if low

### 5.9 Career Planning

#### Viewing Recommendations

**For Form 4 Students:**
1. Go to **Career Guidance** → **Subject Combinations**
2. View recommended A-Level combinations
3. Based on child's O-Level performance
4. Discuss with child which to choose

**For Form 6 Students:**
1. Go to **Career Guidance** → **University Courses**
2. View courses child qualifies for
3. See universities and programs
4. Download recommendations
5. Discuss future plans together

#### University Applications

1. View qualifying courses
2. Note application requirements
3. Check deadlines
4. Guide child in:
   - Choosing courses
   - Preparing applications
   - Gathering documents

### 5.10 Parent Settings

#### Account Settings

1. Go to **Settings**
2. Update:
   - Your contact information
   - Email address
   - Phone number
   - Notification preferences
3. Change password:
   - Enter current password
   - Enter new password
   - Confirm new password
4. Save changes

#### Notification Preferences

1. Go to **Settings** → **Notifications**
2. Choose what to receive:
   - ✓ SMS for absences
   - ✓ Email for grades
   - ✓ App notifications for messages
   - ✓ Weekly progress summary
   - ✓ Fee reminders
3. Set notification frequency
4. Save preferences

#### Language Preference

1. Go to **Settings** → **Language**
2. Select:
   - English
   - Luganda
   - Other local languages (if available)
3. Interface updates immediately

---

## Troubleshooting

### Common Issues & Solutions

#### Login Problems

**Issue: Forgot Password**
1. Click **"Forgot Password"** on login page
2. Enter email OR phone number
3. Check email/SMS for reset link
4. Click link
5. Create new password
6. Login with new password

**Issue: Account Locked**
- After multiple failed login attempts
- Wait 30 minutes OR
- Contact school administrator to unlock

**Issue: Invalid Credentials**
- Double-check username/email
- Ensure CAPS LOCK is off
- Check for spaces before/after text
- Contact school if persists

#### Resource Access Issues

**Issue: Video Won't Play**
1. Check internet connection
2. Try different browser
3. Clear browser cache
4. Update browser
5. Try mobile app instead

**Issue: Can't Download File**
1. Check storage space on device
2. Check internet stability
3. Try again after few minutes
4. Contact teacher if file corrupted

**Issue: Missing Resources**
- Teacher may not have published yet
- Check you're viewing correct subject/class
- Contact teacher to confirm

#### Assignment Submission Issues

**Issue: File Won't Upload**
- Check file size (max 10MB)
- Ensure file type is allowed
- Check internet connection
- Try compressing file
- Or submit in smaller parts

**Issue: Submission Deadline Passed**
- Contact teacher immediately
- Explain situation
- Ask for extension
- Some teachers may allow late submission

#### Marks/Grades Issues

**Issue: Marks Not Showing**
- Marks may not be approved yet
- Check with teacher
- Refresh page
- Try different device

**Issue: Wrong Marks Displayed**
- Screenshot the error
- Contact teacher immediately
- They can review and correct

#### Mobile App Issues

**Issue: App Crashes**
1. Close and reopen app
2. Clear app cache
3. Update to latest version
4. Restart phone
5. Reinstall app if necessary

**Issue: Slow Performance**
1. Close other apps
2. Clear cache
3. Check internet speed
4. Update app
5. Free up phone storage

### Getting Help

#### Student Support
- **Email**: support@naftechacademy.com
- **Phone**: +256-XXX-XXXXXX
- **WhatsApp**: +256-XXX-XXXXXX
- **Live Chat**: Available on website (8 AM - 6 PM)

#### School Administrator Contact
- Contact your school's administrator through:
  - In-app messaging
  - School phone number
  - School email

#### Teacher Contact
- Use in-app messaging system
- During school hours
- Allow 24-48 hours for response

#### Technical Support Hours
- Monday - Friday: 8:00 AM - 8:00 PM
- Saturday: 9:00 AM - 5:00 PM
- Sunday: Emergency support only
- Response time: Within 24 hours

---

## Support

### Help Resources

#### Online Help Center
- Visit: `https://help.naftechacademy.com`
- Browse:
  - Video tutorials
  - Step-by-step guides
  - FAQs
  - Downloadable PDFs

#### User Manuals
- **System Admin Manual**: Detailed technical documentation
- **School Admin Manual**: School management guide
- **Teacher Manual**: Teaching tools and features
- **Student Manual**: Learning platform guide
- **Parent Manual**: Monitoring and engagement

### Training & Workshops

#### For School Administrators
- Free onboarding session (2 hours)
- Advanced features workshop
- Monthly webinars
- On-site training available

#### For Teachers
- Platform orientation
- Resource creation training
- Assessment tools workshop
- Best practices sessions

#### For Students
- Orientation video (15 minutes)
- Quick start guide
- Tutorial videos for each feature
- Peer mentoring program

#### For Parents
- Welcome orientation
- Navigation tutorial
- Monthly Q&A sessions
- User group meetings

### Feedback & Suggestions

#### Submit Feedback
1. Go to **Help** → **Feedback**
2. Choose category:
   - Bug report
   - Feature request
   - General feedback
   - Complaint
3. Describe in detail
4. Add screenshots (optional)
5. Submit
6. Track status of your feedback

#### Feature Requests
- Suggest new features
- Vote on others' suggestions
- Participate in user surveys
- Join beta testing program

### Contact Information

**NAF Tech Academy Support**
- **Email**: support@naftechacademy.com
- **Phone**: +256-XXX-XXXXXX
- **WhatsApp**: +256-XXX-XXXXXX
- **Address**: [Your Office Address]
- **Website**: www.naftechacademy.com
- **Social Media**:
  - Facebook: @NAFTechAcademy
  - Twitter: @NAFTechAcademy
  - YouTube: NAF Tech Academy

**Emergency Contact**
- 24/7 Hotline: +256-XXX-XXXXXX
- Emergency Email: emergency@naftechacademy.com

---

## Appendix

### Glossary of Terms

- **Aggregate Points**: Total points from A-Level principal passes used for university admission
- **Assignment**: Task given by teacher for students to complete
- **A-Level**: Advanced Level (Form 5 & 6)
- **Dashboard**: Main page after login showing overview
- **O-Level**: Ordinary Level (Form 1-4)
- **Principal Pass**: A-Level subject passed with at least 2 points
- **Resource**: Learning material (video, document, past paper)
- **Subsidiary Pass**: A-Level subject passed at lower level
- **Term**: Academic period (usually 3 per year)
- **UACE**: Uganda Advanced Certificate of Education
- **UCE**: Uganda Certificate of Education

### Keyboard Shortcuts

**General:**
- `Ctrl + S`: Save
- `Ctrl + P`: Print
- `Ctrl + F`: Search
- `Esc`: Close modal/popup

**Navigation:**
- `Alt + D`: Dashboard
- `Alt + M`: Messages
- `Alt + N`: Notifications
- `Alt + S`: Settings

### System Requirements

**Minimum Requirements:**
- **Internet**: 2 Mbps or faster
- **Browser**: 
  - Chrome 90+
  - Firefox 88+
  - Safari 14+
  - Edge 90+
- **Screen**: 1024x768 or higher
- **Mobile**: Android 8.0+ or iOS 12+

**Recommended:**
- **Internet**: 5 Mbps or faster
- **Screen**: 1366x768 or higher
- **RAM**: 4GB minimum
- **Mobile**: Latest OS version

### Supported File Formats

**Uploads:**
- **Documents**: PDF, DOC, DOCX, PPT, PPTX
- **Images**: JPG, PNG, GIF
- **Videos**: MP4, AVI, MOV, WMV
- **Archives**: ZIP, RAR

**Downloads:**
- All uploaded formats
- Reports: PDF, Excel

### Frequently Asked Questions (FAQ)

#### For All Users

**Q: How do I reset my password?**
A: Click "Forgot Password" on login page, enter your email, and follow the link sent to you.

**Q: Can I use the system on my phone?**
A: Yes, the system is fully mobile-responsive. You can also download our mobile app.

**Q: Is my data safe?**
A: Yes, we use industry-standard encryption and security measures. Data is backed up daily.

#### For Students

**Q: Can I access resources offline?**
A: You can download materials when online and view them offline later.

**Q: What if I miss the assignment deadline?**
A: Contact your teacher immediately. Some teachers allow late submissions with penalties.

**Q: How do I know if my marks are correct?**
A: Review your marks and if you notice an error, report to your teacher within 7 days.

#### For Teachers

**Q: Can I bulk upload marks for all students?**
A: Yes, download the Excel template, fill it in, and upload. All marks are entered at once.

**Q: How do I make resources private?**
A: Open the resource, click settings, and change visibility to "Private" or select specific classes.

**Q: Can I edit marks after submission?**
A: Yes, but once approved by admin, you may need permission to edit.

#### For Parents

**Q: How often are marks updated?**
A: Teachers enter marks after each assessment. Updates appear once approved by school.

**Q: Can I see marks from previous terms?**
A: Yes, use the term selector to view historical data.

**Q: How do I report absence?**
A: Contact the school directly. Parents cannot mark attendance.

---

**End of User Guide**

*For the latest updates and additional resources, visit our online help center or contact support.*

**Document Version:** 1.0  
**Last Updated:** December 14, 2025  
**Next Review:** March 2026
