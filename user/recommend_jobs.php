<?php
session_start();
include('../includes/db.php');

// Make sure user is logged in and is job_seeker
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'job_seeker') {
    die(json_encode(["error" => "Unauthorized Access"]));
}

$job_seeker_id = $_SESSION['user_id'];

// 1. Fetch job seeker skills (profile text)
$stmt = $conn->prepare("
    SELECT sm.skill_name
    FROM job_seeker_skills jss
    JOIN skill_master sm ON jss.skill_id = sm.skill_id
    WHERE jss.job_seeker_id = ?
");
$stmt->execute([$job_seeker_id]);
$skills = $stmt->fetchAll(PDO::FETCH_COLUMN);
$seeker_profile_text = implode(' ', $skills);

// 2. Fetch all jobs
$stmt2 = $conn->prepare("
    SELECT job_id, job_title, tags, description
    FROM jobs
    ORDER BY posted_on DESC
    LIMIT 100
");
$stmt2->execute();
$jobs = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Prepare jobs data for matching
$jobs_data = [];
foreach ($jobs as $job) {
    $job_text = $job['job_title'] . ' ' . $job['tags'] . ' ' . $job['description'];
    $jobs_data[] = [
        'job_id' => $job['job_id'],
        'text' => $job_text
    ];
}

// 3. Prepare full data array to send to Python
$recommendation_input = [
    'seeker_profile' => $seeker_profile_text,
    'jobs' => $jobs_data
];

// 4. Call Python script
$input_json = json_encode($recommendation_input);

// Secure passing using escapeshellarg
$command = "python3 ../python_scripts/recommend_jobs.py " . escapeshellarg($input_json);
$output = shell_exec($command);

// 5. Return back JSON response
if ($output) {
    echo $output; // Python script should return JSON
} else {
    echo json_encode(["error" => "No recommendations found."]);
}
?>
