<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Get form data from URL parameters
$fullName = isset($_GET['fullName']) ? htmlspecialchars($_GET['fullName']) : '';
$fullAddress = isset($_GET['fullAddress']) ? htmlspecialchars($_GET['fullAddress']) : '';
$detailsOfLoss = isset($_GET['detailsOfLoss']) ? htmlspecialchars($_GET['detailsOfLoss']) : '';
$dateOfNotary = isset($_GET['dateOfNotary']) ? htmlspecialchars($_GET['dateOfNotary']) : '';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Affidavit of Loss (Boticab Booklet/ID)');
$pdf->SetSubject('Affidavit of Loss (Boticab Booklet/ID)');

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
        AFFIDAVIT OF LOSS
    </div>
    <br/>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, <u><b>{$fullName}</b></u>, Filipino, of legal age, and with residence and <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; post office address at <u><b>{$fullAddress}</b></u>, after <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; being duly sworn in accordance with law hereby depose and say that:
    </div>
    <br>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. &nbsp;That I am the lawful holder of a Boticab Booklet/ID issued by the<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; appropriate government agency;
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. &nbsp;That the said Boticab Booklet/ID was lost under the following<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; circumstances:<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$detailsOfLoss}</b></u><br>
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. &nbsp;That I have exerted diligent efforts to locate and recover the said<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Boticab Booklet/ID but to no avail;
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. &nbsp;That I am executing this affidavit to attest the truth of the foregoing<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; facts and for whatever intents it may serve in accordance with law;
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. &nbsp;That I am executing this affidavit to support my application for the<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; replacement of my lost Boticab Booklet/ID.
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
$pdf->Output('Affidavit_of_Loss_Boticab_Booklet_ID.pdf', 'I');
?>