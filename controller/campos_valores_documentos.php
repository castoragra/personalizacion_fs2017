<?php

/**
 * Description of campos_valores_documentos
 *
 * @author Cástor Agra
 */
class campos_valores_documentos extends fs_controller {

    /**
     *
     * @var array
     */
    public $lista;
    public $campo;
    public $campos;
    public $campos_valores;
    public $campo_valor;
    public $idrelacion;

    public function __construct() {
        parent::__construct(__CLASS__, 'Valores personalizados', 'Personalización', FALSE, FALSE);
        //$this->campo = new campo_valor();
    }

    protected function private_core() {
        $this->share_extension();

        $this->campos_valores = [];

        $atr1 = new campo_valor();

        $accion = (isset($_POST['accion']) ? $_POST['accion'] : '');

        //$this->new_error_msg('Error al crear la lista.' . $accion);

//                var_dump($accion);
        switch ($accion) {
            case 'eliminar':
                $this->idrelacion = "" . $_REQUEST['idrelacion'];
                $this->eliminar_campo($atr1);
                break;
            case 'editar_valor':
                $this->idrelacion = "" . $_REQUEST['idrelacion'];
                if (isset($_REQUEST['id'])) {
                    $this->campo = $atr1->get($_REQUEST['id']);

                    //$this->new_error_msg('Error al crear la lista.' . $this->campo->id);
                    $this->editar_campo();
                }
                break;
            case 'nuevo_valor':
                $this->campo = new campo_valor();
                $this->idrelacion = "" . $_REQUEST['idrelacion'];
                if (isset($_REQUEST['id'])) {
                    $this->campo->idcampo = "" . $_REQUEST['idcampo'];
                    $this->campo->idrelacion = "" . $_REQUEST['idrelacion'];
//                    if (isset($_GET['id'])) {
//
//                        $this->campo->idrelacion = $this->idrelacion; //$_GET['id'];
//                    }
                }
//                if (isset($_REQUEST['cod'])) {
//
//                }
//                $this->campo->idrelacion = $this->idrelacion; //$_REQUEST['cod'];
                //$this->new_error_msg('Error al crear la lista.' . $this->campo->id);

//                var_dump($this->campo);
                $this->editar_campo();

                break;
            default:
                if (isset($_GET['cod'])) {
                    $this->idrelacion = "" . $_REQUEST['cod'];
                }
                if (isset($_GET['id'])) {
                    $this->idrelacion = "" . $_GET['id'];
                }
                if (isset($_GET['ref'])) {
                    $this->idrelacion = "" . $_GET['ref'];
                }
//                var_dump($this->idrelacion);
                break;
        }


        if (isset($_GET['folder']) && isset($_GET['id'])) {

            $cava = new campo_valor();
        } else {
            $cava = new campo_valor();
        }

        $this->campos = $this->get_campos();
        $this->campos_valores = $this->get_campos_valores();
    }

    private function share_extension() {

        $sql = "SELECT distinct cp_tablas.controlador,cp_tablas.tabla FROM cp_tablas;";
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $fsext = new fs_extension(
                        array(
                    'name' => $d['controlador'],
                    'page_from' => __CLASS__,
                    'page_to' => $d['controlador'],
                    'type' => 'tab',
                    'text' => '<span class="glyphicon glyphicon-file" aria-hidden="true" name="campospersonalizados" id="campospersonalizados" title="Campos personalizados"></span><span class="hidden-xs">&nbsp;Campos personalizados</span>',
                    'params' => '&folder=' . $d['controlador']
                        )
                );
                $fsext->save();
            }
        }
    }

    public function url() {

//        var_dump($_GET); // Element 'foo' is string(1) "a"
//        var_dump($_POST); // Element 'bar' is string(1) "b"
//        var_dump($_REQUEST);

        if (isset($_GET['folder']) AND isset($_GET['id'])) {
            return 'index.php?page=' . __CLASS__ . '&folder=' . $_GET['folder'] . '&id=' . $_GET['id'];
        } else
        if (isset($_GET['folder']) AND ! isset($_GET['id'])) {
            $id = '';
            if (isset($_REQUEST['idrelacion'])) {
                $id = '&id=' . $_REQUEST['idrelacion'];
            }
            return 'index.php?page=' . __CLASS__ . '&folder=' . $_GET['folder'] . $id;
        }

        return parent::url();
    }

    private function get_campos() {

        $lista = [];

        if (isset($_REQUEST['folder'])) {
            $sql = "SELECT * FROM campos WHERE campos.controlador = '" . $this->db->escape_string($_REQUEST['folder']) . "';";
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $d) {
                    $lista[] = new campo($d);
                }
            }
        }

        return $lista;
    }

    public function get_campos_valores() {

        $lista = [];

        if (isset($_POST['folder'])) {
            $sql = "SELECT campos_valores.* FROM campos_valores inner join campos on campos.id = campos_valores.idcampo WHERE campos.controlador = '" . $this->db->escape_string($_POST['folder']) . "';";
            $data = $this->db->select($sql);
            if ($data) {
                foreach ($data as $d) {
                    $lista[] = new campo_valor($d);
                }
            }
        }

        return $lista;
    }

    public function get_valores_lista($codlista) {
        $lista = array();
        $data = $this->db->select("SELECT listas_valores.* "
                . " FROM listas_valores "
                . " Inner Join listas on listas.id = listas_valores.idlista "
                . " WHERE lower(listas.codlista) = '" . mb_strtolower($codlista, 'UTF8') . "'"
                . " Order by listas_valores.orden;");


        //$this->new_message('asdfasdfsdfsd guardado correctamente.' . count($data) . '--' . $codlista);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista_valor($d);
            }
        }

        return $lista;
    }

    public function get_campo_valor($idcampo) {

        $campo_valor = new campo_valor();

        $sql = "SELECT campos_valores.* FROM campos_valores WHERE idcampo = '" . $this->db->escape_string($idcampo) . "' "
                . ((isset($_GET["id"]) && $_GET["id"]) ? " and idrelacion = '" . $this->db->escape_string($this->idrelacion) . "'" : "")
                . ";";
        $data = $this->db->select($sql);
        //print_r($sql);
        if ($data) {
            $campo_valor = new campo_valor($data[0]);
        }

        return $campo_valor;
    }

    public function get_local_campo_valor($idcampo) {

        $this->campo_valor = new campo_valor();

        $sql = "SELECT campos_valores.* FROM campos_valores WHERE idcampo = '" . $this->db->escape_string($idcampo) . "' "
                . ((isset($this->idrelacion) && $this->idrelacion) ? " and idrelacion = '" . $this->db->escape_string($this->idrelacion) . "'" : "")
                . ";";
        //print_r($sql);
        $data = $this->db->select($sql);
        if ($data) {
            $this->campo_valor = new campo_valor($data[0]);
        }
    }

    private function eliminar_campo(&$atr1) {
        $campo = $atr1->get($_REQUEST['id']);

        if ($campo) {
            if ($campo->delete()) {
                $this->new_message('Valor eliminado correctamente.');
            } else {
                $this->new_error_msg('Imposible eliminar el valor.');
            }
        }

//        $campo = $atr1->get($_REQUEST['id']);
//
//        $campo->idrelacion = $_REQUEST['idrelacion'];
//        $campo->valor = NULL;
//
//        if ($campo) {
//            if ($campo->save()) {
//                $this->new_message('Valor eliminado correctamente.');
//            } else {
//                $this->new_error_msg('Imposible eliminar el valor.');
//            }
//        }
    }

    private function editar_campo() {
        $this->campo->valor = $_POST['valor'];

        if ($this->campo->save()) {
            $this->new_message('Datos guardados correctamente.');
        } else {
            $this->new_error_msg('Error al guardar los datos.');
        }
    }

}
