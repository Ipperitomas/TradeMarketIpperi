<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use Illuminate\Http\Request;

class InventoryController extends ApiResponseController
{
    protected $msgerr = null;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $reg_cabecera = array();
        $reg_cuerpo = array();
        try {
            $data_post = $request->post();
            // $validador = new ValidadorController();
            if(!is_array($data_post) or empty($data_post)){
                return $this->sendResponse(404,null,"Datos incompletos.");
            }
            if(!isset($data_post['cabecera']) or empty($data_post['cabecera'])){
                return $this->sendResponse(404,null,"No se indico la cabecera.");
            }
            if(!isset($data_post['cuerpo']) or empty($data_post['cuerpo'])){
                return $this->sendResponse(404,null,"No se indico la cuerpo.");
            }
            
            DB::beginTransaction();

            $cabecera = $data_post['cabecera'];
            $reg_cabecera['fecha'] = (isset($cabecera['accion'])) ? $cabecera['fecha'] : $this->msgerr['fecha'] = "No se indico la fecha";
            // if(!Validator::date($reg_cabecera['fecha'])){
            //     $this->msgerr['fecha'] = "La fecha es invalida.";
            // }
            
            $reg_cabecera['tipo_accion'] = (isset($cabecera['accion'])) ? $cabecera['accion'] : $this->msgerr['accion'] = "No se indico la accion";
            
            if(is_array($this->msgerr) && !empty($this->msgerr)){
                return $this->sendResponse(406,null,$this->msgerr);
            }
            $reg_cabecera['created_at'] = date('Y-m-d H:i:s');
            $reg_cabecera['updated_at'] = date('Y-m-d H:i:s');
            DB::table("inventory_cabecera")->insert($reg_cabecera);
            $id = DB::getPdo()->lastInsertId();
            $cuerpo = $data_post['cuerpo'];
            if(is_iterable($cuerpo)){
                foreach ($cuerpo as $key => $value) {
                    $reg_cuerpo = array();
                    $reg_cuerpo['cabecera_id'] = $id;
                    $reg_cuerpo['articulo_id'] = $value['product_id'];
                    $reg_cuerpo['cantidad'] = $value['cantidad'];
                    $reg_cuerpo['created_at'] = date('Y-m-d H:i:s');
                    $reg_cuerpo['updated_at'] = date('Y-m-d H:i:s');
                    DB::table("inventory_renglones")->insert($reg_cuerpo);
                }
            }

            DB::commit();
            return $this->sendResponse(200,array("created"=>true),"Articulo creado correctamente");
        } catch (\Exception $e) {
            throw $e;
            DB::rollback();
            return $this->sendResponse(404,null,$e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
