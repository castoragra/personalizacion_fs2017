<?php

/**
 * Description of campos de valores
 *
 * @author Cástor Agra
 */
class campos extends fs_controller {

    public $campo;
    public $resultados;
    public $tipos;
    public $controladores;
    public $requerido;

    public function __construct() {
        //parent::__construct(__CLASS__, 'Campos de valores', 'admin', FALSE, FALSE);
        parent::__construct(__CLASS__, 'Campo personalizado', '');
    }

    protected function private_core() {
        parent::private_core();
        $this->share_extensions();

        //$this->template = 'campo';
        $this->campo = FALSE;
        $atr1 = new campo();

        $accion = (isset($_POST['accion']) ? $_POST['accion'] : '');

        switch ($accion) {
            case 'nuevo':
                $this->nuevo_campo($atr1);
                break;
            case 'eliminar':
                $this->eliminar_campo($atr1);
                break;
            case 'editar':
                if (isset($_POST['id'])) {
                    $this->campo = $atr1->get($_POST['id']);
                    $this->editar_campo();
                }
                break;
            default:
                if (isset($_REQUEST['cod']) && $_REQUEST['cod'] != 0) {
                    $this->campo = $atr1->get($_REQUEST['cod']);
                } else {
                    $this->campo = new \campo();
                }

                $grupo = new cp_grupo();
                $tabla = new cp_tabla();
                break;
        }

        $lista = new lista();
        $llvv = new lista_valor();
        $this->tipos = $llvv->all_from_codlista('cboTiposCampos');
        //$this->controladores = $llvv->all_from_codlista('cboControlador');
        $tablas_controladores = new cp_tabla();
        $this->controladores = $tablas_controladores->get_controladores();

        $this->requerido = $llvv->all_from_codlista('cboRequerido');
    }

    private function share_extensions() {
//        $fsext = new fs_extension();
//        $fsext->name = 'btn_campos';
//        $fsext->from = __CLASS__;
//        $fsext->to = 'ventas_cliente';
//        $fsext->params = '&back=ventas_cliente';
//        $fsext->type = 'tab';
//        $fsext->text = '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>'
//                . '<span class="hidden-xs">&nbsp; Campos</span>';
//        $fsext->save();
    }

    public function url() {
        return parent::url();
    }

    public function url_lista() {
        return "index.php?page=list_campo";
    }

//    public function url_back() {
//        if (isset($_GET["back"])) {
//            return "index.php?page=" . $_GET["back"];
//        } else
//            return parent::url();
//    }
    /// Campos
    private function nuevo_campo(&$atr1) {
        //$atr1->id = 0;
        //$atr1->codcampo = substr($_POST['nuevo'], 0, 20);
        $atr1->controlador = $_POST['controlador'];
        $atr1->campo = $_POST['campo'];
        $atr1->nombre = $_POST['nombre'];
        $atr1->tipo = $_POST['tipo'];
        $atr1->requerido = (isset($_POST['requerido']) && $_POST['requerido']) ? 1 : 0;
        $atr1->minimo = $_POST['minimo'];
        $atr1->maximo = $_POST['maximo'];
        $atr1->msg_error = $_POST['msg_error'];

            $this->new_message('asdfasdfsdfsd guardado correctamente.');
        if ($atr1->save()) {
            $this->new_message('Campo guardado correctamente.');
            $this->campo = $atr1;
        } else {
            $this->new_error_msg('Error al crear el campo.');
        }
    }

    private function eliminar_campo(&$atr1) {
        $campo = $atr1->get($_POST['id']);
        if ($campo) {
            if ($campo->delete()) {
                $this->new_message('Campo eliminado correctamente.');

                header('Location: ' . $this->url_lista());
            } else {
                $this->new_error_msg('Imposible eliminar el campo.');
            }
        }
    }

    private function editar_campo() {
        $this->campo->controlador = $_POST['controlador'];
        $this->campo->campo = $_POST['campo'];
        $this->campo->nombre = $_POST['nombre'];
        $this->campo->tipo = $_POST['tipo'];
        $this->campo->requerido = (isset($_POST['requerido']) && $_POST['requerido']) ? 1 : 0;
        $this->campo->minimo = $_POST['minimo'];
        $this->campo->maximo = $_POST['maximo'];
        $this->campo->msg_error = $_POST['msg_error'];

        if ($this->campo->save()) {
            $this->new_message('Datos guardados correctamente.');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

}
