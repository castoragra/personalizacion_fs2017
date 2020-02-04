<?php

/**
 * Description of listas de valores
 *
 * @author Cástor Agra
 */
class listas extends fs_controller {

    public $lista;
    public $resultados;

    public function __construct() {
        //parent::__construct(__CLASS__, 'Listas de valores', 'admin', FALSE, FALSE);
        parent::__construct(__CLASS__, 'Listas de valores', 'Personalización');
    }

    protected function private_core() {
        parent::private_core();
        $this->share_extensions();

        $this->lista = FALSE;
        $atr1 = new lista();

        $accion = (isset($_POST['accion']) ? $_POST['accion'] : '');

        switch ($accion) {
            case 'nueva_lista':
                $this->nueva_lista($atr1);
                break;
            case 'eliminar_lista':
                $this->eliminar_lista($atr1);
                break;
            case 'nuevo_valor':
                if (isset($_REQUEST['cod'])) {
                    $this->lista = $atr1->get($_REQUEST['cod']);
                    $this->nuevo_valor();
                }
                break;
            case 'editar_valor':
                if (isset($_REQUEST['cod'])) {
                    $this->lista = $atr1->get($_REQUEST['cod']);
                    $this->editar_valor();
                }
                break;
            case 'eliminar_valor':
                if (isset($_REQUEST['cod'])) {
                    $this->lista = $atr1->get($_REQUEST['cod']);
                    $this->eliminar_valor();
                }
                break;
            case 'editar_lista':
                if (isset($_REQUEST['cod'])) {
                    $this->lista = $atr1->get($_REQUEST['cod']);
                    $this->editar_lista();
                }
                break;
            default:
                if (isset($_REQUEST['cod'])) {
                    $this->lista = $atr1->get($_REQUEST['cod']);
                }
                break;
        }

        if ($this->lista) {
            $this->template = 'lista';

            $valor = new lista_valor();
            $this->resultados = $valor->all_from_lista($this->lista->id);
        } else {
            $this->resultados = $atr1->all();
        }
    }

    private function share_extensions() {
        $fsext = new fs_extension();
        $fsext->name = 'btn_listas';
        $fsext->from = __CLASS__;
        $fsext->to = 'ventas_cliente';
        $fsext->params = '&back=ventas_cliente';
        $fsext->type = 'tab';
        $fsext->text = '<span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>'
                . '<span class="hidden-xs">&nbsp; Listas</span>';
        $fsext->save();
    }

    public function url_back() {
        if (isset($_GET["back"])) {
            return "index.php?page=" . $_GET["back"];
        } else
            return parent::url();
    }


    /// Listas
    private function nueva_lista(&$atr1) {
        //$atr1->id = 0;
        $atr1->codlista = substr($_POST['nuevo'], 0, 20);
        $atr1->nombre = $_POST['nombre'];
        $atr1->activo = (isset($_POST['activo']) && $_POST['activo']) ? 1 : 0;

        if ($atr1->save()) {
            $this->new_message('Lista guardada correctamente.');
            $this->lista = $atr1;
        } else {
            $this->new_error_msg('Error al crear la lista.');
        }
    }

    private function eliminar_lista(&$atr1) {
        $lista = $atr1->get($_POST['id']);
        if ($lista) {
            if ($lista->delete()) {
                $this->new_message('Lista eliminada correctamente.');
            } else {
                $this->new_error_msg('Imposible eliminar la lista.');
            }
        }
    }

    private function editar_lista() {
        $this->lista->codlista = $_POST['codlista'];
        $this->lista->nombre = $_POST['nombre'];
        $this->lista->activo = (isset($_POST['activo']) && $_POST['activo']) ? 1 : 0;

        if ($this->lista->save()) {
            $this->new_message('Datos guardados correctamente.');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

    /// Valores de listas
    private function nuevo_valor() {
        $valor = new lista_valor();
        $valor->idlista = $this->lista->id;
        $valor->codigo = $_POST['nuevo_codigo'];
        $valor->codigo_alternativo = $_POST['nuevo_codigo_alternativo'];
        $valor->valor = $_POST['nuevo_valor'];
        $valor->orden = $_POST['nuevo_orden'];
        $valor->activo = (isset($_POST['nuevo_activo']) && $_POST['nuevo_activo']) ? 1 : 0;

        if (!$valor->exists_codigo_lista($this->lista->id, $valor->codigo)) {
            if ($valor->save()) {
                $this->new_message('Datos guardados correctamente.');
            } else {
                $this->new_error_msg('Error al guardar los datos.');
            }
        } else {

            $this->new_error_msg('Error al guardar los datos. Ya existe un valor de la lista con ese código.');
        }
    }

    private function editar_valor() {
        $val0 = new lista_valor();
        $valor = $val0->get($_POST['id']);
        if ($valor) {
            $valor->codigo = $_POST['codigo'];
            $valor->codigo_alternativo = $_POST['codigo_alternativo'];
            $valor->valor = $_POST['valor'];
            $valor->orden = $_POST['orden'];
            $valor->activo = (isset($_POST['activo']) && $_POST['activo'] == 'TRUE') ? 1 : 0;

            if ($valor->save()) {
                $this->new_message('Datos guardados correctamente.');
            } else {
                $this->new_error_msg('Error al guardar los datos.');
            }
        }
    }

    private function eliminar_valor() {
        $val0 = new lista_valor();
        $valor = $val0->get($_POST['id']);
        if ($valor) {
            if ($valor->delete()) {
                $this->new_message('Valor eliminado correctamente.');
            } else {
                $this->new_error_msg('Error al eliminar el valor.');
            }
        }
    }

    public function max_orden_valor($idlista) {
        $ordenmaximo = 0;

        if ($this->db->table_exists('listas_valores')) {
            $sql = "SELECT max(orden) as orden FROM listas_valores WHERE idlista = " . $this->lista->var2str($idlista);

            $data = $this->db->select($sql);
            if ($data) {
                $ordenmaximo = $data[0]['orden'];
            }
        }

        return ++$ordenmaximo;
    }

    public function count_valores_lista($id) {
        $cuenta = 0;

        if ($this->db->table_exists('listas_valores')) {
            $sql = "SELECT count(id) as cuenta FROM listas_valores WHERE idlista = " . $id;

            $data = $this->db->select($sql);
            if ($data) {
                $cuenta = $data[0]['cuenta'];
            }
        }

        return $cuenta;
    }

}
