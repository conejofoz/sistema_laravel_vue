<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    //protected $table = 'categorias';
    /*laravel eloquent já assume que o nome da tabela vai ser o nome da classe com a primeira letra minuscula*/
    //protected $primaryKey = 'id';
    /*também não precisa pq eloquent já assuma que a chave primaria se chamará id*/

    protected $fillable = ['nombre', 'descripcion', 'condicion'];
    /**proteger os campos aos quais vamos enviar valores */



    /*indicar que uma categoria pertence a varios artigos*/
    public function articulos(){
        return $this->hasMany('App\Articulo');
    }
}
