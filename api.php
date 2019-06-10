<?php

require_once('./api/Rest.php');


class API extends REST {
    
    private $buscarcontenido = null;
    public function __construct() 
    {
        parent::__construct(); // Inicializa 
    }

    public function processApi() 
    {
        $func = strtolower(trim(str_replace("/", "", $_REQUEST['request'])));
        if ((int) method_exists($this, $func) > 0)
            $this->$func();
        else
            $this->response('', 404);
    }

    private function metodoLogin() 
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        
        $Configuracion = array(
            'loginuser' => $_POST['loginuser'],
            'passuser' => $_POST['passuser'],
            'id_application' => $_POST['id_application']
        );
   
        $ArchivoModelo= './api/modelos/'.$this->_request['modelo'].'.php';
        if(file_exists($ArchivoModelo))
        {
            include $ArchivoModelo;
            $this->User = new User($_POST['base']);
            $datos = $this->User->Consulta($Configuracion );        
            $this->response($this->json($datos), 200);
        }
        else{
            $this->response('', 405);
        }
        
        //$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        //$this->response($this->json($error), 400);
    }

    private function GetMenu() 
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        
        $Configuracion = array(
            'id_perfil' => $_POST['id_perfil'],            
        );   
        $ArchivoModelo= './api/modelos/'.$this->_request['modelo'].'.php';
        if(file_exists($ArchivoModelo))
        {
            include $ArchivoModelo;
            $this->User = new User($_POST['base']);
            $datos = $this->User->ConsultaOpciones($Configuracion );        
            $this->response($this->json($datos), 200);
        }
        else{
            $this->response('', 405);
        }
        
        //$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        //$this->response($this->json($error), 400);
    }

    private function EditarPassword() 
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }       
       
        $ArchivoModelo= './api/modelos/'.$this->_request['modelo'].'.php';
        if(file_exists($ArchivoModelo))
        {
            include $ArchivoModelo;
            //print_r(array('error' =>'ok'));
            $this->User = new User($_POST['base']);
            $this->User->editar(
                array('passuser'=>$_POST['passuser']),
                array('id_user'=>$_POST['id_user']),
                $_POST['NombreTabla']);        
            $this->response($this->json(array('error' =>'ok')),200);
        }
        else{
            $this->response($this->json(array('error' =>'ok')), 405);
        }
        
        //$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        //$this->response($this->json($error), 400);
    }


    private function getClientes() 
    {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }
        
         
        $ArchivoModelo= './api/modelos/'.$this->_request['modelo'].'.php';
        if(file_exists($ArchivoModelo))
        {
            include $ArchivoModelo;
            $this->Cliente = new Cliente($_POST['base']);
            $datos = $this->Cliente->Consulta($_POST['configuracionconsulta'] );        
            $this->response($this->json($datos), 200);
        }
        else{
            $this->response('', 405);
        }
        
        //$error = array('status' => "Failed", "msg" => "Invalid Email address or Password");
        //$this->response($this->json($error), 400);
    }


    private function getReporte() {
        if ($this->get_request_method() != "POST") {
            $this->response('', 406);
        }

        $ArchivoModelo= './api/modelos/'.$this->_request['modelo'].'.php';
        if(file_exists($ArchivoModelo))
        {
            include $ArchivoModelo;
            $this->Reporte = new Reporte($_POST['base']);
            $datos = $this->Cliente->ConsultaInventario($_POST['id_cliente'] );        
            $this->response($this->json($datos), 200);
        }
        else{
            $this->response('', 405);
        }
    }
    
    private function json($data) {
        if (is_array($data)) {
            return json_encode($data);
        }
    }

}

$api = new API;
$api->processApi();
