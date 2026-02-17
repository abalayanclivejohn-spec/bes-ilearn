<?php
require_once "../includes/db.php";

if (!isset($_GET['file'], $_GET['type'])) exit('File not found');

$file = $_GET['file'];
$type = $_GET['type'];

$content = "Preview not available";

if ($type === 'document' && file_exists($file)) {
    $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    if ($ext === 'pdf') {
        require '../vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($file);
        $content = nl2br(htmlspecialchars($pdf->getText()));
    } elseif (in_array($ext,['docx','doc'])) {
        require '../vendor/autoload.php';
        $phpWord = \PhpOffice\PhpWord\IOFactory::load($file);
        $text = '';
        foreach($phpWord->getSections() as $section) {
            foreach($section->getElements() as $element) {
                if (method_exists($element,'getText')) $text .= htmlspecialchars($element->getText()) . "<br>";
            }
        }
        $content = $text ?: "No text content found.";
    } elseif (in_array($ext,['pptx','ppt'])) {
        require '../vendor/autoload.php';
        $ppt = \PhpOffice\PhpPresentation\IOFactory::load($file);
        $text = '';
        foreach($ppt->getAllSlides() as $slide) {
            foreach($slide->getShapeCollection() as $shape) {
                if (method_exists($shape,'getText')) $text .= htmlspecialchars($shape->getText()) . "<br>";
            }
        }
        $content = $text ?: "No text content found.";
    }
}

echo $content;
