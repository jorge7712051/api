<?php

/**
 * @access public
 * @copyright (c) 2018, Manejo Tenico de la Informacion
 * @version 1.0 - Clase para el manejo de base de datos
 *
 * @author v1.0 Jorge Correa <jorge.correa@tomsgreg.com>
 */
class DataBase {

    private $host;
    private $DB;
    private $user;
    private $pass;
    private $charset;
    private $PDO;
    private $Debug = false;
    private $AutoCommit;

    // connexion BD
    //function __construct($DB, $AutoCommit = true) {
    function __construct($DB = 'atlas', $AutoCommit = true) {
        //$Datos = $this->seleccionarDB($DB);
       $Datos = $this->seleccionarDB($DB);
        $this->AutoCommit = $AutoCommit;
        try {
            $DNS = "pgsql:host=" . $Datos["Servidor"] . ";dbname=" . $Datos["NombreDB"];
            $this->PDO = new PDO($DNS, $Datos["Usuario"], $Datos["Clave"]);
            $this->DB = $DB;
        } catch (PDOException $e) {
            echo "Falló la conexión: " . $e->getMessage();
            exit;
        }
        $this->setAutoCommit($AutoCommit);
    }

    protected function getDB() {
        return $this->DB;
    }

    # En caso de estar en falso el auto commit, se asigna el inicio de una transacción que debe ser confirmable al final del proceso

    public function setAutoCommit($AutoCommit) {

        $this->AutoCommit = $AutoCommit;

        if ($this->AutoCommit == false) {
            $this->PDO->beginTransaction();
        }
    }

    private function seleccionarDB($DB) {

        $BasesDeDatos = array(
            'atlaspreproduc' => array(
                'Servidor' => '10.10.253.111',
                'NombreDB' => 'atlasdbcolp',
                'Usuario' => 'postgres',
                'Clave' => 'Des2015',
                'Motor' => 'pgsql',
            ),
            'genreppreproduc' => array(
                'Servidor' => '10.10.253.111',
                'NombreDB' => 'generador_reportes',
                'Usuario' => 'postgres',
                'Clave' => 'Des2015',
                'Motor' => 'pgsql',
            ),
            'managerusersdav' => array(
                'Servidor' => '10.10.253.111',
                'NombreDB' => 'manageruserdav',
                'Usuario' => 'postgres',
                'Clave' => 'Des2015',
                'Motor' => 'pgsql',
            ),
            'atlasqa' => array(
                'Servidor' => '10.10.253.31',
                'NombreDB' => 'atlasdb',
                'Usuario' => 'QADTORRES',
                'Clave' => 'Test1974',
                'Motor' => 'pgsql',
            ),
            'managerusersqa' => array(
                'Servidor' => '10.10.253.31',
                'NombreDB' => 'manageruser',
                'Usuario' => 'QADTORRES',
                'Clave' => 'Test1974',
                'Motor' => 'pgsql',
            ),
            'genrepqa' => array(
                'Servidor' => '10.10.253.31',
                'NombreDB' => 'generador_reportes',
                'Usuario' => 'QADTORRES',
                'Clave' => 'Test1974',
                'Motor' => 'pgsql',
            ),
            'manageruserspro' => array(
                'Servidor' => '172.17.4.28',
                'NombreDB' => 'manageruser',
                'Usuario' => 'pgdba',
                'Clave' => 'mtipgsis01',
                'Motor' => 'pgsql',
            ),
            'atlas' => array(
                'Servidor' => '172.17.4.16',
                'NombreDB' => 'atlasdb',
                'Usuario' => 'usrwebdeceval',
                'Clave' => 'S76Y$tgs9oij',
                'Motor' => 'pgsql',
            ),
            'genrep' => array(
                'Servidor' => '172.17.4.16',
                'NombreDB' => 'generador_reportes',
                'Usuario' => 'postgres',
                'Clave' => 'mt14tl45',
                'Motor' => 'pgsql',
            ),
        );
        return $BasesDeDatos[$DB];
    }

//ejecucion de sonsultas select sin uso de la sentencia preparada
    public function pasarelaSql($Sql, $Metodo = "assoc") {
        return $this->selectSimple($Sql, $Metodo);
    }

//ejecucion de sonsultas select uso de la sentencia preparada
    public function preparaSql($Sql, $Metodo = "assoc", $Filtros) {
        return $this->ConsultaPreparada($Sql, $Metodo, $Filtros);
    }

    protected function selectSimple($Sql, $Metodo = "") {
        try {
            $GSent = $this->PDO->prepare($Sql);
            $Respuesta = $GSent->execute();
            $fetch_style = $this->numAssoc($Metodo);
            return $GSent->fetchAll($fetch_style);
        } catch (Exception $e) {
            echo "Falló la consulta: " . $e->getMessage() . "<br />Codigo error: " . $this->GBD->errorCode() . "<br />Descripción error: " . $this->GBD->errorInfo();
            echo "<pre>$Sql</pre>";
            $this->reversar();
            exit;
        }
    }

    protected function ConsultaPreparada($Sql, $Metodo = "", $Filtros) {

        try {

            $STH = $this->PDO->prepare($Sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
            $STH->execute($Filtros);
            $fetch_style = $this->numAssoc($Metodo);
            return $STH->fetchAll($fetch_style);
        } catch (Exception $e) {
            echo "Falló la consulta: " . $e->getMessage() . "<br />Codigo error: " . $this->GBD->errorCode() . "<br />Descripción error: " . $this->GBD->errorInfo();
            echo "<pre>$Sql</pre>";
            $this->reversar();
            exit;
        }
    }

    protected function numAssoc($Metodo = "assoc") {
        if ($Metodo == "both") {
            $fetch_style = PDO::FETCH_BOTH;
        } else if ($Metodo == "num") {
            $fetch_style = PDO::FETCH_NUM;
        } else {
            $fetch_style = PDO::FETCH_ASSOC;
        }
        return $fetch_style;
    }

    protected function crearFiltro($AliasTabla, $CampoTabla, $Contenido) {

        $Contenido = $this->sanarContenidoParaConsultaSQL($Contenido);

        ($AliasTabla != '') ? $PreIndicardor = $AliasTabla . "." : $PreIndicardor = '';

        $Filtro = '';
        switch ($CampoTabla) {
            case 'loginuser':
                $Filtro = $PreIndicardor . 'loginuser ' . ((is_array($Contenido)) ? "IN (" . implode(",", $Contenido) . ")" : "= :" . $CampoTabla . "");
                break;

            case 'passuser':
                $Filtro = $PreIndicardor . 'passuser ' . ((is_array($Contenido)) ? "IN (" . implode(",", $Contenido) . ")" : "= :" . $CampoTabla . "");
                break;

            case 'id_perfil':
                $Filtro = $PreIndicardor . 'id_perfil ' . ((is_array($Contenido)) ? "IN (" . implode(",", $Contenido) . ")" : " = " . $Contenido . "");
                break;

            case 'id_application':
                $Filtro = $PreIndicardor . 'id_application ' . ((is_array($Contenido)) ? "IN (" . implode(",", $Contenido) . ")" : " = " . $Contenido . "");
                break;

            case 'id_cliente':
                $Filtro = $PreIndicardor . 'id_cliente ' . ((is_array($Contenido)) ? "IN (" . implode(",", $Contenido) . ")" : " = :" . $CampoTabla . "");
                break;

            case 'razonsocial' :
                $Filtro = $PreIndicardor . 'razonsocial ' . ((is_array($Contenido)) ? "IN ('" . implode("','", $Contenido) . "')" : "LIKE '" . $Contenido . "%'");

            default:
                break;
        }
        return $Filtro;
    }

    protected function sanarContenidoParaConsultaSQL($input) {

        if (is_array($input)) {
            $output = array();
            foreach ($input as $var => $val) {
                $output[$var] = $this->sanarContenidoParaConsultaSQL($val);
            }
        } else {
            if (!get_magic_quotes_gpc()) {
                $search = array("\\" => "\\\\", "'" => "''");
                $input = $this->str_replace_deep($search, $input);
            } else {
                $input = addslashes($input);
            }
            $output = $this->validarSQLInjection($input);
        }
        return $output;
    }

    private function str_replace_deep(array $replace, $subject) {

        if (is_array($subject)) {
            foreach ($subject as &$oneSubject)
                $oneSubject = str_replace_deep($replace, $oneSubject);
            unset($oneSubject);
            return $subject;
        } else {
            return str_replace(array_keys($replace), array_values($replace), $subject);
        }
    }

    private function validarSQLInjection($Contenido) {

        $Examina = strtoupper($Contenido);
        $RegExpre = "(('(''|[^'])*')|(;)|(\b(ALTER|GRANT|CREATE|DELETE|DROP|EXEC(UTE){0,1}|INSERT( +INTO){0,1}|MERGE|SELECT|UPDATE|UNION( +ALL){0,1})\b))";

        if (preg_match($RegExpre, $Examina)) {


            exit;
        } else {
            return $Contenido;
        }
    }

    protected function reversar() {

        if ($this->AutoCommit == false) {
            $this->PDO->rollBack();
        }
    }

    protected function crearPar($Datos, $FiltroSQL = array()) {

        $NuevosDatos = array();
        foreach ($Datos as $key => $value) {
            if ($value != '' && $value != null) {
                if (count($FiltroSQL > 0)) {
                    foreach ($FiltroSQL as $llave => $valor) {
                        $posicion_coincidencia = strpos($valor, ":" . $key);
                        ($posicion_coincidencia) ? $NuevosDatos[":" . $key] = $value : '';
                    }
                } else {
                    $NuevosDatos[":" . $key] = $value;
                }
            }
        }

        return $NuevosDatos;
    }

    protected function creaOrden($OrdenarPor) {

        $Orden = '';
        foreach ($OrdenarPor AS $Campo => $ModoOrden) {

            if ($Orden != '')
                $Orden .= ', ';
            $Orden .= $Campo . " " . $ModoOrden;
        }
        return $Orden;
    }

    protected function crearUpdate($Set, $Where, $Tabla, $Returning = "",$esquema) {

        if (count($Set) > 0 and count($Where) > 0) {

            return $this->update( $esquema.$Tabla, $Set, $Where, $Returning);
        }
    }

    protected function update($Tabla, $ArSet, $ArWhere, $Returning = "", $Metodo = "") {

        if (count($ArSet) > 0 and count($ArWhere) > 0) {

            $Set = $this->crearDatos($ArSet, "Set");
            $Where = $this->crearDatos($ArWhere, "Where");
            $Sql = "
            UPDATE " . $Tabla . " 
            SET " . implode(",", $Set["Pares"]) . "
            WHERE " . implode(" AND ", $Where["Pares"]) . "
            " . (($Returning != "") ? "RETURNING " . $Returning : "") . "
            ";
            $GSent = $this->PDO->prepare($Sql);
            /*
              if($this->getDebug() == true){
              echo "<pre>$Sql";
              print_r($Set["Bind"]);
              print_r($Where["Bind"]);
              echo "</pre>";
              } */
            $Respuesta = $GSent->execute(array_merge($Set["Bind"], $Where["Bind"]));

            if ($Respuesta == false) {

                echo "Falló la consulta: <br />Descripción error: " . implode("<br />", $GSent->errorInfo()) . "<br />";
                echo "<pre>$Sql</pre>";
                $this->reversar();
                exit;
            }

            if ($Returning == "") {
                return array();
            } else {
                $fetch_style = $this->numAssoc($Metodo);
                return $GSent->fetchAll($fetch_style);
            }
        }
    }

    protected function crearDatos($ArPares, $Tipo) {

        $Pares = array();
        $Bind = array();
        foreach ($ArPares as $Campo => $Valor) {

            $Pares[] = $Campo . " = :" . $Tipo . $Campo;
            $Bind[":" . $Tipo . $Campo] = $Valor;
        }

        return array(
            "Pares" => $Pares,
            "Bind" => $Bind
        );
    }

}

?>