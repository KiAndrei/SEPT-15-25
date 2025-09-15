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

// Handle request approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'review_request') {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['review_action']; // 'approve' or 'reject'
        $review_notes = trim($_POST['review_notes']);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Update request status
            $status = ($action === 'approve') ? 'Approved' : 'Rejected';
            $stmt = $conn->prepare("UPDATE client_request_form SET status = ?, reviewed_at = NOW(), reviewed_by = ?, review_notes = ? WHERE id = ?");
            $stmt->bind_param("sisi", $status, $employee_id, $review_notes, $request_id);
            $stmt->execute();
            
            // Insert review record
            $stmt = $conn->prepare("INSERT INTO employee_request_reviews (request_form_id, employee_id, action, review_notes) VALUES (?, ?, ?, ?)");
            $review_action = ($action === 'approve') ? 'Approved' : 'Rejected';
            $stmt->bind_param("iiss", $request_id, $employee_id, $review_action, $review_notes);
            $stmt->execute();
            
            // If approved, create conversation
            if ($action === 'approve') {
                $stmt = $conn->prepare("INSERT INTO client_employee_conversations (request_form_id, client_id, employee_id) VALUES (?, (SELECT client_id FROM client_request_form WHERE id = ?), ?)");
                $stmt->bind_param("iii", $request_id, $request_id, $employee_id);
                $stmt->execute();
            }
            
            $conn->commit();
            
            // Log to audit trail
            global $auditLogger;
            $auditLogger->logAction(
                $employee_id,
                $employee_name,
                'employee',
                'Request Review',
                'Communication',
                "Request ID: $request_id - Action: $review_action",
                'success',
                'medium'
            );
            
            $success_message = "Request " . strtolower($review_action) . " successfully!";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Failed to process request. Please try again.";
        }
    }
    
    if ($_POST['action'] === 'assign_attorney') {
        $conversation_id = intval($_POST['conversation_id']);
        $client_id = intval($_POST['client_id']);
        $attorney_id = intval($_POST['attorney_id']);
        $assignment_reason = trim($_POST['assignment_reason']);
        
        // Start transaction
        $conn->begin_transaction();
        
        try {
            // Create attorney assignment
            $stmt = $conn->prepare("INSERT INTO client_attorney_assignments (conversation_id, client_id, employee_id, attorney_id, assignment_reason) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiiii", $conversation_id, $client_id, $employee_id, $attorney_id, $assignment_reason);
            $stmt->execute();
            
            $assignment_id = $conn->insert_id;
            
            // Create attorney conversation
            $stmt = $conn->prepare("INSERT INTO client_attorney_conversations (assignment_id, client_id, attorney_id) VALUES (?, ?, ?)");
            $stmt->bind_param("iii", $assignment_id, $client_id, $attorney_id);
            $stmt->execute();
            
            // Update employee conversation status
            $stmt = $conn->prepare("UPDATE client_employee_conversations SET conversation_status = 'Completed' WHERE id = ?");
            $stmt->bind_param("i", $conversation_id);
            $stmt->execute();
            
            $conn->commit();
            
            // Log to audit trail
            global $auditLogger;
            $auditLogger->logAction(
                $employee_id,
                $employee_name,
                'employee',
                'Attorney Assignment',
                'Communication',
                "Assigned attorney ID: $attorney_id to client ID: $client_id",
                'success',
                'medium'
            );
            
            $success_message = "Attorney assigned successfully!";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Failed to assign attorney. Please try again.";
        }
    }
}

// Fetch pending requests
$stmt = $conn->prepare("
    SELECT crf.*, u.name as client_name, u.email as client_email
    FROM client_request_form crf
    JOIN user_form u ON crf.client_id = u.id
    WHERE crf.status = 'Pending'
    ORDER BY crf.submitted_at ASC
");
$stmt->execute();
$res = $stmt->get_result();
$pending_requests = [];
while ($row = $res->fetch_assoc()) {
    $pending_requests[] = $row;
}

// Fetch approved requests with conversations and attorney assignments
$stmt = $conn->prepare("
    SELECT crf.*, u.name as client_name, u.email as client_email, 
           cec.id as conversation_id, cec.conversation_status, cec.concern_identified,
           caa.id as assignment_id, caa.status as assignment_status, caa.attorney_id,
           att.name as attorney_name
    FROM client_request_form crf
    JOIN user_form u ON crf.client_id = u.id
    LEFT JOIN client_employee_conversations cec ON crf.id = cec.request_form_id
    LEFT JOIN client_attorney_assignments caa ON cec.id = caa.conversation_id
    LEFT JOIN user_form att ON caa.attorney_id = att.id
    WHERE crf.status = 'Approved'
    ORDER BY crf.submitted_at DESC
");
$stmt->execute();
$res = $stmt->get_result();
$approved_requests = [];
while ($row = $res->fetch_assoc()) {
    $approved_requests[] = $row;
}

// Fetch available attorneys and admins for assignment
$stmt = $conn->prepare("SELECT id, name, user_type FROM user_form WHERE user_type IN ('attorney', 'admin') ORDER BY user_type, name");
$stmt->execute();
$res = $stmt->get_result();
$attorneys = [];
while ($row = $res->fetch_assoc()) {
    $attorneys[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Management - Opiña Law Office</title>
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
                    <li><a href="employee_send_files.php"><i class="fas fa-paper-plane"></i><span>Send Files</span></a></li>
                </ul>
            </li>
            <li><a href="employee_schedule.php"><i class="fas fa-calendar-alt"></i><span>Schedule</span></a></li>
            <li><a href="employee_clients.php"><i class="fas fa-users"></i><span>Client Management</span></a></li>
            <li><a href="employee_request_management.php" class="active"><i class="fas fa-clipboard-check"></i><span>Request Review</span></a></li>
            <li><a href="employee_messages.php"><i class="fas fa-envelope"></i><span>Messages</span></a></li>
            <li><a href="employee_audit.php"><i class="fas fa-history"></i><span>Audit Trail</span></a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <h1>Request Management</h1>
                <p>Review and manage client messaging requests</p>
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
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success_message) ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error_message) ?>
                </div>
            <?php endif; ?>

            <!-- Pending Requests Section -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-clock"></i> Pending Requests</h2>
                    <span class="badge"><?= count($pending_requests) ?></span>
                </div>
                
                <?php if (empty($pending_requests)): ?>
                    <div class="empty-state">
                        <i class="fas fa-inbox"></i>
                        <h3>No Pending Requests</h3>
                        <p>All client requests have been reviewed.</p>
                    </div>
                <?php else: ?>
                    <div class="requests-grid">
                        <?php foreach ($pending_requests as $request): ?>
                            <div class="request-card pending">
                                <div class="request-header">
                                    <div class="request-info">
                                        <h3><?= htmlspecialchars($request['client_name']) ?></h3>
                                        <p class="request-id">Request ID: <?= htmlspecialchars($request['request_id']) ?></p>
                                        <p class="client-email">
                                            <i class="fas fa-envelope"></i>
                                            <?= htmlspecialchars($request['client_email']) ?>
                                        </p>
                                        <p class="submitted-date">
                                            <i class="fas fa-calendar"></i>
                                            Submitted: <?= date('M d, Y H:i', strtotime($request['submitted_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="request-status pending">
                                        <i class="fas fa-clock"></i>
                                        Pending
                                    </div>
                                </div>
                                
                                <div class="request-actions">
                                    <button class="btn btn-info" onclick="viewRequestDetails(<?= $request['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                    <button class="btn btn-success" onclick="reviewRequest(<?= $request['id'] ?>, 'approve')">
                                        <i class="fas fa-check"></i>
                                        Approve
                                    </button>
                                    <button class="btn btn-danger" onclick="reviewRequest(<?= $request['id'] ?>, 'reject')">
                                        <i class="fas fa-times"></i>
                                        Reject
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Approved Requests Section -->
            <div class="section">
                <div class="section-header">
                    <h2><i class="fas fa-check-circle"></i> Approved Requests</h2>
                    <span class="badge"><?= count($approved_requests) ?></span>
                </div>
                
                <?php if (empty($approved_requests)): ?>
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <h3>No Approved Requests</h3>
                        <p>Approved requests will appear here.</p>
                    </div>
                <?php else: ?>
                    <div class="requests-grid">
                        <?php foreach ($approved_requests as $request): ?>
                            <div class="request-card approved">
                                <div class="request-header">
                                    <div class="request-info">
                                        <h3><?= htmlspecialchars($request['client_name']) ?></h3>
                                        <p class="request-id">Request ID: <?= htmlspecialchars($request['request_id']) ?></p>
                                        <p class="client-email">
                                            <i class="fas fa-envelope"></i>
                                            <?= htmlspecialchars($request['client_email']) ?>
                                        </p>
                                        <p class="submitted-date">
                                            <i class="fas fa-calendar"></i>
                                            Approved: <?= date('M d, Y H:i', strtotime($request['reviewed_at'])) ?>
                                        </p>
                                    </div>
                                    <div class="request-status approved">
                                        <i class="fas fa-check-circle"></i>
                                        Approved
                                    </div>
                                </div>
                                
                                <div class="conversation-status">
                                    <?php if ($request['conversation_id']): ?>
                                        <div class="status-info">
                                            <i class="fas fa-comments"></i>
                                            <span>Conversation Status: <?= htmlspecialchars($request['conversation_status']) ?></span>
                                        </div>
                                        <?php if ($request['concern_identified']): ?>
                                            <div class="status-info">
                                                <i class="fas fa-lightbulb"></i>
                                                <span>Concern Identified: Yes</span>
                                            </div>
                                            <?php if ($request['assignment_id']): ?>
                                                <div class="status-info">
                                                    <i class="fas fa-user-tie"></i>
                                                    <span>Attorney Assigned: <?= htmlspecialchars($request['attorney_name']) ?></span>
                                                </div>
                                                <div class="status-info">
                                                    <i class="fas fa-info-circle"></i>
                                                    <span>Assignment Status: <?= htmlspecialchars($request['assignment_status']) ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="status-info">
                                                <i class="fas fa-search"></i>
                                                <span>Concern Identified: No</span>
                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="status-info">
                                            <i class="fas fa-exclamation-triangle"></i>
                                            <span>No conversation started</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="request-actions">
                                    <?php if ($request['conversation_id']): ?>
                                        <a href="employee_messages.php?conversation_id=<?= $request['conversation_id'] ?>" class="btn btn-primary">
                                            <i class="fas fa-comments"></i>
                                            <?php if ($request['concern_identified']): ?>
                                                View Conversation
                                            <?php else: ?>
                                                Chat & Identify Concern
                                            <?php endif; ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($request['concern_identified'] && !$request['assignment_id']): ?>
                                        <button class="btn btn-info" onclick="assignAttorney(<?= $request['conversation_id'] ?>, <?= $request['client_id'] ?>)">
                                            <i class="fas fa-user-tie"></i>
                                            Assign Attorney
                                        </button>
                                    <?php elseif (!$request['concern_identified'] && $request['conversation_id']): ?>
                                        <div class="action-note">
                                            <i class="fas fa-info-circle"></i>
                                            <span>First identify the client's concern in the conversation above</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($request['assignment_id']): ?>
                                        <div class="assignment-info">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Client has been assigned to attorney for legal consultation</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Request Details Toggle -->
                                <div class="request-details-toggle">
                                    <button class="btn btn-info" onclick="viewRequestDetails(<?= $request['id'] ?>)">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                </div>
                                
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Review Request Modal -->
    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Review Request</h2>
                <button class="close-modal" onclick="closeReviewModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="requestId" name="request_id">
                    <input type="hidden" id="reviewAction" name="review_action">
                    
                    <div class="form-group">
                        <label for="reviewNotes">Review Notes</label>
                        <textarea id="reviewNotes" name="review_notes" rows="4" placeholder="Add your review notes here..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeReviewModal()">Cancel</button>
                        <button type="submit" class="btn" id="submitBtn">
                            <i class="fas fa-check"></i>
                            <span id="submitText">Submit</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign Attorney Modal -->
    <div id="assignModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Assign Attorney</h2>
                <button class="close-modal" onclick="closeAssignModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="assignForm">
                    <input type="hidden" id="conversationId" name="conversation_id">
                    <input type="hidden" id="clientId" name="client_id">
                    
                    <div class="form-group">
                        <label for="attorneySelect">Select Attorney</label>
                        <select id="attorneySelect" name="attorney_id" required>
                            <option value="">Choose an attorney or admin...</option>
                            <?php 
                            $current_type = '';
                            foreach ($attorneys as $attorney): 
                                if ($attorney['user_type'] !== $current_type) {
                                    $current_type = $attorney['user_type'];
                                    echo '<optgroup label="' . ucfirst($current_type) . 's">';
                                }
                            ?>
                                <option value="<?= $attorney['id'] ?>"><?= htmlspecialchars($attorney['name']) ?></option>
                            <?php 
                            endforeach; 
                            if ($current_type) echo '</optgroup>';
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="assignmentReason">Assignment Reason</label>
                        <textarea id="assignmentReason" name="assignment_reason" rows="3" placeholder="Explain why this attorney is being assigned..."></textarea>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeAssignModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-user-tie"></i>
                            Assign Attorney
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .content-container {
            padding: 30px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border: 1px solid #f5c6cb;
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

        .badge {
            background: var(--primary-color);
            color: white;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
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

        .requests-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .request-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(93, 14, 38, 0.12);
            border: 2px solid rgba(93, 14, 38, 0.08);
            transition: all 0.3s ease;
        }

        .request-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(93, 14, 38, 0.15);
        }

        .request-card.pending {
            border-left: 5px solid #ffc107;
        }

        .request-card.approved {
            border-left: 5px solid #28a745;
        }

        .request-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .request-info h3 {
            color: var(--primary-color);
            margin: 0 0 5px 0;
            font-size: 1.3rem;
        }

        .request-id {
            color: #666;
            font-size: 0.9rem;
            margin: 0 0 5px 0;
        }

        .submitted-date {
            color: #999;
            font-size: 0.85rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .request-status {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .request-status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .request-status.approved {
            background: #d4edda;
            color: #155724;
        }

        .request-details {
            margin-bottom: 20px;
        }

        .detail-item {
            display: flex;
            margin-bottom: 10px;
        }

        .detail-item label {
            font-weight: 600;
            color: var(--primary-color);
            min-width: 80px;
            margin-right: 10px;
        }

        .detail-item span {
            color: #666;
            flex: 1;
        }

        .file-link {
            color: var(--primary-color);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .file-link:hover {
            text-decoration: underline;
        }

        .conversation-status {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(93, 14, 38, 0.05);
            border-radius: 10px;
        }

        .status-info {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .status-info:last-child {
            margin-bottom: 0;
        }

        .status-info i {
            color: var(--primary-color);
        }

        .request-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%);
            color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .action-note {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 10px;
            color: #1565c0;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 10px;
        }

        .action-note i {
            color: #1976d2;
        }

        .assignment-info {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 15px;
            background: #e8f5e8;
            border: 1px solid #4caf50;
            border-radius: 10px;
            color: #2e7d32;
            font-size: 0.9rem;
            font-weight: 500;
            margin-top: 10px;
        }

        .assignment-info i {
            color: #4caf50;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 0;
            border-radius: 20px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            padding: 25px 30px;
            border-bottom: 2px solid rgba(93, 14, 38, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h2 {
            margin: 0;
            color: var(--primary-color);
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: #999;
        }

        .close-modal:hover {
            color: var(--primary-color);
        }

        .modal-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgba(93, 14, 38, 0.1);
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(93, 14, 38, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        @media (max-width: 768px) {
            .requests-grid {
                grid-template-columns: 1fr;
            }
            
            .request-header {
                flex-direction: column;
                gap: 15px;
            }
            
            .request-actions {
                justify-content: center;
            }
        }

        /* ID Display Styles */
        .id-display {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .id-preview {
            margin-top: 8px;
        }

        .id-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.3s ease;
            object-fit: cover;
        }

        .id-image:hover {
            border-color: #1976d2;
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(25, 118, 210, 0.3);
        }

        .pdf-preview {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            color: #666;
        }

        .pdf-preview i {
            font-size: 1.5rem;
            color: #dc3545;
        }

        .no-file {
            color: #dc3545;
            font-style: italic;
        }

        .privacy-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .privacy-status.consented {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .privacy-status.not-consented {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Image Modal */
        .image-modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            cursor: pointer;
        }

        .image-modal-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            max-width: 90%;
            max-height: 90%;
            object-fit: contain;
        }

        .image-modal-close {
            position: absolute;
            top: 20px;
            right: 35px;
            color: #fff;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .image-modal-close:hover {
            color: #ccc;
        }

        /* Request Details Toggle */
        .request-details-toggle {
            margin-top: 15px;
            text-align: center;
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #1976d2;
            color: #1976d2;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-outline:hover {
            background: #1976d2;
            color: white;
        }

        .request-details-collapse {
            margin-top: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .request-details-collapse .request-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .request-details-collapse .detail-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .request-details-collapse .detail-item label {
            font-weight: 600;
            color: #1976d2;
            font-size: 0.9rem;
        }

        .request-details-collapse .detail-item span {
            color: #333;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .request-details-collapse .request-details {
                grid-template-columns: 1fr;
            }
        }

        /* Request Details Modal */
        .request-details-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(8px);
        }

        .request-details-modal .modal-content {
            background: #ffffff;
            margin: 1% auto;
            padding: 0;
            border-radius: 20px;
            width: 98%;
            max-width: 1400px;
            max-height: 95vh;
            overflow-y: auto;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4);
            border: 2px solid rgba(93, 14, 38, 0.15);
        }

        .request-details-modal .modal-header {
            background: linear-gradient(135deg, #5D0E26 0%, #8B1538 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(93, 14, 38, 0.3);
        }

        .request-details-modal .modal-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .request-details-modal .modal-header h2::before {
            content: "📋";
            font-size: 1.2rem;
        }

        .request-details-modal .modal-close {
            color: white;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .request-details-modal .modal-close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: rotate(90deg);
        }

        .request-details-modal .modal-body {
            padding: 40px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            min-height: 60vh;
        }

        .request-details-modal .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .request-details-modal .details-section {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(93, 14, 38, 0.1);
        }

        .request-details-modal .details-section h3 {
            color: #5D0E26;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid rgba(93, 14, 38, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .request-details-modal .details-section h3::before {
            content: "📋";
            font-size: 1.1rem;
        }

        .request-details-modal .images-section {
            grid-column: 1 / -1;
            margin-top: 0;
        }

        .request-details-modal .detail-item {
            margin-bottom: 15px;
            padding: 18px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 12px;
            border: 1px solid rgba(93, 14, 38, 0.1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            position: relative;
        }

        .request-details-modal .detail-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(93, 14, 38, 0.15);
            border-color: rgba(93, 14, 38, 0.2);
        }

        .request-details-modal .detail-item::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: linear-gradient(135deg, #5D0E26 0%, #8B1538 100%);
            border-radius: 0 2px 2px 0;
        }

        .request-details-modal .detail-item label {
            display: block;
            font-weight: 700;
            color: #5D0E26;
            margin-bottom: 8px;
            font-size: 0.95rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .request-details-modal .detail-item span {
            color: #333;
            font-size: 1rem;
            line-height: 1.5;
            font-weight: 500;
        }

        .request-details-modal .id-display {
            margin-top: 10px;
        }

        .request-details-modal .id-image {
            max-width: 100%;
            max-height: 150px;
            border-radius: 6px;
            border: 2px solid #e0e0e0;
            cursor: pointer;
            transition: all 0.2s ease;
            object-fit: cover;
        }

        .request-details-modal .id-image:hover {
            border-color: #5D0E26;
            transform: scale(1.02);
        }

        .request-details-modal .file-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #5D0E26;
            text-decoration: none;
            font-weight: 600;
            margin-bottom: 10px;
            padding: 8px 12px;
            background: linear-gradient(135deg, rgba(93, 14, 38, 0.05) 0%, rgba(93, 14, 38, 0.1) 100%);
            border-radius: 8px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            border: 1px solid rgba(93, 14, 38, 0.1);
        }

        .request-details-modal .file-link:hover {
            background: linear-gradient(135deg, rgba(93, 14, 38, 0.1) 0%, rgba(93, 14, 38, 0.2) 100%);
            transform: translateY(-1px);
            box-shadow: 0 2px 10px rgba(93, 14, 38, 0.2);
        }

        .request-details-modal .file-link i {
            font-size: 1.1rem;
        }

        .request-details-modal .pdf-preview {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 15px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            color: #666;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .request-details-modal .pdf-preview:hover {
            border-color: #dc3545;
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.1);
        }

        .request-details-modal .pdf-preview i {
            font-size: 1.8rem;
            color: #dc3545;
        }

        .request-details-modal .pdf-preview span {
            font-weight: 600;
            font-size: 1rem;
        }

        .request-details-modal .no-file {
            color: #dc3545;
            font-style: italic;
            font-weight: 600;
            padding: 12px 16px;
            background: linear-gradient(135deg, rgba(220, 53, 69, 0.05) 0%, rgba(220, 53, 69, 0.1) 100%);
            border-radius: 8px;
            border-left: 4px solid #dc3545;
            font-size: 0.95rem;
            text-align: center;
            box-shadow: 0 2px 10px rgba(220, 53, 69, 0.1);
        }

        .request-details-modal .privacy-status {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 25px;
            font-weight: 600;
            font-size: 0.95rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .request-details-modal .privacy-status.consented {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
            border-color: #c3e6cb;
        }

        .request-details-modal .privacy-status.not-consented {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-color: #f5c6cb;
        }

        .request-details-modal .privacy-status i {
            font-size: 1.2rem;
        }

        /* Images Section */
        .images-section {
            margin-top: 0;
            padding: 30px;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-radius: 20px;
            border: 2px solid rgba(93, 14, 38, 0.1);
            box-shadow: 0 6px 30px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .images-section::before {
            content: "📄";
            position: absolute;
            top: -20px;
            left: 30px;
            background: white;
            padding: 12px 16px;
            border-radius: 50%;
            font-size: 1.4rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
            border: 2px solid rgba(93, 14, 38, 0.1);
        }

        .images-section h3 {
            color: #5D0E26;
            margin-bottom: 25px;
            font-size: 1.4rem;
            font-weight: 700;
            text-align: center;
            padding-top: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .image-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .image-item {
            display: flex;
            flex-direction: column;
            background: white;
            padding: 25px;
            border-radius: 16px;
            border: 2px solid rgba(93, 14, 38, 0.1);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .image-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 35px rgba(93, 14, 38, 0.2);
            border-color: rgba(93, 14, 38, 0.3);
        }

        .image-item label {
            font-weight: 700;
            color: #5D0E26;
            margin-bottom: 15px;
            font-size: 1.1rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: center;
            padding: 8px 0;
            border-bottom: 2px solid rgba(93, 14, 38, 0.1);
        }

        .image-item .id-image {
            max-width: 100%;
            max-height: 200px;
            border-radius: 12px;
            border: 3px solid rgba(93, 14, 38, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .image-item .id-image:hover {
            border-color: #5D0E26;
            transform: scale(1.08);
            box-shadow: 0 12px 35px rgba(93, 14, 38, 0.25);
        }

        .btn-info {
            background: #5D0E26;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-info:hover {
            background: #8B1538;
            transform: translateY(-1px);
        }

        .btn-info i {
            margin-right: 6px;
            font-size: 0.9rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .request-details-modal .modal-body {
                padding: 20px;
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .request-details-modal .modal-content {
                margin: 2% auto;
                width: 98%;
                max-height: 95vh;
            }
            
            .request-details-modal .modal-header {
                padding: 20px 25px;
            }
            
            .request-details-modal .modal-header h2 {
                font-size: 1.3rem;
            }
            
            .request-details-modal .detail-item {
                padding: 15px;
                margin-bottom: 12px;
            }
            
            .image-row {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .images-section {
                padding: 20px;
            }
            
            .image-item .id-image {
                max-height: 150px;
            }
        }
    </style>

    <script>
        function reviewRequest(requestId, action) {
            document.getElementById('requestId').value = requestId;
            document.getElementById('reviewAction').value = action;
            
            const modal = document.getElementById('reviewModal');
            const title = document.getElementById('modalTitle');
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            
            if (action === 'approve') {
                title.textContent = 'Approve Request';
                submitBtn.className = 'btn btn-success';
                submitText.textContent = 'Approve';
            } else {
                title.textContent = 'Reject Request';
                submitBtn.className = 'btn btn-danger';
                submitText.textContent = 'Reject';
            }
            
            modal.style.display = 'block';
        }

        function closeReviewModal() {
            document.getElementById('reviewModal').style.display = 'none';
            document.getElementById('reviewForm').reset();
        }

        function assignAttorney(conversationId, clientId) {
            document.getElementById('conversationId').value = conversationId;
            document.getElementById('clientId').value = clientId;
            document.getElementById('assignModal').style.display = 'block';
        }

        function closeAssignModal() {
            document.getElementById('assignModal').style.display = 'none';
            document.getElementById('assignForm').reset();
        }

        // Profile Dropdown Functions
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }
        
        function editProfile() {
            alert('Profile editing functionality will be implemented.');
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
            
            // Close modals when clicking outside
            if (event.target == document.getElementById('reviewModal')) {
                closeReviewModal();
            }
            if (event.target == document.getElementById('assignModal')) {
                closeAssignModal();
            }
        }

        // Handle review form submission
        document.getElementById('reviewForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'review_request');
            
            fetch('employee_request_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.includes('successfully')) {
                    location.reload();
                } else {
                    alert('Error processing request. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error processing request. Please try again.');
            });
        });

        // Handle assign form submission
        document.getElementById('assignForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'assign_attorney');
            
            fetch('employee_request_management.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                if (result.includes('successfully')) {
                    location.reload();
                } else {
                    alert('Error assigning attorney. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error assigning attorney. Please try again.');
            });
        });
    </script>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal" onclick="closeImageModal()">
        <span class="image-modal-close" onclick="closeImageModal()">&times;</span>
        <img class="image-modal-content" id="modalImage">
    </div>

    <!-- Request Details Modal -->
    <div id="requestDetailsModal" class="request-details-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Request Details</h2>
                <span class="modal-close" onclick="closeRequestDetailsModal()">&times;</span>
            </div>
            <div class="modal-body" id="requestDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        // Image Modal Functions
        function openImageModal(imageSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = 'block';
            modalImg.src = imageSrc;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = 'none';
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });

        // Toggle Request Details
        function toggleRequestDetails(requestId) {
            const detailsDiv = document.getElementById('requestDetails' + requestId);
            const button = event.target.closest('button');
            const icon = button.querySelector('i');
            
            if (detailsDiv.style.display === 'none') {
                detailsDiv.style.display = 'block';
                icon.className = 'fas fa-eye-slash';
                button.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Request Details';
            } else {
                detailsDiv.style.display = 'none';
                icon.className = 'fas fa-eye';
                button.innerHTML = '<i class="fas fa-eye"></i> View Request Details';
            }
        }

        // View Request Details Modal
        function viewRequestDetails(requestId) {
            // Fetch request details via AJAX
            fetch('get_request_details.php?id=' + requestId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const request = data.request;
                        const modalContent = document.getElementById('requestDetailsContent');
                        
                        modalContent.innerHTML = `
                            <div class="details-section">
                                <h3>Personal Information</h3>
                                <div class="detail-item">
                                    <label>Client Name:</label>
                                    <span>${request.client_name}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Email:</label>
                                    <span>${request.client_email}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Address:</label>
                                    <span>${request.address}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Gender:</label>
                                    <span>${request.gender}</span>
                                </div>
                            </div>
                            
                            <div class="details-section">
                                <h3>Request Details</h3>
                                <div class="detail-item">
                                    <label>Request ID:</label>
                                    <span>${request.request_id}</span>
                                </div>
                                <div class="detail-item">
                                    <label>Privacy Consent:</label>
                                    <span class="privacy-status ${request.privacy_consent ? 'consented' : 'not-consented'}">
                                        <i class="fas fa-${request.privacy_consent ? 'check-circle' : 'times-circle'}"></i>
                                        ${request.privacy_consent ? 'Agreed to Data Privacy Act' : 'Not agreed to Data Privacy Act'}
                                    </span>
                                </div>
                                <div class="detail-item">
                                    <label>Submitted Date:</label>
                                    <span>${new Date(request.submitted_at).toLocaleString()}</span>
                                </div>
                            </div>
                            
                            <!-- Images Section - Full Width -->
                            <div class="images-section">
                                <h3>Government ID Documents</h3>
                                
                                <div class="image-row">
                                    <div class="image-item">
                                        <label>Government ID Front</label>
                                        ${request.valid_id_front_path ? `
                                            <div class="id-display">
                                                <a href="${request.valid_id_front_path}" target="_blank" class="file-link">
                                                    <i class="fas fa-image"></i>
                                                    ${request.valid_id_front_filename}
                                                </a>
                                                <div class="id-preview">
                                                    ${request.valid_id_front_path.toLowerCase().includes('.pdf') ? `
                                                        <div class="pdf-preview">
                                                            <i class="fas fa-file-pdf"></i>
                                                            <span>PDF Document</span>
                                                        </div>
                                                    ` : `
                                                        <img src="${request.valid_id_front_path}" alt="Front ID" class="id-image" onclick="openImageModal('${request.valid_id_front_path}')">
                                                    `}
                                                </div>
                                            </div>
                                        ` : '<span class="no-file">No front ID uploaded</span>'}
                                    </div>
                                    
                                    <div class="image-item">
                                        <label>Government ID Back</label>
                                        ${request.valid_id_back_path ? `
                                            <div class="id-display">
                                                <a href="${request.valid_id_back_path}" target="_blank" class="file-link">
                                                    <i class="fas fa-image"></i>
                                                    ${request.valid_id_back_filename}
                                                </a>
                                                <div class="id-preview">
                                                    ${request.valid_id_back_path.toLowerCase().includes('.pdf') ? `
                                                        <div class="pdf-preview">
                                                            <i class="fas fa-file-pdf"></i>
                                                            <span>PDF Document</span>
                                                        </div>
                                                    ` : `
                                                        <img src="${request.valid_id_back_path}" alt="Back ID" class="id-image" onclick="openImageModal('${request.valid_id_back_path}')">
                                                    `}
                                                </div>
                                            </div>
                                        ` : '<span class="no-file">No back ID uploaded</span>'}
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('requestDetailsModal').style.display = 'block';
                    } else {
                        alert('Error loading request details: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading request details. Please try again.');
                });
        }

        // Close Request Details Modal
        function closeRequestDetailsModal() {
            document.getElementById('requestDetailsModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('requestDetailsModal');
            if (event.target === modal) {
                closeRequestDetailsModal();
            }
        }
    </script>

    <style>
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
