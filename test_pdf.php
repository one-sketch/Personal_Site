<?php
// Simple test file to check what's working
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Testing Resume PDF Generator</h2>";

// Test 1: Check if config.php exists
echo "<p><strong>Test 1: Config file</strong><br>";
if (file_exists('config.php')) {
    echo "✓ config.php found<br>";
    require 'config.php';
    if (isset($conn)) {
        echo "✓ Database connection variable exists<br>";
        if (mysqli_ping($conn)) {
            echo "✓ Database connection successful</p>";
        } else {
            echo "✗ Database connection failed: " . mysqli_connect_error() . "</p>";
        }
    } else {
        echo "✗ Database connection variable not set</p>";
    }
} else {
    echo "✗ config.php not found</p>";
}

// Test 2: Check if dompdf exists
echo "<p><strong>Test 2: Dompdf library</strong><br>";
if (file_exists('dompdf-master/autoload.inc.php')) {
    echo "✓ dompdf-master/autoload.inc.php found<br>";
    require 'dompdf-master/autoload.inc.php';
    
    try {
        $dompdf = new Dompdf\Dompdf();
        echo "✓ Dompdf class loaded successfully</p>";
    } catch (Exception $e) {
        echo "✗ Error loading Dompdf: " . $e->getMessage() . "</p>";
    }
} else {
    echo "✗ dompdf-master/autoload.inc.php not found</p>";
}

// Test 3: Check if we can query the database
echo "<p><strong>Test 3: Database query</strong><br>";
if (isset($conn)) {
    $result = mysqli_query($conn, "SELECT * FROM resumes LIMIT 1");
    if ($result) {
        $count = mysqli_num_rows($result);
        echo "✓ Database query successful<br>";
        echo "✓ Found $count resume(s) in database</p>";
        
        if ($count > 0) {
            $resume = mysqli_fetch_assoc($result);
            echo "<p><strong>Sample resume data:</strong><br>";
            echo "ID: " . $resume['id'] . "<br>";
            echo "Name: " . $resume['name'] . "<br>";
            echo "Education field type: " . gettype($resume['education']) . "</p>";
        }
    } else {
        echo "✗ Database query failed: " . mysqli_error($conn) . "</p>";
    }
}

echo "<hr>";
echo "<p>If all tests pass above, the issue might be with the actual PDF generation logic.</p>";
?>