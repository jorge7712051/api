<?php 

/**
 * 
 */

require_once ('Database.php');

class Cliente extends DataBase
{
	
	public $NombreTabla='cliente';

	function __construct($DB)
	{
		parent::__construct($DB);
	}

	public function Consulta($ConfiguracionEnviada = array())
	{	
		$ConfiguracionDefecto = array(
            'SELECT' => array(),
            'id_cliente' => '',
            'nit' =>'',
            'razonsocial' =>'',
            'limit' => '',
            'orden' => array()            
        );

		
		$ConfiguracionExtendida = array_merge($ConfiguracionDefecto, $ConfiguracionEnviada);


		if($ConfiguracionExtendida['id_cliente'] != ''){
            $FiltroSQL[] = $this->crearFiltro('cl', 'id_cliente', $ConfiguracionExtendida['id_cliente']);
        }
        if($ConfiguracionExtendida['razonsocial'] != ''){
            $FiltroSQL[] = $this->crearFiltro('cl', 'razonsocial', $ConfiguracionExtendida['razonsocial']);
        }
     
        $ConsultaSQL = "            
        SELECT
        	cl.id_cliente, 
        	cl.nit , 
        	cl.razonsocial,
        	cl.cod_sap,
        	cl.usuario,
        	cl.codigo
        FROM
            atlas.".$this->NombreTabla." cl "                 
        .((count($FiltroSQL) > 0) ? ' WHERE '.implode(" AND ",$FiltroSQL) : '')."
        ".((isset($OrdenarPor)) ? "ORDER BY ".$OrdenarPor : '')."                     
        ".((isset($Limite)) ? $Limite : '')."
        ";
   
        $ResultadoSQL = $this->pasarelaSql($ConsultaSQL,'assoc');  
        return $ResultadoSQL;
	}
}

 ?>