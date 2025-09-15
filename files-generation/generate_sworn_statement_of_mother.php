<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Sworn Statement of Mother');
$pdf->SetSubject('Sworn Statement of Mother');

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

// Sworn Statement of Mother content
$html = <<<EOD
<div style="font-size:11pt; line-height:1.2;">
    <br/>
    
    <div style="margin-top:10px;">
        REPUBLIC OF THE PHILIPPINES&nbsp;&nbsp;&nbsp;)<br/>&nbsp;
        PROVINCE OF LAGUNA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<br>
        CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;S.S
    </div>
    <br>
    
    <div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:15px;">
        SWORN STATEMENT OF MOTHER
    </div>
    <br>
    <br>
    
    
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;I, &nbsp;_____________________________________, &nbsp;of legal age, &nbsp;Filipino, &nbsp;single &nbsp;and &nbsp;with<br/>
        residence and postal address at _____________________________________________________<br>
after being duly sworn in accordance with law, hereby depose and say that;
    <br>
    <br>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That I am the biological mother of _____________________________________,<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;who &nbsp;&nbsp; was &nbsp; born &nbsp; out &nbsp; of &nbsp; wedlock &nbsp; on __________________________________ &nbsp;&nbsp;at<br> 
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_____________________________________;
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. The biological father of my child is ____________________________;
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. That the birth of the above named child was not registered in the City Civil<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Registry of _________________________ City, due to negligence on our part.
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. That I am now taking the appropriate action to register the birth of my said<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;child.
    </div>
    
    <div style="margin-left: 40px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. I am executing this affidavit to attest to the truth of the foregoing facts and<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;be use for whatever legal purpose it may serve.
    </div>
    <br>
    
    <div style="margin-left: 60px;">
IN WITNESS WHEREOF, I have hereunto set my hands this
        __________________________,  in <br>
the City of Cabuyao, Laguna.
        <br>
        <br>
    
    <div style="text-align:center; margin:15px 0;">
        ____________________________<br/>
        <b>AFFIANT</b>
    </div>
    
    <br>
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUBCRIBED AND SWORN to before me this ____________________________ at the City of Cabuyao, Laguna affiant exhibiting to me her ____________________________ as respective proof of identity.
    </div>
    <br>
    
    <div style="text-align:left; margin-left: -5px;">
Doc. No. _______<br/>
        Book No. _______<br/>
        Page No. _______<br/>
        Series of _______
    </div>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('Sworn_Statement_of_Mother.pdf', 'D'); 