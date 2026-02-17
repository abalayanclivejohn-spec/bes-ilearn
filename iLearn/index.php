<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>iLearn</title>
    <link rel="icon" type="image/png" href="assets/img/fav.jpg">
    <link rel="stylesheet" href="assets/css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts for fancy look -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
<!-- Add this at the top of your sidebar -->
<div class="menu-toggle" onclick="toggleMenu()">☰ Menu</div>

<header class="top-nav">
    <div class="brand">
        <div class="icon-wrap">
            <i class="fas fa-book-reader"></i>
        </div>
        <span class="brand-text">iLearn</span>
    </div>

    <nav class="nav-links">
        <a href="auth/teacher_login.php" class="nav-btn teacher">Teacher Login</a>
        <a href="auth/student_login.php" class="nav-btn student">Student Login</a>
    </nav>
</header>


<!-- HERO -->
<section class="hero">
    <div class="hero-box">
        <img src="assets/img/logo.jpg" alt="Bacuyangan Logo" class="hero-logo">
        <h2 class="school-name">Bacuyangan Elementary School</h2>

        <h1>The Best Learning Platform</h1>
        <p>
            Smart learning for students and powerful teaching tools for educators.
            Learn anytime, anywhere with iLearn.
        </p>
    </div>
</section>

<!-- VISION & MISSION -->
<section class="vm">
    <div class="vm-card">
        <h2>🎯 Vision</h2>
        <p>
            We dream of Filipinos who passionately love their country and whose values and competencies
             enable them to realize their full potential and contribute meaningfully to building the nation.
        </p>
    </div>

    <div class="vm-card">
        <h2>🚀 Mission</h2>
        <p>
            To protect and promote the right of every Filipino to quality, suitable, culture-based, and complete basic education where:
             Students learn in a child-friendly, gender-sensitive, safe, and motivating environment. 
             Teachers facilitate learning and constantly nurture every learner. Administrators and staff, as stewards of the institutuon,
              ensure an enabling and supportive environment for effective learning to happen. Family, Community, and other stakeholders 
              are actively engaged and share responsibility for developing life-long learners.
        </p>
    </div>
</section>

</body>
<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/sw.js")
    .then(() => console.log("Service Worker Registered"));
}
</script>

</html>
