<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Affidavit of Loss (Senior ID)');
$pdf->SetSubject('Affidavit of Loss (Senior ID)');

// Set default header data
$pdf->SetHeaderData('', 0, '', '');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins
$pdf->SetMargins(28, 5, 28);
$pdf->SetAutoPageBreak(FALSE);

// Set font
$pdf->SetFont('times', '', 11);

// Add a page
$pdf->AddPage();

// Affidavit of Loss (Senior ID) content
$html = <<<EOD
<div style="font-size:11pt; line-height:1.2;">
    <br/>
    
    <div style="margin-top:10px;">
        REPUBLIC OF THE PHILIPPINES)<br/>&nbsp;
        PROVINCE OF LAGUNA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;S.S<br>
        CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
    </div>
    
    <br>
    <div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:15px;">
        AFFIDAVIT OF LOSS<br/>
        (SENIOR ID)
    </div>
    <br>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, &nbsp;_______________________________________, &nbsp;&nbsp;Filipino, &nbsp;&nbsp;of &nbsp;&nbsp;legal &nbsp;&nbsp;age, &nbsp;&nbsp;and &nbsp;&nbsp;with<br/>
        residence &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; and &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; currently &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; residing &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; at<br>
____________________________________________________________after having been sworn<br/>
        in accordance with law hereby depose and state:
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That I am the _________________________ of __________________________, who is<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the lawful owner of a Senior Citizen ID issued by OSCA-Cabuyao;
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. That &nbsp; &nbsp; unfortunately &nbsp;&nbsp; the &nbsp;&nbsp; said &nbsp;&nbsp; Senior &nbsp;&nbsp; ID &nbsp;&nbsp;&nbsp; was &nbsp;&nbsp;&nbsp; lost &nbsp;&nbsp;&nbsp; under &nbsp;&nbsp;&nbsp; the &nbsp;&nbsp;&nbsp; following<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;circumstance:<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________________________________________________<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________________________________________________<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;______________________________________________________________________
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. That &nbsp; despite &nbsp; diligent &nbsp; efforts &nbsp; to &nbsp; retrieve &nbsp; the &nbsp; said &nbsp; Senior ID, &nbsp; the &nbsp; same  &nbsp; can  no<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;longer be restored and therefore considered lost;
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. I &nbsp; am &nbsp; executing &nbsp; this &nbsp; affidavit &nbsp; to &nbsp; attest &nbsp; to &nbsp; the &nbsp; truth  &nbsp; of &nbsp; the foregoing facts and<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;for whatever legal intents and purposes whatever legal intents and<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;purposes.
    </div>
    
    <div style="margin-left: 40px; margin-top:15px;">
AFFIANT FURTHER SAYETH NAUGHT.
    </div>
    
    <div style="margin-left: 40px; margin-top:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN &nbsp;&nbsp;&nbsp;&nbsp; WITNESS  &nbsp;&nbsp;&nbsp;&nbsp; WHEREOF, &nbsp;&nbsp;&nbsp;&nbsp; I &nbsp;&nbsp;&nbsp;&nbsp; have &nbsp;&nbsp;&nbsp;&nbsp; hereunto &nbsp;&nbsp;&nbsp;&nbsp; set &nbsp;&nbsp;&nbsp;&nbsp; my &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; hand &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; this<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________________ in the City of Cabuyao, Laguna.
        <br>
    
    <div style="text-align:center; margin:15px 0;">
        ____________________________<br/>
        <b>AFFIANT</b>
    </div>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUBSCRIBED &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; AND &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; SWORN &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; to &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; before &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; me, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; this<br> 
______________________________ in the City of Cabuyao, Laguna, affiant exhibiting<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to me his/her ____________________________ as valid proof of identification.
    </div>
    <br>

    <div style="text-align:left; margin-left: -5px;">
Doc. No. _______<br/>
        Page No. _______<br/>
        Book No. _______<br/>
        Series of _______
    </div>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('Affidavit_of_Loss_Senior_ID.pdf', 'D'); 