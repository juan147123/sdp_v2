<?php

namespace App\Repositories;

use App\Models\PersonalPeru;
use App\Interfaces\PersonalPeruRepositoryInterface;
use App\Interfaces\SolicitudColaboradorRepositoryInterface;
use App\Models\SolicitudColaborador;
use Illuminate\Support\Facades\DB;


class PersonalPeruRepository extends BaseRepository implements PersonalPeruRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $repositorySolicitudColaborador;
    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        PersonalPeru $model,
        SolicitudColaboradorRepositoryInterface $repositorySolicitudColaborador
    ) {
        $this->model = $model;
        $this->repositorySolicitudColaborador = $repositorySolicitudColaborador;
    }


    public function getDataByEmailLider($correo)
    {
        $query = "
            with lideres_pe as(
                select email,dni from rrhh.ejb_rrhh_planilla_ad_gf
                where email = :correo_lider
            ),
            centro_gestion as(
                select 
                    mu.ruc_empresa,
                mu.codigo_unidad,
                mu.unidad_descripcion,
                cc.norma_reparto as centro_gestion,
                upper(cc.descripcion) as des_centro_gestion 
                from ggo.sap_maestro_unidades_corp_gf mu 
                    join ggo.sap_dim_centro_gestion_costo_bi cc
                    on cc.cod_unidad_corp = mu.codigo_unidad
                    group by 
                        mu.ruc_empresa,
                mu.codigo_unidad,
                mu.unidad_descripcion,
                centro_gestion,
                des_centro_gestion 
            )
            select 
                c.dni,
                c.email ,
                c.nombres,
                c.apellido,
                c.rut_empresa,
                c.empresa,
                cg.codigo_unidad,
                cg.unidad_descripcion,
                c.centro_costo,
                cg.des_centro_gestion,
                c.area,
                c.lider_des_dni,
                c.lider_des_nombre 
                from lideres_pe lp 
                left join rrhh.ejb_rrhh_planilla_ad_gf c 
                left join centro_gestion cg on (c.rut_empresa = cg.ruc_empresa and rtrim(c.centro_costo) = cg.centro_gestion)
                on c.lider_ren_dn = lp.dni or c.lider_des_dni = lp.dni;
            ";
        $colaboradores = DB::connection('dw_peru')->select(DB::raw($query), ['correo_lider' => $correo]);
        $colaboradores = collect($colaboradores)->map(function ($colaborador) {
            $colaborador->solicitudes = $this->repositorySolicitudColaborador->CountSolicitudByUserId($colaborador->dni);
            return $colaborador;
        });
        return  $colaboradores;
    }

    public function getDniLideres()
    {
        $query = "
                select distinct l.* from 
                    (select 
                    distinct c.lider_ren_dn 
                    from rrhh.ejb_rrhh_planilla_ad_gf c
                    where c.estado = 'VIG'
                    and lider_ren_dn is not null
                    and lider_ren_dn <> 'ALLENDE MARINO JOSE FRANCISCO'
                    and lider_ren_dn <> 'MARCO MORENO MURILLO'
                    union all 
                    select 
                    distinct c.lider_des_dni  
                    from rrhh.ejb_rrhh_planilla_ad_gf c
                    where c.estado = 'VIG'
                    and lider_des_dni is not null
                    and lider_des_dni <> 'NA')
                as l;
            ";
        $resultados = DB::connection('dw_peru')
            ->select(DB::raw($query));

        $dniLideresPe = collect($resultados)->pluck('lider_ren_dn')->toArray();

        return $dniLideresPe;
    }
    
    public function UserFindByEmail($email)
    {
        $dniLideres =  $this->getDniLideres();
        return $this->model
            ->select('dni', 'email')
            ->where('email', $email)
            ->where('estado', 'VIG')
            ->whereIn('dni', $dniLideres)
            ->first();
    }
}
