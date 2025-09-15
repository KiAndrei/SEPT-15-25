<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Get form data from URL parameters
$fullName = isset($_GET['fullName']) ? htmlspecialchars($_GET['fullName']) : '';
$completeAddress = isset($_GET['completeAddress']) ? htmlspecialchars($_GET['completeAddress']) : '';
$childrenNames = isset($_GET['childrenNames']) ? htmlspecialchars($_GET['childrenNames']) : '';
$yearsUnderCase = isset($_GET['yearsUnderCase']) ? htmlspecialchars($_GET['yearsUnderCase']) : '';
$reasonSection = isset($_GET['reasonSection']) ? htmlspecialchars($_GET['reasonSection']) : '';
$otherReason = isset($_GET['otherReason']) ? htmlspecialchars($_GET['otherReason']) : '';
$employmentStatus = isset($_GET['employmentStatus']) ? htmlspecialchars($_GET['employmentStatus']) : '';
$employeeAmount = isset($_GET['employeeAmount']) ? htmlspecialchars($_GET['employeeAmount']) : '';
$selfEmployedAmount = isset($_GET['selfEmployedAmount']) ? htmlspecialchars($_GET['selfEmployedAmount']) : '';
$unemployedDependent = isset($_GET['unemployedDependent']) ? htmlspecialchars($_GET['unemployedDependent']) : '';
$dateOfNotary = isset($_GET['dateOfNotary']) ? htmlspecialchars($_GET['dateOfNotary']) : '';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Sworn Affidavit of Solo Parent');
$pdf->SetSubject('Sworn Affidavit of Solo Parent');

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

// Generate the HTML content
$html = <<<EOD
<div style="font-size:11pt; line-height:1.2;">
    <br/>
    
    <div style="margin-top:10px;">
        REPUBLIC OF THE PHILIPPINES)<br/>&nbsp;
        PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>S.S</b><br/>&nbsp;
        CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
    </div>
    
    <div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:-15px 0;">
        SWORN AFFIDAVIT OF SOLO PARENT
    </div>
    <br/>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, <u><b>{$fullName}</b></u>, Filipino, of legal age, and with residence and <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; post office address at <u><b>{$completeAddress}</b></u>, after <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; being duly sworn in accordance with law hereby depose and say that:
    </div>
    <br>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. &nbsp;That I am the parent of the following child/children:<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$childrenNames}</b></u><br>
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. &nbsp;That I have been a solo parent for <u><b>{$yearsUnderCase}</b></u> years under the following<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; circumstance:<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$reasonSection}</b></u><br>
EOD;

// Add other reason if selected
if ($reasonSection === 'Other reason, please state' && !empty($otherReason)) {
    $html .= <<<EOD
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$otherReason}</b></u><br>
EOD;
}

$html .= <<<EOD
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. &nbsp;That my employment status is as follows:<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$employmentStatus}</b></u><br>
EOD;

// Add specific employment details
if ($employmentStatus === 'Employee and earning') {
    $html .= <<<EOD
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Monthly Income: <u><b>{$employeeAmount}</b></u><br>
EOD;
} elseif ($employmentStatus === 'Self-employed and earning') {
    $html .= <<<EOD
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Monthly Income: <u><b>{$selfEmployedAmount}</b></u><br>
EOD;
} elseif ($employmentStatus === 'Un-employed and dependent upon') {
    $html .= <<<EOD
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Dependent upon: <u><b>{$unemployedDependent}</b></u><br>
EOD;
}

$html .= <<<EOD
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. &nbsp;&nbsp;I am executing this affidavit to attest the truth of the foregoing facts and<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for whatever intents it may serve in accordance with law.
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, I have hereunto set my hand this<br/>
        <u><b>{$dateOfNotary}</b></u>, in the City of Cabuyao, Laguna.
    </div>
    
    <br/>
    <div style="text-align:center; margin:15px 0;">
        <u><b>{$fullName}</b></u><br/>
        <b>AFFIANT</b>
    </div>
    
    <br/>
    <div style="text-align:justify; margin-bottom:15px;">
SUBSCRIBED AND SWORN TO before me this date above mentioned at the City of <br>
Cabuyao, Laguna, affiant exhibiting to me his/her respective proofs of identity, <br>
indicated below their names personally attesting that the foregoing statements is true <br>
to their best of knowledge and belief.
    </div>
    
    <br/>
    <div style="text-align:left; margin-left: -5px;">
Doc. No._______<br/>
        Page No._______<br/>
        Book No._______<br/>
        Series of _______
    </div>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF for viewing (not download)
$pdf->Output('Sworn_Affidavit_of_Solo_Parent.pdf', 'I');
?>
