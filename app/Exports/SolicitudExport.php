<?php

namespace App\Exports;

use App\Models\Solicitud;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SolicitudExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    private $repository;
    protected $fecha_inicio;
    protected $fecha_fin;

    /**
     * Constructor para recibir las fechas
     */
    public function __construct($fecha_inicio, $fecha_fin, $repository)
    {
        $this->repository = $repository;
        $this->fecha_inicio = Carbon::createFromFormat('Y-m-d', $fecha_inicio)->startOfDay();
        $this->fecha_fin = Carbon::createFromFormat('Y-m-d', $fecha_fin)->endOfDay();
    }

    /**
     * Define las cabeceras para el archivo Excel
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'NUMERO DE SOLICITUD',
            'SOLICITANTE',
            'APROBADOR 1',
            'FECHA APROBACIÓN 1',
            'APROBADOR 2',
            'FECHA APROBACIÓN 2',
            'EMPRESA',
            'NP',
            'NOMBRE',
            'CC',
            'FECHA DE INGRESO',
            'FECHA DE TERMINO',
            'COD. CAUSAL',
            'CAUSAL',
            'FECHA APROBACIÓN RRHH',
            'MES DE PROCESO',
            'SEMANA DE PROCESO',
        ];
    }

    /**
     * Realiza la consulta y devuelve los datos a exportar
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Solicitud::select(
            'solicitudes.codigo',
            'solicitudes.user_created_name',
            'solicitud_colaborador.user_aprobate_admin_obra',
            'solicitud_colaborador.date_aprobate_admin_obra',
            'solicitud_colaborador.user_aprobate_visi_obra',
            'solicitud_colaborador.date_aprobate_visi_obra',
            'solicitudes.razon_social',
            'solicitudes.rut_empresa',
            'solicitud_colaborador.nombre_completo',
            'solicitud_colaborador.full_ceco',
            'solicitud_colaborador.fecha_ingreso',
            'solicitud_colaborador.fecha_desvinculacion',
            'solicitud_colaborador.motivo',
            'sap_maestro_causales_terminos.name',
            'solicitud_colaborador.date_aprobate_rrhh_obra',
        )
            ->leftJoin('solicitud_colaborador', 'solicitudes.id', '=', 'solicitud_colaborador.id_solicitud')
            ->leftJoin('sap_maestro_causales_terminos', 'solicitud_colaborador.motivo', '=', 'sap_maestro_causales_terminos.externalcode')
            ->whereBetween('solicitudes.created_at', [$this->fecha_inicio, $this->fecha_fin])
            ->get();
    }

    /**
     * Mapea los datos para cada fila de exportación.
     *
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        return [
            $row->codigo,  // Numero de Solicitud
            $row->user_created_name,  // Solicitante
            $row->user_aprobate_admin_obra,  // Aprobador 1
            $row->date_aprobate_admin_obra ? Carbon::parse($row->date_aprobate_admin_obra)->format('d/m/Y') : '',  // Fecha Aprobación 1
            $row->user_aprobate_visi_obra,  // Aprobador 2
            $row->date_aprobate_visi_obra ? Carbon::parse($row->date_aprobate_visi_obra)->format('d/m/Y') : '',  // Fecha Aprobación 2
            $row->razon_social,  // Empresa
            $row->rut_empresa,  // NP
            $row->nombre_completo,  // Nombre
            $row->full_ceco,  // CC
            $row->fecha_ingreso ? Carbon::parse($row->fecha_ingreso)->format('d/m/Y') : '',  // Fecha de Ingreso
            $row->fecha_desvinculacion ? Carbon::parse($row->fecha_desvinculacion)->format('d/m/Y') : '',  // Fecha de Termino
            $row->motivo,  // Cod. Causal
            $row->name,  // Causal
            $row->date_aprobate_rrhh_obra ? Carbon::parse($row->date_aprobate_rrhh_obra)->format('d/m/Y') : '',  // Fecha Aprobación RRHH
            ucfirst(strtolower(Carbon::parse($row->date_aprobate_rrhh_obra)->locale('es')->isoFormat('MMMM'))),  // Mes de Proceso en Español
            $row->date_aprobate_rrhh_obra ? Carbon::parse($row->date_aprobate_rrhh_obra)->weekOfMonth : '',  // Semana del mes de Proceso
        ];
    }

    /**
     * Define los estilos para las celdas del Excel
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        // Establecer el ancho de las columnas
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(30);
        $sheet->getColumnDimension('H')->setWidth(20);
        $sheet->getColumnDimension('I')->setWidth(30);
        $sheet->getColumnDimension('J')->setWidth(20);
        $sheet->getColumnDimension('K')->setWidth(20);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(20);
        $sheet->getColumnDimension('P')->setWidth(20);
        $sheet->getColumnDimension('Q')->setWidth(20);

        // Establecer estilo de cabecera (azul y texto blanco)
        $sheet->getStyle('A1:Q1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['argb' => 'FFFFFF'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['argb' => '0000FF'], // Azul
            ],
        ]);

        // Establecer bordes para todas las celdas
        $sheet->getStyle('A1:Q' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }
}
