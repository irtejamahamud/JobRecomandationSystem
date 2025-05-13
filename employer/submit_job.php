<?php
session_start();
include('../includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $recruiter_id = $_SESSION['user_id'];

    // Job Main Info
    $job_title     = $_POST['job_title'];
    $tags          = $_POST['tags'];
    $job_role      = $_POST['job_role'];
    $min_salary    = $_POST['min_salary'];
    $max_salary    = $_POST['max_salary'];
    $currency      = $_POST['currency'];
    $vacancies     = $_POST['vacancies'];
    $job_level     = $_POST['job_level'];
    $job_type      = $_POST['job_type'];
    $category      = $_POST['category']; // ✅ NEW
    $country       = $_POST['country'];
    $city          = $_POST['city'];
    $skills        = $_POST['skills'];
    $description   = $_POST['job_description'];
    $start_date    = $_POST['start_date'];
    $expire_date   = $_POST['expire_date'];

    // Job Details Extra Info
    $experience_required  = $_POST['experience_required'];
    $degree_required      = $_POST['degree_required'];
    $responsibilities     = $_POST['responsibilities'];
    $professional_skills  = $_POST['professional_skills'];
    $map_embed_url        = $_POST['map_embed_url'];

    try {
        $conn->beginTransaction();

        // Insert into jobs table
        $stmt = $conn->prepare("
            INSERT INTO jobs (
                recruiter_id, job_title, tags, job_role, min_salary, max_salary, currency,
                vacancies, job_level, job_type, category, country, city, job_description, skills, start_date, expire_date
            ) VALUES (
                :recruiter_id, :job_title, :tags, :job_role, :min_salary, :max_salary, :currency,
                :vacancies, :job_level, :job_type, :category, :country, :city, :job_description, :skills, :start_date, :expire_date
            )
        ");
        $stmt->execute([
            ':recruiter_id'     => $recruiter_id,
            ':job_title'        => $job_title,
            ':tags'             => $tags,
            ':job_role'         => $job_role,
            ':min_salary'       => $min_salary,
            ':max_salary'       => $max_salary,
            ':currency'         => $currency,
            ':vacancies'        => $vacancies,
            ':job_level'        => $job_level,
            ':job_type'         => $job_type,
            ':category'         => $category, // ✅ NEW
            ':country'          => $country,
            ':city'             => $city,
            ':job_description'  => $description,
            ':skills'           => $skills,
            ':start_date'       => $start_date,
            ':expire_date'      => $expire_date
        ]);

        $job_id = $conn->lastInsertId(); // get job_id from previous insert

        // Insert into job_details table
        $stmt2 = $conn->prepare("
            INSERT INTO job_details (
                job_id, experience_required, degree_required, responsibilities,
                professional_skills, map_embed_url
            ) VALUES (
                :job_id, :experience_required, :degree_required, :responsibilities,
                :professional_skills, :map_embed_url
            )
        ");
        $stmt2->execute([
            ':job_id'             => $job_id,
            ':experience_required'=> $experience_required,
            ':degree_required'    => $degree_required,
            ':responsibilities'   => $responsibilities,
            ':professional_skills'=> $professional_skills,
            ':map_embed_url'      => $map_embed_url
        ]);

        $conn->commit();
        header("Location: post_job.php?success=1");
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        die("Error saving job: " . $e->getMessage());
    }

} else {
    // Unauthorized or direct access
    header("Location: ../login.php");
    exit;
}
?>
