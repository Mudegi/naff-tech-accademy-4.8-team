<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixClassesTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:classes-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix the classes table by adding missing is_system_class column if needed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Checking classes table structure...');
        
        // Check if column exists
        $hasColumn = Schema::hasColumn('classes', 'is_system_class');
        
        if ($hasColumn) {
            $this->info('✅ Column is_system_class already exists!');
            $this->info('No action needed.');
            return 0;
        }
        
        $this->warn('⚠️  Column is_system_class is missing!');
        $this->info('🔧 Adding the column now...');
        
        try {
            // Add the column using raw SQL to be safe
            DB::statement('ALTER TABLE classes ADD COLUMN is_system_class TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active');
            
            $this->info('✅ Column added successfully!');
            
            // Verify it was added
            $hasColumn = Schema::hasColumn('classes', 'is_system_class');
            if ($hasColumn) {
                $this->info('✅ Verification passed - column exists now!');
                $this->newLine();
                $this->info('You can now run your seeders:');
                $this->line('   php artisan db:seed --class=ClassSeeder');
                return 0;
            } else {
                $this->error('❌ Verification failed - column still missing!');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error adding column: ' . $e->getMessage());
            $this->newLine();
            $this->info('💡 You may need to add it manually:');
            $this->line('   ALTER TABLE classes ADD COLUMN is_system_class TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;');
            return 1;
        }
    }
}
