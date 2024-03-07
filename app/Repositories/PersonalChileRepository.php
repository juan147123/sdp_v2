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
            ->where('correo_flesan', $email)
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
        m.nombre_un AS unidad_negocio,
        m.external_code_un AS id_unidad_negocio,
        c.nombre_departamento,
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
            $colaborador->solicitudes = $this->repositorySolicitudColaborador->CountSolicitudByUserId($colaborador->user_id);
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
            join sap_maestro_empresa_dep_un_cc on
                (sap_maestro_empresa_dep_un_cc.external_code_empresa || '-' || sap_maestro_empresa_dep_un_cc.nombre_empresa = tabla_encargados_cc.sociedad
                    and sap_maestro_empresa_dep_un_cc.external_code_cc || '-' || sap_maestro_empresa_dep_un_cc.nombre_cc = tabla_encargados_cc.centro_coto)
            left join PUBLIC.maestro_rut on
                (maestro_rut.id_sap = sap_maestro_empresa_dep_un_cc.external_code_empresa)
            where
                correo is not null)
        select 
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
            m.nombre_un AS unidad_negocio,
            m.external_code_un AS id_unidad_negocio,
            c.nombre_departamento,
            c.division,
            c.nombre_division
        from cecos_lider cl 
        inner join flesan_rrhh.sap_maestro_colaborador c on cl.external_code_cc = c.centro_costo
        inner join flesan_rrhh.sap_maestro_cargos smc on c.external_cod_cargo = smc.external_code
        inner join public.maestro_rut mr  ON mr.id_sap = c.empresa
        inner JOIN flesan_rrhh.sap_maestro_empresa_dep_un_cc m ON m.external_code_cc = c.centro_costo
        where smc.planta_noplanta = 'NP'
        and c.empl_status = '41111'
        and cl.correo_lider = :correo_lider
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
            $colaborador->solicitudes = $this->repositorySolicitudColaborador->CountSolicitudByUserId($colaborador->user_id);
            return $colaborador;
        });
        return  $colaboradores;
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
    
    public function getAdministradorDepartamento()
    {
        $query = "
        select LOWER(smc.correo_flesan)  as correo from sap_maestro_colaborador smc where smc.user_id  in (
            select distinct  l.lider_departamento::INT from flesan_rrhh.sap_maestro_empresa_dep_un_cc l
            where l.status_cc = 'A' and l.status_departamento = 'A'
            and l.lider_departamento <> ''
            )
            and smc.empl_status = '41111';";

        $resultados = DB::connection('dw_chile')
            ->select(DB::raw($query));

        $correoAdminDep = collect($resultados)->pluck('correo')->toArray();

        return $correoAdminDep;
    }
}
