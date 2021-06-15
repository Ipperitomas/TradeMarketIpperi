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
    public function index(Request $request)
    {
        $rubros_all = false;
        try {
            if($request->input('all','')){
                $rubros_all = Rubros::all();
            }else{
                $rubros_all = DB::table('rubros')->orderBy('id','asc')->paginate(15);
            }
            return $this->sendResponse(200,$rubros_all," Se encontraron registros exisosamente");
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
        $articles_all = false;
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
    public function destroy($id)
    {
        //
    }
}
