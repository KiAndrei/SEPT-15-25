<?php
require_once 'session_manager.php';
validateUserAccess('client');
require_once 'config.php';
require_once 'audit_logger.php';
require_once 'action_logger_helper.php';
$client_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT profile_image FROM user_form WHERE id=?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$res = $stmt->get_result();
$profile_image = '';
if ($res && $row = $res->fetch_assoc()) {
    $profile_image = $row['profile_image'];
}
if (!$profile_image || !file_exists($profile_image)) {
        $profile_image = 'images/default-avatar.jpg';
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Generation - Opiña Law Office</title>
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
            <li>
                <a href="client_dashboard.php">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="client_documents.php">
                    <i class="fas fa-file-alt"></i>
                    <span>Document Generation</span>
                </a>
            </li>
            <li>
                <a href="client_cases.php">
                    <i class="fas fa-gavel"></i>
                    <span>My Cases</span>
                </a>
            </li>
            <li>
                <a href="client_schedule.php">
                    <i class="fas fa-calendar-alt"></i>
                    <span>My Schedule</span>
                </a>
            </li>
            <li>
                <a href="client_messages.php">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <?php 
        $page_title = 'Document Generation';
        $page_subtitle = 'Generate and manage your document storage';
        include 'components/profile_header.php'; 
        ?>

         <!-- Document Generation Grid -->
         <div class="document-grid">
            <!-- Row 1 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3>Affidavit of Loss</h3>
                <p>Generate affidavit of loss document</p>
                <button onclick="openAffidavitLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-id-card"></i>
                </div>
                <h3>Affidavit of Loss<br><span style="font-size: 0.9em; font-weight: 500;">(Senior ID)</span></h3>
                <p>Generate affidavit of loss for senior ID</p>
                <a href="files-generation/generate_affidavit_of_loss_senior_id.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-user"></i>
                </div>
                <h3>Sworn Affidavit of Solo Parent</h3>
                <p>Generate sworn affidavit of solo parent</p>
                <button onclick="openSoloParentModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <!-- Row 2 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-female"></i>
                </div>
                <h3>Sworn Affidavit of Mother</h3>
                <p>Generate sworn affidavit of mother</p>
                <a href="files-generation/generate_sworn_statement_of_mother.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-wheelchair"></i>
                </div>
                <h3>Affidavit of Loss<br><span style="font-size: 0.9em; font-weight: 500;">(PWD ID)</span></h3>
                <p>Generate affidavit of loss for PWD ID</p>
                <button onclick="openPWDLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-book"></i>
                </div>
                <h3>Affidavit of Loss (Boticab Booklet/ID)</h3>
                <p>Generate affidavit of loss for Boticab booklet/ID</p>
                <button onclick="openBoticabLossModal()" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </button>
            </div>

            <!-- Row 3 -->
            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3>Joint Affidavit (Two Disinterested Person)</h3>
                <p>Generate joint affidavit of two disinterested person</p>
                <a href="files-generation/generate_two_disintersted.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-handshake"></i>
                </div>
                <h3>Joint Affidavit of Two Disinterested Person (Solo Parent)</h3>
                <p>Generate joint affidavit of two disinterested person (solo parent)</p>
                <a href="files-generation/generate_joint_affidavit_solo_parent.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>

            <div class="document-box">
                <div class="document-icon">
                    <i class="fas fa-file-signature"></i>
                </div>
                <h3>Sworn Affidavit (Solo Parent)</h3>
                <p>Generate sworn affidavit for solo parent</p>
                <a href="files-generation/generate_sworn_affidavit_solo_parent.php" class="btn btn-primary generate-btn">
                    <i class="fas fa-edit"></i> Fill Up
                </a>
            </div>
        </div>
    </div>

    <!-- Affidavit of Loss Modal -->
    <div id="affidavitLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Affidavit of Loss</h2>
                <span class="close" onclick="closeAffidavitLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="affidavitLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="fullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="fullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="completeAddress">Complete Address <span class="required">*</span></label>
                        <input type="text" id="completeAddress" name="completeAddress" required placeholder="Enter your complete address">
                    </div>
                    
                    <div class="form-group">
                        <label for="specifyItemLost">Specify Item Lost <span class="required">*</span></label>
                        <input type="text" id="specifyItemLost" name="specifyItemLost" required placeholder="e.g., Driver's License, Passport, ID Card">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemLost">Item Lost <span class="required">*</span></label>
                        <input type="text" id="itemLost" name="itemLost" required placeholder="Describe the specific item that was lost">
                    </div>
                    
                    <div class="form-group">
                        <label for="itemDetails">Item Details <span class="required">*</span></label>
                        <input type="text" id="itemDetails" name="itemDetails" required placeholder="Provide detailed description of the lost item">
                    </div>
                    
                    <div class="form-group">
                        <label for="dateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="dateOfNotary" name="dateOfNotary" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeAffidavitLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveAffidavitLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewAffidavitLoss()" class="btn btn-primary" style="background: #28a745;">View</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Solo Parent Affidavit Modal -->
    <div id="soloParentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-user"></i> Sworn Affidavit of Solo Parent</h2>
                <span class="close" onclick="closeSoloParentModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="soloParentForm" class="modal-form">
                    <div class="form-group">
                        <label for="soloFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="soloFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="soloCompleteAddress">Complete Address <span class="required">*</span></label>
                        <textarea id="soloCompleteAddress" name="completeAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="childrenNames">Name of Child/Children <span class="required">*</span></label>
                        <textarea id="childrenNames" name="childrenNames" required placeholder="Enter names of all children (separate each name with comma if multiple)" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">If multiple children, separate each name with a comma</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="yearsUnderCase">Number of Years as Solo Parent <span class="required">*</span></label>
                        <input type="text" id="yearsUnderCase" name="yearsUnderCase" required placeholder="e.g., 5 years">
                    </div>
                    
                    <div class="form-group">
                        <label>Reason for Being Solo Parent <span class="required">*</span></label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="reason1" name="reasonSection" value="Left the family home and abandoned us" style="width: auto; margin: 0;">
                                <label for="reason1" style="margin: 0; font-weight: 500; cursor: pointer;">Left the family home and abandoned us</label>
                            </div>
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="reason2" name="reasonSection" value="Died last" style="width: auto; margin: 0;">
                                <label for="reason2" style="margin: 0; font-weight: 500; cursor: pointer;">Died last</label>
                            </div>
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="reason3" name="reasonSection" value="Other reason, please state" style="width: auto; margin: 0;">
                                <label for="reason3" style="margin: 0; font-weight: 500; cursor: pointer;">Other reason, please state</label>
                            </div>
                        </div>
                        
                        <div id="otherReasonField" class="conditional-field" style="margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); display: none;">
                            <label for="otherReason" style="font-weight: 600; color: var(--text-dark); font-size: 0.85rem; margin-bottom: 6px; display: block;">Please specify other reason:</label>
                            <input type="text" id="otherReason" name="otherReason" placeholder="Enter the specific reason" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif; width: 100%;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Employment Status <span class="required">*</span></label>
                        <div class="radio-group" style="display: flex; flex-direction: column; gap: 8px; margin-top: 8px;">
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="emp1" name="employmentStatus" value="Employee and earning" style="width: auto; margin: 0;">
                                <label for="emp1" style="margin: 0; font-weight: 500; cursor: pointer;">Employee and earning</label>
                            </div>
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="emp2" name="employmentStatus" value="Self-employed and earning" style="width: auto; margin: 0;">
                                <label for="emp2" style="margin: 0; font-weight: 500; cursor: pointer;">Self-employed and earning</label>
                            </div>
                            <div class="radio-item" style="display: flex; align-items: center; gap: 8px; padding: 8px; border: 2px solid var(--border-color); border-radius: 8px; cursor: pointer; transition: all 0.3s ease;">
                                <input type="radio" id="emp3" name="employmentStatus" value="Un-employed and dependent upon" style="width: auto; margin: 0;">
                                <label for="emp3" style="margin: 0; font-weight: 500; cursor: pointer;">Un-employed and dependent upon</label>
                            </div>
                        </div>

                        <div id="employeeAmountField" class="conditional-field" style="margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); display: none;">
                            <label for="employeeAmount" style="font-weight: 600; color: var(--text-dark); font-size: 0.85rem; margin-bottom: 6px; display: block;">Monthly Income Amount:</label>
                            <input type="text" id="employeeAmount" name="employeeAmount" placeholder="e.g., ₱25,000.00" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif; width: 100%;">
                        </div>

                        <div id="selfEmployedAmountField" class="conditional-field" style="margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); display: none;">
                            <label for="selfEmployedAmount" style="font-weight: 600; color: var(--text-dark); font-size: 0.85rem; margin-bottom: 6px; display: block;">Monthly Income Amount:</label>
                            <input type="text" id="selfEmployedAmount" name="selfEmployedAmount" placeholder="e.g., ₱30,000.00" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif; width: 100%;">
                        </div>

                        <div id="unemployedDependentField" class="conditional-field" style="margin-top: 12px; padding: 12px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); display: none;">
                            <label for="unemployedDependent" style="font-weight: 600; color: var(--text-dark); font-size: 0.85rem; margin-bottom: 6px; display: block;">Dependent upon:</label>
                            <input type="text" id="unemployedDependent" name="unemployedDependent" placeholder="e.g., parents, relatives, government assistance" style="padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif; width: 100%;">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="soloDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="soloDateOfNotary" name="dateOfNotary" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeSoloParentModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveSoloParent()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewSoloParent()" class="btn btn-primary" style="background: #28a745;">View</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- PWD ID Loss Modal -->
    <div id="pwdLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-wheelchair"></i> Affidavit of Loss (PWD ID)</h2>
                <span class="close" onclick="closePWDLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="pwdLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="pwdFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="pwdFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdFullAddress">Full Address <span class="required">*</span></label>
                        <textarea id="pwdFullAddress" name="fullAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdDetailsOfLoss">Details of Loss <span class="required">*</span></label>
                        <textarea id="pwdDetailsOfLoss" name="detailsOfLoss" required placeholder="Describe the circumstances of how the PWD ID was lost" style="resize: vertical; min-height: 80px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Please provide detailed information about when, where, and how the PWD ID was lost</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="pwdDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="pwdDateOfNotary" name="dateOfNotary" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closePWDLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="savePWDLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewPWDLoss()" class="btn btn-primary" style="background: #28a745;">View</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Boticab Booklet/ID Loss Modal -->
    <div id="boticabLossModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-book"></i> Affidavit of Loss (Boticab Booklet/ID)</h2>
                <span class="close" onclick="closeBoticabLossModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="boticabLossForm" class="modal-form">
                    <div class="form-group">
                        <label for="boticabFullName">Full Name <span class="required">*</span></label>
                        <input type="text" id="boticabFullName" name="fullName" required placeholder="Enter your complete name">
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabFullAddress">Full Address <span class="required">*</span></label>
                        <textarea id="boticabFullAddress" name="fullAddress" required placeholder="Enter your complete address including street, barangay, city, province" style="resize: vertical; min-height: 60px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabDetailsOfLoss">Details of Loss <span class="required">*</span></label>
                        <textarea id="boticabDetailsOfLoss" name="detailsOfLoss" required placeholder="Describe the circumstances of how the Boticab booklet/ID was lost" style="resize: vertical; min-height: 80px; padding: 10px 12px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 0.85rem; transition: all 0.3s ease; background: white; font-family: 'Poppins', sans-serif;"></textarea>
                        <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 4px;">Please provide detailed information about when, where, and how the Boticab booklet/ID was lost</div>
                    </div>
                    
                    <div class="form-group">
                        <label for="boticabDateOfNotary">Date of Notary <span class="required">*</span></label>
                        <input type="date" id="boticabDateOfNotary" name="dateOfNotary" required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="button" onclick="closeBoticabLossModal()" class="btn btn-secondary">Cancel</button>
                        <button type="button" onclick="saveBoticabLoss()" class="btn btn-primary">Save</button>
                        <button type="button" onclick="viewBoticabLoss()" class="btn btn-primary" style="background: #28a745;">View</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-color: #5D0E26;
            --primary-dark: #3D0A1A;
            --secondary-color: #8B1538;
            --accent-color: #D4AF37;
            --text-dark: #2C3E50;
            --text-light: #7F8C8D;
            --border-color: #E8E8E8;
            --shadow-light: rgba(0, 0, 0, 0.08);
            --shadow-medium: rgba(0, 0, 0, 0.12);
            --shadow-heavy: rgba(0, 0, 0, 0.2);
        }

        .document-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            padding: 20px;
        }

        .document-box {
            background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
            border-radius: 12px;
            padding: 20px 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            gap: 12px;
            transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            box-shadow: 0 4px 20px var(--shadow-light);
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }

        .document-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .document-box:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 12px 40px var(--shadow-medium);
            border-color: var(--primary-color);
        }

        .document-box:hover::before {
            transform: scaleX(1);
        }

        .document-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 8px 24px rgba(93, 14, 38, 0.3);
            transition: all 0.3s ease;
            position: relative;
        }

        .document-box:hover .document-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 32px rgba(93, 14, 38, 0.4);
        }

        .document-icon::after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(135deg, var(--accent-color), var(--primary-color));
            border-radius: 22px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .document-box:hover .document-icon::after {
            opacity: 1;
        }

        .document-box h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-dark);
            line-height: 1.3;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, var(--text-dark), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .document-box p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.85rem;
            line-height: 1.4;
            font-weight: 400;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(93, 14, 38, 0.3);
            position: relative;
            overflow: hidden;
            min-width: 120px;
            justify-content: center;
            text-decoration: none;
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--secondary-color) 0%, var(--primary-color) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(93, 14, 38, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-primary:active {
            transform: translateY(0);
            box-shadow: 0 4px 16px rgba(93, 14, 38, 0.3);
        }

        .btn-primary i {
            font-size: 1.1rem;
            transition: transform 0.3s ease;
        }

        .btn-primary:hover i {
            transform: scale(1.1);
        }

        /* Responsive design improvements */
        @media (max-width: 1200px) {
            .document-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                padding: 20px;
            }
        }

        @media (max-width: 1024px) {
            .document-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .document-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Focus states for accessibility */
        .btn-primary:focus {
            outline: 2px solid var(--accent-color);
            outline-offset: 2px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
            margin: auto;
            padding: 0;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideIn 0.3s ease;
            border: 1px solid var(--border-color);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            flex-shrink: 0;
        }

        .modal-header h2 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .modal-header h2 i {
            font-size: 1.1rem;
        }

        .close {
            color: white;
            font-size: 24px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }

        .close:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: scale(1.1);
        }

        .modal-body {
            padding: 16px 20px;
            overflow-y: auto;
            flex: 1;
        }

        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .form-group label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .required {
            color: #e74c3c;
            font-weight: bold;
        }

        .form-group input {
            padding: 10px 12px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            background: white;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(93, 14, 38, 0.1);
            transform: translateY(-1px);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid var(--border-color);
            flex-shrink: 0;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.85rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(108, 117, 125, 0.3);
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(108, 117, 125, 0.4);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translate(-50%, -60%) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
        }

        /* Responsive modal */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                max-height: 85vh;
                margin: auto;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .modal-header {
                padding: 10px 16px;
            }
            
            .modal-header h2 {
                font-size: 1rem;
            }
            
            .modal-body {
                padding: 12px 16px;
            }
            
            .modal-form {
                gap: 10px;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 8px;
            }
            
            .btn-secondary,
            .btn-primary {
                width: 100%;
                padding: 10px 16px;
            }
        }

        @media (max-height: 700px) {
            .modal-content {
                max-height: 95vh;
                margin: auto;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            
            .modal-form {
                gap: 8px;
            }
            
            .form-group {
                gap: 3px;
            }
            
            .form-group input {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .form-group label {
                font-size: 0.8rem;
            }
        }

    </style>
    
    <script>
        // Profile Dropdown Functions
        function toggleProfileDropdown() {
            const dropdown = document.getElementById('profileDropdown');
            dropdown.classList.toggle('show');
        }
        
        function editProfile() {
            document.getElementById('editProfileModal').style.display = 'block';
            // Close dropdown when opening modal
            document.getElementById('profileDropdown').classList.remove('show');
        }

        function closeEditProfileModal() {
            document.getElementById('editProfileModal').style.display = 'none';
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

        // Affidavit of Loss Modal Functions
        function openAffidavitLossModal() {
            document.getElementById('affidavitLossModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeAffidavitLossModal() {
            document.getElementById('affidavitLossModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            // Reset form
            document.getElementById('affidavitLossForm').reset();
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('affidavitLossModal');
            if (event.target === modal) {
                closeAffidavitLossModal();
            }
        }

        // Save Affidavit Loss function
        function saveAffidavitLoss() {
            const form = document.getElementById('affidavitLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                completeAddress: formData.get('completeAddress'),
                specifyItemLost: formData.get('specifyItemLost'),
                itemLost: formData.get('itemLost'),
                itemDetails: formData.get('itemDetails'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.completeAddress || !data.specifyItemLost || !data.itemLost || !data.itemDetails || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const saveBtn = document.querySelector('button[onclick="saveAffidavitLoss()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Simulate saving (replace with actual API call)
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                alert('Document saved successfully!');
            }, 1500);
        }

        // View Affidavit Loss function
        function viewAffidavitLoss() {
            const form = document.getElementById('affidavitLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                completeAddress: formData.get('completeAddress'),
                specifyItemLost: formData.get('specifyItemLost'),
                itemLost: formData.get('itemLost'),
                itemDetails: formData.get('itemDetails'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.completeAddress || !data.specifyItemLost || !data.itemLost || !data.itemDetails || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const viewBtn = document.querySelector('button[onclick="viewAffidavitLoss()"]');
            const originalText = viewBtn.innerHTML;
            viewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            viewBtn.disabled = true;

            // Simulate viewing (replace with actual API call)
            setTimeout(() => {
                viewBtn.innerHTML = originalText;
                viewBtn.disabled = false;
                
                // Open document in modal for viewing only (no download)
                openDocumentViewer('files-generation/generate_affidavit_of_loss.php', 'affidavitLossForm');
                window.currentFormType = 'affidavitLoss';
            }, 1500);
        }

        // Set today's date as default for date of notary
        document.addEventListener('DOMContentLoaded', function() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('dateOfNotary').value = today;
            document.getElementById('soloDateOfNotary').value = today;
            document.getElementById('pwdDateOfNotary').value = today;
            document.getElementById('boticabDateOfNotary').value = today;
        });

        // Solo Parent Modal Functions
        function openSoloParentModal() {
            document.getElementById('soloParentModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeSoloParentModal() {
            document.getElementById('soloParentModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            // Reset form
            document.getElementById('soloParentForm').reset();
            // Hide all conditional fields
            document.querySelectorAll('#soloParentModal .conditional-field').forEach(field => {
                field.style.display = 'none';
            });
            // Remove selected styling from radio items
            document.querySelectorAll('#soloParentModal .radio-item').forEach(item => {
                item.style.borderColor = 'var(--border-color)';
                item.style.backgroundColor = 'transparent';
            });
        }

        // Handle radio button selection styling and conditional fields for Solo Parent modal
        document.addEventListener('DOMContentLoaded', function() {
            // Handle reason section radio buttons
            document.querySelectorAll('#soloParentModal input[name="reasonSection"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected styling from all reason radio items
                    document.querySelectorAll('#soloParentModal input[name="reasonSection"]').forEach(r => {
                        r.closest('.radio-item').style.borderColor = 'var(--border-color)';
                        r.closest('.radio-item').style.backgroundColor = 'transparent';
                    });
                    
                    // Add selected styling to current item
                    this.closest('.radio-item').style.borderColor = 'var(--primary-color)';
                    this.closest('.radio-item').style.backgroundColor = 'rgba(93, 14, 38, 0.05)';
                    
                    // Handle conditional fields
                    handleSoloParentConditionalFields();
                });
            });

            // Handle employment status radio buttons
            document.querySelectorAll('#soloParentModal input[name="employmentStatus"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    // Remove selected styling from all employment radio items
                    document.querySelectorAll('#soloParentModal input[name="employmentStatus"]').forEach(r => {
                        r.closest('.radio-item').style.borderColor = 'var(--border-color)';
                        r.closest('.radio-item').style.backgroundColor = 'transparent';
                    });
                    
                    // Add selected styling to current item
                    this.closest('.radio-item').style.borderColor = 'var(--primary-color)';
                    this.closest('.radio-item').style.backgroundColor = 'rgba(93, 14, 38, 0.05)';
                    
                    // Handle conditional fields
                    handleSoloParentConditionalFields();
                });
            });

            // Add click handlers to radio items for better UX
            document.querySelectorAll('#soloParentModal .radio-item').forEach(item => {
                item.addEventListener('click', function() {
                    const radio = this.querySelector('input[type="radio"]');
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                });
            });
        });

        function handleSoloParentConditionalFields() {
            // Hide all conditional fields
            document.querySelectorAll('#soloParentModal .conditional-field').forEach(field => {
                field.style.display = 'none';
            });

            // Show relevant conditional fields based on selections
            const reasonSection = document.querySelector('#soloParentModal input[name="reasonSection"]:checked');
            const employmentStatus = document.querySelector('#soloParentModal input[name="employmentStatus"]:checked');

            // Handle reason section
            if (reasonSection && reasonSection.value === 'Other reason, please state') {
                document.getElementById('otherReasonField').style.display = 'block';
            }

            // Handle employment status
            if (employmentStatus) {
                switch(employmentStatus.value) {
                    case 'Employee and earning':
                        document.getElementById('employeeAmountField').style.display = 'block';
                        break;
                    case 'Self-employed and earning':
                        document.getElementById('selfEmployedAmountField').style.display = 'block';
                        break;
                    case 'Un-employed and dependent upon':
                        document.getElementById('unemployedDependentField').style.display = 'block';
                        break;
                }
            }
        }

        // Save Solo Parent function
        function saveSoloParent() {
            const form = document.getElementById('soloParentForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                completeAddress: formData.get('completeAddress'),
                childrenNames: formData.get('childrenNames'),
                yearsUnderCase: formData.get('yearsUnderCase'),
                reasonSection: formData.get('reasonSection'),
                otherReason: formData.get('otherReason'),
                employmentStatus: formData.get('employmentStatus'),
                employeeAmount: formData.get('employeeAmount'),
                selfEmployedAmount: formData.get('selfEmployedAmount'),
                unemployedDependent: formData.get('unemployedDependent'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.completeAddress || !data.childrenNames || !data.yearsUnderCase || !data.reasonSection || !data.employmentStatus || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const saveBtn = document.querySelector('button[onclick="saveSoloParent()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Simulate saving (replace with actual API call)
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                alert('Document saved successfully!');
            }, 1500);
        }

        // View Solo Parent function
        function viewSoloParent() {
            const form = document.getElementById('soloParentForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                completeAddress: formData.get('completeAddress'),
                childrenNames: formData.get('childrenNames'),
                yearsUnderCase: formData.get('yearsUnderCase'),
                reasonSection: formData.get('reasonSection'),
                otherReason: formData.get('otherReason'),
                employmentStatus: formData.get('employmentStatus'),
                employeeAmount: formData.get('employeeAmount'),
                selfEmployedAmount: formData.get('selfEmployedAmount'),
                unemployedDependent: formData.get('unemployedDependent'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.completeAddress || !data.childrenNames || !data.yearsUnderCase || !data.reasonSection || !data.employmentStatus || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const viewBtn = document.querySelector('button[onclick="viewSoloParent()"]');
            const originalText = viewBtn.innerHTML;
            viewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            viewBtn.disabled = true;

            // Simulate viewing (replace with actual API call)
            setTimeout(() => {
                viewBtn.innerHTML = originalText;
                viewBtn.disabled = false;
                
                // Open document in modal for viewing only (no download)
                openDocumentViewer('files-generation/generate_affidavit_of_solo_parent.php', 'soloParentForm');
                window.currentFormType = 'soloParent';
            }, 1500);
        }

        // Close Solo Parent modal when clicking outside
        window.addEventListener('click', function(event) {
            const soloModal = document.getElementById('soloParentModal');
            if (event.target === soloModal) {
                closeSoloParentModal();
            }
        });

        // PWD Loss Modal Functions
        function openPWDLossModal() {
            document.getElementById('pwdLossModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closePWDLossModal() {
            document.getElementById('pwdLossModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            // Reset form
            document.getElementById('pwdLossForm').reset();
        }

        // Save PWD Loss function
        function savePWDLoss() {
            const form = document.getElementById('pwdLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                fullAddress: formData.get('fullAddress'),
                detailsOfLoss: formData.get('detailsOfLoss'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.fullAddress || !data.detailsOfLoss || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const saveBtn = document.querySelector('button[onclick="savePWDLoss()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Simulate saving (replace with actual API call)
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                alert('Document saved successfully!');
            }, 1500);
        }

        // View PWD Loss function
        function viewPWDLoss() {
            const form = document.getElementById('pwdLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                fullAddress: formData.get('fullAddress'),
                detailsOfLoss: formData.get('detailsOfLoss'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.fullAddress || !data.detailsOfLoss || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const viewBtn = document.querySelector('button[onclick="viewPWDLoss()"]');
            const originalText = viewBtn.innerHTML;
            viewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            viewBtn.disabled = true;

            // Simulate viewing (replace with actual API call)
            setTimeout(() => {
                viewBtn.innerHTML = originalText;
                viewBtn.disabled = false;
                
                // Open document in modal for viewing only (no download)
                openDocumentViewer('files-generation/generate_affidavit_of_loss_pwd_id.php', 'pwdLossForm');
                window.currentFormType = 'pwdLoss';
            }, 1500);
        }

        // Close PWD Loss modal when clicking outside
        window.addEventListener('click', function(event) {
            const pwdModal = document.getElementById('pwdLossModal');
            if (event.target === pwdModal) {
                closePWDLossModal();
            }
        });

        // Boticab Loss Modal Functions
        function openBoticabLossModal() {
            document.getElementById('boticabLossModal').style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        }

        function closeBoticabLossModal() {
            document.getElementById('boticabLossModal').style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            // Reset form
            document.getElementById('boticabLossForm').reset();
        }

        // Save Boticab Loss function
        function saveBoticabLoss() {
            const form = document.getElementById('boticabLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                fullAddress: formData.get('fullAddress'),
                detailsOfLoss: formData.get('detailsOfLoss'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.fullAddress || !data.detailsOfLoss || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const saveBtn = document.querySelector('button[onclick="saveBoticabLoss()"]');
            const originalText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
            saveBtn.disabled = true;

            // Simulate saving (replace with actual API call)
            setTimeout(() => {
                saveBtn.innerHTML = originalText;
                saveBtn.disabled = false;
                alert('Document saved successfully!');
            }, 1500);
        }

        // View Boticab Loss function
        function viewBoticabLoss() {
            const form = document.getElementById('boticabLossForm');
            const formData = new FormData(form);
            const data = {
                fullName: formData.get('fullName'),
                fullAddress: formData.get('fullAddress'),
                detailsOfLoss: formData.get('detailsOfLoss'),
                dateOfNotary: formData.get('dateOfNotary')
            };

            // Validate required fields
            if (!data.fullName || !data.fullAddress || !data.detailsOfLoss || !data.dateOfNotary) {
                alert('Please fill in all required fields.');
                return;
            }

            // Show loading state
            const viewBtn = document.querySelector('button[onclick="viewBoticabLoss()"]');
            const originalText = viewBtn.innerHTML;
            viewBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
            viewBtn.disabled = true;

            // Simulate viewing (replace with actual API call)
            setTimeout(() => {
                viewBtn.innerHTML = originalText;
                viewBtn.disabled = false;
                
                // Open document in modal for viewing only (no download)
                openDocumentViewer('files-generation/generate_affidavit_of_loss_boticab.php', 'boticabLossForm');
                window.currentFormType = 'boticabLoss';
            }, 1500);
        }

        // Close Boticab Loss modal when clicking outside
        window.addEventListener('click', function(event) {
            const boticabModal = document.getElementById('boticabLossModal');
            if (event.target === boticabModal) {
                closeBoticabLossModal();
            }
        });
    </script>

    <!-- Document Viewer Modal -->
    <div id="documentViewerModal" class="modal" style="z-index: 9999;">
        <div class="modal-content" style="width: 55vw !important; height: 75vh !important; max-width: none !important; max-height: none !important; margin: 12.5vh auto !important; position: fixed !important; top: 0 !important; left: 50% !important; transform: translateX(-50%) !important; border-radius: 12px !important; overflow: hidden !important; box-shadow: 0 25px 80px rgba(0, 0, 0, 0.4) !important; z-index: 10000 !important;">
            <div class="modal-header" style="padding: 10px 20px; border-bottom: 1px solid #ddd; background: #f8f9fa; display: flex; align-items: center; justify-content: space-between; height: 50px;">
                <h2 style="margin: 0; font-size: 18px;">Document Preview</h2>
                <div style="display: flex; align-items: center; gap: 10px;">
                    <button onclick="editDocument()" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px; background: #007bff;">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button onclick="sendDocument()" class="btn btn-primary" style="padding: 8px 16px; font-size: 14px; background: #28a745;">
                        <i class="fas fa-paper-plane"></i> Send
                    </button>
                    <span class="close" onclick="closeDocumentViewer()" style="font-size: 24px; font-weight: bold; cursor: pointer; color: #aaa;">&times;</span>
                </div>
            </div>
            <div class="modal-body" style="padding: 0; height: calc(75vh - 50px); overflow: hidden; background: white;">
                <div id="documentContent" style="width: 100%; height: 100%; border: none; background: white; padding: 0; box-sizing: border-box; overflow-y: auto;">
                    <!-- Document will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Document Preview Generation Function
        function generateDocumentPreview(data) {
            const today = new Date();
            const formattedDate = today.toLocaleDateString('en-US', { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            });

            return `
                <div style="max-width: 800px; margin: 0 auto; line-height: 1.6; font-size: 12pt;">
                    <div style="text-align: center; margin-bottom: 30px;">
                        <div style="font-size: 11pt; margin-bottom: 20px;">
                            REPUBLIC OF THE PHILIPPINES)<br/>
                            PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>S.S</strong><br/>
                            CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
                        </div>
                        
                        <div style="font-size: 14pt; font-weight: bold; margin-bottom: 30px;">
                            AFFIDAVIT OF LOSS
                        </div>
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        I, <strong>${data.fullName}</strong>, of legal age, Filipino, and residing at <strong>${data.completeAddress}</strong>, after having been duly sworn to in accordance with law, hereby depose and say:
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        That I am the lawful owner of <strong>${data.specifyItemLost}</strong> described as follows:
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        <strong>${data.itemLost}</strong>
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        That the said <strong>${data.itemLost}</strong> was lost on <strong>${data.itemDetails}</strong>;
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        That I have exerted all efforts to locate the same but to no avail;
                    </div>

                    <div style="text-align: justify; margin-bottom: 20px;">
                        That I am executing this affidavit to attest to the truth of the foregoing and for whatever legal purpose it may serve.
                    </div>

                    <div style="text-align: justify; margin-bottom: 30px;">
                        IN WITNESS WHEREOF, I have hereunto set my hand this <strong>${data.dateOfNotary}</strong> at Cabuyao City, Laguna.
                    </div>

                    <div style="text-align: center; margin-top: 50px;">
                        <div style="margin-bottom: 50px;">
                            <strong>${data.fullName}</strong><br/>
                            Affiant
                        </div>
                        
                        <div style="margin-top: 30px;">
                            <div style="border-bottom: 1px solid black; width: 200px; margin: 0 auto;"></div>
                            <div style="margin-top: 5px;">
                                Notary Public<br/>
                                Until December 31, 2025<br/>
                                PTR No. 1234567 / ${today.getFullYear()}<br/>
                                IBP No. 123456 / ${today.getFullYear()}<br/>
                                Roll No. 12345<br/>
                                MCLE Compliance No. 1234567
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        // Document Viewer Modal Functions
        function openDocumentViewer(documentUrl, formId) {
            const modal = document.getElementById('documentViewerModal');
            const documentContent = document.getElementById('documentContent');
            
            // Show loading
            documentContent.innerHTML = '<div style="display: flex; justify-content: center; align-items: center; height: 100%; background: #f8f9fa;"><div style="text-align: center;"><i class="fas fa-spinner fa-spin fa-3x" style="color: #007bff; margin-bottom: 20px;"></i><p style="color: #666; font-size: 16px;">Generating document...</p></div></div>';
            
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden'; // Prevent scrolling
            
            // Generate the actual PDF and display it
            setTimeout(() => {
                const form = document.getElementById(formId);
                const formData = new FormData(form);
                
                // Get form data
                const data = {
                    fullName: formData.get('fullName') || '',
                    completeAddress: formData.get('completeAddress') || '',
                    specifyItemLost: formData.get('specifyItemLost') || '',
                    itemLost: formData.get('itemLost') || '',
                    itemDetails: formData.get('itemDetails') || '',
                    dateOfNotary: formData.get('dateOfNotary') || ''
                };

                // Create URL with form data
                const params = new URLSearchParams(data);
                const pdfUrl = `${documentUrl}?${params.toString()}&view_only=1`;
                
                // Display PDF using embedded viewer
                documentContent.innerHTML = `
                    <iframe src="${pdfUrl}" 
                            style="width: 100%; height: 100%; border: none; background: white;" 
                            sandbox="allow-same-origin allow-scripts"
                            oncontextmenu="return false;"
                            onselectstart="return false;">
                    </iframe>
                `;
            }, 1000);
        }

        function closeDocumentViewer() {
            const modal = document.getElementById('documentViewerModal');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto'; // Restore scrolling
            
            // Clear content
            document.getElementById('documentContent').innerHTML = '';
        }

        // Edit Document Function
        function editDocument() {
            // Close the document viewer modal
            closeDocumentViewer();
            
            // Get the current form type from the global variable
            if (window.currentFormType) {
                // Open the appropriate form modal based on the current form type
                switch(window.currentFormType) {
                    case 'affidavitLoss':
                        openAffidavitLossModal();
                        break;
                    case 'soloParent':
                        openSoloParentModal();
                        break;
                    case 'pwdLoss':
                        openPWDLossModal();
                        break;
                    case 'boticabLoss':
                        openBoticabLossModal();
                        break;
                    default:
                        alert('Form type not recognized');
                }
            } else {
                alert('No form data available to edit');
            }
        }


        // Send Document Function
        function sendDocument() {
            // Show confirmation dialog
            if (confirm('Are you sure you want to send this document to the employee? This action cannot be undone.')) {
                // Show loading state
                const sendBtn = document.querySelector('button[onclick="sendDocument()"]');
                const originalText = sendBtn.innerHTML;
                sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                sendBtn.disabled = true;

                // Get the current form data
                let formData = {};
                let formType = '';
                
                if (window.currentFormType) {
                    formType = window.currentFormType;
                    
                    // Get form data based on current form type
                    switch(window.currentFormType) {
                        case 'affidavitLoss':
                            const affidavitForm = document.getElementById('affidavitLossForm');
                            if (affidavitForm) {
                                const form = new FormData(affidavitForm);
                                formData = {
                                    fullName: form.get('fullName'),
                                    completeAddress: form.get('completeAddress'),
                                    specifyItemLost: form.get('specifyItemLost'),
                                    itemLost: form.get('itemLost'),
                                    itemDetails: form.get('itemDetails'),
                                    dateOfNotary: form.get('dateOfNotary')
                                };
                            }
                            break;
                        case 'soloParent':
                            const soloForm = document.getElementById('soloParentForm');
                            if (soloForm) {
                                const form = new FormData(soloForm);
                                formData = {
                                    fullName: form.get('fullName'),
                                    completeAddress: form.get('completeAddress'),
                                    childrenNames: form.get('childrenNames'),
                                    yearsUnderCase: form.get('yearsUnderCase'),
                                    reasonSection: form.get('reasonSection'),
                                    otherReason: form.get('otherReason'),
                                    employmentStatus: form.get('employmentStatus'),
                                    employeeAmount: form.get('employeeAmount'),
                                    selfEmployedAmount: form.get('selfEmployedAmount'),
                                    unemployedDependent: form.get('unemployedDependent'),
                                    dateOfNotary: form.get('dateOfNotary')
                                };
                            }
                            break;
                        case 'pwdLoss':
                            const pwdForm = document.getElementById('pwdLossForm');
                            if (pwdForm) {
                                const form = new FormData(pwdForm);
                                formData = {
                                    fullName: form.get('fullName'),
                                    fullAddress: form.get('fullAddress'),
                                    detailsOfLoss: form.get('detailsOfLoss'),
                                    dateOfNotary: form.get('dateOfNotary')
                                };
                            }
                            break;
                        case 'boticabLoss':
                            const boticabForm = document.getElementById('boticabLossForm');
                            if (boticabForm) {
                                const form = new FormData(boticabForm);
                                formData = {
                                    fullName: form.get('fullName'),
                                    fullAddress: form.get('fullAddress'),
                                    detailsOfLoss: form.get('detailsOfLoss'),
                                    dateOfNotary: form.get('dateOfNotary')
                                };
                            }
                            break;
                    }
                }

                // Debug: Log what we're sending
                console.log('Sending document:', {
                    formType: formType,
                    formData: formData
                });

                // Send data to server
                fetch('send_document_handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'form_type=' + encodeURIComponent(formType) + '&form_data=' + encodeURIComponent(JSON.stringify(formData))
                })
                .then(response => response.json())
                .then(result => {
                    sendBtn.innerHTML = originalText;
                    sendBtn.disabled = false;
                    
                    // Debug: Log server response
                    console.log('Server response:', result);
                    
                    if (result.status === 'success') {
                        alert('Document sent successfully to the employee!');
                        closeDocumentViewer();
                    } else {
                        alert('Error: ' + result.message);
                        console.error('Error details:', result.debug_info);
                    }
                })
                .catch(error => {
                    sendBtn.innerHTML = originalText;
                    sendBtn.disabled = false;
                    console.error('Error:', error);
                    alert('Error sending document. Please try again.');
                });
            }
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('documentViewerModal');
            if (event.target === modal) {
                closeDocumentViewer();
            }
        });
    </script>
</body>
</html> 