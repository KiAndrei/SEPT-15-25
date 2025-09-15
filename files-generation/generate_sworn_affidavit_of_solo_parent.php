<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
$pdf->SetCreator('BOSS-KIAN');
$pdf->SetAuthor('BOSS-KIAN');
$pdf->SetTitle('Sworn Affidavit (Solo Parent)');
$pdf->SetSubject('Sworn Affidavit (Solo Parent)');

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

// Sworn Affidavit (Solo Parent) content
$html = <<<EOD
<div style="font-size:11pt; line-height:1.2;">
    <br/>
    
    <div style="margin-top:10px;">
        REPUBLIC OF THE PHILIPPINES)<br/>&nbsp;
        PROVINCE OF LAGUNA&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>S.S</b><br/>&nbsp;
        CITY OF CABUYAO&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;)
    </div>
    
    <div style="text-align:center; font-size:12pt; font-weight:bold; margin-top:-15px 0;">
        SWORN AFFIDAVIT OF SOLO PARENT
    </div>
    <br/>
    
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;That &nbsp;&nbsp; I, &nbsp; _____________________________________, &nbsp; Filipino &nbsp; Citizen, &nbsp; of &nbsp; legal &nbsp; age,<br/>
        _____________________________________, and with residence and postal address at<br/>
        __________________________________________________, after having been duly sworn<br/>
        in accordance with law, hereby depose and say;
    </div>
    
    <div style="text-align:justify;">
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;1. That I am a single parent and the Mother/Father of the following child/children namely:
        </div>
        
        <div style="margin-left: 60px; margin-right: 60px;">
            <table style="width:100%; border-collapse:collapse; margin-bottom:2px;">
                <tr>
                    <td style="width:80%; text-align:center; border:none;"><b>Name</b></td>
                    <td style="width:20%; text-align:center; border:none;"><b>Age</b></td>
                </tr>
            </table>
            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    <td style="width:80%; padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                    <td style="width:20%; padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                    <td style="padding:8px 5px; border:1px solid #000;">&nbsp;</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;2. That I am solely taking care and providing for my said child's / children's needs and everything indispensable for his/her/their wellbeing for ________________ year/s now since his/her / their biological Mother/Father
        </div>
        
        <div style="margin-left: 60px;">
            <table style="border-collapse:collapse;">
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>left the family home and abandoned us;</td>
                </tr>
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>died last <span style="display:inline-block; border-bottom:1px solid #000; width: 180px;"></span>;</td>
                </tr>
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>(other reason please state) <span style="display:inline-block; border-bottom:1px solid #000; width: 220px;"></span>;</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;3. I am attesting to the fact that I am not cohabiting with anybody to date;
        </div>
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;4. I am currently:
        </div>
        
        <div style="margin-left: 60px;">
            <table style="border-collapse:collapse;">
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>Employed and earning Php <span style="display:inline-block; border-bottom:1px solid #000; width: 160px;"></span> per month;</td>
                </tr>
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>
                        <div>Self-employed and earning Php <span style="display:inline-block; border-bottom:1px solid #000; width: 160px;"></span> per month, from</div>
                        <div>my job as <span style="display:inline-block; border-bottom:1px solid #000; width: 160px;"></span>;</div>
                    </td>
                </tr>
                <tr>
                    <td style="width:16px; vertical-align:top; padding-top:2px;"><div style="width:12px; height:12px; border:1px solid #000;"></div></td>
                    <td>Un-employed and dependent upon <span style="display:inline-block; border-bottom:1px solid #000; width: 200px;"></span>;</td>
                </tr>
            </table>
        </div>
        
        <div style="margin-left: 40px;">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;5. That I am executing this affidavit, to affirm the truth and veracity of the foregoing statements and be use for whatever legal purpose it may serve.
        </div>
    </div>
    
    <br/>
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;IN WITNESS WHEREOF, I have hereunto affixed my signature this<br/>
        ____________________________ at the City of Cabuyao, Laguna.
    </div>
    
    <br/>
    <div style="text-align:center; margin:15px 0;">
        ____________________________<br/>
        <b>AFFIANT</b>
    </div>
    
    <br/>
    <div style="text-align:justify; margin-bottom:15px;">
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SUBSCRIBED AND SWORN to before me this ____________________________ at the City of Cabuyao, Laguna, affiant personally appeared and exhibiting to me his/her ____________________________ with ID No. ____________________________ as competent proof of identity.
    </div>
    
    <br/>
    <div style="text-align:left; margin-left: -5px;">
        Doc. No. _______<br/>
        Page No. _______<br/>
        Book No. _______<br/>
        Series of 2025
    </div>
</div>
EOD;

$pdf->writeHTML($html, true, false, true, false, '');

// Output PDF
$pdf->Output('Sworn_Affidavit_Solo_Parent.pdf', 'D'); 