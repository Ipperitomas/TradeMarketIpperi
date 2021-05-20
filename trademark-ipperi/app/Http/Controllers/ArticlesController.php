<?php

namespace App\Http\Controllers;

use App\Models\Articles;
use Illuminate\Http\Request;
use App\Http\Controllers\ValidadorController;
class ArticlesController extends ApiResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function index(Request $request)
    {
        $articles_all = false;
        try {
            $articles_all = Articles::all();
            return $this->sendResponse(200,$articles_all," Se encontraron los registros exisosamente");
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
       $data_post = $request->post();
       $validador = new ValidadorController();
       //var_dump($data_post);
       if(!is_array($data_post) or empty($data_post)){
            return $this->sendResponse(404,null,"Datos incompletos.");
       }
       $validador->Validar("articles",$data_post);
       var_dump($validador->msgerr);
       if(is_array($validador->msgerr) && !empty($validador->msgerr)){
        return $this->sendResponse(406,null,$validador->msgerr);
       }
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $articles_all = false;
        try {
            $articles_all = Articles::find($id);
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Articles $articles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Articles  $articles
     * @return \Illuminate\Http\Response
     */
    public function destroy(Articles $articles)
    {
        //
    }
}
