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
    public function index(Request $request)
    {   
        $page = 1;
        $limit = 15;
        $limit_sql = "";
        // $orden = " fecha DESC ";
        try {
            if($request->input('page','')){
                $page = $request->input('page','');
            }

            if($request->input('limit','')){
                $limit = $request->input('limit','');
            }

            if($request->input('all','')){
                
            }else{
                $sql = " SELECT inventory_renglones.id , inventory_cabecera.tipo_accion , inventory_cabecera.fecha , articles.nombre as 'nombre_articulo' , rubros.nombre as 'nombre_rubro' , inventory_renglones.cantidad , articles.precio , (articles.precio*inventory_renglones.cantidad) as 'p_total' FROM inventory_renglones , inventory_cabecera , articles , rubros WHERE inventory_renglones.cabecera_id = inventory_cabecera.id AND inventory_renglones.articulo_id = articles.id AND articles.rubro_id = rubros.id";
                if($page){
                    $form = ($limit*$page)-$limit;
				    $to = $limit*$page;
				    $limit_sql = ' LIMIT '.$form.",".$to;
                }
                
                $sql = $sql.$limit_sql;
                $inventory_all = DB::select($sql);
                $links = array();
                $cantidad = DB::select("SELECT COUNT(FOUND_ROWS()) AS `cantidad` FROM inventory_renglones");
                
                $links = $this->ArmarLinks($cantidad,$limit,$page,$request);
                $response = array("current_page"=>$page,"data"=>$inventory_all,"links"=>$links);

            }
            return $this->sendResponse(200,$response," Se encontraron registros exisosamente");
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }


    private function ArmarLinks($cantidad,$limit,$page,$request){
        $i = 0;
        $links = array();
        if($cantidad[0]->cantidad > $limit){
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Previous"; 
            $links[$i]["active"] = false; 
            $cant_pages = ceil($cantidad[0]->cantidad/$page);
            for ($i=1; $i < $cant_pages; $i++) { 
                $links[$i]["url"] = url('/')."/".$request->path()."?page=".$i; 
                $links[$i]["label"] = $i; 
                $links[$i]["active"] = false;     
            }
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Next"; 
            $links[$i]["active"] = false; 
            
        }else{
            $cant_pages = 1;

            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Previous"; 
            $links[$i]["active"] = false; 
            $i++;
            $links[$i]["url"] = url('/')."/".$request->path()."?page=".$cant_pages;; 
            $links[$i]["label"] = "1"; 
            $links[$i]["active"] = true; 
            $i++;
            $links[$i]["url"] = null; 
            $links[$i]["label"] = "Next"; 
            $links[$i]["active"] = false; 
        }

        return $links;

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
            $reg_cabecera['nro_comprobante'] = (isset($cabecera['nro_comprobante'])) ? $cabecera['nro_comprobante'] : $this->msgerr['nro_comprobante'] = "No se indico el nro_comprobante";
            
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
    public function show($id,Request $request)
    {
        
        $page = 1;
        $limit = 10;
        if($id == "heads"){
            try {
                if($request->input('page','')){
                    $page = $request->input('page','');
                }

                if($request->input('limit','')){
                    $limit = $request->input('limit','');
                }
    
                if($request->input('all','')){
                    
                }else{
                    $sql = " SELECT * FROM  inventory_cabecera ";
                    if($page){
                        $form = ($limit*$page)-$limit;
                        $to = $limit*$page;
                        $limit_sql = ' LIMIT '.$form.",".$to;
                    }
                    
                    $sql = $sql.$limit_sql;
                    $inventory_all = DB::select($sql);
                    $links = array();
                    $cantidad = DB::select("SELECT COUNT(FOUND_ROWS()) AS `cantidad` FROM inventory_cabecera");
                    
                    $links = $this->ArmarLinks($cantidad,$limit,$page,$request);
                    $response = array("current_page"=>$page,"data"=>$inventory_all,"links"=>$links);
    
                }
                return $this->sendResponse(200,$response," Se encontraron registros exisosamente");
            } catch (\Exception $e) {
                throw $e;
                return $this->sendResponse(404,null,$e);
            }

        }
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
