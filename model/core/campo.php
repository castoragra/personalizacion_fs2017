<?php

namespace FacturaScripts\model;

/**
 * Un campo para un controlador.
 *
 * @author Cástor Agra <castor.agra@gmail.com>
 */
class campo extends \fs_model {

    /**
     * Clave primaria.
     * @var string 
     */
    
    public $id;
    public $controlador;
    public $nombre;
    public $campo;
    public $tipo;
    public $requerido;
    public $minimo;
    public $maximo;
    public $msg_error;

    public function __construct($data = FALSE)
    {
        parent::__construct('campos');
        if ($data) {
            $this->id = $data['id'];
            $this->campo = $data['campo'];
            $this->nombre = $data['nombre'];
            $this->controlador = $data['controlador'];
            $this->tipo = $data['tipo'];
            $this->requerido = $data['requerido'];
            $this->minimo = $data['minimo'];
            $this->maximo = $data['maximo'];
            $this->msg_error = $data['msg_error'];
        } else {
            $this->id = 0;
            $this->campo = NULL;
            $this->nombre = NULL;
            $this->controlador = NULL;
            $this->tipo = NULL;
            $this->requerido = FALSE;
            $this->minimo = 0;
            $this->maximo = 0;
            $this->msg_error = NULL;
        }
    }

    protected function install()
    {
        return "
            CREATE OR REPLACE ALGORITHM = UNDEFINED
            VIEW `vcampos` AS
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

    public function url()
    {
        return 'index.php?page=campos&cod=' . urlencode($this->id);
    }

    public function get($code) {
        $data = $this->db->select("SELECT * FROM campos WHERE id = " . $this->var2str($code) . ";");
        if ($data) {
            return new \campo($data[0]);
        }

        return FALSE;
    }

    public function get_from_campo($code) {
        $data = $this->db->select("SELECT * FROM campos WHERE campo = " . $this->var2str($code) . ";");
        if ($data) {
            return new \campo($data[0]);
        }

        return FALSE;
    }

//    public function get_by_nombre($nombre, $minusculas = FALSE)
//    {
//        if ($minusculas) {
//            $data = $this->db->select("SELECT * FROM campos WHERE lower(nombre) = " . $this->var2str(mb_strtolower($nombre, 'UTF8')) . ";");
//        } else {
//            $data = $this->db->select("SELECT * FROM campos WHERE nombre = " . $this->var2str($nombre) . ";");
//        }
//
//        if ($data) {
//            return new \campo($data[0]);
//        }
//
//        return FALSE;
//    }

    public function exists()
    {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM campos WHERE id = " . $this->var2str($this->id) . ";");
    }
    

    public function save()
    {
        $this->campo = $this->no_html($this->campo);
        $this->nombre = $this->no_html($this->nombre);
        $this->controlador = $this->no_html($this->controlador);
        $this->tipo = $this->no_html($this->tipo);
        $this->requerido = $this->no_html($this->requerido);
        $this->minimo = $this->no_html($this->minimo);
        $this->maximo = $this->no_html($this->maximo);
        $this->msg_error = $this->no_html($this->msg_error);

        if ($this->exists()) {
            $sql = "UPDATE campos SET "
                    . "  campo = " . $this->var2str($this->campo)
                    . ", nombre = " . $this->var2str($this->nombre)
                    . ", controlador = " . $this->var2str($this->controlador)
                    . ", tipo = " . $this->var2str($this->tipo)
                    . ", requerido = " . $this->var2str($this->requerido)
                    . ", minimo = " . $this->var2str($this->minimo)
                    . ", maximo = " . $this->var2str($this->maximo)
                    . ", msg_error = " . $this->var2str($this->msg_error)
                    . " WHERE id = " . $this->var2str($this->id) . ";";

            $ret = $this->db->exec($sql);
        } else {
            $sql = "INSERT INTO campos (campo, nombre, controlador, tipo, requerido, minimo, maximo, msg_error) VALUES "
                    . "(" . $this->var2str($this->campo)
                    . "," . $this->var2str($this->nombre)
                    . "," . $this->var2str($this->controlador)
                    . "," . $this->var2str($this->tipo)
                    . "," . $this->var2str($this->requerido)
                    . "," . $this->var2str($this->minimo)
                    . "," . $this->var2str($this->maximo)
                    . "," . $this->var2str($this->msg_error) . ");";
            $ret = $this->db->exec($sql);
            $this->id = $this->db->lastval();
            header('Location: ' . $this->url());
        }
        
        return $ret;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM campos WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     *
     * @return \campo[]
     */
    public function all() {
        $campo = [];

        $data = $this->db->select("SELECT * FROM campos ORDER BY LOWER(controlador), LOWER(campo) ASC;");
        if ($data) {
            foreach ($data as $d) {
                $campo[] = new \campo($d);
            }
        }

        return $campo;
    }

    /**
     *
     * @return \campo[]
     */
    public function all_from_controlador($controlador) {
        $campo = [];

        $data = $this->db->select("SELECT * FROM campos WHERE controlador = " . $this->var2str($controlador) . " ORDER BY LOWER(controlador), LOWER(campo) ASC;");
        if ($data) {
            foreach ($data as $d) {
                $campo[] = new \campo($d);
            }
        }

        return $campo;
    }

    public function all_from_codlista($cod) {
        $data = $this->db->select("SELECT listas_valores.* "
                . " FROM listas_valores "
                . " Inner Join listas on listas.id = listas_valores.idlista "
                . " WHERE lower(listas.codlista) = " . $this->var2str(mb_strtolower($cod, 'UTF8')) . " Order by listas_valores.orden;");

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \lista_valor($d);
            }
        }

        return $lista;
    }

}
