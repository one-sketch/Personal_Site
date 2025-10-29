<?php
require 'config.php';
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Get resume ID
$resume_id = intval($_POST['resume_id']);

// Fetch resume from DB
$sql = "SELECT * FROM resumes WHERE id=$resume_id";
$result = mysqli_query($conn, $sql);
$resume = mysqli_fetch_assoc($result);

if(!$resume) exit("Resume not found!");

// Decode JSON fields
$education = json_decode($resume['education'], true);
$projects = json_decode($resume['projects'], true);
$work = json_decode($resume['work'], true);
$leadership = json_decode($resume['leadership'], true);

// Generate HTML for PDF
$html = '<h1>'.$resume['name'].'</h1>';
$html .= '<p>'.$resume['phone'].' | '.$resume['email'].' | '.$resume['linkedin'].' | '.$resume['website'].'</p>';
$html .= '<h2>Objective</h2><p>'.$resume['objective'].'</p>';

// Education
$html .= '<h2>Education</h2>';
foreach($education as $edu){
    $html .= '<p>'.$edu['school'].'<br>'.$edu['degree'].'<br>'.$edu['dates'].'</p>';
}

// Skills
$html .= '<h2>Skills</h2><p>'.$resume['skills'].'</p>';

// Projects
$html .= '<h2>Projects</h2>';
foreach($projects as $proj){
    $bullets = explode('|', $proj['bullets']);
    $html .= '<p>'.$proj['title'].' ('.$proj['dates'].')<br>';
    foreach($bullets as $b) $html .= '• '.trim($b).'<br>';
    $html .= '</p>';
}

// Work
$html .= '<h2>Work Experience</h2>';
foreach($work as $w){
    $bullets = explode('|', $w['bullets']);
    $html .= '<p>'.$w['title'].' - '.$w['location'].' ('.$w['dates'].')<br>';
    foreach($bullets as $b) $html .= '• '.trim($b).'<br>';
    $html .= '</p>';
}

// Leadership
$html .= '<h2>Leadership Experience</h2>';
foreach($leadership as $l){
    $bullets = explode('|', $l['bullets']);
    $html .= '<p>'.$l['title'].' - '.$l['organization'].' ('.$l['location'].')<br>';
    foreach($bullets as $b) $html .= '• '.trim($b).'<br>';
    $html .= '</p>';
}

// Initialize dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output PDF to browser
$dompdf->stream($resume['name'].'_Resume.pdf', array("Attachment" => true));
