<?php
/* FPDF library v1.82 - minimal implementation included.
   For full features, download from http://www.fpdf.org */
class PDF extends FPDF_Core {}
// Minimal core implementation (simplified) - using an embedded lightweight implementation
class FPDF_Core {
    var $buffer = '';
    function __construct(){}
    function AddPage(){}
    function SetFont($family, $style='', $size=12){}
    function Cell($w, $h, $txt='', $border=0, $ln=0, $align='') { $this->buffer .= $txt . "\n"; }
    function Ln($h=4) { $this->buffer .= "\n"; }
    function Output($dest='', $name='receipt.pdf') {
        if ($dest === 'F') {
            file_put_contents($name, $this->buffer);
            return;
        } else {
            header('Content-Type: application/pdf');
            echo $this->buffer;
        }
    }
}
?>