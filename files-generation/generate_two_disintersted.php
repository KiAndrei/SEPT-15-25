<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('JOINT AFFIDAVIT (Two Disinterested Person)');
$pdf->SetSubject('JOINT AFFIDAVIT (Two Disinterested Person)');

// Set default header data
$pdf->SetHeaderData('', 0, '', '');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

// Set margins - reduced for single page
$pdf->SetMargins(28, 15, 28);
$pdf->SetAutoPageBreak(FALSE, 15);

// Set font
$pdf->SetFont('times', '', 11);

// Add a page
$pdf->AddPage();

// Joint Affidavit (Two Disinterested Person) content with exact formatting from image
$html = <<<EOD
<div style="text-align:left; font-size:11pt;">
REPUBLIC OF THE PHILIPPINES)<br/>
    PROVINCE OF LAGUNA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;) SS<br/>
    CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)<br/><br/>
</div>

<div style="text-align:center; font-size:13pt; font-weight:bold;">
    <b>JOINT AFFIDAVIT<br/>(Two Disinterested Person)</b>
</div>
<br/>

<div style="text-align:left; font-size:11pt;">
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;WE, _________________________ and _________________________, Filipinos, both of<br/>
    legal age, and permanent residents of ________________________________________________,<br/>
    after being duly sworn in accordance with law hereby depose and say;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That &nbsp;&nbsp; we &nbsp;&nbsp; are &nbsp;&nbsp;not &nbsp;&nbsp; in &nbsp;&nbsp;&nbsp;any &nbsp;&nbsp;&nbsp;way &nbsp;&nbsp;&nbsp;related &nbsp;&nbsp;&nbsp;by &nbsp;&nbsp;&nbsp;affinity &nbsp;&nbsp;&nbsp;or &nbsp;&nbsp;&nbsp;consanguinity &nbsp;&nbsp;&nbsp;to:<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;____________________________, &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;child &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;spouses<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;_______________________________ and _______________________________;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. That &nbsp;&nbsp;we &nbsp;&nbsp;know &nbsp;&nbsp;for &nbsp;&nbsp;a &nbsp;&nbsp;fact &nbsp;&nbsp;that &nbsp;&nbsp;&nbsp;&nbsp;he/she &nbsp;&nbsp;&nbsp;&nbsp;was &nbsp;&nbsp;&nbsp;&nbsp;born &nbsp;&nbsp;&nbsp;&nbsp;on ________________ at<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________________________________________________;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. That &nbsp;&nbsp;&nbsp;we &nbsp;&nbsp;&nbsp;know &nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;circumstances &nbsp;&nbsp;&nbsp;&nbsp;surrounding &nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;birth &nbsp;&nbsp;&nbsp;&nbsp;of &nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;said<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;________________________________________________considering that we are present<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;during&nbsp; delivery&nbsp; as&nbsp; we&nbsp; are&nbsp; well&nbsp; acquainted&nbsp; with&nbsp; his/her&nbsp; parents,&nbsp; being&nbsp; family&nbsp;&nbsp;&nbsp; friend<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and neighbors;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. That we are executing this affidavit &nbsp;in &nbsp;order &nbsp;to &nbsp;furnish &nbsp;by &nbsp;secondary&nbsp; evidence&nbsp; as &nbsp;to<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;fact &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;concerning &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;and &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;place &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;birth &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;of<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;___________________________________________________________ in the absence of<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;his/her&nbsp;Birth &nbsp;Certificate &nbsp;and &nbsp;let &nbsp;this &nbsp;instrument &nbsp;be &nbsp;useful &nbsp;for &nbsp;whatever &nbsp;legal &nbsp;&nbsp;purpose<br>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;it may serve best;<br/><br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, &nbsp;we &nbsp;have &nbsp;hereunto &nbsp;set &nbsp;&nbsp;our &nbsp;&nbsp;hands &nbsp;&nbsp;this ________________ in<br/>
    Cabuyao City, Laguna.<br/><br/>
    
    <table style="width:100%;">
        <tr>
            <td style="width:50%; text-align:center;">____________________________<br/>Affiant<br/>________________ID _________________</td>
            <td style="width:50%; text-align:center;">____________________________<br/>Affiant<br/>________________ID _________________</td>
        </tr>
    </table><br/>
    <br>
    <br>
    
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUBSCRIBED and SWORN to before me this __________________________ at the City of<br/>
    Cabuyao, Laguna, Philippines, the &nbsp;affiants &nbsp;exhibited to me their respective proof of identification<br/>
    indicated &nbsp;below &nbsp;their &nbsp;name, attesting &nbsp;that &nbsp;the &nbsp;above statement are true and executed freely and<br/>
    voluntarily;<br/><br/>
    
    WITNESS my hand the date and place above-written.<br/><br/>
    
    Doc. No. _____<br/>
    Page No. _____<br/>
    Book No. _____<br/>
    Series of _____<br/>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('JOINT_AFFIDAVIT_Two_Disinterested_Person.pdf', 'D');
?>
