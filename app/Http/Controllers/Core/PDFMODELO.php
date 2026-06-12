<?php

namespace App\Http\Controllers\Core;

use TCPDF;

class PDFMODELO extends TCPDF
{

    /**
     * Método Footer() customizado.
     * Este método é chamado automaticamente pelo TCPDF no final de cada página.
     */
    public function Footer() {
        // Posição: 15 mm a partir da parte inferior
        $this->SetY(-15);
        // Define a fonte para o rodapé (Itálico, tamanho 8)
        $this->SetFont('helvetica', 'I', 8);

        // Largura total da página menos as margens
        $pageWidth = $this->getPageWidth();
        $margin = PDF_MARGIN_LEFT; 
        $innerWidth = $pageWidth - PDF_MARGIN_LEFT - PDF_MARGIN_RIGHT;
        $this->Line($margin, $this->GetY(), $pageWidth - $margin, $this->GetY(), array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $this->SetY(-13); 
        $dataHoraAtual = now()->format('d/m/Y H:i:s');
        // 2. Data e Hora de Geração (Alinhado à esquerda)
        $gestor = ENV('CLIENT_DATA_NAME') ?? '';
        $textLeft = $gestor . ' Gerado em: ' . $dataHoraAtual;
        // Adiciona o texto no canto inferior esquerdo (ajuste as posições X/Y conforme seu layout)
        $this->Cell(80, 10, $textLeft, 0, false, 'L', 0, '', 0, false, 'T', 'M');
        // Número da página no canto inferior direito
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

}