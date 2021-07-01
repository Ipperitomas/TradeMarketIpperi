<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\ValidadorController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ArticlesController extends ApiResponseController
{

    protected $msgerr = null;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request,$rs_ws = true)
    {
        $response = false;
        $search = "";
        $page = 1;
        $limit = 15;
        $where = " ";
        try {
            if($request->input('page','') == "all"){
                $response = Articles::all();
            }else{
                if($request->input('page','')){
                    $page = $request->input('page','');
                }
    
                if($request->input('limit','')){
                    $limit = $request->input('limit','');
                }

                if($request->input('id','')){
                    $id = $request->input('id','');
                    $where .= " AND `articles`.`id` = ".$id;
                }
                if($request->input('nombre','')){
                    $nombre = $request->input('nombre','');
                    $where .= " AND `articles`.`nombre` LIKE '%".$nombre."%'";;
                }
                if($request->input('codigo','')){
                    $codigo = $request->input('codigo','');
                    $where .= " AND `articles`.`codigo` LIKE '%".$codigo."%'";
                }

                $sql = " SELECT articles.id as 'id' , articles.* , rubros.nombre as 'nombre_rubro' FROM articles , rubros WHERE articles.rubro_id = rubros.id";
                if($page){
                    $form = ($limit*$page)-$limit;
				    $to = $limit*$page;
				    $limit_sql = ' LIMIT '.$form.",".$to;
                }
                
                $sql = $sql.$where.$limit_sql;
                $articles_all = DB::select($sql);
                $links = array();
                $cantidad = DB::select("SELECT COUNT(FOUND_ROWS()) AS `cantidad` FROM articles");
                
                $links = $this->ArmarLinks($cantidad,$limit,$page,$request);
                $response = array("current_page"=>$page,"data"=>$articles_all,"links"=>$links);

                
                
            }
            if($rs_ws){
                return $this->sendResponse(200,$response," Se encontraron los registros exisosamente");
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
            // $validador->Validar("articles",$data_post);
            
            $reg_articles = array();
            $reg_articles['rubro_id'] = (isset($data_post['rubro_id'])) ? $data_post['rubro_id'] : $this->msgerr['rubro_id']="Dato no proporcionado."; 
            $reg_articles['nombre'] = (isset($data_post['nombre'])) ? $data_post['nombre'] : $this->msgerr['nombre']="Dato no proporcionado."; 
            $reg_articles['codigo'] = (isset($data_post['codigo'])) ? $data_post['codigo'] : $this->msgerr['codigo']="Dato no proporcionado."; 
            $reg_articles['precio'] = (isset($data_post['precio'])) ? $data_post['precio'] : $this->msgerr['precio']="Dato no proporcionado."; 
            $reg_articles['stock_max'] = (isset($data_post['stock_max'])) ? $data_post['stock_max'] : $this->msgerr['stock_max']="Dato no proporcionado."; 
            $reg_articles['stock_min'] = (isset($data_post['stock_min'])) ? $data_post['stock_min'] : $this->msgerr['stock_min']="Dato no proporcionado."; 
            $reg_articles['precio'] = (isset($data_post['precio'])) ? $data_post['precio'] : $this->msgerr['precio']="Dato no proporcionado."; 
            $reg_articles['descripcion'] = (isset($data_post['descripcion'])) ? $data_post['descripcion'] : "";
            $reg_articles['caracteristicas'] = (isset($data_post['caracteristicas'])) ? $data_post['caracteristicas'] : ""; 
            
            if(is_array($this->msgerr) && !empty($this->msgerr)){
                return $this->sendResponse(406,null,$this->msgerr);
            }
            if($last_insert = Articles::create($reg_articles)->id){
                return $this->sendResponse(200,array("last_id"=>$last_insert),"Articulo creado correctamente");
            }else{
                return $this->sendResponse(406,null,"No se pudo crear el Articulo indicado.");
            }

        }catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {
        
        $preg = '/^([0-9])*$/';
        $articles_all = false;
        try {
            $uri = $request->path();
            $uri_complete = explode("/",$uri);
            if(isset($uri_complete[2])){
                if(preg_match($preg,$uri_complete[2])){
                    $articles_all = Articles::find($uri_complete[2]);
                }else{
                    switch (mb_strtolower($uri_complete[2])) {
                        case 'getbyname':
                            $name = $request->input('name');
                            $articles_all = Articles::where('Nombre',$name)->get();
                        break;
                        case 'all':
                            $articles_all = Articles::all();
                        break;
                        
                        default:
                            
                        break;
                    }
                }
            }else{
                return $this->sendResponse(404,null,"Url invalida");
            }
            
            if(!empty($articles_all)){
                return $this->sendResponse(200,$articles_all," Se encontraron los registros exisosamente");
            }else{
                return $this->sendResponse(200,$articles_all," No se encontraron articulos");
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }   

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function edit(Articles $articles)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Articles $articles,$id)
    {
        try {
            $reg = array();
            $articulo = Articles::find($id);
            $valores_send = $request->all();
            $columns = Schema::getColumnListing('articles');
            foreach ($columns as $key => $value) {
                if(isset($valores_send[$value]) && !empty($valores_send[$value])){
                    $reg[$value] = $valores_send[$value];
                }
            }
            //var_dump($reg);
            if(Articles::where('id',$id)->update($reg)){
                return $this->sendResponse(200,Articles::find($id),"Articulo actualizado correctamente.");
            }else{
                return $this->sendResponse(404,null,"No se pudo editar el Articulo N° ".$id);
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
        
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
        try {
            $id = (int) $id;
            if(empty($id) or !is_int($id)){
                return $this->sendResponse(404,null,"No se pudo eliminar el Articulo N° ".$id);
            }
            if(Articles::where('id',$id)->delete()){
                $listado = $this->index($request,false);
                return $this->sendResponse(200,$listado,"Articulo Eliminado correctamente.");
            }else{
                return $this->sendResponse(404,null,"No se pudo Eliminar el Articulo N° ".$id);
            }
        } catch (\Exception $e) {
            throw $e;
            return $this->sendResponse(404,null,$e);
        }
    }
}
