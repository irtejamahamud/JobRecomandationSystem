<?php
session_start();
include('../includes/db.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'job_seeker') {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$job_seeker_id = $_SESSION['user_id'];

$skillsStmt = $conn->prepare("
    SELECT sm.name 
    FROM job_seeker_skills js 
    JOIN skill_master sm ON js.skill_id = sm.id 
    WHERE js.job_seeker_id = ?
");
$skillsStmt->execute([$job_seeker_id]);
$skills = $skillsStmt->fetchAll(PDO::FETCH_COLUMN);

if (empty($skills)) {
    echo json_encode(['error' => 'No skills found']);
    exit();
}

$jobsStmt = $conn->prepare("
    SELECT job_id, job_title, tags, skills 
    FROM jobs 
    WHERE expire_date >= NOW()
");
$jobsStmt->execute();
$jobs = $jobsStmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($jobs)) {
    echo json_encode(['error' => 'No jobs found']);
    exit();
}

$seeker_profile = implode(', ', $skills);

$jobs_for_python = array_map(function($job) {
    return [
        'job_id' => $job['job_id'],
        'text' => $job['job_title'] . ' ' . $job['tags'] . ' ' . $job['skills']
    ];
}, $jobs);

$input = [
    'seeker_profile' => $seeker_profile,
    'jobs' => $jobs_for_python
];

$input_json = json_encode($input);

file_put_contents("debug_input.json", $input_json);

$descriptorspec = [
    0 => ["pipe", "r"],
    1 => ["pipe", "w"],
    2 => ["pipe", "w"]
];

$process = proc_open("python ../ai/recommend.py", $descriptorspec, $pipes);

if (is_resource($process)) {
    fwrite($pipes[0], $input_json);
    fclose($pipes[0]);

    $output = stream_get_contents($pipes[1]);
    fclose($pipes[1]);

    $error_output = stream_get_contents($pipes[2]);
    fclose($pipes[2]);

    $return_code = proc_close($process);

    file_put_contents("debug_output.txt", $output);
    file_put_contents("debug_error.txt", $error_output);

    if ($return_code !== 0 || empty($output)) {
        echo json_encode([
            "error" => "Python script error",
            "details" => $error_output,
            "code" => $return_code
        ]);
        exit();
    }

    echo $output;
} else {
    echo json_encode(['error' => 'Failed to start Python process']);
    exit();
}
?>
