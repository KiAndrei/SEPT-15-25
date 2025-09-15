<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('JOINT AFFIDAVIT OF TWO DISINTERESTED PERSON (SOLO PARENT)');
$pdf->SetSubject('JOINT AFFIDAVIT OF TWO DISINTERESTED PERSON (SOLO PARENT)');

// Set default header data
$pdf->SetHeaderData('', 0, '', '');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins - reduced for single page
$pdf->SetMargins(28, 15, 28);
$pdf->SetAutoPageBreak(FALSE, 15);

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('times', '', 11);

// Joint Affidavit of Two Disinterested Person (Solo Parent) content with exact formatting from image
$html = <<<EOD
<div style="text-align:left; font-size:11pt;">
REPUBLIC OF THE PHILIPPINES)<br/>
    PROVINCE OF LAGUNA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) SS<br/>
    CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/><br/>
</div>

<div style="text-align:center; font-size:11pt; font-weight:bold;">
    JOINT AFFIDAVIT OF TWO DISINTERESTED PERSON<br/>(SOLO PARENT)
</div>
<br/>

<div style="text-align:left; font-size:11pt;">
WE, _____________________________ and _____________________________, Filipinos, both
    of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;legal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;age, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;permanent &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;residents &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of<br/>
    __________________________________________________________after being duly sworn in 
    accordance with law hereby depose and say:<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That &nbsp;&nbsp;&nbsp;&nbsp;we &nbsp;&nbsp;&nbsp;&nbsp;are &nbsp;&nbsp;&nbsp;&nbsp;not &nbsp;&nbsp;&nbsp;&nbsp;in &nbsp;&nbsp;&nbsp;&nbsp;any &nbsp;&nbsp;&nbsp;&nbsp;way &nbsp;&nbsp;related &nbsp;&nbsp;by &nbsp;&nbsp;affinity &nbsp;&nbsp;or &nbsp;&nbsp;consanguinity &nbsp;&nbsp;to<br> 
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:__________________________________________, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;a &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;resident &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of <br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________________________________________________ City of Cabuyao, Laguna;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. That &nbsp;we &nbsp;know &nbsp;her / him &nbsp;as &nbsp;a &nbsp;single &nbsp;parent and the &nbsp;Mother / Father &nbsp;of this children:<br><br>
    
    <table style="width:100%; border:1px solid black;">
        <tr>
            <td style="width:80%; text-align:center; border:1px solid black; padding:5px; background-color:#f0f0f0;">Name</td>
            <td style="width:20%; text-align:center; border:1px solid black; padding:5px; background-color:#f0f0f0;">Age</td>
        </tr>
        <tr><td style="border:1px solid black; padding:5px; height:20px;"></td><td style="border:1px solid black; padding:5px; height:20px;"></td></tr>
        <tr><td style="border:1px solid black; padding:5px; height:20px;"></td><td style="border:1px solid black; padding:5px; height:20px;"></td></tr>
        <tr><td style="border:1px solid black; padding:5px; height:20px;"></td><td style="border:1px solid black; padding:5px; height:20px;"></td></tr>
        <tr><td style="border:1px solid black; padding:5px; height:20px;"></td><td style="border:1px solid black; padding:5px; height:20px;"></td></tr>
    </table><br><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. That &nbsp;&nbsp;she/he &nbsp;&nbsp;is &nbsp;&nbsp;solely &nbsp;&nbsp;taking &nbsp;&nbsp;care &nbsp;&nbsp;and &nbsp;&nbsp;providing &nbsp;&nbsp;for her/his children's needs and<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;everything &nbsp;&nbsp;indispensable &nbsp;&nbsp;for &nbsp;&nbsp;her / his &nbsp;&nbsp;well-being &nbsp;&nbsp;since &nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;biological &nbsp;&nbsp;&nbsp;&nbsp;Father<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/Mother abandoned her / his children;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. That &nbsp;&nbsp; we &nbsp;&nbsp;know &nbsp;&nbsp;for &nbsp;&nbsp;a &nbsp;&nbsp;fact &nbsp;&nbsp;that &nbsp;&nbsp;she/he &nbsp;&nbsp;is &nbsp;&nbsp;not &nbsp;&nbsp;cohabitating with any other man / <br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;woman since she / he become a solo parent until present;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. That&nbsp;&nbsp; we &nbsp;&nbsp;execute &nbsp;&nbsp;this &nbsp;&nbsp;affidavit &nbsp;&nbsp;to &nbsp;&nbsp;attest &nbsp;&nbsp;to &nbsp;&nbsp;the truth of the foregoing and let this<br> 
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;instrument be useful for whatever legal intents it may serve.<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, we have hereunto set our hands this ________________ in the City of Cabuyao, Laguna.<br/><br/>
    
    <div style="text-align:center; font-weight:bold;">AFFIANTS:</div><br/>
    
    _____________________________________&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________________________<br>
<table style="width:100%;">
        <tr>
<td style="width:50%; text-align:left;">Valid ID No. _________________________</td>
<td style="width:50%; text-align:right;">Valid ID No. _________________________</td>
        </tr>
    </table><br/>
    <br>
SUBSCRIBED AND SWORN TO before me this date above mentioned at the City of Cabuyao, Laguna, affiants exhibiting to me their respective proofs of identity personally attesting that the foregoing statements are true to the best of their knowledge and beliefs.<br/><br/>
    
    Doc. No. _____<br/>
    Book No. _____<br/>
    Page No. _____<br/>
    Series of _____<br/>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('JOINT_AFFIDAVIT_SOLO_PARENT.pdf', 'D');
?>
