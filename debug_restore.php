<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔍 Debugging restore process...\n";

// Get latest backup file
$backupFiles = glob(storage_path('app/backups/backup_*.sql'));
if (!empty($backupFiles)) {
    $latestBackup = max($backupFiles);
    echo "📁 File backup: " . basename($latestBackup) . "\n";
    
    $content = file_get_contents($latestBackup);
    
    // Test the parsing method
    $statements = array_filter(
        array_map('trim', explode(';', $content)),
        function($stmt) {
            $stmt = trim($stmt);
            return !empty($stmt) && 
                   !preg_match('/^--/', $stmt) && 
                   !preg_match('/^SET\s+/i', $stmt) &&
                   (preg_match('/^(CREATE|INSERT|DROP|ALTER|UPDATE|DELETE|SELECT)\s+/i', $stmt) ||
                    preg_match('/^\(/', $stmt));
        }
    );
    
    echo "📊 Total statements: " . count($statements) . "\n";
    
    // Test each statement type
    $insertCount = 0;
    $createCount = 0;
    $dropCount = 0;
    $otherCount = 0;
    
    foreach ($statements as $index => $statement) {
        if (preg_match('/^INSERT\s+INTO/i', $statement)) {
            $insertCount++;
            echo "INSERT #{$insertCount}: " . substr($statement, 0, 80) . "...\n";
        } elseif (preg_match('/^CREATE\s+TABLE/i', $statement)) {
            $createCount++;
            echo "CREATE #{$createCount}: " . substr($statement, 0, 80) . "...\n";
        } elseif (preg_match('/^DROP\s+TABLE/i', $statement)) {
            $dropCount++;
            echo "DROP #{$dropCount}: " . substr($statement, 0, 80) . "...\n";
        } else {
            $otherCount++;
            echo "OTHER #{$otherCount}: " . substr($statement, 0, 80) . "...\n";
        }
    }
    
    echo "\n📈 Summary:\n";
    echo "   - INSERT statements: {$insertCount}\n";
    echo "   - CREATE TABLE statements: {$createCount}\n";
    echo "   - DROP TABLE statements: {$dropCount}\n";
    echo "   - Other statements: {$otherCount}\n";
    
    // Test if we can execute a simple INSERT
    if ($insertCount > 0) {
        echo "\n🧪 Testing simple INSERT execution...\n";
        try {
            // Find first INSERT statement
            $firstInsert = null;
            foreach ($statements as $statement) {
                if (preg_match('/^INSERT\s+INTO/i', $statement)) {
                    $firstInsert = $statement;
                    break;
                }
            }
            
            if ($firstInsert) {
                echo "Testing: " . substr($firstInsert, 0, 100) . "...\n";
                
                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::statement('SET UNIQUE_CHECKS=0;');
                DB::statement('SET AUTOCOMMIT=0;');
                
                // Try to execute
                DB::unprepared($firstInsert);
                echo "✅ INSERT executed successfully!\n";
                
                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::statement('SET UNIQUE_CHECKS=1;');
                DB::statement('SET AUTOCOMMIT=1;');
            }
        } catch (\Exception $e) {
            echo "❌ INSERT failed: " . $e->getMessage() . "\n";
        }
    }
    
} else {
    echo "❌ No backup files found!\n";
}

echo "\nDebug selesai.\n";
