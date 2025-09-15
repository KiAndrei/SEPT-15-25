<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Get form data from URL parameters
$fullName = isset($_GET['fullName']) ? htmlspecialchars($_GET['fullName']) : '';
$completeAddress = isset($_GET['completeAddress']) ? htmlspecialchars($_GET['completeAddress']) : '';
$specifyItemLost = isset($_GET['specifyItemLost']) ? htmlspecialchars($_GET['specifyItemLost']) : '';
$itemLost = isset($_GET['itemLost']) ? htmlspecialchars($_GET['itemLost']) : '';
$itemDetails = isset($_GET['itemDetails']) ? htmlspecialchars($_GET['itemDetails']) : '';
$dateOfNotary = isset($_GET['dateOfNotary']) ? htmlspecialchars($_GET['dateOfNotary']) : '';

// Check if this is view-only mode
$viewOnly = isset($_GET['view_only']) && $_GET['view_only'] == '1';

if ($viewOnly) {
    // Output HTML version for viewing
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Affidavit of Loss - Preview</title>
        <style>
            body {
                font-family: 'Times New Roman', serif;
                font-size: 12pt;
                line-height: 1.8;
                margin: 0;
                padding: 0;
                background: white;
                color: black;
                width: 100%;
                min-height: 100vh;
                box-sizing: border-box;
            }
            .document {
                width: 100%;
                background: white;
                color: black;
                min-height: 100%;
                padding: 20px 30px;
                font-family: 'Times New Roman', serif;
                font-size: 11pt;
                line-height: 1.2;
                box-sizing: border-box;
            }
            .underline {
                text-decoration: underline;
                font-weight: bold;
            }
            .spacing {
                margin-bottom: 15px;
                display: block;
            }
            .indent {
                margin-left: 40px;
                display: block;
            }
            .center {
                text-align: center;
                display: block;
            }
            .justify {
                text-align: justify;
                display: block;
            }
        </style>
    </head>
    <body>
        <div class="document" style="font-size:11pt; line-height:1.2; padding: 20px 30px;">
            <br/>
            
            <div style="margin-top:10px;">
                REPUBLIC OF THE PHILIPPINES)<br/>&nbsp;
                PROVINCE OF LAGUNA;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>S.S</b><br/>&nbsp;
                CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/>
            </div>
            <br/>
            
            <div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:-15px 0;">
                AFFIDAVIT OF LOSS
            </div>
            <br/>
            
            <div style="text-align:justify; margin-bottom:15px;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, <u><b><?= $fullName ?: '[FULL NAME]' ?></b></u>, Filipino, of legal age, and with residence and <br/>
                post office address at <u><b><?= $completeAddress ?: '[COMPLETE ADDRESS]' ?></b></u>, after <br/>
                being duly sworn in accordance with law hereby depose and say that:
            </div>
            <br/>
            
                    <div style="margin-left: 40px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. &nbsp;That &nbsp;&nbsp;&nbsp; I &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; am &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; the &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; true &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; and lawful owner/possessor of <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b><?= $specifyItemLost ?: '[SPECIFY ITEM LOST]' ?></b></u>;<br>
                    </div>
                    <br/>    
                
                
                
                <div style="margin-left: 40px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. &nbsp;That unfortunately the said <u><b><?= $itemLost ?: '[ITEM LOST]' ?></b></u> was lost under the following<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; circumstance:<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b><?= $itemDetails ?: '[ITEM DETAILS]' ?></b></u><br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________;
                </div>
                <br/>
                
                
                <div style="margin-left: 40px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. &nbsp;&nbsp;Despite diligent effort to search for the missing item, the same can no longer <br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;be found;
                </div>
                <br/>
                
                
                <div style="margin-left: 40px;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. &nbsp;&nbsp;I am executing this affidavit to attest the truth of the foregoing facts and<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for whatever intents it may serve in accordance with law.
                </div>
                <br/>
                <br>
            
            
            <div style="text-align:justify; margin-bottom:15px;">
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, &nbsp; I &nbsp; have &nbsp; hereunto &nbsp; set my hand this ________ day of<br/>
                <u><b><?= $dateOfNotary ?: '[DATE OF NOTARY]' ?></b></u>, in the City of Cabuyao, Laguna.
            </div>
            
            <br/>
            <div style="text-align:center; margin:15px 0;">
                <u><b><?= $fullName ?: '[FULL NAME]' ?></b></u><br/>
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
    </body>
    </html>
    <?php
    exit;
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Affidavit of Loss');
$pdf->SetSubject('Affidavit of Loss');

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

// Exact spacing to match the image precisely
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
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; post office address at <u><b>{$completeAddress}</b></u>, after <br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; being duly sworn in accordance with law hereby depose and say that:
    </div>
    <br>
    
        
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. &nbsp;That &nbsp;&nbsp;&nbsp; I &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; am &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; the &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; true &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; and lawful owner/possessor of <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$specifyItemLost}</b></u>;<br>
            
        
        
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. &nbsp;That unfortunately the said <u><b>{$itemLost}</b></u> was lost under the following<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; circumstance:<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <u><b>{$itemDetails}</b></u><br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ________________________________________________________________;
        </div>
        
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. &nbsp;&nbsp;Despite diligent effort to search for the missing item, the same can no longer <br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;be found;
        </div>
        <br>
        
        
        
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. &nbsp;&nbsp;I am executing this affidavit to attest the truth of the foregoing facts and<br>
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for whatever intents it may serve in accordance with law.
        <br/>
    
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, &nbsp; I &nbsp; have &nbsp; hereunto &nbsp; set my hand this ________ day of<br/>
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

// Output PDF
$pdf->Output('Affidavit_of_Loss.pdf', 'D'); 