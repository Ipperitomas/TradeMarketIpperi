<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ValidadorController extends Controller
{
    //  
    private $diccionario_keys_articles = array(
        "rubro_id"=>array("type"=>"string","obligatorio"=>true),
        "nombre"=>array("type"=>"string","obligatorio"=>true),
        "descripcion"=>array("type"=>"string","obligatorio"=>true),
        "caracteristicas"=>array("type"=>"string","obligatorio"=>true),
    );
    public $msgerr = null;

    public function Validar($key = "articles",$data){
        $this->msgerr = array();
        if($key == "articles"){
            //var_dump($this->diccionario_keys_articles);
            foreach ($this->diccionario_keys_articles as $key_articles => $value_articles) {
                foreach ($data as $key => $value) {
                    if($value_articles['obligatorio']){    
                        if($key == $key_articles){
                            if($value_articles['type'] == "int"){
                                if(!is_int($value)){
                                    $this->msgerr[$key] = " El dato debe ser un entero";    
                                }
                                if(!is_string($value)){
                                    $this->msgerr[$key] = " El dato debe ser un string";    
                                }
                            }
                        }
                    }else{
                        $this->msgerr[$key] = "Dato no proporcionado";
                    }
                }
            }
        }
    }
}
