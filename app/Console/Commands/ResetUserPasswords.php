<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:reset-passwords 
                            {--list : List all users with their account types}
                            {--email= : Reset password for specific email}
                            {--type= : Reset passwords for all users of a specific account type}
                            {--password=password : New password to set (default: password)}
                            {--all : Reset all school staff passwords}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List users and reset their passwords for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // List all users
        if ($this->option('list')) {
            return $this->listUsers();
        }

        $password = $this->option('password');
        $resetCount = 0;

        // Reset by email
        if ($email = $this->option('email')) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->password = Hash::make($password);
                $user->save();
                $this->info("✓ Password reset for: {$user->name} ({$user->email}) - Account Type: {$user->account_type}");
                $resetCount++;
            } else {
                $this->error("User with email '{$email}' not found.");
                return 1;
            }
        }
        // Reset by account type
        elseif ($type = $this->option('type')) {
            $users = User::where('account_type', $type)->get();
            if ($users->isEmpty()) {
                $this->warn("No users found with account type: {$type}");
                return 0;
            }
            
            if (!$this->confirm("Reset passwords for {$users->count()} user(s) with account type '{$type}'?")) {
                return 0;
            }

            foreach ($users as $user) {
                $user->password = Hash::make($password);
                $user->save();
                $this->info("✓ Password reset for: {$user->name} ({$user->email})");
                $resetCount++;
            }
        }
        // Reset all school staff
        elseif ($this->option('all')) {
            $schoolStaffTypes = ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher'];
            $users = User::whereIn('account_type', $schoolStaffTypes)->get();
            
            if ($users->isEmpty()) {
                $this->warn("No school staff users found.");
                return 0;
            }

            if (!$this->confirm("Reset passwords for {$users->count()} school staff user(s)?")) {
                return 0;
            }

            foreach ($users as $user) {
                $user->password = Hash::make($password);
                $user->save();
                $this->info("✓ Password reset for: {$user->name} ({$user->email}) - Account Type: {$user->account_type}");
                $resetCount++;
            }
        }
        else {
            $this->error("Please specify --list, --email=, --type=, or --all option.");
            $this->info("\nUsage examples:");
            $this->info("  php artisan users:reset-passwords --list");
            $this->info("  php artisan users:reset-passwords --email=admin@example.com");
            $this->info("  php artisan users:reset-passwords --type=director_of_studies");
            $this->info("  php artisan users:reset-passwords --all");
            $this->info("  php artisan users:reset-passwords --type=school_admin --password=newpassword");
            return 1;
        }

        $this->info("\n✓ Successfully reset {$resetCount} password(s) to: {$password}");
        return 0;
    }

    /**
     * List all users with their details
     */
    private function listUsers()
    {
        $users = User::orderBy('account_type')->orderBy('name')->get();
        
        if ($users->isEmpty()) {
            $this->warn("No users found.");
            return 0;
        }

        $this->info("\n=== All Users ===");
        $this->info(str_repeat('-', 100));
        
        $headers = ['ID', 'Name', 'Email', 'Phone', 'Account Type', 'School ID', 'Active'];
        $data = [];

        foreach ($users as $user) {
            $data[] = [
                $user->id,
                $user->name,
                $user->email ?? 'N/A',
                $user->phone_number ?? 'N/A',
                $user->account_type,
                $user->school_id ?? 'N/A',
                $user->is_active ? 'Yes' : 'No',
            ];
        }

        $this->table($headers, $data);

        // Show summary by account type
        $this->info("\n=== Summary by Account Type ===");
        $summary = $users->groupBy('account_type')->map->count();
        foreach ($summary as $type => $count) {
            $this->info("  {$type}: {$count} user(s)");
        }

        // Show school staff specifically
        $schoolStaffTypes = ['school_admin', 'director_of_studies', 'head_of_department', 'subject_teacher'];
        $schoolStaff = $users->whereIn('account_type', $schoolStaffTypes);
        
        if ($schoolStaff->isNotEmpty()) {
            $this->info("\n=== School Staff Users ===");
            $this->info(str_repeat('-', 100));
            foreach ($schoolStaff as $user) {
                $schoolName = $user->school ? $user->school->name : 'N/A';
                $this->info("  • {$user->name} ({$user->email}) - {$user->account_type} - School: {$schoolName}");
            }
        }

        return 0;
    }
}
