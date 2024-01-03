<?php

namespace App\Repositories;

use App\Models\PersonalPeru;
use App\Interfaces\PersonalPeruRepositoryInterface;
use App\Models\SolicitudColaborador;
use Illuminate\Support\Facades\DB;


class PersonalPeruRepository extends BaseRepository implements PersonalPeruRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;
    protected $modelSolicitudColaborador;
    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(
        PersonalPeru $model,
        SolicitudColaborador $modelSolicitudColaborador
    ) {
        $this->model = $model;
        $this->modelSolicitudColaborador = $modelSolicitudColaborador;
    }


    public function getDataByEmailLider($correo)
    {
        $empresas = [];
        $unidades = [];
        $areas = [];
        $centros_costo = [];
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

        $colaboradores = collect($colaboradores)->map(function ($colaborador) use (&$empresas, &$unidades, &$areas, &$centros_costo) {
            $this->getEmpresas($colaborador, $empresas);
            $this->getUnidades($colaborador, $unidades);
            $this->getCentroCosto($colaborador, $centros_costo);
            $this->getArea($colaborador, $areas);
            $colaborador->solicitudes = $this->modelSolicitudColaborador
                ->where('user_id', $colaborador->dni)
                ->count();
            return $colaborador;
        });

        return array(
            "colaboradores" => $colaboradores,
            "empresas" => $empresas,
            'unidades' => $unidades,
            'centrosCosto' => $centros_costo,
            'areas' => $areas,
        );
    }

    public function getEmpresas($data, &$empresas)
    {
        $empresa = [
            'id' => $data->rut_empresa,
            'descripcion' => $data->empresa,
        ];

        if (!in_array($empresa, $empresas)) {
            array_push($empresas, $empresa);
        }
    }

    public function getUnidades($data, &$unidades)
    {
        $unidad = [
            'id' => $data->codigo_unidad,
            'cod_empresa' => $data->rut_empresa,
            'descripcion' => $data->unidad_descripcion,
        ];
        if (!in_array($unidad, $unidades)) {
            array_push($unidades, $unidad);
        }
    }


    public function getCentroCosto($data, &$centros_costo)
    {
        $centro_costo = [
            'id' => $data->centro_costo,
            'cod_unidad' => $data->codigo_unidad,
            'cod_empresa' => $data->rut_empresa,
            'descripcion' => $data->des_centro_gestion,
        ];
        if (!in_array($centro_costo, $centros_costo)) {
            array_push($centros_costo, $centro_costo);
        }
    }

    public function getArea($data, &$areas)
    {
        $area = [
            'id' => $data->area,
            'cod_empresa' => $data->rut_empresa,
            'cod_unidad' => $data->codigo_unidad,
            'descripcion' => $data->area
        ];
        if (!in_array($area, $areas)) {
            array_push($areas, $area);
        }
    }
}
