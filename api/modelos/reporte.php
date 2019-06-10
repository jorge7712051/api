<?php 

/**
 * 
 */

require_once ('Database.php');

class Reporte extends Database
{
	public $Cliente;

	function __construct($DB)
	{
		parent::__construct($DB);
	}


	public function ConsultaInventario($id_cliente)
    {
         $ConsultaSQL="SELECT
         ca.numcontrato ,ca.numidentificacion ,c.razonsocial,de.id_deceval
         FROM atlas.detalle_carpeta_deceval de 
         inner join atlas.carpeta ca on ca.id_carpeta = de.id_carpeta 
         inner join atlas.cliente c on c.id_cliente = ca.id_cliente
         inner join atlas.estado e on (ca.id_estado=e.id_estado and e.custodia = 'S')
         where ca.id_cliente  = ".$id_cliente." and de.id_deceval > 0";
         $ResultadoSQL= $this->pasarelaSql($ConsultaSQL,'assoc');
        return $ResultadoSQL;       
    }
}
 ?>