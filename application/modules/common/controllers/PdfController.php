<?php
class Common_PdfController extends Ec_Controller_Action
{

    public function indexAction()
    {
        
        // Include the main TCPDF library (search for installation path).
        require_once('tcpdf/examples/config/tcpdf_config_alt.php');
        require_once('tcpdf/tcpdf.php');
        
        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Nicola Asuni');
        $pdf->SetTitle('TCPDF Example 021');
        $pdf->SetSubject('TCPDF Tutorial');
        $pdf->SetKeywords('TCPDF, PDF, example, test, guide');
        
//         // set default header data
//         $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE . ' 021', PDF_HEADER_STRING);
        
//         // set header and footer fonts
//         $pdf->setHeaderFont(Array(
//             PDF_FONT_NAME_MAIN,
//             '',
//             PDF_FONT_SIZE_MAIN
//         ));
//         $pdf->setFooterFont(Array(
//             PDF_FONT_NAME_DATA,
//             '',
//             PDF_FONT_SIZE_DATA
//         ));
        
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        
        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        // set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        // ---------------------------------------------------------
        
        // set font
        $pdf->SetFont('helvetica', '', 9);
        
        // add a page
        $pdf->AddPage();
        
        // create some HTML content
        $html = file_get_contents('http://www.oms-heb.com/lodop/print_url_content.html');
        $html = str_replace('"/', '"http://www.oms-heb.com/', $html);
        $html = str_replace("'/", "'http://www.oms-heb.com/", $html);
//         echo $html;exit;
        // output the HTML content
        $html = "dfdfd";
        $pdf->writeHTML($html, true, 0, true, 0);
        
        // reset pointer to the last page
        $pdf->lastPage();
        
        // ---------------------------------------------------------
        
        // Close and output PDF document
        $pdf->Output('example_021.pdf', 'I');
        
        // ============================================================+
        // END OF FILE
        // ============================================================+
    }
}