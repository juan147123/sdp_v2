<?php

namespace App\Repositories;

use App\Models\PersonalChile;
use App\Interfaces\PersonalChileRepositoryInterface;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Models\SolicitudColaborador;
use Illuminate\Support\Facades\DB;


class PersonalChileRepository extends BaseRepository implements PersonalChileRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $modelSolicitudColaborador;
    protected $repositorySolicitudColaborador;
    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        PersonalChile $model,
        SolicitudColaborador $modelSolicitudColaborador,
        SolicitudColaboradorRepositoryInterface $repositorySolicitudColaborador
    ) {
        $this->model = $model;
        $this->modelSolicitudColaborador = $modelSolicitudColaborador;
        $this->repositorySolicitudColaborador = $repositorySolicitudColaborador;
    }
    public function UserFindByEmail($email)
    {
        return $this->model
            ->where('correo_flesan', $email)
            ->where('empl_status', 41111)
            ->first();
    }

    public function getNpLideres()
    {
        return $this->model
            ->select('np_lider')
            ->where('empl_status', 41111)
            ->pluck('np_lider')
            ->toArray();
    }

    public function getNpLiderByEmail($email)
    {
        return $this->model
            ->select('user_id')
            ->where('empl_status', 41111)
            ->where(DB::raw('LOWER(correo_flesan)'), strtolower($email))
            ->pluck('user_id')
            ->first();
    }

    public function getDataByUserIdLider($userId)
    {
        $query = "
        WITH RECURSIVE colaboradores AS (
            SELECT user_id, np_lider, first_name, last_name, national_id, correo_flesan, correo_gmail, empresa, centro_costo, nombre_centro_costo, departamento, nombre_departamento, division, nombre_division, external_cod_cargo, ubicacion
            FROM flesan_rrhh.sap_maestro_colaborador
            WHERE np_lider = CAST(:userId AS character varying) AND empl_status = '41111'
            UNION ALL
            SELECT mc.user_id, mc.np_lider, mc.first_name, mc.last_name, mc.national_id, mc.correo_flesan, mc.correo_gmail, mc.empresa, mc.centro_costo, mc.nombre_centro_costo, mc.departamento, mc.nombre_departamento, mc.division, mc.nombre_division, mc.external_cod_cargo, mc.ubicacion
            FROM flesan_rrhh.sap_maestro_colaborador mc
            INNER JOIN colaboradores c ON CAST(mc.np_lider AS character varying) = CAST(c.user_id AS character varying)
            WHERE mc.empl_status = '41111'
        )
        SELECT 
        c.user_id,
        c.np_lider,
        c.first_name,
        c.last_name,
        c.first_name || ' ' || c.last_name AS full_name,
        c.national_id,
        c.correo_flesan,
        c.correo_gmail,      
        c.external_cod_cargo,
        smc.nombre_cargo,
        smc.external_code_area_personal,
        smc.planta_noplanta,
        mr.rut,
        mr.razon_social,
        mr.id_sap,
        --vcc.pais,     
        c.empresa,
        c.centro_costo,
        c.nombre_centro_costo,
         c.centro_costo || ' ' || c.nombre_centro_costo AS full_ceco,
        c.departamento,
        m.nombre_cc,
        m.nombre_un AS unidad_negocio,
        m.external_code_un AS id_unidad_negocio,
        m.external_code_un || ' ' || m.nombre_un AS full_unidad,
        c.nombre_departamento,
        c.departamento || ' ' || c.nombre_departamento AS full_dep,
        c.division,
        c.nombre_division
        FROM colaboradores c
        left join flesan_rrhh.sap_maestro_cargos smc on c.external_cod_cargo = smc.external_code 
        left join public.maestro_rut mr  ON mr.id_sap = c.empresa    
        LEFT JOIN flesan_rrhh.sap_maestro_empresa_dep_un_cc m ON (m.external_code_cc=c.centro_costo)
        group by 
        c.user_id,
        c.np_lider,
        c.first_name,
        c.last_name,
        c.national_id,
        c.correo_flesan,
        c.correo_gmail,      
        c.external_cod_cargo,
        smc.nombre_cargo,
        smc.external_code_area_personal,
        smc.planta_noplanta,
        mr.rut,
        mr.razon_social,
        mr.id_sap,
        --vcc.pais,     
        c.empresa,
        c.centro_costo,
        c.nombre_centro_costo,
        c.departamento,
        m.nombre_cc,
        m.nombre_un,
        m.external_code_un,
        c.nombre_departamento,
        c.division,
        c.nombre_division
        ORDER BY first_name ASC;            
        ";

        $colaboradores = DB::connection('dw_chile')->select(DB::raw($query), [
            'userId' => $userId,
        ]);
        $colaboradores = collect($colaboradores)->map(function ($colaborador) {
            $colaborador->solicitudes = $this->CountSolicitudByUserId($colaborador->user_id);
            return $colaborador;
        });
        return  $colaboradores;
    }

    public function getColaboradoresObra($correo)
    {
        $query_obra = "
         with cecos_lider as( 
            select 
                sap_maestro_empresa_dep_un_cc.external_code_cc,
                sap_maestro_empresa_dep_un_cc.nombre_cc,
                correo as correo_lider
            from
                flesan_rrhh.tabla_encargados_cc
            join flesan_rrhh.sap_maestro_empresa_dep_un_cc on
                (sap_maestro_empresa_dep_un_cc.external_code_empresa || '-' || sap_maestro_empresa_dep_un_cc.nombre_empresa = tabla_encargados_cc.sociedad
                    and sap_maestro_empresa_dep_un_cc.external_code_cc || '-' || sap_maestro_empresa_dep_un_cc.nombre_cc = tabla_encargados_cc.centro_coto)
            left join PUBLIC.maestro_rut on
                (maestro_rut.id_sap = sap_maestro_empresa_dep_un_cc.external_code_empresa)
            where
                correo is not null)
        select 
        to_timestamp(CAST(c.fecha_ingreso AS BIGINT) / 1000)::date AS fecha_ingreso,
            c.user_id,
            c.np_lider,
            c.first_name,
            c.last_name,
            c.first_name || ' ' || c.last_name AS full_name,
            c.national_id,
            c.correo_flesan,
            c.correo_gmail,      
            c.external_cod_cargo,
            smc.nombre_cargo,
            smc.external_code_area_personal,
            smc.planta_noplanta,
            mr.rut,
            mr.razon_social,
            mr.id_sap,    
            c.empresa,
            c.centro_costo,
            c.nombre_centro_costo,
            c.centro_costo || ' ' || c.nombre_centro_costo AS full_ceco,
            m.nombre_cc,
            m.nombre_un AS unidad_negocio,
            m.external_code_un AS id_unidad_negocio,
            m.external_code_un || ' ' || m.nombre_un AS full_unidad,
            c.departamento,
            c.nombre_departamento,
            c.departamento || ' ' || c.nombre_departamento AS full_dep,
            c.division,
            c.nombre_division
        from cecos_lider cl 
        inner join flesan_rrhh.sap_maestro_colaborador c on cl.external_code_cc = c.centro_costo
        inner join flesan_rrhh.sap_maestro_cargos smc on c.external_cod_cargo = smc.external_code
        inner join public.maestro_rut mr  ON mr.id_sap = c.empresa
        inner JOIN flesan_rrhh.sap_maestro_empresa_dep_un_cc m ON m.external_code_cc = c.centro_costo
        and c.empl_status = '41111'
        and cl.correo_lider = :correo_lider
        and smc.planta_noplanta = 'NP'
        group by 
        c.fecha_ingreso,
            c.user_id,
            c.np_lider,
            c.first_name,
            c.last_name,
            c.national_id,
            c.correo_flesan,
            c.correo_gmail,      
            c.external_cod_cargo,
            smc.nombre_cargo,
            smc.external_code_area_personal,
            smc.planta_noplanta,
            mr.rut,
            mr.razon_social,
            mr.id_sap,   
            c.empresa,
            c.centro_costo,
            c.nombre_centro_costo,
            c.departamento,
            m.nombre_cc,
            m.nombre_un,
            m.external_code_un,
            c.nombre_departamento,
            c.division,
            c.nombre_division
            ORDER BY first_name ASC;         
        ";

        $colaboradores = DB::connection('dw_chile')->select(DB::raw($query_obra), [
            'correo_lider' => $correo,
        ]);
        $colaboradores = collect($colaboradores)->map(function ($colaborador) {
            $colaborador->solicitudes = $this->CountSolicitudByUserId($colaborador->user_id);
            return $colaborador;
        });
        return  $colaboradores;
    }
    public function getAprobadorObraCL($correo)
    {

        $query_obra = "
            SELECT distinct ARRAY_AGG(external_code_cc) AS cc, sap_maestro_empresa_dep_un_cc.lider_departamento AS np_adm_obra,lower(sap_maestro_colaborador.correo_flesan) as correo,sap_maestro_colaborador.first_name AS nombres_adm_obra,sap_maestro_colaborador.last_name AS apellidos__adm_obra FROM flesan_rrhh.sap_maestro_empresa_dep_un_cc
            LEFT JOIN flesan_rrhh.sap_maestro_colaborador ON (sap_maestro_empresa_dep_un_cc.lider_departamento=sap_maestro_colaborador.user_id::TEXT)
            LEFT JOIN flesan_rrhh.sap_maestro_colaborador AS lider ON (lider.user_id::TEXT=sap_maestro_colaborador.np_lider::TEXT)
            WHERE status_departamento ='A' and lider.user_id is not null and LOWER(sap_maestro_colaborador.correo_flesan) = :correo 
            group by np_adm_obra,correo,nombres_adm_obra,apellidos__adm_obra;      
        ";

        $colaborador = DB::connection('dw_chile')->select(DB::raw($query_obra), [
            'correo' => $correo,
        ]);
        return  $colaborador;
    }

    public function getVisitadorObraCL($correo)
    {

        $query_obra = "
        select distinct ARRAY_AGG(external_code_cc) AS cc, lider.user_id ,lower(lider.correo_flesan)as correo,lider.first_name AS nombre_visitador, lider.last_name AS apellido_visitador FROM flesan_rrhh.sap_maestro_empresa_dep_un_cc
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador ON (sap_maestro_empresa_dep_un_cc.lider_departamento=sap_maestro_colaborador.user_id::TEXT)
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador AS lider ON (lider.user_id::TEXT=sap_maestro_colaborador.np_lider::TEXT)
        WHERE status_departamento ='A' and lider.first_name is not null and LOWER(lider.correo_flesan) = :correo
        group by lider.user_id,correo,nombre_visitador,apellido_visitador;      
        ";

        $colaborador = DB::connection('dw_chile')->select(DB::raw($query_obra), [
            'correo' => $correo,
        ]);
        return  $colaborador;
    }


    public function getLideresObraCl()
    {
        $query = "
        select
            distinct 
                tabla_encargados_cc.correo
            from
                flesan_rrhh.tabla_encargados_cc
            join sap_maestro_empresa_dep_un_cc on
                (sap_maestro_empresa_dep_un_cc.external_code_empresa || '-' || sap_maestro_empresa_dep_un_cc.nombre_empresa = tabla_encargados_cc.sociedad
                    and sap_maestro_empresa_dep_un_cc.external_code_cc || '-' || sap_maestro_empresa_dep_un_cc.nombre_cc = tabla_encargados_cc.centro_coto)
            left join PUBLIC.maestro_rut on
                (maestro_rut.id_sap = sap_maestro_empresa_dep_un_cc.external_code_empresa)
            WHERE correo IS NOT null;
            ";
        $resultados = DB::connection('dw_chile')
            ->select(DB::raw($query));

        $correosLideresObra = collect($resultados)->pluck('correo')->toArray();

        return $correosLideresObra;
    }

    public function getLiderObraCl($correo)
    {
        $query = "
        select
            distinct 
                tabla_encargados_cc.correo
            from
                flesan_rrhh.tabla_encargados_cc
            join sap_maestro_empresa_dep_un_cc on
                (sap_maestro_empresa_dep_un_cc.external_code_empresa || '-' || sap_maestro_empresa_dep_un_cc.nombre_empresa = tabla_encargados_cc.sociedad
                    and sap_maestro_empresa_dep_un_cc.external_code_cc || '-' || sap_maestro_empresa_dep_un_cc.nombre_cc = tabla_encargados_cc.centro_coto)
            left join PUBLIC.maestro_rut on
                (maestro_rut.id_sap = sap_maestro_empresa_dep_un_cc.external_code_empresa)
            WHERE correo = :correo_lider;
            ";
        $resultados = DB::connection('dw_chile')
            ->select(DB::raw($query), [
                'correo_lider' => $correo,
            ]);
        return $resultados;
    }

    public function getAdministradorDepartamento($correo)
    {
        $query = "
        SELECT distinct  sap_maestro_empresa_dep_un_cc.lider_departamento AS np_adm_obra,lower(sap_maestro_colaborador.correo_flesan) as correo,sap_maestro_colaborador.first_name AS nombres_adm_obra,sap_maestro_colaborador.last_name AS apellidos__adm_obra FROM flesan_rrhh.sap_maestro_empresa_dep_un_cc
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador ON (sap_maestro_empresa_dep_un_cc.lider_departamento=sap_maestro_colaborador.user_id::TEXT)
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador AS lider ON (lider.user_id::TEXT=sap_maestro_colaborador.np_lider::TEXT)
        WHERE status_departamento ='A' and lider.user_id is not null and LOWER(sap_maestro_colaborador.correo_flesan) = :correo;";

        $resultados = DB::connection('dw_chile')
            ->select(DB::raw($query), [
                'correo' => $correo,
            ]);

        $correoAdminDep = collect($resultados)->pluck('correo')->toArray();

        return $correoAdminDep;
    }

    public function getVisitadorDepartamento($correo)
    {
        $query = "
        select distinct lider.user_id AS np_visitador,lower(lider.correo_flesan),lider.first_name AS nombre_visitador, lider.last_name AS apellido_visitador FROM flesan_rrhh.sap_maestro_empresa_dep_un_cc
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador ON (sap_maestro_empresa_dep_un_cc.lider_departamento=sap_maestro_colaborador.user_id::TEXT)
        LEFT JOIN flesan_rrhh.sap_maestro_colaborador AS lider ON (lider.user_id::TEXT=sap_maestro_colaborador.np_lider::TEXT)
        WHERE status_departamento ='A' and lider.first_name is not null and LOWER(lider.correo_flesan) = :correo;";

        $resultados = DB::connection('dw_chile')
            ->select(DB::raw($query), [
                'correo' => $correo,
            ]);

        $correoAdminDep = collect($resultados)->pluck('correo')->toArray();

        return $correoAdminDep;
    }

    public function CountSolicitudByUserId($userId)
{
    return DB::connection('pgsql') 
        ->table('solicitud_colaborador as sc')
        ->join('solicitudes as s', 's.id', '=', 'sc.id_solicitud')
        ->where('sc.user_id', $userId)
        ->where('sc.enable', 1)   
        ->where('s.enable', 1) 
        ->distinct('sc.id_solicitud')
        ->count('sc.id_solicitud');
}
}
