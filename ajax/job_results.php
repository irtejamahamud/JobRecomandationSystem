<?php
include('../includes/db.php');

$where = [];
$params = [];

$page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$salaryMin = isset($_GET['salary_min']) ? (int)$_GET['salary_min'] : 0;
$salaryMax = isset($_GET['salary_max']) ? (int)$_GET['salary_max'] : 99999;
$where[] = "jobs.min_salary >= ? AND jobs.max_salary <= ?";
$params[] = $salaryMin;
$params[] = $salaryMax;

if (!empty($_GET['search'])) {
    $where[] = "(jobs.job_title LIKE ? OR company_profiles.company_name LIKE ?)";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}

if (!empty($_GET['city'])) {
    $where[] = "jobs.city = ?";
    $params[] = $_GET['city'];
}

if (!empty($_GET['category'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['category']), '?'));
    $where[] = "jobs.category IN ($placeholders)";
    $params = array_merge($params, $_GET['category']);
}

if (!empty($_GET['job_type'])) {
    $placeholders = implode(',', array_fill(0, count($_GET['job_type']), '?'));
    $where[] = "jobs.job_type IN ($placeholders)";
    $params = array_merge($params, $_GET['job_type']);
}

if (!empty($_GET['experience_level'])) {
    $levels = $_GET['experience_level'];
    $sub = [];
    foreach ($levels as $lvl) {
        $sub[] = "job_details.experience_required LIKE ?";
        $params[] = "%$lvl%";
    }
    $where[] = '(' . implode(' OR ', $sub) . ')';
}

if (!empty($_GET['date_posted'])) {
    $ranges = [
        'Last Hour' => '1 HOUR',
        'Last 24 Hours' => '1 DAY',
        'Last 7 Days' => '7 DAY',
        'Last 30 Days' => '30 DAY',
    ];
    $conds = [];
    foreach ($_GET['date_posted'] as $range) {
        if (isset($ranges[$range])) {
            $conds[] = "jobs.posted_on >= NOW() - INTERVAL {$ranges[$range]}";
        }
    }
    if (!empty($conds)) {
        $where[] = '(' . implode(' OR ', $conds) . ')';
    }
}

$whereClause = !empty($where) ? "WHERE " . implode(' AND ', $where) : "";

// Count for pagination
$countSql = "
    SELECT COUNT(*) 
    FROM jobs
    JOIN recruiters ON jobs.recruiter_id = recruiters.user_id
    LEFT JOIN company_profiles ON company_profiles.recruiter_id = jobs.recruiter_id
    LEFT JOIN job_details ON job_details.job_id = jobs.job_id
    $whereClause
";
$stmtCount = $conn->prepare($countSql);
$stmtCount->execute($params);
$totalJobs = $stmtCount->fetchColumn();
$totalPages = ceil($totalJobs / $limit);

// Fetch jobs
$sql = "
    SELECT jobs.*, company_profiles.logo, company_profiles.company_name
    FROM jobs
    JOIN recruiters ON jobs.recruiter_id = recruiters.user_id
    LEFT JOIN company_profiles ON company_profiles.recruiter_id = jobs.recruiter_id
    LEFT JOIN job_details ON job_details.job_id = jobs.job_id
    $whereClause
    ORDER BY jobs.posted_on DESC
    LIMIT $limit OFFSET $offset
";
$stmt = $conn->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="result-meta">
  <p>Showing <?= $totalJobs ?> result<?= $totalJobs != 1 ? 's' : '' ?></p>
</div>

<div class="login-warning">
  <p><strong>You need to <a href="../login.php">Login</a> to see details and apply for job.</strong></p>
</div>

<?php foreach ($jobs as $job): ?>
  <div class="job-card">
    <div class="card-left">
      <span class="time-badge"><?= date('M d', strtotime($job['posted_on'])) ?></span>
      <div class="job-logo">
  <?php
    $logo = $job['logo'] ?? '';
    $relativeLogoPath = 'uploads/company/' . $logo;
    $absoluteLogoPath = realpath(__DIR__ . '/../uploads/company/' . $logo);
    $defaultLogo = 'assets/img/logo-default.jpg';

    $logoSrc = (!empty($logo) && $absoluteLogoPath && file_exists($absoluteLogoPath)) ? $relativeLogoPath : $defaultLogo;
  ?>
  <img src="<?= htmlspecialchars($logoSrc) ?>" alt="Company Logo" style="max-width: 100px; height: auto;">
</div>


    </div>
    <div class="card-middle">
      <h3><?= htmlspecialchars($job['job_title']) ?></h3>
      <p class="company"><?= htmlspecialchars($job['company_name'] ?? 'Company Name') ?></p>
      <div class="job-meta">
        <span><i class="fas fa-briefcase"></i> <?= htmlspecialchars($job['tags']) ?></span>
        <span><i class="fas fa-wallet"></i> TK<?= number_format($job['min_salary']) ?>–<?= number_format($job['max_salary']) ?></span>
        <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job['city']) ?>, <?= htmlspecialchars($job['country']) ?></span>
      </div>
    </div>
  </div>
<?php endforeach; ?>

<!-- Pagination -->
<div class="pagination">
  <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="#" class="page <?= $i == $page ? 'active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></a>
  <?php endfor; ?>
</div>
