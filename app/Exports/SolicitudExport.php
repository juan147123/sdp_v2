<?php

namespace App\Exports;

use App\Models\Calendar;
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
            'RUT EMPRESA',
            'EMPRESA',
            'NP',
            'NOMBRE',
            'CENTRO DE COSTO',
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
        $calendarios = Calendar::get();

        return Solicitud::select(
            'solicitudes.codigo',
            'solicitudes.user_created_name',
            'solicitud_colaborador.user_aprobate_admin_obra',
            'solicitud_colaborador.date_aprobate_admin_obra',
            'solicitud_colaborador.user_aprobate_visi_obra',
            'solicitud_colaborador.date_aprobate_visi_obra',
            'solicitudes.rut_empresa',
            'solicitudes.razon_social',
            'solicitud_colaborador.user_id',
            'solicitud_colaborador.nombre_completo',
            'solicitud_colaborador.full_ceco',
            'solicitud_colaborador.fecha_ingreso',
            'solicitud_colaborador.fecha_desvinculacion',
            'solicitud_colaborador.motivo',
            'sap_maestro_causales_terminos.name',
            'solicitud_colaborador.date_aprobate_rrhh_obra',
            'solicitud_colaborador.aprobado_rrhh' // Asegúrate de incluir el campo aprobado_rrhh
        )
            ->leftJoin('solicitud_colaborador', 'solicitudes.id', '=', 'solicitud_colaborador.id_solicitud')
            ->leftJoin('sap_maestro_causales_terminos', 'solicitud_colaborador.motivo', '=', 'sap_maestro_causales_terminos.externalcode')
            ->whereBetween('solicitudes.created_at', [$this->fecha_inicio, $this->fecha_fin])
            ->where('solicitudes.status', 4)
            ->get()
            ->map(function ($solicitud) {
                // Filtra solo las solicitudes donde 'aprobado_rrhh' sea igual a 7
                if ($solicitud->solicitud_colaborador->aprobado_rrhh == 7) {
                    return $solicitud;
                }
            })
            ->filter(); // Filtra los valores nulos que se hayan producido en el map
    }


    /**
     * Mapea los datos para cada fila de exportación.
     *
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {

        $calendarios = Calendar::where('enable', 1)->get();


        $mes_de_proceso = 'no registrado';
        foreach ($calendarios as $calendario) {
            $start = Carbon::parse($calendario->start);
            $end = Carbon::parse($calendario->end);


            if ($row->date_aprobate_rrhh_obra && Carbon::parse($row->date_aprobate_rrhh_obra)->between($start, $end)) {
                $mes_de_proceso = preg_replace('/\D/', '', $calendario->title);

                break;
            }
        }

        return [
            $row->codigo,
            $row->user_created_name,
            $row->user_aprobate_admin_obra,
            $row->date_aprobate_admin_obra ? Carbon::parse($row->date_aprobate_admin_obra)->format('d/m/Y') : '',
            $row->user_aprobate_visi_obra,
            $row->date_aprobate_visi_obra ? Carbon::parse($row->date_aprobate_visi_obra)->format('d/m/Y') : '',
            $row->rut_empresa,
            $row->razon_social,
            $row->user_id,
            $row->nombre_completo,
            $row->full_ceco,
            $row->fecha_ingreso ? Carbon::parse($row->fecha_ingreso)->format('d/m/Y') : '',
            $row->fecha_desvinculacion ? Carbon::parse($row->fecha_desvinculacion)->format('d/m/Y') : '',
            $row->motivo,
            $row->name,
            $row->date_aprobate_rrhh_obra ? Carbon::parse($row->date_aprobate_rrhh_obra)->format('d/m/Y') : '',
            ucfirst(strtolower(Carbon::parse($row->date_aprobate_rrhh_obra)->locale('es')->isoFormat('MMMM'))),
            $mes_de_proceso,
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

        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(35);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(35);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(35);
        $sheet->getColumnDimension('H')->setWidth(35);
        $sheet->getColumnDimension('I')->setWidth(17);
        $sheet->getColumnDimension('J')->setWidth(40);
        $sheet->getColumnDimension('K')->setWidth(35);
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getColumnDimension('N')->setWidth(10);
        $sheet->getColumnDimension('O')->setWidth(35);
        $sheet->getColumnDimension('P')->setWidth(25);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(20);


        $sheet->getStyle('A1:R1')->applyFromArray([
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
                'startColor' => ['argb' => '0000FF'],
            ],
        ]);


        $sheet->getStyle('A1:R' . $sheet->getHighestRow())->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ]);
    }
}
