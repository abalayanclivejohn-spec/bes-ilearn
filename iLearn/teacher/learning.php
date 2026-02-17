<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$stmt = $pdo->prepare("SELECT fullname FROM teachers WHERE teacher_id = ?");
$stmt->execute([$_SESSION['teacher_id']]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

$currentPage = 'learning.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Basic Learning | iLearn</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="icon" type="image/png" href="../assets/img/fav.jpg">
<style>
.learning-sidebar {
    width: 180px;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 20px 0;
    background: #f3f4f6;
}
.learning-sidebar button {
    padding: 12px;
    border: none;
    border-radius: 10   px;
    background: #4CAF50;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
}
    .learning-sidebar button.active,
    .learning-sidebar button:hover { background:#45a049; }

    .learning-section { display:none; }

    .learning-table {
        width:100%;
        border-collapse: collapse;
        margin-top:15px;
        background:#fff;
        box-shadow:0 2px 8px rgba(0,0,0,0.1);
    }
.learning-table th {
    background:#4a90e2;
    color:#fff;
    padding:12px;
    text-align:left;
}
.learning-table td {
    padding:12px;
    border-bottom:1px solid #ddd;
}

.image-placeholder {
    width:100%;
    height:300px;
    border:2px dashed #aaa;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#777;
    margin-top:15px;
}
.learning-reminder {
    background: #fff8d6;
    border-left: 6px solid #f4b400;
    padding: 16px;
    border-radius: 8px;
    color: #444;
    font-size: 15px;
    line-height: 1.6;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
/* ================= LEARNING SIDEBAR ================= */
.learning-sidebar {
    width: 200px; /* slightly wider for breathing room */
    display: flex;
    flex-direction: column;
    gap: 15px;
    padding: 25px 15px; /* more padding for smoother layout */
    background: #1f1f2e; /* dark sidebar to match dashboard */
    border-radius: 15px; /* smooth rounded edges */
    box-shadow: 0 8px 20px rgba(0,0,0,0.2); /* subtle shadow */
    transition: all 0.3s;
    margin: 10px; /* creates space from edges of dashboard */
}

.learning-sidebar button {
    padding: 12px 18px; /* more comfortable padding */
    border: none;
    border-radius: 12px; /* smoother, rounded buttons */
    background: #3b82f6;
    color: #fff;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s;
    margin: 0 5px; /* prevents button sticking to sidebar edges */
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
}

.learning-sidebar button.active,
.learning-sidebar button:hover {
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
    box-shadow: 0 6px 15px rgba(108,99,255,0.5);
    transform: translateY(-2px);
}

/* ================= RESPONSIVE ================= */
@media (max-width: 768px) {
    .learning-sidebar {
        width: 100%;
        flex-direction: row;
        overflow-x: auto;
        padding: 10px 5px;
        border-radius: 10px;
        margin: 0 0 15px 0;
    }
    .learning-sidebar button {
        flex: 1;
        font-size: 0.9rem;
        padding: 10px;
        margin: 0 3px;
    }
}

</style>
</head>

<body>
<div class="dashboard">

<aside class="sidebar">
<h2>📘B.E.S. iLearn</h2>
<ul>
<li><a href="dashboard.php">Dashboard</a></li>
<li><a href="subjects.php">Subjects</a></li>
<li><a href="assignments.php">Assignments</a></li>
<li><a href="materials.php">Activities</a></li>
<li><a href="modules.php">Modules</a></li>
<li><a href="quiz.php">Quizzes</a></li>
<li><a href="progress.php">Student Progress</a></li>
<li><a href="learning.php" class="active">Basic Learning</a></li>
<li class="logout"><a href="../auth/logout.php">Logout</a></li>
</ul>
</aside>

<div class="learning-sidebar">
<button class="active" onclick="showSection('reminder',this)">Learning Reminder</button>
<button onclick="showSection('down',this)">Down Syndrome</button>
<button onclick="showSection('autism',this)">Autism</button>
<button onclick="showSection('adhd',this)">ADHD</button>
<button onclick="showSection('deaf',this)">Deaf & Mute</button>
<button onclick="showSection('other',this)">Other Disabilities</button>
</div>

<main class="content">

<div id="reminder" class="learning-section" style="display:block;">
    <div class="learning-reminder">
        <strong>Reminder:</strong><br>
        Before exploring the learning strategies for each disability, it is highly recommended 
        to consult with a specialist (e.g., special education teacher, therapist, or healthcare professional). 
        Each child is unique, and professional guidance ensures the best support and safe learning approach.
    </div>
</div>


<!-- DOWN -->
<div id="down" class="learning-section">
    <h2>Down Syndrome – Learning Profile</h2>

    <table class="learning-table">
        <tr>
            <th>Area</th>
            <th>Profile</th>
            <th>Strategies</th>
        </tr>

        <tr>
            <td>Attention</td>
            <td>Short focus span, easily distracted</td>
            <td>Short tasks, clear steps, frequent breaks</td>
        </tr>

        <tr>
            <td>Memory</td>
            <td>Stronger visual memory than verbal</td>
            <td>Use pictures, flashcards, charts, routines</td>
        </tr>

        <tr>
            <td>Language</td>
            <td>Delayed speech and expressive language</td>
            <td>Simple instructions, gestures, visuals</td>
        </tr>

        <tr>
            <td>Processing Speed</td>
            <td>Slower understanding of new concepts</td>
            <td>Extra time, repetition, step-by-step teaching</td>
        </tr>

        <tr>
            <td>Social Skills</td>
            <td>Friendly, enjoys interaction</td>
            <td>Group work, peer support, positive praise</td>
        </tr>

        <tr>
            <td>Motor Skills</td>
            <td>Delayed fine and gross motor control</td>
            <td>Hands-on activities, movement breaks</td>
        </tr>

        <tr>
            <td>Learning Style</td>
            <td>Visual, hands-on, routine-based</td>
            <td>Games, real objects, structured routines</td>
        </tr>
    </table>
</div>

<!-- AUTISM -->
<div id="autism" class="learning-section">
    <h2>Autism – Learning Profile</h2>

    <table class="learning-table">
        <tr>
            <th>Area</th>
            <th>Profile</th>
            <th>Strategies</th>
        </tr>

        <tr>
            <td>Attention</td>
            <td>May focus too much on one interest or lose focus easily</td>
            <td>Clear routines, visual schedules, short structured tasks</td>
        </tr>

        <tr>
            <td>Memory</td>
            <td>Strong rote and visual memory</td>
            <td>Use visuals, repetition, visual organizers</td>
        </tr>

        <tr>
            <td>Language</td>
            <td>May have delayed, limited, or unusual speech</td>
            <td>Simple language, visuals, modeling, AAC if needed</td>
        </tr>

        <tr>
            <td>Processing</td>
            <td>Difficulty with abstract or social information</td>
            <td>Concrete examples, step-by-step instruction</td>
        </tr>

        <tr>
            <td>Social Skills</td>
            <td>Difficulty with eye contact, sharing, and social cues</td>
            <td>Social stories, role play, peer buddy system</td>
        </tr>

        <tr>
            <td>Sensory</td>
            <td>Sensitive to noise, light, touch, or movement</td>
            <td>Quiet space, sensory tools, calm environment</td>
        </tr>

        <tr>
            <td>Learning Style</td>
            <td>Visual, routine-based, interest-focused</td>
            <td>Visual supports, predictable routines, interest-based tasks</td>
        </tr>
    </table>
</div>

<!-- ADHD -->
<div id="adhd" class="learning-section">
    <h2>ADHD – Learning Profile</h2>

    <table class="learning-table">
        <tr>
            <th>Area</th>
            <th>Profile</th>
            <th>Strategies</th>
        </tr>

        <tr>
            <td>Attention</td>
            <td>Very short attention span, easily distracted</td>
            <td>Short lessons, movement breaks, clear goals</td>
        </tr>

        <tr>
            <td>Memory</td>
            <td>Difficulty remembering instructions</td>
            <td>Repeat directions, write reminders, visuals</td>
        </tr>

        <tr>
            <td>Language</td>
            <td>May interrupt, talk excessively, or miss details</td>
            <td>Clear rules, simple instructions, check understanding</td>
        </tr>

        <tr>
            <td>Processing</td>
            <td>Impulsive, rushes through tasks</td>
            <td>Step-by-step guidance, extra time, task checklists</td>
        </tr>

        <tr>
            <td>Behavior</td>
            <td>Restless, fidgety, easily frustrated</td>
            <td>Movement activities, positive reinforcement</td>
        </tr>

        <tr>
            <td>Organization</td>
            <td>Difficulty managing time and materials</td>
            <td>Visual schedules, color-coded folders, timers</td>
        </tr>

        <tr>
            <td>Learning Style</td>
            <td>Hands-on, active, needs variety</td>
            <td>Games, group tasks, interactive lessons</td>
        </tr>
    </table>
</div>

<div id="deaf" class="learning-section">
    <h2>Deaf and Mute – Learning Profile</h2>

    <table class="learning-table">
        <tr>
            <th>Area</th>
            <th>Profile</th>
            <th>Strategies</th>
        </tr>

        <tr>
            <td>Attention</td>
            <td>Highly visual; may miss spoken instructions</td>
            <td>Use gestures, visual cues, eye contact</td>
        </tr>

        <tr>
            <td>Memory</td>
            <td>Strong visual memory</td>
            <td>Pictures, charts, written instructions</td>
        </tr>

        <tr>
            <td>Language</td>
            <td>Uses sign language or alternative communication</td>
            <td>Sign language, written text, visual aids</td>
        </tr>

        <tr>
            <td>Processing</td>
            <td>Understands best through demonstration</td>
            <td>Step-by-step visuals, modeling, hands-on activities</td>
        </tr>

        <tr>
            <td>Social Skills</td>
            <td>May feel isolated from hearing peers</td>
            <td>Inclusive group work, peer signing buddies</td>
        </tr>

        <tr>
            <td>Classroom Access</td>
            <td>Needs clear view of teacher and board</td>
            <td>Front seating, good lighting, face-to-face instruction</td>
        </tr>

        <tr>
            <td>Learning Style</td>
            <td>Visual and hands-on learning</td>
            <td>Videos, demonstrations, real objects</td>
        </tr>
    </table>
</div>



<!-- OTHER -->
<div id="other" class="learning-section">
    <h2>Other Disabilities – Learning Profiles</h2>
    <p>Use multisensory learning, assistive tools, and individual pacing for these learners.</p>

    <!-- Dyslexia -->
    <h3>Dyslexia – Learning Profile</h3>
    <table class="learning-table">
        <tr><th>Area</th><th>Profile</th><th>Strategies</th></tr>
        <tr><td>Reading</td><td>Difficulty decoding words, slow reading</td><td>Use audiobooks, colored overlays, font adjustments</td></tr>
        <tr><td>Writing</td><td>Spelling errors, letter reversals</td><td>Use speech-to-text, typing, step-by-step writing guides</td></tr>
        <tr><td>Memory</td><td>Struggles with sequencing and short-term memory</td><td>Mnemonics, visual aids, repetition</td></tr>
        <tr><td>Language</td><td>Understanding complex instructions can be slow</td><td>Break instructions into steps, use visuals</td></tr>
        <tr><td>Learning Style</td><td>Strong auditory and visual learning preference</td><td>Use multi-sensory methods: read aloud, hands-on tasks</td></tr>
    </table>

    <!-- Cerebral Palsy -->
    <h3>Cerebral Palsy – Learning Profile</h3>
    <table class="learning-table">
        <tr><th>Area</th><th>Profile</th><th>Strategies</th></tr>
        <tr><td>Motor Skills</td><td>Limited fine and gross motor control</td><td>Adapted equipment, assistive devices, movement breaks</td></tr>
        <tr><td>Attention</td><td>May tire quickly due to physical effort</td><td>Short tasks, frequent breaks, comfortable seating</td></tr>
        <tr><td>Communication</td><td>Speech may be slurred or difficult</td><td>Speech therapy, AAC tools, clear visual instructions</td></tr>
        <tr><td>Learning Style</td><td>Visual and hands-on learning works best</td><td>Demonstrations, tactile activities, real objects</td></tr>
        <tr><td>Participation</td><td>May need help with mobility and classroom access</td><td>Accessible seating, ramps, classroom adaptations</td></tr>
    </table>

    <!-- Speech Delay -->
    <h3>Speech Delay – Learning Profile</h3>
    <table class="learning-table">
        <tr><th>Area</th><th>Profile</th><th>Strategies</th></tr>
        <tr><td>Language</td><td>Delayed or limited speech</td><td>Use gestures, visuals, sign support, simple instructions</td></tr>
        <tr><td>Social Skills</td><td>May struggle to communicate with peers</td><td>Pair with supportive peers, encourage turn-taking</td></tr>
        <tr><td>Memory</td><td>Good visual memory but weak verbal recall</td><td>Visual schedules, flashcards, repetition</td></tr>
        <tr><td>Learning Style</td><td>Visual and hands-on learning works best</td><td>Model actions, use real objects, videos</td></tr>
        <tr><td>Processing</td><td>Needs time to respond verbally</td><td>Allow extra time, encourage non-verbal responses</td></tr>
    </table>
</div>


</main>
</div>

<script>
function showSection(id,btn){
document.querySelectorAll('.learning-section').forEach(s=>s.style.display='none');
document.getElementById(id).style.display='block';
document.querySelectorAll('.learning-sidebar button').forEach(b=>b.classList.remove('active'));
btn.classList.add('active');
}
</script>
</body>
</html>
