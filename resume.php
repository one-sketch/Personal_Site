<?php
include 'config.php';
$id = intval($_GET['id']);
$result = mysqli_query($conn,"SELECT * FROM resumes WHERE id=$id");
$resume = mysqli_fetch_assoc($result);

$education = json_decode($resume['education'],true);
$projects = json_decode($resume['projects'],true);
$work = json_decode($resume['work'],true);
$leadership = json_decode($resume['leadership'],true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?php echo $resume['name']; ?> - Resume</title>
<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h1,h2 { margin: 5px 0; }
p { margin: 2px 0; }
@media print {
  button { display: none; }
}
</style>
</head>
<body>
<h1><?php echo $resume['name']; ?></h1>
<p><?php echo $resume['phone']; ?> | <?php echo $resume['email']; ?> | <?php echo $resume['linkedin']; ?> | <?php echo $resume['website']; ?></p>

<h2>OBJECTIVE</h2>
<p><?php echo $resume['objective']; ?></p>

<h2>EDUCATION</h2>
<?php foreach($education as $edu): ?>
<p><?php echo $edu['school']; ?><br>
<?php echo $edu['degree']; ?><br>
<?php echo $edu['dates']; ?></p>
<?php endforeach; ?>

<h2>SKILLS</h2>
<p><?php echo $resume['skills']; ?></p>

<h2>PROJECTS</h2>
<?php foreach($projects as $proj): 
      $bullets = explode('|',$proj['bullets']); ?>
<p><?php echo $proj['title']; ?> (<?php echo $proj['dates']; ?>)<br>
<?php foreach($bullets as $b) echo "• ".trim($b)."<br>"; ?>
</p>
<?php endforeach; ?>
<!-- after Projects section -->
<h2>Work Experience</h2>
<?php foreach($work as $w):
      $bullets = explode('|',$w['bullets']); ?>
<p><?php echo $w['title']; ?> - <?php echo $w['location']; ?> (<?php echo $w['dates']; ?>)<br>
<?php foreach($bullets as $b) echo "• ".trim($b)."<br>"; ?>
</p>
<?php endforeach; ?>

<h2>Leadership Experience</h2>
<?php foreach($leadership as $l):
      $bullets = explode('|',$l['bullets']); ?>
<p><?php echo $l['title']; ?> - <?php echo $l['organization']; ?> (<?php echo $l['location']; ?>)<br>
<?php foreach($bullets as $b) echo "• ".trim($b)."<br>"; ?>
</p>
<?php endforeach; ?>

<!-- Work and Leadership sections can be added similarly -->

<button onclick="window.print()">Print / Save PDF</button>
</body>
</html>