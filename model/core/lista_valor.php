<?php

namespace FacturaScripts\model;

/**
 * Un Valor para un lista de artículos.
 *
 * @author Cástor Agra
 */
class lista_valor extends \fs_model {

    /**
     * Clave primaria
     * @var integer
     */
    public $id;

    /**
     * Código del lista relacionado.
     * @var string
     */
    public $idlista;
    public $codigo;
    public $codigo_alternativo;
    public $valor;
    public $orden;
    public $activo;

    public function __construct($data = FALSE) {
        parent::__construct('listas_valores');
        if ($data) {
            $this->id = $this->intval($data['id']);
            $this->idlista = $data['idlista'];
            $this->codigo = $data['codigo'];
            $this->codigo_alternativo = $data['codigo_alternativo'];
            $this->valor = $data['valor'];
            $this->orden = $data['orden'];
            $this->activo = $data['activo'];
        } else {
            $this->id = NULL;
            $this->idlista = NULL;
            $this->codigo = NULL;
            $this->codigo_alternativo = NULL;
            $this->valor = NULL;
            $this->orden = NULL;
            $this->activo = FALSE;
        }
    }

    protected function install() {
        return "INSERT INTO listas_valores (idlista, codigo, codigo_alternativo, valor, orden, activo) "
                . "       Select lista.id, 'text', 'text', 'Texto', 1, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, 'number', 'number', 'Número entero', 2, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, 'money', 'number', 'Moneda', 3, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, 'date', 'date', 'Fecha', 4, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, 'time', 'time', 'Hora', 5, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, 'list', 'list', 'Lista', 6, 1 From listas where codlista = 'cboTiposCampos'"
                . " UNION Select lista.id, '0', '0', 'No', 1, 1 From listas where codlista = 'cboRequerido'"
                . " UNION Select lista.id, '1', '1', 'Si', 2, 1 From listas where codlista = 'cboRequerido'"
        ;
    }

    public function url() {
        return 'index.php?page=listas&cod=' . $this->idlista;
    }

    public function get_nombre() {
        $nombre = '';

        $data = $this->db->select("SELECT * FROM listas WHERE idlista = " . $this->var2str($this->idlista) . ";");
        if ($data) {
            $nombre = $data[0]['nombre'];
        }

        return $nombre;
    }

    public function get($id) {
        if ($id) {
            $data = $this->db->select("SELECT * FROM listas_valores WHERE id = " . $this->var2str($id) . ";");
            if ($data) {
                return new \lista_valor($data[0]);
            }
        }

        return FALSE;
    }

    public function exists() {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM listas_valores WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function exists_codigo_lista($id, $codigo) {
        if (is_null($id) || is_null($codigo)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM listas_valores WHERE idlista = " . $this->var2str($id) . " and codigo = " . $this->var2str($codigo) . ";");
    }

    public function save() {
        $this->valor = $this->no_html($this->valor);

        if ($this->exists()) {
            $sql = "UPDATE listas_valores SET "
                    . " codigo = " . $this->var2str($this->codigo)
                    . ", codigo_alternativo = " . $this->var2str($this->codigo_alternativo)
                    . ", valor = " . $this->var2str($this->valor)
                    . ", orden = " . $this->var2str($this->orden)
                    . ", activo = " . $this->activo
                    . "  WHERE id = " . $this->var2str($this->id) . ";";
        } else {


            $sql = "INSERT INTO listas_valores (idlista, codigo, codigo_alternativo, valor, orden, activo) VALUES "
                    . "(" . $this->var2str($this->idlista)
                    . "," . $this->var2str($this->codigo)
                    . "," . $this->var2str($this->codigo_alternativo)
                    . "," . $this->var2str($this->valor)
                    . "," . $this->var2str($this->orden)
                    . "," . $this->var2str($this->activo) . ");";
        }

        return $this->db->exec($sql);
    }

    public function delete() {
        return $this->db->exec("DELETE FROM listas_valores WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function all() {
        $lista = array();

        $data = $this->db->select("SELECT * FROM listas_valores ORDER BY idlista, orden DESC;");
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista_valor($d);
            }
        }

        return $lista;
    }

    public function all_from_lista($cod) {
        $lista = array();
        $sql = "SELECT * FROM listas_valores WHERE idlista = " . $this->var2str($cod)
                . " ORDER BY orden ASC;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista_valor($d);
            }
        }

        return $lista;
    }

    public function all_from_codlista($cod) {
        $lista = array();
        $data = $this->db->select("SELECT listas_valores.* "
                . " FROM listas_valores "
                . " Inner Join listas on listas.id = listas_valores.idlista "
                . " WHERE lower(listas.codlista) = " . $this->var2str(mb_strtolower($cod, 'UTF8'))
                . " Order by listas_valores.orden;");


        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista_valor($d);
            }
        }

        return $lista;
    }

    public function all_from_codlista_controlador($controlador, $cod) {
        $lista = array();
        $data = $this->db->select("select distinct cv.valor, case when c.tipo= 'list' then lv.valor when c.tipo= 'date' then date_format(cv.valor , '%d-%m-%Y') Else cv.valor end as nombre"
                . " from campos c"
                . " inner join campos_valores cv on cv.idcampo = c.id"
                . " left join listas l on l.codlista = c.campo"
                . " left join listas_valores lv on lv.idlista = l.id and lv.codigo = cv.valor"
                . " where c.controlador = " . $this->var2str(mb_strtolower($controlador, 'UTF8'))
                . " and c.campo = " . $this->var2str(mb_strtolower($cod, 'UTF8'))
                . " Order by lv.orden, cv.valor;");

        if ($data) {
            foreach ($data as $d) {
                $lista[] = array('valor' => $d['valor'], 'nombre' => $d['nombre']);
            }
        }

        return $lista;
    }

}
