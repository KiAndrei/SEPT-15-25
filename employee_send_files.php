<?php
require_once 'session_manager.php';
validateUserAccess('employee');
require_once 'config.php';
require_once 'audit_logger.php';
require_once 'action_logger_helper.php';

$employee_id = $_SESSION['user_id'];

// Fetch employee profile image, email, and name
$stmt = $conn->prepare("SELECT profile_image, email, name FROM user_form WHERE id=?");
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$res = $stmt->get_result();
$profile_image = '';
$employee_email = '';
$employee_name = '';
if ($res && $row = $res->fetch_assoc()) {
    $profile_image = $row['profile_image'];
    $employee_email = $row['email'];
    $employee_name = $row['name'];
}
if (!$profile_image || !file_exists($profile_image)) {
    $profile_image = 'images/default-avatar.jpg';
}

// Fetch files sent by clients
$stmt = $conn->prepare("
    SELECT 
        crf.*,
        u.name as client_name,
        u.email as client_email,
        COALESCE(crf.submitted_at, NOW()) as sent_date,
        crf.status as file_status
    FROM client_request_form crf
    JOIN user_form u ON crf.client_id = u.id
    ORDER BY COALESCE(crf.submitted_at, NOW()) DESC
");
$stmt->execute();
$res = $stmt->get_result();
$sent_files = [];
while ($row = $res->fetch_assoc()) {
    $sent_files[] = $row;
}

// Debug: Check total records in client_request_form
$debug_stmt = $conn->prepare("SELECT COUNT(*) as total FROM client_request_form");
$debug_stmt->execute();
$debug_res = $debug_stmt->get_result();
$total_records = $debug_res->fetch_assoc()['total'];

// Debug: Check records with document_type
$debug_stmt2 = $conn->prepare("SELECT COUNT(*) as total FROM client_request_form WHERE document_type IS NOT NULL");
$debug_stmt2->execute();
$debug_res2 = $debug_stmt2->get_result();
$document_records = $debug_res2->fetch_assoc()['total'];

// Debug: Check recent records (handle missing columns gracefully)
try {
    $debug_stmt3 = $conn->prepare("SELECT id, request_id, client_id, document_type, pdf_file_path, pdf_filename, submitted_at FROM client_request_form ORDER BY id DESC LIMIT 10");
    $debug_stmt3->execute();
    $debug_res3 = $debug_stmt3->get_result();
    $recent_records = [];
    while ($row = $debug_res3->fetch_assoc()) {
        $recent_records[] = $row;
    }
} catch (Exception $e) {
    // If columns don't exist yet, just get basic info
    $debug_stmt3 = $conn->prepare("SELECT id, request_id, client_id FROM client_request_form ORDER BY id DESC LIMIT 10");
    $debug_stmt3->execute();
    $debug_res3 = $debug_stmt3->get_result();
    $recent_records = [];
    while ($row = $debug_res3->fetch_assoc()) {
        $recent_records[] = $row;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Files - Opiña Law Office</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/dashboard.css?v=<?= time() ?>">
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <img src="images/logo.png" alt="Logo">
            <h2>Opiña Law Office</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="employee_dashboard.php"><i class="fas fa-home"></i><span>Dashboard</span></a></li>
            <li><a href="employee_documents.php"><i class="fas fa-file-alt"></i><span>Document Storage</span></a></li>
            <li class="has-submenu">
                <a href="#" class="submenu-toggle"><i class="fas fa-file-alt"></i><span>Document Generations</span><i class="fas fa-chevron-down submenu-arrow"></i></a>
                <ul class="submenu">
                    <li><a href="employee_document_generation.php"><i class="fas fa-file-plus"></i><span>Generate Documents</span></a></li>
                    <li><a href="employee_send_files.php" class="active"><i class="fas fa-paper-plane"></i><span>Send Files</span></a></li>
                </ul>
            </li>
            <li><a href="employee_schedule.php"><i class="fas fa-calendar-alt"></i><span>Schedule</span></a></li>
            <li><a href="employee_clients.php"><i class="fas fa-users"></i><span>Client Management</span></a></li>
            <li><a href="employee_request_management.php"><i class="fas fa-clipboard-check"></i><span>Request Review</span></a></li>
            <li><a href="employee_messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a></li>
            <li><a href="employee_audit.php"><i class="fas fa-history"></i><span>Audit Trail</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>Document Generation</h1>
                <p>View and download PDF documents sent by clients</p>
            </div>
            <div class="user-info">
                <div class="profile-dropdown" style="display: flex; align-items: center; gap: 12px;">
                    <img src="<?= htmlspecialchars($profile_image) ?>" alt="Employee" style="object-fit:cover;width:42px;height:42px;border-radius:50%;border:2px solid #1976d2;cursor:pointer;" onclick="toggleProfileDropdown()">
                    <div class="user-details">
                        <h3><?php echo $_SESSION['employee_name']; ?></h3>
                        <p>Employee</p>
                    </div>
                    
                    <!-- Profile Dropdown Menu -->
                    <div class="profile-dropdown-content" id="profileDropdown">
                        <a href="#" onclick="editProfile()">
                            <i class="fas fa-user-edit"></i>
                            Edit Profile
                        </a>
                        <a href="logout.php" class="logout">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-container">
            <!-- Action Bar -->
            <div class="action-bar">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" placeholder="Search files..." onkeyup="filterFiles()">
                </div>
                <div class="filter-options">
                    <select id="typeFilter" onchange="filterFiles()">
                        <option value="">All Document Types</option>
                        <option value="affidavitLoss">Affidavit of Loss</option>
                        <option value="soloParent">Solo Parent</option>
                        <option value="pwdLoss">PWD ID Loss</option>
                        <option value="boticabLoss">Boticab Loss</option>
                    </select>
                </div>
            </div>

            <!-- Files Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-pdf"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= count($sent_files) ?></h3>
                        <p>Total Documents</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= count(array_filter($sent_files, function($file) { return $file['document_type'] === 'affidavitLoss'; })) ?></h3>
                        <p>Affidavit of Loss</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= count(array_filter($sent_files, function($file) { return $file['document_type'] === 'soloParent'; })) ?></h3>
                        <p>Solo Parent Documents</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-wheelchair"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= count(array_filter($sent_files, function($file) { return $file['document_type'] === 'pwdLoss' || $file['document_type'] === 'boticabLoss'; })) ?></h3>
                        <p>ID Loss Documents</p>
                    </div>
                </div>
            </div>

            <!-- Files List -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-file-pdf"></i> Documents Sent by Clients</h2>
                </div>
                
                <?php if (empty($sent_files)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Files Received</h3>
                        <p>Files sent by clients will appear here.</p>
                        <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-size: 0.9rem;">
                            <strong>Debug Info:</strong><br>
                            Total records in client_request_form: <?= $total_records ?><br>
                            Records with document_type: <?= $document_records ?><br>
                            Records displayed: <?= count($sent_files) ?><br><br>
                            <strong>Recent Records:</strong><br>
                            <?php foreach ($recent_records as $record): ?>
                                ID: <?= $record['id'] ?>, Request: <?= $record['request_id'] ?>, Type: <?= isset($record['document_type']) ? $record['document_type'] : 'N/A' ?>, PDF: <?= isset($record['pdf_filename']) ? $record['pdf_filename'] : 'N/A' ?>, Submitted: <?= isset($record['submitted_at']) ? $record['submitted_at'] : 'N/A' ?><br>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="files-grid" id="filesGrid">
                        <?php foreach ($sent_files as $file): ?>
                            <div class="file-card" data-status="<?= strtolower($file['file_status']) ?>">
                                <div class="file-header">
                                    <div class="file-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="file-status status-<?= strtolower($file['file_status']) ?>">
                                        <i class="fas fa-<?= $file['file_status'] === 'Pending' ? 'clock' : ($file['file_status'] === 'Approved' ? 'check' : 'times') ?>"></i>
                                        <?= $file['file_status'] ?>
                                    </div>
                                </div>
                                
                                <div class="file-content">
                                    <h3><?= htmlspecialchars($file['client_name']) ?></h3>
                                    <p class="client-email">
                                        <i class="fas fa-envelope"></i>
                                        <?= htmlspecialchars($file['client_email']) ?>
                                    </p>
                                    <p class="request-id">
                                        <i class="fas fa-hashtag"></i>
                                        Request ID: <?= htmlspecialchars($file['request_id']) ?>
                                    </p>
                                    
                                    <?php if (isset($file['document_type']) && !empty($file['document_type'])): ?>
                                        <p class="document-type">
                                            <i class="fas fa-file-alt"></i>
                                            Document Type: <?= htmlspecialchars($file['document_type']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <?php if (isset($file['pdf_filename']) && !empty($file['pdf_filename'])): ?>
                                        <p class="pdf-file">
                                            <i class="fas fa-file-pdf"></i>
                                            PDF File: <?= htmlspecialchars($file['pdf_filename']) ?>
                                        </p>
                                    <?php endif; ?>
                                    
                                    <div class="file-details">
                                        <div class="detail-item">
                                            <span class="label">Sent:</span>
                                            <span class="value"><?= date('M d, Y H:i', strtotime($file['sent_date'])) ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="label">Address:</span>
                                            <span class="value"><?= htmlspecialchars($file['address']) ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <span class="label">Gender:</span>
                                            <span class="value"><?= htmlspecialchars($file['gender']) ?></span>
                                        </div>
                                        <?php if ($file['privacy_consent']): ?>
                                            <div class="detail-item">
                                                <span class="label">Privacy:</span>
                                                <span class="value privacy-consent">
                                                    <i class="fas fa-check-circle"></i>
                                                    Agreed to Data Privacy Act
                                                </span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="file-actions">
                                    <?php if (isset($file['pdf_file_path']) && !empty($file['pdf_file_path']) && file_exists($file['pdf_file_path'])): ?>
                                        <a href="<?= htmlspecialchars($file['pdf_file_path']) ?>" target="_blank" class="btn btn-info" title="View PDF in new tab">
                                            <i class="fas fa-file-pdf"></i>
                                            View PDF
                                        </a>
                                        <a href="<?= htmlspecialchars($file['pdf_file_path']) ?>" download="<?= isset($file['pdf_filename']) ? htmlspecialchars($file['pdf_filename']) : 'document.pdf' ?>" class="btn btn-success" title="Download PDF file">
                                            <i class="fas fa-download"></i>
                                            Download PDF
                                        </a>
                                    <?php elseif (isset($file['pdf_file_path']) && !empty($file['pdf_file_path'])): ?>
                                        <div class="btn btn-warning" title="PDF file not found">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            PDF Not Found
                                        </div>
                                    <?php else: ?>
                                        <div class="btn btn-secondary" title="No PDF file available">
                                            <i class="fas fa-file-alt"></i>
                                            No PDF Available
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>


    <style>
        .content-container {
            padding: 30px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            color: #666;
            z-index: 1;
        }

        .search-box input {
            padding: 12px 15px 12px 45px;
            border: 2px solid rgba(93, 14, 38, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            width: 300px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(93, 14, 38, 0.1);
        }

        .filter-options {
            display: flex;
            gap: 15px;
        }

        .filter-options select {
            padding: 8px 12px;
            border: 2px solid rgba(93, 14, 38, 0.1);
            border-radius: 8px;
            font-size: 0.9rem;
            background: white;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(93, 14, 38, 0.1);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(93, 14, 38, 0.15);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-content h3 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-content p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 0.9rem;
        }

        .section {
            margin-bottom: 40px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(93, 14, 38, 0.1);
        }

        .section-header h2 {
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .files-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .file-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(93, 14, 38, 0.1);
            transition: all 0.3s ease;
        }

        .file-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(93, 14, 38, 0.15);
        }

        .file-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .file-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .file-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-status.status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .file-status.status-approved {
            background: #d4edda;
            color: #155724;
        }

        .file-status.status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        .file-content h3 {
            color: var(--primary-color);
            margin: 0 0 10px 0;
            font-size: 1.2rem;
        }

        .client-email, .request-id, .document-type, .pdf-file {
            color: #666;
            margin: 0 0 10px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .document-type {
            color: var(--primary-color) !important;
            font-weight: 600;
        }

        .pdf-file {
            color: #dc3545 !important;
            font-weight: 600;
        }

        .file-details {
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.85rem;
        }

        .detail-item .label {
            color: #666;
            font-weight: 500;
        }

        .detail-item .value {
            color: #333;
            font-weight: 600;
        }

        .privacy-consent {
            color: #28a745 !important;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            font-size: 0.85rem;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%);
            color: #212529;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 20px;
            border: 2px dashed rgba(93, 14, 38, 0.2);
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #666;
            margin: 0 0 10px 0;
        }

        .empty-state p {
            color: #999;
            margin: 0;
        }

        @media (max-width: 768px) {
            .action-bar {
                flex-direction: column;
                gap: 15px;
            }

            .search-box input {
                width: 100%;
            }

            .files-grid {
                grid-template-columns: 1fr;
            }

            .file-actions {
                justify-content: center;
            }
        }

        /* Expandable Submenu Styles */
        .has-submenu {
            position: relative;
        }

        .submenu-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-decoration: none;
            color: inherit;
            padding: 12px 20px;
            cursor: pointer;
        }

        .submenu-arrow {
            font-size: 0.8rem;
        }

        .has-submenu.active .submenu-arrow {
            transform: rotate(180deg);
        }

        .submenu {
            max-height: 0;
            overflow: hidden;
            background: rgba(0, 0, 0, 0.2);
        }

        .has-submenu.active .submenu {
            max-height: 200px;
        }

        .submenu li {
            list-style: none;
        }

        .submenu a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px 10px 40px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 0.9rem;
        }

        .submenu i {
            font-size: 0.9rem;
            width: 16px;
            text-align: center;
        }
    </style>

    <script>
        // Profile Dropdown Functions
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }
        
        function editProfile() {
            alert('Profile editing functionality will be implemented.');
        }

        // File Management Functions
        function viewFileDetails(fileId) {
            // Redirect to request management page to view full details
            window.location.href = 'employee_request_management.php';
        }

        function approveFile(fileId) {
            if (confirm('Are you sure you want to approve this file?')) {
                // Update file status to approved
                fetch('employee_send_files.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=approve_file&file_id=' + fileId
                })
                .then(response => response.text())
                .then(result => {
                    if (result.includes('success')) {
                        alert('File approved successfully!');
                        location.reload();
                    } else {
                        alert('Error approving file. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error approving file. Please try again.');
                });
            }
        }

        function rejectFile(fileId) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason && reason.trim() !== '') {
                // Update file status to rejected
                fetch('employee_send_files.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=reject_file&file_id=' + fileId + '&reason=' + encodeURIComponent(reason)
                })
                .then(response => response.text())
                .then(result => {
                    if (result.includes('success')) {
                        alert('File rejected successfully!');
                        location.reload();
                    } else {
                        alert('Error rejecting file. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error rejecting file. Please try again.');
                });
            }
        }

        // Search and Filter Functions
        function filterFiles() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const fileCards = document.querySelectorAll('.file-card');

            fileCards.forEach(card => {
                const clientName = card.querySelector('h3').textContent.toLowerCase();
                const clientEmail = card.querySelector('.client-email').textContent.toLowerCase();
                const requestId = card.querySelector('.request-id').textContent.toLowerCase();
                const status = card.getAttribute('data-status');

                const matchesSearch = clientName.includes(searchTerm) || 
                                    clientEmail.includes(searchTerm) || 
                                    requestId.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesStatus) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('img') && !event.target.closest('.profile-dropdown')) {
                const dropdowns = document.getElementsByClassName('profile-dropdown-content');
                for (let dropdown of dropdowns) {
                    if (dropdown.classList.contains('show')) {
                        dropdown.classList.remove('show');
                    }
                }
            }
        }

        // Expandable Submenu Functionality
        document.addEventListener('DOMContentLoaded', function() {
            const submenuToggle = document.querySelector('.submenu-toggle');
            const hasSubmenu = document.querySelector('.has-submenu');
            
            if (submenuToggle && hasSubmenu) {
                submenuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    hasSubmenu.classList.toggle('active');
                });
            }
        });
    </script>
</body>
</html>
