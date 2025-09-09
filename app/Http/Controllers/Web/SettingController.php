<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CompanyProfile;
use App\Models\BackupHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SettingController extends Controller
{
    // Middleware sudah diterapkan di routes

    /**
     * Show settings dashboard
     */
    public function index()
    {
        $companyProfile = CompanyProfile::first();
        $backupHistories = BackupHistory::with('creator')->latest()->take(10)->get();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('settings.index', compact('companyProfile', 'backupHistories', 'roles', 'permissions'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'current_password' => 'required',
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai.']);
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->password) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }


    /**
     * Build mysqldump command with proper escaping
     */
    private function buildMysqldumpCommand($host, $port, $username, $password, $database, $filepath, $mysqldumpPath = 'mysqldump')
    {
        $command = $mysqldumpPath;
        $command .= ' --host=' . escapeshellarg($host);
        $command .= ' --port=' . escapeshellarg($port);
        $command .= ' --user=' . escapeshellarg($username);

        if (!empty($password)) {
            $command .= ' --password=' . escapeshellarg($password);
        }

        $command .= ' --single-transaction';
        $command .= ' --routines';
        $command .= ' --triggers';
        $command .= ' --events';
        $command .= ' --add-drop-table';
        $command .= ' --add-locks';
        $command .= ' --create-options';
        $command .= ' --disable-keys';
        $command .= ' --extended-insert';
        $command .= ' --quick';
        $command .= ' --lock-tables=false';
        $command .= ' --complete-insert';
        $command .= ' --hex-blob';
        $command .= ' --default-character-set=utf8mb4';
        $command .= ' ' . escapeshellarg($database);
        $command .= ' > ' . escapeshellarg($filepath) . ' 2>&1';

        return $command;
    }

    /**
     * Find mysqldump executable path
     */
    private function findMysqldumpPath()
    {
        $possiblePaths = [
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\wamp64\\bin\\mysql\\mysql8.0.21\\bin\\mysqldump.exe',
            'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
        ];

        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Create backup using Laravel database features (fallback method)
     */
    private function createLaravelBackup($filepath)
    {
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        $dbKey = 'Tables_in_' . $database;

        $sql = "-- Laravel Database Backup\n";
        $sql .= "-- Generated on: " . now() . "\n\n";

        foreach ($tables as $table) {
            $tableName = $table->$dbKey;
            $sql .= "-- Table structure for table `$tableName`\n";
            $sql .= "DROP TABLE IF EXISTS `$tableName`;\n";

            $createTable = DB::select("SHOW CREATE TABLE `$tableName`");
            $sql .= $createTable[0]->{'Create Table'} . ";\n\n";

            $sql .= "-- Dumping data for table `$tableName`\n";
            $rows = DB::table($tableName)->get();

            if ($rows->count() > 0) {
                $firstRow = $rows->first();
                $columns = array_keys((array) $firstRow);
                $sql .= "INSERT INTO `$tableName` (`" . implode('`, `', $columns) . "`) VALUES\n";

                $values = [];
                foreach ($rows as $row) {
                    $rowValues = [];
                    foreach ($columns as $column) {
                        $value = $row->$column;
                        if (is_null($value)) {
                            $rowValues[] = 'NULL';
                        } else {
                            $rowValues[] = "'" . addslashes($value) . "'";
                        }
                    }
                    $values[] = '(' . implode(', ', $rowValues) . ')';
                }

                $sql .= implode(",\n", $values) . ";\n\n";
            }
        }

        file_put_contents($filepath, $sql);
    }

    /**
     * Create optimized Laravel backup for shared hosting
     */
    private function createLaravelBackupOptimized($filepath)
    {
        $sql = "-- Laravel Database Backup (Optimized for Shared Hosting)\n";
        $sql .= "-- Generated on: " . now() . "\n";
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET UNIQUE_CHECKS=0;\n";
        $sql .= "SET AUTOCOMMIT=0;\n\n";

        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Get table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= "-- Table structure for table `{$tableName}`\n";
                $createStatement = $createTable[0]->{'Create Table'};
                
                // Add IF NOT EXISTS to prevent table already exists error
                $createStatement = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $createStatement);
                $sql .= $createStatement . ";\n\n";

                // Get table data in chunks to avoid memory issues
                $chunkSize = 1000;
                $offset = 0;
                $hasData = false;

                do {
                    $rows = DB::table($tableName)->offset($offset)->limit($chunkSize)->get();
                    
                    if ($rows->count() > 0 && !$hasData) {
                        $sql .= "-- Data for table `{$tableName}`\n";
                        $firstRow = $rows->first();
                        $columns = array_keys((array) $firstRow);
                        $sql .= "INSERT IGNORE INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";
                        $hasData = true;
                    }

                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($columns as $column) {
                            $value = $row->$column;
                            if (is_null($value)) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    
                    if (!empty($values)) {
                        $sql .= implode(",\n", $values) . ",\n";
                    }
                    
                    $offset += $chunkSize;
                } while ($rows->count() == $chunkSize);

                if ($hasData) {
                    // Remove last comma and add semicolon
                    $sql = rtrim($sql, ",\n") . ";\n\n";
                }
            }

            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "SET UNIQUE_CHECKS=1;\n";
            $sql .= "SET AUTOCOMMIT=1;\n";

            file_put_contents($filepath, $sql);
            
        } catch (\Exception $e) {
            Log::error('Laravel backup failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Download backup file
     */
    public function downloadBackup(BackupHistory $backupHistory)
    {
        $filePath = storage_path('app/' . $backupHistory->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download($filePath, $backupHistory->filename);
    }

    /**
     * Create automatic backup before restore
     */
    private function createAutomaticBackup($type = 'auto')
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_auto_{$type}_{$timestamp}.sql";
            $filepath = storage_path('app/backups/' . $filename);

            // Ensure backup directory exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Get database configuration
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Build mysqldump command
            $command = $this->buildMysqldumpCommand($host, $port, $username, $password, $database, $filepath);
            $output = [];
            $returnCode = 0;
            exec($command, $output, $returnCode);

            if ($returnCode === 0 && file_exists($filepath) && filesize($filepath) > 0) {
                // Save backup history
                BackupHistory::create([
                    'filename' => $filename,
                    'file_path' => 'backups/' . $filename,
                    'file_size' => filesize($filepath),
                    'type' => $type,
                    'created_by' => Auth::id(),
                    'notes' => "Automatic backup before restore - {$type}",
                ]);

                Log::info('Automatic backup created before restore', [
                    'filename' => $filename,
                    'type' => $type,
                    'user' => Auth::user()->name
                ]);
            } else {
                Log::warning('Failed to create automatic backup', [
                    'output' => implode("\n", $output),
                    'type' => $type
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error creating automatic backup', [
                'error' => $e->getMessage(),
                'type' => $type
            ]);
        }
    }

    /**
     * Restore database from backup with improved error handling
     */
    public function restoreBackup(Request $request, BackupHistory $backupHistory)
    {
        $request->validate([
            'confirm_restore' => 'required|accepted',
        ]);

        // Security: Validate user is authenticated and active
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Anda harus login untuk melakukan restore database!');
        }

        // Security: Validate user is not suspended
        if (Auth::user()->deleted_at !== null) {
            return redirect()->route('settings.index')->with('error', 'Akun Anda telah dinonaktifkan!');
        }

        // Security check: Only admin can restore (disabled in development)
        // $enableAdminCheck = false; // Disabled for development
        // if ($enableAdminCheck && !Auth::user()->hasPermissionTo('manage-settings')) {
        //     return redirect()->route('settings.index')->with('error', 'Hanya admin yang dapat melakukan restore database!');
        // }

        try {
            // Security: Validate file path to prevent path traversal
            $filePath = storage_path('app/' . $backupHistory->file_path);
            
            // Security: Ensure file is within allowed directory
            $allowedPath = storage_path('app/backups/');
            $realFilePath = realpath($filePath);
            $realAllowedPath = realpath($allowedPath);
            
            if (!$realFilePath || !$realAllowedPath || strpos($realFilePath, $realAllowedPath) !== 0) {
                throw new \Exception('File path tidak valid atau tidak diizinkan!');
            }
            
            // Security: Validate file extension
            $allowedExtensions = ['.sql', '.gz', '.zip'];
            $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            if (!in_array('.' . $fileExtension, $allowedExtensions)) {
                throw new \Exception('File extension tidak diizinkan! Hanya .sql, .gz, .zip yang diperbolehkan.');
            }

            if (!file_exists($filePath)) {
                throw new \Exception('File backup tidak ditemukan: ' . $filePath);
            }

            // Security check: Validate backup file size (max 100MB) - ALWAYS ENABLED
            $fileSize = filesize($filePath);
            if ($fileSize > 100 * 1024 * 1024) { // 100MB
                return redirect()->route('settings.index')->with('error', 'File backup terlalu besar (max 100MB)!');
            }
            
            // Security check: Validate minimum file size (must be at least 1KB)
            if ($fileSize < 1024) { // 1KB
                return redirect()->route('settings.index')->with('error', 'File backup terlalu kecil atau corrupt!');
            }

            // Security check: Validate backup file content - ALWAYS ENABLED
            $fileContent = file_get_contents($filePath, false, null, 0, 2000); // Read first 2000 chars
            $dangerousCommands = [
                'DROP DATABASE', 'DROP SCHEMA', 'DELETE FROM mysql', 'TRUNCATE TABLE mysql',
                'ALTER USER', 'CREATE USER', 'DROP USER', 'GRANT ALL', 'REVOKE ALL',
                'FLUSH PRIVILEGES', 'SET PASSWORD', 'CHANGE MASTER', 'RESET MASTER'
            ];
            
            foreach ($dangerousCommands as $command) {
                if (stripos($fileContent, $command) !== false) {
                    return redirect()->route('settings.index')->with('error', "File backup mengandung perintah berbahaya: {$command}!");
                }
            }
            
            // Security check: Validate that file contains SQL content
            $sqlKeywords = ['CREATE TABLE', 'INSERT INTO', 'DROP TABLE', 'ALTER TABLE', '-- MySQL dump'];
            $hasValidSQL = false;
            foreach ($sqlKeywords as $keyword) {
                if (stripos($fileContent, $keyword) !== false) {
                    $hasValidSQL = true;
                    break;
                }
            }
            
            if (!$hasValidSQL) {
                return redirect()->route('settings.index')->with('error', 'File backup tidak mengandung konten SQL yang valid!');
            }
            
            // Security: Validate file is readable and not corrupted
            if (!is_readable($filePath)) {
                return redirect()->route('settings.index')->with('error', 'File backup tidak dapat dibaca atau corrupt!');
            }
            
            // Security: Validate file is not empty and has reasonable content
            if (strlen($fileContent) < 100) {
                return redirect()->route('settings.index')->with('error', 'File backup terlalu kecil atau tidak valid!');
            }

            // Security: Log restore attempt
            Log::info('Database restore attempt started', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'backup_file' => $backupHistory->filename,
                'file_size' => $fileSize,
                'file_path' => $backupHistory->file_path,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => now()
            ]);

            // Security check: Rate limiting - max 1 restore per 5 minutes per user (for testing)
            $lastRestore = DB::table('backup_histories')
                ->where('created_by', Auth::id())
                ->where('notes', 'like', '%restore%') // Check for restore operations in notes
                ->where('created_at', '>', now()->subMinutes(5))
                ->first();
                
            if ($lastRestore) {
                return redirect()->route('settings.index')->with('error', 'Anda hanya dapat melakukan restore 1 kali per 5 menit!');
            }

            // Get database configuration
            $host = config('database.connections.mysql.host');
            $port = config('database.connections.mysql.port');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');

            // Check if database exists, create if not
            $this->ensureDatabaseExists($host, $port, $username, $password, $database);

            $restoreMethods = [
                'Method 1: Direct MySQL (Hosting)',
                'Method 2: Laravel DB (Safe)',
                'Method 3: Laravel DB (All Statements)',
                'Method 4: Laravel DB (Debug Mode)'
            ];

            $errors = [];

            // Method 1: Direct MySQL (Hosting Specific) with Foreign Key Handling
            try {
                // First, disable foreign key checks
                $disableFKCommand = sprintf(
                    'mysql --user=%s --password=%s --host=localhost --port=3306 %s -e "SET FOREIGN_KEY_CHECKS=0;" 2>&1',
                    escapeshellarg('u447139306_pcmnet'),
                    escapeshellarg('pcm-250883'),
                    escapeshellarg('u447139306_pcm_db')
                );
                
                $output = [];
                $returnCode = 0;
                exec($disableFKCommand, $output, $returnCode);

                // Then import the backup
                $command = sprintf(
                    'mysql --user=%s --password=%s --host=localhost --port=3306 %s < %s 2>&1',
                    escapeshellarg('u447139306_pcmnet'),
                    escapeshellarg('pcm-250883'),
                    escapeshellarg('u447139306_pcm_db'),
                    escapeshellarg($filePath)
                );

                $output = [];
                $returnCode = 0;
                exec($command, $output, $returnCode);

                // Finally, re-enable foreign key checks
                $enableFKCommand = sprintf(
                    'mysql --user=%s --password=%s --host=localhost --port=3306 %s -e "SET FOREIGN_KEY_CHECKS=1;" 2>&1',
                    escapeshellarg('u447139306_pcmnet'),
                    escapeshellarg('pcm-250883'),
                    escapeshellarg('u447139306_pcm_db')
                );
                
                exec($enableFKCommand, $output, $returnCode);

                if ($returnCode === 0) {
                    Log::info('Database restored using Method 1 (Direct MySQL with FK handling)', [
                        'backup_file' => $backupHistory->filename,
                        'user' => Auth::user()->name
                    ]);
                    return redirect()->route('settings.index')->with('success', 'Database berhasil di-restore menggunakan Method 1 (Direct MySQL)!');
                } else {
                    $errors[] = $restoreMethods[0] . ' failed: ' . implode("\n", $output);
                }
            } catch (\Exception $e) {
                $errors[] = $restoreMethods[0] . ' failed: ' . $e->getMessage();
            }

            // Method 2: Laravel DB (Safe) - Fixed parsing
            try {
                $sqlContent = file_get_contents($filePath);
                if ($sqlContent === false) {
                    throw new \Exception('Cannot read SQL file');
                }

                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::statement('SET UNIQUE_CHECKS=0;');
                DB::statement('SET AUTOCOMMIT=0;');

                // Simple and reliable SQL parsing - split by semicolon
                $statements = array_filter(
                    array_map('trim', explode(';', $sqlContent)),
                    function($stmt) {
                        $stmt = trim($stmt);
                        // Skip empty statements, comments, and SET statements
                        return !empty($stmt) && 
                               !preg_match('/^--/', $stmt) && 
                               !preg_match('/^SET\s+/i', $stmt) &&
                               (preg_match('/^(CREATE|INSERT|DROP|ALTER|UPDATE|DELETE|SELECT)\s+/i', $stmt) ||
                                preg_match('/^\(/', $stmt)); // Allow statements starting with (
                    }
                );

                $insertedCount = 0;
                $skippedCount = 0;
                $errorCount = 0;

                Log::info('Method 2: Processing statements', [
                    'total_statements' => count($statements),
                    'backup_file' => $backupHistory->filename,
                    'statements_preview' => array_slice($statements, 0, 5) // Show first 5 statements
                ]);

                // Process statements safely
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        Log::info("Method 2: Processing statement {$index}", [
                            'statement_preview' => substr($statement, 0, 100) . '...',
                            'statement_type' => preg_match('/^INSERT/i', $statement) ? 'INSERT' : 
                                              (preg_match('/^CREATE/i', $statement) ? 'CREATE' : 'OTHER')
                        ]);
                        
                        try {
                            // Handle CREATE TABLE statements
                            if (preg_match('/CREATE\s+TABLE.*?`([^`]+)`/i', $statement, $matches)) {
                                $tableName = $matches[1];
                                
                                // Skip system tables
                                if (in_array($tableName, ['migrations', 'password_resets', 'personal_access_tokens'])) {
                                    $skippedCount++;
                                    continue;
                                }
                                
                                // Skip if table already exists (hosting compatibility)
                                if (DB::getSchemaBuilder()->hasTable($tableName)) {
                                    $skippedCount++;
                                    Log::info("Method 2: Skipped CREATE TABLE {$tableName} (already exists)");
                                    continue;
                                }
                                
                                // Create the table
                                DB::unprepared($statement);
                                $insertedCount++;
                                Log::info("Method 2: Created table {$tableName}");
                            }
                            // Handle INSERT statements
                            else if (preg_match('/INSERT\s+INTO.*?`([^`]+)`/i', $statement, $matches)) {
                                $tableName = $matches[1];
                                
                                // Skip system tables
                                if (in_array($tableName, ['migrations', 'password_resets', 'personal_access_tokens'])) {
                                    $skippedCount++;
                                    continue;
                                }
                                
                                // Execute the INSERT statement
                                try {
                                    DB::unprepared($statement);
                                    $insertedCount++;
                                    Log::info("Method 2: Inserted data into {$tableName}", [
                                        'statement_preview' => substr($statement, 0, 100) . '...',
                                        'statement_length' => strlen($statement)
                                    ]);
                                } catch (\Exception $insertError) {
                                    Log::error("Method 2: INSERT failed for {$tableName}", [
                                        'error' => $insertError->getMessage(),
                                        'statement_preview' => substr($statement, 0, 100) . '...'
                                    ]);
                                    $errorCount++;
                                }
                            }
                            // Skip DROP TABLE statements
                            else if (preg_match('/DROP\s+TABLE/i', $statement)) {
                                $skippedCount++;
                                continue;
                            }
                            // Handle other statements
                            else {
                                DB::unprepared($statement);
                                $insertedCount++;
                            }
                        } catch (\Exception $e) {
                            Log::warning('SQL statement failed during restore (Method 2)', [
                                'statement_index' => $index,
                                'statement_preview' => substr($statement, 0, 100) . '...',
                                'error' => $e->getMessage(),
                                'error_code' => $e->getCode(),
                                'statement_type' => preg_match('/^INSERT/i', $statement) ? 'INSERT' : 
                                                  (preg_match('/^CREATE/i', $statement) ? 'CREATE' : 'OTHER')
                            ]);
                            $errorCount++;
                        }
                    }
                }

                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::statement('SET UNIQUE_CHECKS=1;');
                DB::statement('SET AUTOCOMMIT=1;');

                Log::info('Database restored using Method 2 (Laravel DB Safe)', [
                    'backup_file' => $backupHistory->filename,
                    'user' => Auth::user()->name,
                    'inserted_statements' => $insertedCount,
                    'skipped_statements' => $skippedCount,
                    'error_statements' => $errorCount
                ]);
                return redirect()->route('settings.index')->with('success', "Database berhasil di-restore menggunakan Method 2 (Laravel DB Safe)! Inserted: {$insertedCount}, Skipped: {$skippedCount}, Errors: {$errorCount}");

            } catch (\Exception $e) {
                $errors[] = $restoreMethods[1] . ' failed: ' . $e->getMessage();
            }

            // Method 3: Laravel DB (All Statements) - Fixed parsing
            try {
                $sqlContent = file_get_contents($filePath);
                if ($sqlContent === false) {
                    throw new \Exception('Cannot read SQL file');
                }

                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                DB::statement('SET UNIQUE_CHECKS=0;');
                DB::statement('SET AUTOCOMMIT=0;');

                // Better SQL parsing - handle multiline statements
                $lines = explode("\n", $sqlContent);
                $currentStatement = '';
                $statements = [];

                foreach ($lines as $line) {
                    $line = trim($line);
                    
                    // Skip comments and empty lines
                    if (empty($line) || strpos($line, '--') === 0) {
                        continue;
                    }
                    
                    // Skip SET statements
                    if (preg_match('/^SET\s+/i', $line)) {
                        continue;
                    }
                    
                    $currentStatement .= $line . ' ';
                    
                    // If line ends with semicolon, we have a complete statement
                    if (substr($line, -1) === ';') {
                        $statement = trim($currentStatement);
                        if (!empty($statement)) {
                            $statements[] = $statement;
                        }
                        $currentStatement = '';
                    }
                }

                $insertedCount = 0;
                $errorCount = 0;

                Log::info('Method 3: Processing statements', [
                    'total_statements' => count($statements),
                    'backup_file' => $backupHistory->filename
                ]);

                // Process ALL statements without filtering
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        try {
                            // Execute ALL statements
                            DB::unprepared($statement);
                            $insertedCount++;
                        } catch (\Exception $e) {
                            Log::warning('SQL statement failed during restore (Method 3)', [
                                'statement_index' => $index,
                                'statement_preview' => substr($statement, 0, 100) . '...',
                                'error' => $e->getMessage()
                            ]);
                            $errorCount++;
                        }
                    }
                }

                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                DB::statement('SET UNIQUE_CHECKS=1;');
                DB::statement('SET AUTOCOMMIT=1;');

                Log::info('Database restored using Method 3 (Laravel DB All Statements)', [
                    'backup_file' => $backupHistory->filename,
                    'user' => Auth::user()->name,
                    'inserted_statements' => $insertedCount,
                    'error_statements' => $errorCount
                ]);
                return redirect()->route('settings.index')->with('success', "Database berhasil di-restore menggunakan Method 3 (Laravel DB All Statements)! Inserted: {$insertedCount}, Errors: {$errorCount}");

            } catch (\Exception $e) {
                $errors[] = $restoreMethods[2] . ' failed: ' . $e->getMessage();
            }

            // Method 4: Laravel DB (Debug Mode)
            try {
                $sqlContent = file_get_contents($filePath);
                if ($sqlContent === false) {
                    throw new \Exception('Cannot read SQL file');
                }

                // Disable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=0;');

                // Split SQL into individual statements
                $statements = array_filter(
                    array_map('trim', explode(';', $sqlContent)),
                    function($stmt) {
                        return !empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^SET/', $stmt);
                    }
                );

                $insertedCount = 0;
                $errorCount = 0;

                Log::info('Method 4 Debug: Starting to process statements', [
                    'total_statements' => count($statements),
                    'backup_file' => $backupHistory->filename
                ]);

                // Process ALL statements with maximum logging
                foreach ($statements as $index => $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        try {
                            Log::info("Method 4 Debug: Processing statement #{$index}", [
                                'statement_preview' => substr($statement, 0, 300) . '...',
                                'statement_type' => $this->getStatementType($statement),
                                'statement_length' => strlen($statement),
                                'full_statement' => $statement
                            ]);
                            
                            // Execute ALL statements without any filtering
                            $result = DB::unprepared($statement);
                            $insertedCount++;
                            
                            Log::info("Method 4 Debug: Successfully executed statement #{$index}", [
                                'result' => $result,
                                'statement_type' => $this->getStatementType($statement)
                            ]);
                            
                        } catch (\Exception $e) {
                            Log::warning("Method 4 Debug: Statement #{$index} failed", [
                                'statement_preview' => substr($statement, 0, 200) . '...',
                                'error' => $e->getMessage(),
                                'statement_type' => $this->getStatementType($statement)
                            ]);
                            $errorCount++;
                        }
                    }
                }

                // Re-enable foreign key checks
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');

                Log::info('Database restored using Method 4 (Debug Mode)', [
                    'backup_file' => $backupHistory->filename,
                    'user' => Auth::user()->name,
                    'inserted_statements' => $insertedCount,
                    'error_statements' => $errorCount
                ]);
                return redirect()->route('settings.index')->with('success', "Database berhasil di-restore menggunakan Method 4 (Debug Mode)! Inserted: {$insertedCount}, Errors: {$errorCount}");

            } catch (\Exception $e) {
                $errors[] = $restoreMethods[3] . ' failed: ' . $e->getMessage();
            }

            // All methods failed
            Log::error('All restore methods failed', [
                'errors' => $errors,
                'user' => Auth::user()->name,
                'backup_file' => $backupHistory->filename
            ]);

            $errorMessage = 'All restore methods failed:<br><ul>';
            foreach ($errors as $error) {
                $errorMessage .= '<li>' . htmlspecialchars($error) . '</li>';
            }
            $errorMessage .= '</ul>';

            throw new \Exception($errorMessage);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Database restore failed', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name,
                'backup_file' => $backupHistory->filename
            ]);

            return redirect()->route('settings.index')->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    /**
     * Ensure database exists, create if not
     */
    private function ensureDatabaseExists($host, $port, $username, $password, $database)
    {
        // First, try to connect to the database to check if it exists
        try {
            $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
            $pdo = new \PDO($dsn, $username, $password);
            // If connection successful, database exists
            return;
        } catch (\PDOException $e) {
            // Database doesn't exist or connection failed, try to create it
        }

        // Try to create database using mysql command
        $createCommand = sprintf(
            'mysql --host=%s --port=%s --user=%s %s -e "CREATE DATABASE IF NOT EXISTS %s CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            $password ? '--password=' . escapeshellarg($password) : '',
            escapeshellarg($database)
        );

        $output = [];
        $returnCode = 0;
        exec($createCommand, $output, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Failed to create database: ' . implode("\n", $output));
        }
    }

    /**
     * Get statement type for debugging
     */
    private function getStatementType($statement)
    {
        $statement = trim($statement);
        if (preg_match('/^INSERT/i', $statement)) return 'INSERT';
        if (preg_match('/^CREATE/i', $statement)) return 'CREATE';
        if (preg_match('/^DROP/i', $statement)) return 'DROP';
        if (preg_match('/^ALTER/i', $statement)) return 'ALTER';
        if (preg_match('/^UPDATE/i', $statement)) return 'UPDATE';
        if (preg_match('/^DELETE/i', $statement)) return 'DELETE';
        if (preg_match('/^SELECT/i', $statement)) return 'SELECT';
        return 'UNKNOWN';
    }

    /**
     * Create new role
     */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $request->name]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return back()->with('success', 'Role berhasil dibuat.');
    }

    /**
     * Update role permissions
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        // Update role name
        $role->update(['name' => $request->name]);

        // Update permissions
        $role->syncPermissions($request->permissions ?? []);

        return back()->with('success', 'Role berhasil diperbarui.');
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        try {
            $role->syncPermissions($request->permissions ?? []);
            return redirect()->route('settings.index')->with('success', 'Permission role berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('settings.index')->with('error', 'Gagal memperbarui permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role)
    {
        if ($role->users()->count() > 0) {
            $users = $role->users->pluck('name')->join(', ');
            return redirect()->route('settings.index', ['tab' => 'roles'])->withErrors(['role' => "Role '{$role->name}' tidak dapat dihapus karena masih digunakan oleh user: {$users}. Silakan ubah role user tersebut terlebih dahulu."]);
        }

        $role->delete();

        return redirect()->route('settings.index', ['tab' => 'roles'])->with('success', "Role '{$role->name}' berhasil dihapus.");
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user->syncRoles([$request->role]);

        return back()->with('success', 'Role berhasil ditetapkan ke user.');
    }

    /**
     * Update company profile
     */
    public function updateCompanyProfile(Request $request)
    {
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_lengkap_perusahaan' => 'nullable|string|max:255',
            'inisial_perusahaan' => 'nullable|string|max:10',
            'alamat' => 'required|string',
            'nomor_kontak' => 'required|string|max:20',
            'whatsapp' => 'nullable|string|max:20',
            'email_support' => 'required|email',
            'website' => 'nullable|url',
            'deskripsi' => 'nullable|string',
            'payment_code_prefix' => 'required|string|max:3|min:1',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $companyProfile = CompanyProfile::firstOrNew();

        $updateData = [
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_lengkap_perusahaan' => $request->nama_lengkap_perusahaan,
            'inisial_perusahaan' => $request->inisial_perusahaan,
            'alamat' => $request->alamat,
            'nomor_kontak' => $request->nomor_kontak,
            'whatsapp' => $request->whatsapp,
            'email_support' => $request->email_support,
            'website' => $request->website,
            'deskripsi' => $request->deskripsi,
            'payment_code_prefix' => strtoupper($request->payment_code_prefix),
        ];

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($companyProfile->logo_path) {
                Storage::delete($companyProfile->logo_path);
            }

            $logoPath = $request->file('logo')->store('company-logos', 'public');
            $updateData['logo_path'] = $logoPath;
        }

        $companyProfile->fill($updateData);
        $companyProfile->save();

        return back()->with('success', 'Profil perusahaan berhasil diperbarui.');
    }

    /**
     * Create backup - Simple and reliable system
     */
    public function createBackup(Request $request)
    {
        try {
            $timestamp = now()->format('Y-m-d_H-i-s');
            $filename = "backup_{$timestamp}.sql";
            $filepath = storage_path('app/backups/' . $filename);

            // Ensure backup directory exists
            if (!file_exists(storage_path('app/backups'))) {
                mkdir(storage_path('app/backups'), 0755, true);
            }

            // Create simple backup using Laravel DB
            $this->createSimpleBackup($filepath);

            if (!file_exists($filepath) || filesize($filepath) == 0) {
                throw new \Exception('Backup file tidak berhasil dibuat');
            }

            // Save backup history
            BackupHistory::create([
                'filename' => $filename,
                'file_path' => 'backups/' . $filename,
                'file_size' => filesize($filepath),
                'type' => 'manual',
                'created_by' => Auth::id(),
                'notes' => $request->input('notes', 'Backup database sederhana - kompatibel dengan restore dan phpMyAdmin'),
            ]);

            Log::info('Simple backup created successfully', [
                'filename' => $filename,
                'user' => Auth::user()->name,
                'file_size' => filesize($filepath)
            ]);

            return back()->with('success', 'Backup database berhasil dibuat! File sederhana dan kompatibel dengan restore dan phpMyAdmin import.');

        } catch (\Exception $e) {
            Log::error('Backup failed', [
                'error' => $e->getMessage(),
                'user' => Auth::user()->name
            ]);

            return back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    /**
     * Create simple backup - Works for backup, restore, and phpMyAdmin import
     */
    private function createSimpleBackup($filepath)
    {
        $sql = "-- MySQL Database Backup\n";
        $sql .= "-- Generated on: " . now() . "\n";
        $sql .= "-- Compatible with: System Restore, phpMyAdmin Import\n\n";
        
        // Disable foreign key checks
        $sql .= "SET FOREIGN_KEY_CHECKS=0;\n";
        $sql .= "SET UNIQUE_CHECKS=0;\n";
        $sql .= "SET AUTOCOMMIT=0;\n";
        $sql .= "START TRANSACTION;\n\n";

        try {
            // Get all tables
            $tables = DB::select('SHOW TABLES');
            $databaseName = config('database.connections.mysql.database');
            $dbKey = 'Tables_in_' . $databaseName;

            foreach ($tables as $table) {
                $tableName = $table->$dbKey;
                
                // Skip backup_histories table to avoid conflicts
                if ($tableName === 'backup_histories') {
                    continue;
                }
                
                // Get table structure
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $createStatement = $createTable[0]->{'Create Table'};
                
                $sql .= "-- Table structure for table `{$tableName}`\n";
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                $sql .= $createStatement . ";\n\n";

                // Get table data in chunks for large tables
                $chunkSize = 1000;
                $offset = 0;
                $hasData = false;

                do {
                    $rows = DB::table($tableName)->offset($offset)->limit($chunkSize)->get();
                    
                    if ($rows->count() > 0 && !$hasData) {
                        $sql .= "-- Data for table `{$tableName}`\n";
                        $firstRow = $rows->first();
                        $columns = array_keys((array) $firstRow);
                        $sql .= "INSERT INTO `{$tableName}` (`" . implode('`, `', $columns) . "`) VALUES\n";
                        $hasData = true;
                    }

                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ($columns as $column) {
                            $value = $row->$column;
                            if (is_null($value)) {
                                $rowValues[] = 'NULL';
                            } else {
                                // Better escaping for hosting
                                $escapedValue = str_replace(['\\', "'", '"', "\n", "\r", "\t"], ['\\\\', "\\'", '\\"', '\\n', '\\r', '\\t'], $value);
                                $rowValues[] = "'" . $escapedValue . "'";
                            }
                        }
                        $values[] = '(' . implode(', ', $rowValues) . ')';
                    }
                    
                    if (!empty($values)) {
                        $sql .= implode(",\n", $values) . ",\n";
                    }
                    
                    $offset += $chunkSize;
                } while ($rows->count() == $chunkSize);

                if ($hasData) {
                    // Remove last comma and add semicolon
                    $sql = rtrim($sql, ",\n") . ";\n\n";
                }
            }

            // Re-enable foreign key checks
            $sql .= "COMMIT;\n";
            $sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
            $sql .= "SET UNIQUE_CHECKS=1;\n";
            $sql .= "SET AUTOCOMMIT=1;\n";

            file_put_contents($filepath, $sql);
            
        } catch (\Exception $e) {
            Log::error('Simple backup failed', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Remove DROP TABLE statements from backup file
     */
    private function removeDropTableStatements($filepath)
    {
        $content = file_get_contents($filepath);
        
        // Split into lines
        $lines = explode("\n", $content);
        $filteredLines = [];
        
        foreach ($lines as $line) {
            // Skip lines that start with DROP TABLE
            if (preg_match('/^\s*DROP\s+TABLE\s+/i', $line)) {
                continue;
            }
            $filteredLines[] = $line;
        }
        
        // Join lines back
        $content = implode("\n", $filteredLines);
        
        // Clean up multiple empty lines
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content);
        
        file_put_contents($filepath, $content);
    }

    /**
     * Build mysqldump command for phpMyAdmin compatibility
     */
    private function buildMysqldumpCommandPhpMyAdmin($host, $port, $username, $password, $database, $filepath, $mysqldumpPath = 'mysqldump')
    {
        $command = $mysqldumpPath;
        $command .= ' --host=' . escapeshellarg($host);
        $command .= ' --port=' . escapeshellarg($port);
        $command .= ' --user=' . escapeshellarg($username);

        if (!empty($password)) {
            $command .= ' --password=' . escapeshellarg($password);
        }

        $command .= ' --single-transaction';
        $command .= ' --routines';
        $command .= ' --triggers';
        $command .= ' --events';
        // Remove --add-drop-table to avoid DROP TABLE statements
        $command .= ' --add-locks';
        $command .= ' --create-options';
        $command .= ' --disable-keys';
        $command .= ' --extended-insert';
        $command .= ' --quick';
        $command .= ' --lock-tables=false';
        $command .= ' --complete-insert';
        $command .= ' --hex-blob';
        $command .= ' --default-character-set=utf8mb4';
        $command .= ' ' . escapeshellarg($database);
        $command .= ' > ' . escapeshellarg($filepath) . ' 2>&1';

        return $command;
    }

    /**
     * Import database manually with foreign key constraint handling
     */
}