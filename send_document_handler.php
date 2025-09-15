<?php
require_once 'session_manager.php';
require_once 'config.php';
require_once 'audit_logger.php';
require_once __DIR__ . '/vendor/autoload.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Validate user access
validateUserAccess('client');
$client_id = $_SESSION['user_id'];

// Get client name for logging
$stmt = $conn->prepare("SELECT name FROM user_form WHERE id = ?");
$stmt->bind_param("i", $client_id);
$stmt->execute();
$res = $stmt->get_result();
$client_name = '';
if ($res && $row = $res->fetch_assoc()) {
    $client_name = $row['name'];
}

// Get form data
$form_type = $_POST['form_type'] ?? '';
$form_data = $_POST['form_data'] ?? [];

// Debug logging
error_log("Document Handler Debug - Form Type: " . $form_type);
error_log("Document Handler Debug - Form Data: " . print_r($form_data, true));

if (empty($form_type) || empty($form_data)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// Generate unique request ID
$request_id = 'DOC_' . date('Ymd') . '_' . str_pad($client_id, 4, '0', STR_PAD_LEFT) . '_' . rand(1000, 9999);

// Prepare data for database insertion
$full_name = $form_data['fullName'] ?? '';
$address = $form_data['completeAddress'] ?? $form_data['fullAddress'] ?? '';
$gender = $form_data['gender'] ?? '';

// Generate the actual PDF file
$pdf_filename = '';
$pdf_path = '';

try {
    // Create PDF based on document type
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('Opiña Law Office');
    $pdf->SetAuthor('Opiña Law Office');
    $pdf->SetTitle($form_type);
    $pdf->SetSubject($form_type);
    
    // Set default header data
    $pdf->SetHeaderData('', '', '', '');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(28, 5, 28);
    $pdf->SetAutoPageBreak(FALSE);
    
    // Set font
    $pdf->SetFont('times', '', 11);
    
    // Add a page
    $pdf->AddPage();
    
    // Generate HTML content based on document type
    $html = generateDocumentHTML($form_type, $form_data);
    
    $pdf->writeHTML($html, true, false, true, false, '');
    
    // Generate filename and path
    $pdf_filename = $form_type . '_' . $request_id . '.pdf';
    $pdf_path = 'uploads/documents/' . $pdf_filename;
    
    // Create uploads directory if it doesn't exist
    if (!file_exists('uploads/documents/')) {
        mkdir('uploads/documents/', 0755, true);
    }
    
    // Save PDF to file
    $pdf->Output($pdf_path, 'F');
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to generate PDF: ' . $e->getMessage()]);
    exit;
}

// First, add the new columns if they don't exist
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS document_type VARCHAR(100) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS document_data TEXT DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS pdf_file_path VARCHAR(500) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS pdf_filename VARCHAR(255) DEFAULT NULL");
$conn->query("ALTER TABLE client_request_form ADD COLUMN IF NOT EXISTS submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");

// Insert document request into database
$stmt = $conn->prepare("
    INSERT INTO client_request_form 
    (request_id, client_id, full_name, address, gender, valid_id_front_path, valid_id_front_filename, valid_id_back_path, valid_id_back_filename, privacy_consent, status, document_type, document_data, pdf_file_path, pdf_filename, submitted_at) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1, 'Pending', ?, ?, ?, ?, NOW())
");

$document_type = $form_type;
$document_data = json_encode($form_data);

$stmt->bind_param("sisssssssssss", 
    $request_id, 
    $client_id, 
    $full_name, 
    $address, 
    $gender, 
    $pdf_path, // Use PDF path for front ID
    $pdf_filename, // Use PDF filename for front ID filename
    $pdf_path, // Use PDF path for back ID
    $pdf_filename, // Use PDF filename for back ID filename
    $document_type,
    $document_data,
    $pdf_path,
    $pdf_filename
);

if ($stmt->execute()) {
    // Debug logging
    error_log("Document Handler Debug - Database insertion successful for request ID: " . $request_id);
    
    // Log to audit trail
    global $auditLogger;
    $auditLogger->logAction(
        $client_id,
        $client_name,
        'client',
        'Document Submission',
        'Document Generation',
        "Submitted $form_type document with request ID: $request_id",
        'success',
        'medium'
    );
    
    echo json_encode([
        'status' => 'success', 
        'message' => 'Document sent successfully!',
        'request_id' => $request_id,
        'pdf_path' => $pdf_path,
        'debug_info' => [
            'client_id' => $client_id,
            'form_type' => $form_type,
            'pdf_filename' => $pdf_filename,
            'document_type' => $document_type
        ]
    ]);
} else {
    // Debug logging
    error_log("Document Handler Debug - Database insertion failed: " . $conn->error);
    
    echo json_encode([
        'status' => 'error', 
        'message' => 'Failed to send document. Please try again.',
        'debug_info' => [
            'error' => $conn->error,
            'client_id' => $client_id,
            'form_type' => $form_type
        ]
    ]);
}

// Function to generate HTML content based on document type
function generateDocumentHTML($form_type, $form_data) {
    switch($form_type) {
        case 'affidavitLoss':
            return generateAffidavitLossHTML($form_data);
        case 'soloParent':
            return generateSoloParentHTML($form_data);
        case 'pwdLoss':
            return generatePWDLossHTML($form_data);
        case 'boticabLoss':
            return generateBoticabLossHTML($form_data);
        default:
            return '<p>Unknown document type</p>';
    }
}

function generateAffidavitLossHTML($data) {
    $today = new DateTime();
    return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='font-size: 11pt; margin-bottom: 20px;'>
                REPUBLIC OF THE PHILIPPINES)<br/>
                PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>S.S</strong><br/>
                CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
            </div>
            
            <div style='font-size: 14pt; font-weight: bold; margin-bottom: 30px;'>
                AFFIDAVIT OF LOSS
            </div>
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            I, <strong>{$data['fullName']}</strong>, of legal age, Filipino, and residing at <strong>{$data['completeAddress']}</strong>, after having been duly sworn to in accordance with law, hereby depose and say:
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am the lawful owner of <strong>{$data['specifyItemLost']}</strong> described as follows:
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            <strong>{$data['itemLost']}</strong>
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That the said <strong>{$data['itemLost']}</strong> was lost on <strong>{$data['itemDetails']}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I have exerted all efforts to locate the same but to no avail;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am executing this affidavit to attest to the truth of the foregoing and for whatever legal purpose it may serve.
        </div>

        <div style='text-align: justify; margin-bottom: 30px;'>
            IN WITNESS WHEREOF, I have hereunto set my hand this <strong>{$data['dateOfNotary']}</strong> at Cabuyao City, Laguna.
        </div>

        <div style='text-align: center; margin-top: 50px;'>
            <div style='margin-bottom: 50px;'>
                <strong>{$data['fullName']}</strong><br/>
                Affiant
            </div>
            
            <div style='margin-top: 30px;'>
                <div style='border-bottom: 1px solid black; width: 200px; margin: 0 auto;'></div>
                <div style='margin-top: 5px;'>
                    Notary Public<br/>
                    Until December 31, 2025<br/>
                    PTR No. 1234567 / {$today->format('Y')}<br/>
                    IBP No. 123456 / {$today->format('Y')}<br/>
                    Roll No. 12345<br/>
                    MCLE Compliance No. 1234567
                </div>
            </div>
        </div>
    ";
}

function generateSoloParentHTML($data) {
    $today = new DateTime();
    $reason = $data['reasonSection'] ?? '';
    if ($reason === 'Other reason, please state') {
        $reason = $data['otherReason'] ?? '';
    }
    
    $employment = $data['employmentStatus'] ?? '';
    $income = '';
    if ($employment === 'Employee and earning') {
        $income = $data['employeeAmount'] ?? '';
    } elseif ($employment === 'Self-employed and earning') {
        $income = $data['selfEmployedAmount'] ?? '';
    } elseif ($employment === 'Un-employed and dependent upon') {
        $income = $data['unemployedDependent'] ?? '';
    }
    
    return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='font-size: 11pt; margin-bottom: 20px;'>
                REPUBLIC OF THE PHILIPPINES)<br/>
                PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>S.S</strong><br/>
                CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
            </div>
            
            <div style='font-size: 14pt; font-weight: bold; margin-bottom: 30px;'>
                SWORN AFFIDAVIT OF SOLO PARENT
            </div>
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            I, <strong>{$data['fullName']}</strong>, of legal age, Filipino, and residing at <strong>{$data['completeAddress']}</strong>, after having been duly sworn to in accordance with law, hereby depose and say:
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am a solo parent of the following child/children: <strong>{$data['childrenNames']}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I have been a solo parent for <strong>{$data['yearsUnderCase']}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That the reason for being a solo parent is: <strong>{$reason}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That my employment status is: <strong>{$employment}</strong>";
    
    if ($income) {
        $html .= "<br/>That my monthly income/dependency is: <strong>{$income}</strong>";
    }
    
    $html .= ";
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am executing this affidavit to attest to the truth of the foregoing and for whatever legal purpose it may serve.
        </div>

        <div style='text-align: justify; margin-bottom: 30px;'>
            IN WITNESS WHEREOF, I have hereunto set my hand this <strong>{$data['dateOfNotary']}</strong> at Cabuyao City, Laguna.
        </div>

        <div style='text-align: center; margin-top: 50px;'>
            <div style='margin-bottom: 50px;'>
                <strong>{$data['fullName']}</strong><br/>
                Affiant
            </div>
            
            <div style='margin-top: 30px;'>
                <div style='border-bottom: 1px solid black; width: 200px; margin: 0 auto;'></div>
                <div style='margin-top: 5px;'>
                    Notary Public<br/>
                    Until December 31, 2025<br/>
                    PTR No. 1234567 / {$today->format('Y')}<br/>
                    IBP No. 123456 / {$today->format('Y')}<br/>
                    Roll No. 12345<br/>
                    MCLE Compliance No. 1234567
                </div>
            </div>
        </div>
    ";
    
    return $html;
}

function generatePWDLossHTML($data) {
    $today = new DateTime();
    return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='font-size: 11pt; margin-bottom: 20px;'>
                REPUBLIC OF THE PHILIPPINES)<br/>
                PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>S.S</strong><br/>
                CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
            </div>
            
            <div style='font-size: 14pt; font-weight: bold; margin-bottom: 30px;'>
                AFFIDAVIT OF LOSS (PWD ID)
            </div>
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            I, <strong>{$data['fullName']}</strong>, of legal age, Filipino, and residing at <strong>{$data['fullAddress']}</strong>, after having been duly sworn to in accordance with law, hereby depose and say:
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am the lawful owner of a Person with Disability (PWD) ID card;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That the said PWD ID card was lost under the following circumstances: <strong>{$data['detailsOfLoss']}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I have exerted all efforts to locate the same but to no avail;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am executing this affidavit to attest to the truth of the foregoing and for whatever legal purpose it may serve.
        </div>

        <div style='text-align: justify; margin-bottom: 30px;'>
            IN WITNESS WHEREOF, I have hereunto set my hand this <strong>{$data['dateOfNotary']}</strong> at Cabuyao City, Laguna.
        </div>

        <div style='text-align: center; margin-top: 50px;'>
            <div style='margin-bottom: 50px;'>
                <strong>{$data['fullName']}</strong><br/>
                Affiant
            </div>
            
            <div style='margin-top: 30px;'>
                <div style='border-bottom: 1px solid black; width: 200px; margin: 0 auto;'></div>
                <div style='margin-top: 5px;'>
                    Notary Public<br/>
                    Until December 31, 2025<br/>
                    PTR No. 1234567 / {$today->format('Y')}<br/>
                    IBP No. 123456 / {$today->format('Y')}<br/>
                    Roll No. 12345<br/>
                    MCLE Compliance No. 1234567
                </div>
            </div>
        </div>
    ";
}

function generateBoticabLossHTML($data) {
    $today = new DateTime();
    return "
        <div style='text-align: center; margin-bottom: 30px;'>
            <div style='font-size: 11pt; margin-bottom: 20px;'>
                REPUBLIC OF THE PHILIPPINES)<br/>
                PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>S.S</strong><br/>
                CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
            </div>
            
            <div style='font-size: 14pt; font-weight: bold; margin-bottom: 30px;'>
                AFFIDAVIT OF LOSS (BOTICAB BOOKLET/ID)
            </div>
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            I, <strong>{$data['fullName']}</strong>, of legal age, Filipino, and residing at <strong>{$data['fullAddress']}</strong>, after having been duly sworn to in accordance with law, hereby depose and say:
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am the lawful owner of a Boticab booklet/ID card;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That the said Boticab booklet/ID card was lost under the following circumstances: <strong>{$data['detailsOfLoss']}</strong>;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I have exerted all efforts to locate the same but to no avail;
        </div>

        <div style='text-align: justify; margin-bottom: 20px;'>
            That I am executing this affidavit to attest to the truth of the foregoing and for whatever legal purpose it may serve.
        </div>

        <div style='text-align: justify; margin-bottom: 30px;'>
            IN WITNESS WHEREOF, I have hereunto set my hand this <strong>{$data['dateOfNotary']}</strong> at Cabuyao City, Laguna.
        </div>

        <div style='text-align: center; margin-top: 50px;'>
            <div style='margin-bottom: 50px;'>
                <strong>{$data['fullName']}</strong><br/>
                Affiant
            </div>
            
            <div style='margin-top: 30px;'>
                <div style='border-bottom: 1px solid black; width: 200px; margin: 0 auto;'></div>
                <div style='margin-top: 5px;'>
                    Notary Public<br/>
                    Until December 31, 2025<br/>
                    PTR No. 1234567 / {$today->format('Y')}<br/>
                    IBP No. 123456 / {$today->format('Y')}<br/>
                    Roll No. 12345<br/>
                    MCLE Compliance No. 1234567
                </div>
            </div>
        </div>
    ";
}
?>
