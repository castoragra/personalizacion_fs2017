<?php
namespace FacturaScripts\model;

/**
 * Un lista para artículos.
 *
 * @author Cástor Agra
 */
class lista extends \fs_model {

    /**
     * Clave primaria.
     * @var string 
     */
    public $id;
    public $codlista;
    public $nombre;
    public $activo;

    public function __construct($data = FALSE) {
        parent::__construct('listas');
        if ($data) {
            $this->id = $data['id'];
            $this->codlista = $data['codlista'];
            $this->nombre = $data['nombre'];
            $this->activo = $data['activo'];
        } else {
            $this->id = 0;
            $this->codlista = NULL;
            $this->nombre = NULL;
            $this->activo = false;
        }
    }

    protected function install() {
        return "
            Insert into listas (codlista, nombre, activo) Values ('cboTiposCampos', 'Tipo de campo', 1), ('cboRequerido', 'Requerido', 1);
            CREATE OR REPLACE VIEW `vcampos` AS
                SELECT
                    `campos`.`id` AS `id`,
                    `campos`.`controlador` AS `controlador`,
                    `campos`.`campo` AS `campo`,
                    `campos`.`nombre` AS `nombre`,
                    `campos`.`tipo` AS `tipo`,
                    `campos`.`requerido` AS `requerido`,
                    `campos`.`minimo` AS `minimo`,
                    `campos`.`maximo` AS `maximo`,
                    `campos`.`msg_error` AS `msg_error`,
                    `l_c`.`nombre_controlador`,
                    `lv_t`.`valor` AS `nombre_tipo`,
                    `lv_r`.`valor` AS `nombre_requerido`
                FROM
                    `campos`
                    inner join cp_tablas l_c on l_c.controlador = campos.controlador
                    LEFT JOIN `listas` `l_t` ON `l_t`.`codlista` = 'cboTiposCampos'
                    LEFT JOIN `listas` `l_r` ON `l_r`.`codlista` = 'cboRequerido'
                    /*LEFT JOIN `listas_valores` `lv_c` ON `lv_c`.`codigo` = `campos`.`controlador` AND `l_c`.`id` = `lv_c`.`idlista`*/
                    LEFT JOIN `listas_valores` `lv_t` ON `lv_t`.`codigo` = `campos`.`tipo`
                        AND `l_t`.`id` = `lv_t`.`idlista`
                    LEFT JOIN `listas_valores` `lv_r` ON `lv_r`.`codigo` = `campos`.`requerido`
                        AND `l_r`.`id` = `lv_r`.`idlista`
                WHERE
                    1;
            ";
    }

    public function url() {
        return 'index.php?page=listas&cod=' . urlencode($this->id);
    }

    public function valores() {
        $valor0 = new \lista_valor();
        return $valor0->all_from_lista($this->codlista);
    }

    public function get($cod) {
        $data = $this->db->select("SELECT * FROM listas WHERE id = " . $this->var2str($cod) . ";");
        if ($data) {
            return new \lista($data[0]);
        }

        return FALSE;
    }

    public function get_by_nombre($nombre, $minusculas = FALSE) {
        if ($minusculas) {
            $data = $this->db->select("SELECT * FROM listas WHERE lower(nombre) = " . $this->var2str(mb_strtolower($nombre, 'UTF8')) . ";");
        } else {
            $data = $this->db->select("SELECT * FROM listas WHERE nombre = " . $this->var2str($nombre) . ";");
        }

        if ($data) {
            return new \lista($data[0]);
        }

        return FALSE;
    }

    public function exists() {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM listas WHERE id = " . $this->var2str($this->id) . ";");
    }

    public function save() {
        $this->codlista = $this->no_html($this->codlista);
        $this->nombre = $this->no_html($this->nombre);
        //$this->activo = $this->no_html($this->activo);

        if ($this->exists()) {
            $sql = "UPDATE listas SET nombre = " . $this->var2str($this->nombre)
                    . ", activo = " . $this->var2str($this->activo)
                    . " WHERE id = " . $this->var2str($this->id) . ";";

            //$this->new_message('' . $sql);
            $ret = $this->db->exec($sql);
        } else {
            $sql = "INSERT INTO listas (codlista, nombre, activo) VALUES "
                    . "(" . $this->var2str($this->codlista)
                    . "," . $this->var2str($this->nombre)
                    . "," . $this->var2str($this->activo) . ");";
            $ret = $this->db->exec($sql);
            $this->id = $this->db->lastval();
        }

        return $ret;
    }

    public function delete() {
        return $this->db->exec("DELETE FROM listas WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     * 
     * @return \lista[]
     */
    public function all() {
        $lista = [];

        $data = $this->db->select("SELECT * FROM listas WHERE codlista NOT IN ('cboRequerido', 'cboTiposCampos') ORDER BY LOWER(nombre) ASC;");
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista($d);
            }
        }

        return $lista;
    }

}
