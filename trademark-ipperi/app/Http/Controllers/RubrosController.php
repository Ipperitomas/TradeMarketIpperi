<?php

namespace App\Http\Controllers;

use App\Models\Rubros;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;

class RubrosController extends ApiResponseController
{
    protected $msgerr = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$rs_ws = true)
    {
        $rubros_all = false;
        $page = 1;
        $limit = 15;
        $where = "";
        try {
            if($request->input('page','') == "all"){
                $response = Rubros::all();
            }else{
                if($request->input('page','')){
                    $page = $request->input('page','');
                }
    
                if($request->input('limit','')){
                    $limit = $request->input('limit','');
                }

                
                if($request->input('nombre','')){
                    $nombre = $request->input('nombre','');
                    $where .= " AND `rubros`.`nombre` LIKE '%".$nombre."%'";;
                }

                $sql = " SELECT rubros.* FROM rubros WHERE 1 = 1";
                if($page){
                    $form = ($limit*$page)-$limit;
				    $to = $limit*$page;
				    $limit_sql = ' LIMIT '.$form.",".$to;
                }
                
                $sql = $sql.$where.$limit_sql;
                $rubros_all = DB::select($sql);
                $links = array();
                $cantidad = DB::select("SELECT COUNT(FOUND_ROWS()) AS `cantidad` FROM articles");
                
                $links = $this->ArmarLinks($cantidad,$limit,$page,$request);
                $response = array("current_page"=>$page,"data"=>$rubros_all,"links"=>$links);
            }
            if($rs_ws){
                return $this->sendResponse(200,$response," Se encontraron registros exisosamente");
            }else{
                return $response;
            }
                
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data_post = $request->post();
            // $validador = new ValidadorController();
            if(!is_array($data_post) or empty($data_post)){
                return $this->sendResponse(404,null,"Datos incompletos.");
            }
            
            $reg_rubros = array();
            $reg_rubros['nombre'] = (isset($data_post['nombre'])) ? $data_post['nombre'] : $this->msgerr['nombre']="Dato no proporcionado."; 
            //hacer este control
            // if(Rubros::where("nombre",mb_strtolower($reg_rubros['nombre']))){
            //     $this->msgerr['error'] = " El Rubro que intenta crear ya existe";
            // }
            if(is_array($this->msgerr) && !empty($this->msgerr)){
                return $this->sendResponse(406,null,$this->msgerr);
            }
            if($last_id = Rubros::create($reg_rubros)->id){
                return $this->sendResponse(200,array("last_id"=>$last_id),"Rubro creado correctamente");
            }else{
                return $this->sendResponse(406,null,"No se pudo crear el Rubro indicado.");
            }
        }catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        $reg = '/^([0-9])*$/';
        $rubros_all = false;
        try {
            $uri = $request->path();
            $uri_complete = explode("/",$uri);
            if(isset($uri_complete[2])){
                if(preg_match($reg,$uri_complete[2])){
                    $rubros_all = Rubros::find($uri_complete[2]);
                }else{
                    switch (mb_strtolower($uri_complete[2])) {
                        case 'getbyname':
                            $name = $request->input('name');
                            $rubros_all = Rubros::where('nombre',$name)->get();
                        break;
                        case 'all':
                            $rubros_all = Rubros::all();
                        break;
                        
                        default:
                            
                        break;
                    }
                }
            }else{
                return $this->sendResponse(404,null,"Url invalida");
            }
            
            if(!empty($rubros_all)){
                return $this->sendResponse(200,$rubros_all," Se encontraron los registros exisosamente");
            }else{
                return $this->sendResponse(200,$rubros_all," No se encontraron articulos");
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
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
        try {
            $reg = array();
            $articulo = Rubros::find($id);
            $valores_send = $request->all();
            $columns = Schema::getColumnListing('rubros');
            foreach ($columns as $key => $value) {
                if(isset($valores_send[$value]) && !empty($valores_send[$value])){
                    if($value != "id"){
                        $reg[$value] = $valores_send[$value];
                    }
                }
            }
            if(Rubros::where('id',$id)->update($reg)){
                return $this->sendResponse(200,Rubros::find($id),"Rubro actualizado correctamente.");
            }else{
                return $this->sendResponse(404,null,"No se pudo editar el Rubro N° ".$id);
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try {
            $id = (int) $id;
            if(empty($id) or !is_int($id)){
                return $this->sendResponse(404,null,"No se pudo eliminar el Rubro N° ".$id);
            }
            if(Rubros::where('id',$id)->delete()){
                $listado = $this->index($request,false);
                return $this->sendResponse(200,$listado,"Rubro Eliminado correctamente.");
            }else{
                return $this->sendResponse(404,null,"No se pudo eliminar el Rubro N° ".$id);
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }
}
