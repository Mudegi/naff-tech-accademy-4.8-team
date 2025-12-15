<?php
/**
 * Check PHP Upload Configuration
 * Access this file directly: https://nafacademy.com/check-upload-limits.php
 * DELETE THIS FILE after checking!
 */

echo "<h1>PHP Upload Configuration</h1>";
echo "<pre>";
echo "upload_max_filesize:  " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size:        " . ini_get('post_max_size') . "\n";
echo "max_execution_time:   " . ini_get('max_execution_time') . " seconds\n";
echo "max_input_time:       " . ini_get('max_input_time') . " seconds\n";
echo "memory_limit:         " . ini_get('memory_limit') . "\n";
echo "max_file_uploads:     " . ini_get('max_file_uploads') . "\n";
echo "\n";
echo "PHP Version:          " . phpversion() . "\n";
echo "Server Software:      " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "\n";
echo "<strong>Recommended for your app:</strong>\n";
echo "upload_max_filesize:  20M or higher\n";
echo "post_max_size:        25M or higher\n";
echo "max_execution_time:   300 seconds\n";
echo "max_input_time:       300 seconds\n";
echo "</pre>";

echo "<hr>";
echo "<p style='color: red;'><strong>IMPORTANT: Delete this file after checking!</strong></p>";
