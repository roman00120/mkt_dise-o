<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportFilterRequest;
use App\Services\Analytics\ReportMetricsService;
use Symfony\Component\HttpFoundation\Response;

class ReportExportController extends Controller
{
    public function csv(ReportFilterRequest $request, ReportMetricsService $reports): Response
    {
        $data = $reports->dashboard($request->user(), $request->filters());

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Métrica', 'Valor', 'Universo']);
            foreach ($data['counts'] as $key => $value) {
                $labelName = match(strtolower($key)) {
                    'created' => 'Solicitudes Creadas',
                    'sent' => 'Solicitudes Enviadas',
                    'completed' => 'Solicitudes Completadas',
                    'cancelled', 'canceled' => 'Solicitudes Canceladas',
                    'in_progress' => 'En Proceso Creativo',
                    'in_validation' => 'Pendientes de Aprobación',
                    'drafts', 'draft' => 'Borradores',
                    default => ucfirst(str_replace('_', ' ', $key))
                };
                fputcsv($out, [$this->safe($labelName), $value, 'Solicitudes dentro del periodo y alcance']);
            }
            fclose($out);
        }, 'tg-report-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function pdf(ReportFilterRequest $request, ReportMetricsService $reports): Response
    {
        $data = $reports->dashboard($request->user(), $request->filters());
        $stream = $this->makeDesignedPdf($data);

        return response($stream, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="tg-report-'.now()->format('Ymd-His').'.pdf"',
        ]);
    }

    private function safe(string $value): string
    {
        return preg_match('/^[=+\-@]/', $value) ? "'".$value : $value;
    }

    private function makeDesignedPdf(array $data): string
    {
        $fromDate = $data['filters']['from_date']->toDateString();
        $toDate = $data['filters']['to_date']->toDateString();
        $created = $data['counts']['created'] ?? 0;
        $sent = $data['counts']['sent'] ?? 0;
        $completed = $data['counts']['completed'] ?? 0;
        $inProgress = $data['counts']['in_progress'] ?? 0;
        $drafts = $data['counts']['drafts'] ?? $data['counts']['draft'] ?? 0;
        $cancelled = $data['counts']['cancelled'] ?? $data['counts']['canceled'] ?? 0;

        $completionRate = $created > 0 ? round(($completed / $created) * 100) : 100;

        $pdfStream = [];

        // 1. Top Red Accent Line
        $pdfStream[] = "0.88 0.12 0.16 rg 0 786 612 6 re f";

        // 2. Dark Navy Header Banner
        $pdfStream[] = "0.06 0.09 0.16 rg 0 710 612 76 re f";

        // Header Title Text
        $pdfStream[] = "BT /F2 16 Tf 1 1 1 rg 40 750 Td (" . $this->pdfEscape("TOTAL GROUND  |  TG CREATIVE HUB") . ") Tj ET";
        $pdfStream[] = "BT /F1 9 Tf 0.70 0.75 0.85 rg 40 730 Td (" . $this->pdfEscape("REPORTE EJECUTIVO DE KPIS Y RENDIMIENTO OPERATIVO") . ") Tj ET";

        // 3. Period & Date Info Box
        $pdfStream[] = "0.95 0.96 0.98 rg 40 635 532 55 re f";
        $pdfStream[] = "0.82 0.85 0.90 RG 1 w 40 635 532 55 re s";

        $pdfStream[] = "BT /F2 9 Tf 0.15 0.20 0.30 rg 55 670 Td (" . $this->pdfEscape("PERIODO EVALUADO:") . ") Tj ET";
        $pdfStream[] = "BT /F1 9 Tf 0.25 0.30 0.40 rg 160 670 Td (" . $this->pdfEscape($fromDate . "  a  " . $toDate) . ") Tj ET";

        $pdfStream[] = "BT /F2 9 Tf 0.15 0.20 0.30 rg 55 650 Td (" . $this->pdfEscape("FECHA DE EMISION:") . ") Tj ET";
        $pdfStream[] = "BT /F1 9 Tf 0.25 0.30 0.40 rg 160 650 Td (" . $this->pdfEscape(now()->format('Y-m-d H:i:s') . " (Hora Local)") . ") Tj ET";

        // 4. Executive KPI Summary Cards (4 Cards)
        // Card 1: Solicitudes Recibidas (Blue Accent)
        $pdfStream[] = "0.95 0.97 1.00 rg 40 525 250 85 re f";
        $pdfStream[] = "0.82 0.87 0.95 RG 1 w 40 525 250 85 re s";
        $pdfStream[] = "0.20 0.45 0.85 rg 40 605 250 5 re f";
        $pdfStream[] = "BT /F2 8 Tf 0.30 0.40 0.60 rg 55 585 Td (" . $this->pdfEscape("SOLICITUDES RECIBIDAS") . ") Tj ET";
        $pdfStream[] = "BT /F2 26 Tf 0.10 0.18 0.35 rg 55 545 Td (" . $this->pdfEscape((string)$sent) . ") Tj ET";

        // Card 2: Solicitudes Completadas (Green Accent)
        $pdfStream[] = "0.94 0.98 0.95 rg 322 525 250 85 re f";
        $pdfStream[] = "0.78 0.90 0.82 RG 1 w 322 525 250 85 re s";
        $pdfStream[] = "0.10 0.70 0.40 rg 322 605 250 5 re f";
        $pdfStream[] = "BT /F2 8 Tf 0.15 0.50 0.30 rg 337 585 Td (" . $this->pdfEscape("SOLICITUDES COMPLETADAS") . ") Tj ET";
        $pdfStream[] = "BT /F2 26 Tf 0.05 0.45 0.25 rg 337 545 Td (" . $this->pdfEscape((string)$completed) . ") Tj ET";

        // Card 3: En Proceso (Indigo Accent)
        $pdfStream[] = "0.96 0.95 1.00 rg 40 420 250 85 re f";
        $pdfStream[] = "0.83 0.80 0.95 RG 1 w 40 420 250 85 re s";
        $pdfStream[] = "0.40 0.30 0.85 rg 40 500 250 5 re f";
        $pdfStream[] = "BT /F2 8 Tf 0.35 0.25 0.60 rg 55 480 Td (" . $this->pdfEscape("EN PROCESO CREATIVO") . ") Tj ET";
        $pdfStream[] = "BT /F2 26 Tf 0.22 0.15 0.45 rg 55 440 Td (" . $this->pdfEscape((string)$inProgress) . ") Tj ET";

        // Card 4: Tasa de Efectividad SLA (Gold Accent)
        $pdfStream[] = "0.99 0.97 0.93 rg 322 420 250 85 re f";
        $pdfStream[] = "0.92 0.85 0.70 RG 1 w 322 420 250 85 re s";
        $pdfStream[] = "0.85 0.60 0.15 rg 322 500 250 5 re f";
        $pdfStream[] = "BT /F2 8 Tf 0.65 0.45 0.10 rg 337 480 Td (" . $this->pdfEscape("TASA DE EFECTIVIDAD (SLA)") . ") Tj ET";
        $pdfStream[] = "BT /F2 26 Tf 0.60 0.40 0.05 rg 337 440 Td (" . $this->pdfEscape($completionRate . "%") . ") Tj ET";

        // Section Title: Tabla de Desglose
        $pdfStream[] = "BT /F2 11 Tf 0.10 0.15 0.25 rg 40 385 Td (" . $this->pdfEscape("DESGLOSE DETALLADO DE KPIS OPERATIVOS") . ") Tj ET";

        // 5. Metrics Table Header
        $pdfStream[] = "0.08 0.12 0.22 rg 40 350 532 24 re f";
        $pdfStream[] = "BT /F2 9 Tf 1 1 1 rg 52 360 Td (" . $this->pdfEscape("METRICA / INDICADOR") . ") Tj ET";
        $pdfStream[] = "BT /F2 9 Tf 1 1 1 rg 300 360 Td (" . $this->pdfEscape("CANTIDAD") . ") Tj ET";
        $pdfStream[] = "BT /F2 9 Tf 1 1 1 rg 440 360 Td (" . $this->pdfEscape("ESTADO OPERATIVO") . ") Tj ET";

        // Table Rows
        $tableRows = [
            ['Solicitudes Creadas', (string)$created, 'Registrado en sistema'],
            ['Solicitudes Enviadas a Flujo', (string)$sent, 'En marcha / Activas'],
            ['Solicitudes Completadas con Éxito', (string)$completed, 'Entregado a conformidad'],
            ['Solicitudes en Proceso Creativo', (string)$inProgress, 'En diseño / edición'],
            ['Borradores Pendientes de Envío', (string)$drafts, 'En preparación'],
            ['Solicitudes Canceladas / Rechazadas', (string)$cancelled, $cancelled > 0 ? 'Revisar motivos' : 'Excelente (0)'],
        ];

        $y = 322;
        foreach ($tableRows as $idx => $row) {
            $bgColor = ($idx % 2 === 0) ? "0.97 0.98 0.99 rg" : "1 1 1 rg";
            $pdfStream[] = "{$bgColor} 40 {$y} 532 26 re f";
            $pdfStream[] = "0.88 0.90 0.94 RG 0.5 w 40 {$y} 532 26 re s";

            $textY = $y + 9;
            $pdfStream[] = "BT /F2 9 Tf 0.18 0.22 0.30 rg 52 {$textY} Td (" . $this->pdfEscape($row[0]) . ") Tj ET";
            $pdfStream[] = "BT /F2 10 Tf 0.10 0.15 0.25 rg 300 {$textY} Td (" . $this->pdfEscape($row[1]) . ") Tj ET";
            $pdfStream[] = "BT /F1 8.5 Tf 0.35 0.40 0.50 rg 440 {$textY} Td (" . $this->pdfEscape($row[2]) . ") Tj ET";

            $y -= 26;
        }

        // 6. Footer Section
        $pdfStream[] = "0.85 0.88 0.92 rg 40 55 532 1 re f";
        $pdfStream[] = "BT /F1 8 Tf 0.45 0.50 0.58 rg 40 40 Td (" . $this->pdfEscape("Confidencial  ·  Total Ground Creative Hub  ·  Documento oficial generado para uso ejecutivo.") . ") Tj ET";
        $pdfStream[] = "BT /F2 8 Tf 0.45 0.50 0.58 rg 480 40 Td (" . $this->pdfEscape("Página 1 de 1") . ") Tj ET";

        $content = implode("\n", $pdfStream);

        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /Kids [3 0 R] /Count 1 >>',
            '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>',
            "<< /Length " . strlen($content) . " >>\nstream\n" . $content . "\nendstream",
        ];

        $pdf = "%PDF-1.4\n";
        $offsets = [];

        foreach ($objects as $i => $object) {
            $offsets[$i + 1] = strlen($pdf);
            $pdf .= ($i + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n0000000000 65535 f \n";

        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefOffset . "\n%%EOF\n";

        return $pdf;
    }

    private function pdfEscape(string $text): string
    {
        $converted = mb_convert_encoding($text, 'ISO-8859-1', 'UTF-8');
        return addcslashes($converted, "()\\");
    }
}
