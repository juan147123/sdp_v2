<?php

namespace App\Exports;

use App\Interfaces\SolicitudRepositoryInterface;
use App\Models\Solicitud;
use Maatwebsite\Excel\Concerns\FromCollection;
use Carbon\Carbon;

class SolicitudExport implements FromCollection
{
    private $repository;
    protected $fecha_inicio;
    protected $fecha_fin;

    /**
     * Constructor para recibir las fechas
     */
    public function __construct($fecha_inicio, $fecha_fin, $repository,)
    {
        $this->repository = $repository;
        $this->fecha_inicio = Carbon::createFromFormat('Y-m-d', $fecha_inicio)->startOfDay();
        $this->fecha_fin = Carbon::createFromFormat('Y-m-d', $fecha_fin)->endOfDay();
    }

    /**
     * Realiza la consulta con las fechas (solo la parte de la fecha de 'created_at')
     * 
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Filtrar las solicitudes donde la fecha de 'created_at' esté entre las fechas proporcionadas
        $data = Solicitud::select(
            'solicitudes.codigo',
            'solicitudes.user_created_name',
            'solicitudes.rut_empresa',
            'solicitudes.razon_social',
            'solicitud_colaborador.user_id' ,
            'solicitud_colaborador.nombre_completo', 
            'solicitud_colaborador.full_ceco', 
            'solicitud_colaborador.fecha_ingreso', 
            'solicitud_colaborador.fecha_desvinculacion', 
            'solicitud_colaborador.motivo', 
            'sap_maestro_causales_terminos.name', 
        )
        ->leftJoin('solicitud_colaborador', 'solicitudes.id', '=', 'solicitud_colaborador.id_solicitud')
        ->leftJoin('sap_maestro_causales_terminos', 'solicitud_colaborador.motivo', '=', 'sap_maestro_causales_terminos.externalcode')
        ->whereBetween('solicitudes.created_at', [$this->fecha_inicio, $this->fecha_fin])
        ->get();
    

        $response = $data->values();
        dd($response);
    }
}
