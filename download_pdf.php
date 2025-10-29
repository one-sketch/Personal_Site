<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors to user
ini_set('log_errors', 1);

require 'config.php';
require 'dompdf-master/autoload.inc.php';
use Dompdf\Dompdf;

try {

    // Validate resume ID
    if (!isset($_POST['resume_id']) || empty($_POST['resume_id'])) {
        throw new Exception("Resume ID is required");
    }

    $resume_id = intval($_POST['resume_id']);
    
    if ($resume_id <= 0) {
        throw new Exception("Invalid resume ID");
    }

    // Fetch resume from DB with prepared statement to prevent SQL injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM resumes WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . mysqli_error($conn));
    }
    
    mysqli_stmt_bind_param($stmt, "i", $resume_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $resume = mysqli_fetch_assoc($result);

    if(!$resume) {
        throw new Exception("Resume not found!");
    }

    // Decode JSON fields with error handling
    $education = json_decode($resume['education'], true);
    $projects = json_decode($resume['projects'], true);
    $work = json_decode($resume['work'], true);
    $leadership = json_decode($resume['leadership'], true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding resume data: " . json_last_error_msg());
    }

    // Sanitize HTML output
    function sanitize($text) {
        return htmlspecialchars($text ?? '', ENT_QUOTES, 'UTF-8');
    }

    // Generate HTML for PDF
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; font-size: 12pt; }
            h1 { font-size: 18pt; margin-bottom: 5px; }
            h2 { font-size: 14pt; margin-top: 15px; margin-bottom: 5px; border-bottom: 1px solid #000; }
            p { margin: 5px 0; }
        </style>
    </head>
    <body>';
    
    $html .= '<h1>'.sanitize($resume['name']).'</h1>';
    $html .= '<p>'.sanitize($resume['phone']).' | '.sanitize($resume['email']).' | '.sanitize($resume['linkedin']).' | '.sanitize($resume['website']).'</p>';
    $html .= '<h2>Objective</h2><p>'.sanitize($resume['objective']).'</p>';

    // Education
    if (is_array($education) && !empty($education)) {
        $html .= '<h2>Education</h2>';
        foreach($education as $edu){
            if (is_array($edu)) {
                $html .= '<p>'.sanitize($edu['school'] ?? '').'<br>'.sanitize($edu['degree'] ?? '').'<br>'.sanitize($edu['dates'] ?? '').'</p>';
            }
        }
    }

    // Skills
    $html .= '<h2>Skills</h2><p>'.sanitize($resume['skills']).'</p>';

    // Projects
    if (is_array($projects) && !empty($projects)) {
        $html .= '<h2>Projects</h2>';
        foreach($projects as $proj){
            if (is_array($proj)) {
                $bullets = isset($proj['bullets']) ? explode('|', $proj['bullets']) : [];
                $html .= '<p><strong>'.sanitize($proj['title'] ?? '').'</strong> ('.sanitize($proj['dates'] ?? '').')<br>';
                foreach($bullets as $b) {
                    $trimmed = trim($b);
                    if (!empty($trimmed)) {
                        $html .= '• '.sanitize($trimmed).'<br>';
                    }
                }
                $html .= '</p>';
            }
        }
    }

    // Work
    if (is_array($work) && !empty($work)) {
        $html .= '<h2>Work Experience</h2>';
        foreach($work as $w){
            if (is_array($w)) {
                $bullets = isset($w['bullets']) ? explode('|', $w['bullets']) : [];
                $html .= '<p><strong>'.sanitize($w['title'] ?? '').'</strong> - '.sanitize($w['location'] ?? '').' ('.sanitize($w['dates'] ?? '').')<br>';
                foreach($bullets as $b) {
                    $trimmed = trim($b);
                    if (!empty($trimmed)) {
                        $html .= '• '.sanitize($trimmed).'<br>';
                    }
                }
                $html .= '</p>';
            }
        }
    }

    // Leadership
    if (is_array($leadership) && !empty($leadership)) {
        $html .= '<h2>Leadership Experience</h2>';
        foreach($leadership as $l){
            if (is_array($l)) {
                $bullets = isset($l['bullets']) ? explode('|', $l['bullets']) : [];
                $html .= '<p><strong>'.sanitize($l['title'] ?? '').'</strong> - '.sanitize($l['organization'] ?? '').' ('.sanitize($l['location'] ?? '').')<br>';
                foreach($bullets as $b) {
                    $trimmed = trim($b);
                    if (!empty($trimmed)) {
                        $html .= '• '.sanitize($trimmed).'<br>';
                    }
                }
                $html .= '</p>';
            }
        }
    }
    
    $html .= '</body></html>';

    // Initialize dompdf
    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output PDF to browser
    $filename = sanitize($resume['name']).'_Resume.pdf';
    $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename); // Clean filename
    $dompdf->stream($filename, array("Attachment" => true));

} catch (Exception $e) {
    // Log the error
    error_log("PDF Generation Error: " . $e->getMessage());
    
    // Send user-friendly error
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Failed to generate PDF. Please try again or contact support.',
        'debug' => $e->getMessage() // Remove this in production
    ]);
}
?>