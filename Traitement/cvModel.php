<?php 
// le model se connecte à la base de données et traite les données
require('lib/fpdf/fpdf.php');

class PDF extends FPDF {
    function Header() {
        // Empty header
    }

    function Footer() {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Page number
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    // Function to handle UTF-8 strings
    function WriteUTF8($text) {
        // Convert UTF-8 to ISO-8859-1 (Latin1)
        $text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $text);
        return $text;
    }

    // Override Cell to handle UTF-8
    function CellUTF8($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = false, $link = '') {
        $txt = $this->WriteUTF8($txt);
        $this->Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
    }

    // Override MultiCell to handle UTF-8
    function MultiCellUTF8($w, $h, $txt, $border = 0, $align = 'J', $fill = false) {
        $txt = $this->WriteUTF8($txt);
        $this->MultiCell($w, $h, $txt, $border, $align, $fill);
    }
}

function generateCV($data) {
    // Create PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->AliasNbPages();
    
    // Set default font
    $pdf->SetFont('Arial', '', 12);
    
    // Constants for layout
    $leftWidth = 50;  // Width of left section in mm
    $rightMargin = 10;
    $lineHeight = 7;
    
    // Left Section (25% of width)
    $pdf->SetFillColor(245, 245, 245);
    $pdf->Rect(0, 0, $leftWidth, 297, 'F');
    
    // Add photo if provided
    if (isset($data['photo']) && $data['photo']['tmp_name']) {
        $imageType = '';
        $imagePath = $data['photo']['tmp_name'];
        
        // Get image type from the actual file
        $imageInfo = getimagesize($imagePath);
        if ($imageInfo !== false) {
            switch($imageInfo[2]) {
                case IMAGETYPE_JPEG: $imageType = 'JPEG'; break;
                case IMAGETYPE_PNG: $imageType = 'PNG'; break;
                case IMAGETYPE_GIF: $imageType = 'GIF'; break;
            }
        }
        
        if ($imageType) {
            try {
                $pdf->Image($imagePath, 10, 10, 30, 0, $imageType);
            } catch (Exception $e) {
                // If image fails to load, skip it without breaking the PDF generation
                error_log("Failed to load image: " . $e->getMessage());
            }
        }
    }
    
    // Contact Info in left section
    $pdf->SetXY(5, 50);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8($leftWidth - 10, 10, 'CONTACT', 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    $pdf->SetX(5);
    $pdf->MultiCellUTF8($leftWidth - 10, $lineHeight, $data['email']);
    $pdf->SetX(5);
    $pdf->MultiCellUTF8($leftWidth - 10, $lineHeight, $data['telephone']);
    $pdf->SetX(5);
    
    // Format full address
    $fullAddress = $data['adresse'] . "\n" . $data['code_postal'] . " " . $data['ville'];
    $pdf->MultiCellUTF8($leftWidth - 10, $lineHeight, $fullAddress);
    
    // Compétences in left section
    $pdf->SetXY(5, 100);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8($leftWidth - 10, 10, 'COMPÉTENCES', 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    foreach ($data['competences'] as $competence) {
        $pdf->SetX(5);
        $pdf->CellUTF8($leftWidth - 10, 5, $competence['nom'], 0, 1);
        $pdf->SetX(5);
        // Draw skill bar
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 40, 3, 'F');
        $pdf->SetFillColor(74, 144, 226);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 40 * ($competence['niveau']/100), 3, 'F');
        $pdf->Ln(7);
    }
    
    // Langues in left section
    $pdf->SetXY(5, 160);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8($leftWidth - 10, 10, 'LANGUES', 0, 1);
    
    $pdf->SetFont('Arial', '', 10);
    foreach ($data['langues'] as $langue) {
        $pdf->SetX(5);
        $pdf->CellUTF8($leftWidth - 10, 5, $langue['nom'], 0, 1);
        $pdf->SetX(5);
        // Draw language bar
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 40, 3, 'F');
        $pdf->SetFillColor(74, 144, 226);
        $pdf->Rect($pdf->GetX(), $pdf->GetY(), 40 * ($langue['niveau']/100), 3, 'F');
        $pdf->Ln(7);
    }
    
    // Main section (right side)
    $pdf->SetXY($leftWidth + $rightMargin, 10);
    
    // Name and title in large text
    $pdf->SetFont('Arial', 'B', 24);
    $pdf->CellUTF8(0, 15, $data['prenom'] . ' ' . $data['nom'], 0, 1);
    
    $pdf->SetXY($leftWidth + $rightMargin, 25);
    $pdf->SetFont('Arial', '', 16);
    $pdf->CellUTF8(0, 10, $data['titre'], 0, 1);
    
    // Profile section
    $pdf->SetXY($leftWidth + $rightMargin, 40);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8(0, 10, 'PROFIL', 0, 1);
    
    $pdf->SetX($leftWidth + $rightMargin);
    $pdf->SetFont('Arial', '', 10);
    $pdf->MultiCellUTF8(0, $lineHeight, $data['description']);
    $pdf->Ln(5);
    
    // Formation section
    $pdf->SetX($leftWidth + $rightMargin);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8(0, 10, 'FORMATION', 0, 1);
    
    $mois = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    
    foreach ($data['formations'] as $formation) {
        $moisDebut = $mois[intval($formation['mois_debut']) - 1];
        $moisFin = $mois[intval($formation['mois_fin']) - 1];
        $dateFormation = $moisDebut . ' ' . $formation['annee_debut'] . ' - ' . $moisFin . ' ' . $formation['annee_fin'];
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->CellUTF8(0, 8, $formation['titre'], 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', '', 10);
        $pdf->CellUTF8(0, 6, $formation['etablissement'] . ', ' . $formation['ville'], 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->CellUTF8(0, 6, $dateFormation, 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCellUTF8(0, $lineHeight, $formation['description']);
        $pdf->Ln(5);
    }
    
    // Experience section
    $pdf->SetX($leftWidth + $rightMargin);
    $pdf->SetFont('Arial', 'B', 14);
    $pdf->CellUTF8(0, 10, 'EXPÉRIENCE PROFESSIONNELLE', 0, 1);
    
    foreach ($data['experiences'] as $experience) {
        $moisDebut = $mois[intval($experience['mois_debut']) - 1];
        $moisFin = $mois[intval($experience['mois_fin']) - 1];
        $dateExperience = $moisDebut . ' ' . $experience['annee_debut'] . ' - ' . $moisFin . ' ' . $experience['annee_fin'];
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->CellUTF8(0, 8, $experience['poste'], 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', '', 10);
        $pdf->CellUTF8(0, 6, $experience['employeur'] . ', ' . $experience['ville'], 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->CellUTF8(0, 6, $dateExperience, 0, 1);
        
        $pdf->SetX($leftWidth + $rightMargin);
        $pdf->SetFont('Arial', '', 10);
        $pdf->MultiCellUTF8(0, $lineHeight, $experience['description']);
        $pdf->Ln(5);
    }
    
    // Return the PDF object instead of outputting it directly
    return $pdf;
}