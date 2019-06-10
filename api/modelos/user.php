<?php

/**
 * 
 */
require_once ('Database.php');


class User extends DataBase
{
  public $NombreTabla="mu_users";
  public $TablaPerfiles="mu_perfiloptions";
  

function __construct($DB)
  {
    parent::__construct($DB);
  }

  public function Consulta($ConfiguracionEnviada = array())
  {  
      $ConfiguracionDefecto = array(
            'SELECT' => array(),
            'loginuser' => '',
            'passuser' =>'',
            'limit' => '',
            'id_application'=>'',
            'orden' => array()            
        );

    $ConfiguracionExtendida = array_merge($ConfiguracionDefecto, $ConfiguracionEnviada);


    if($ConfiguracionExtendida['loginuser'] != ''){
            $FiltroSQL[] = $this->crearFiltro('u', 'loginuser', $ConfiguracionExtendida['loginuser']);
  }
        if($ConfiguracionExtendida['passuser'] != ''){
            $FiltroSQL[] = $this->crearFiltro('u', 'passuser', $ConfiguracionExtendida['passuser']);
        }
        if($ConfiguracionExtendida['id_application'] != ''){
            $FiltroSQL[] = $this->crearFiltro('uapp', 'id_application', $ConfiguracionExtendida['id_application']);
        }


      
        $ConsultaSQL = "            
        SELECT
          u.loginuser as login,
          u.id_user ,
          u.passuser ,
          u.lastname,
          u.firstname,
          u.mailuser,
          u.num_identification as identificacion,
          u.disabled,
          uapp.id_profile,
          uapp.oficina,
          uapp.id_cliente
        FROM
            ".$this->NombreTabla." u 
        LEFT JOIN  mu_userapps uapp ON u.id_user = uapp.id_user "                 
        .((count($FiltroSQL) > 0) ? ' WHERE '.implode(" AND ",$FiltroSQL) : '')."
        ".((isset($OrdenarPor)) ? "ORDER BY ".$OrdenarPor : '')."                     
        ".((isset($Limite)) ? $Limite : '')."
        "; 
        $filtros=array();
        $filtros=$this->crearPar($ConfiguracionExtendida,$FiltroSQL);        
        $ResultadoSQL = $this->preparaSql($ConsultaSQL,'assoc',$filtros);
        return $ResultadoSQL;  
  }




    public function ConsultaOpciones($ConfiguracionEnviada = array())
  { 
    $ConfiguracionDefecto = array(
          'id_perfil'=>'',
            'limit' => '',
            'orden' => array('op.orden'=>'ASC')            
        );

    $ConfiguracionExtendida = array_merge($ConfiguracionDefecto, $ConfiguracionEnviada);  

    if($ConfiguracionExtendida['id_perfil'] != ''){
            $FiltroSQL[] = $this->crearFiltro('po', 'id_perfil', $ConfiguracionExtendida['id_perfil']);
        }
    if(is_array($ConfiguracionExtendida['orden']) && count($ConfiguracionExtendida['orden']) > 0){
            $OrdenarPor = $this->creaOrden($ConfiguracionExtendida['orden']);
        }
        
      
        $ConsultaSQL = "            
        SELECT
          op.descripcion,
          op.name_action as url,
          op.modulo,
          op.icono,
          op.orden
        FROM
            ".$this->TablaPerfiles." po 
        LEFT JOIN mu_options op on po.id_option= op.id_option"                 
        .((count($FiltroSQL) > 0) ? ' WHERE '.implode(" AND ",$FiltroSQL) : '')."
        ".((isset($OrdenarPor)) ? "ORDER BY ".$OrdenarPor : '')."                     
        ".((isset($Limite)) ? $Limite : '')."
        "; 
        $ResultadoSQL = $this->pasarelaSql($ConsultaSQL,'assoc');  
        return $ResultadoSQL;
  }

  public function editar($Set = array(), $Where = array(), $Tabla ,$Returning = "") {
        
        if(count($Set) > 0 and count($Where) > 0){
            
            return $this->crearUpdate($Set, $Where,$Tabla,"","" );
        }
    }


}



  ?>