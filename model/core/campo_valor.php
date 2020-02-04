<?php

namespace FacturaScripts\model;

/**
 * Un campo para artículos.
 *
 * @author Cástor Agra
 */
class campo_valor extends \fs_model {

    /**
     * Clave primaria.
     * @var string 
     */
    
    public $id;
    public $idcampo;
    public $idrelacion;
    public $valor;

    public function __construct($data = FALSE)
    {
        parent::__construct('campos_valores');
        if ($data) {
            $this->id = $data['id'];
            $this->idcampo = $data['idcampo'];
            $this->idrelacion = "" . $data['idrelacion'];
            $this->valor = $data['valor'];
        } else {
            $this->id = 0;
            $this->idcampo = NULL;
            $this->idrelacion = NULL;
            $this->valor = NULL;
        }
    }

    protected function install()
    {
        return '';
    }

//    public function url()
//    {
//        return 'index.php?page=campos&cod=' . urlencode($this->id);
//    }

    public function get($code) {
        $data = $this->db->select("SELECT * FROM campos_valores WHERE id = " . $this->var2str($code) . ";");
        if ($data) {
            return new \campo_valor($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->id)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM campos_valores WHERE id = " . $this->var2str($this->id) . ";");
    }
    

    public function save()
    {
        $this->idcampo = $this->no_html($this->idcampo);
        $this->idrelacion = $this->no_html($this->idrelacion);
        $this->valor = $this->no_html($this->valor);

        if ($this->exists()) {
            $sql = "UPDATE campos_valores SET "
                    . "  valor = " . $this->var2str($this->valor)
                    . ", idrelacion = " . $this->var2str($this->idrelacion)
                    . " WHERE id = " . $this->var2str($this->id) . ";";

            $ret = $this->db->exec($sql);
        } else {
            $sql = "INSERT INTO campos_valores (idcampo,valor, idrelacion) VALUES "
                    . "(" . $this->var2str($this->idcampo)
                    . "," . $this->var2str($this->valor)
                    . "," . $this->var2str($this->idrelacion)
                    . ");";
            $ret = $this->db->exec($sql);
            $this->id = $this->db->lastval();
            //header('Location: ' . $this->url());
        }

        //print_r($sql);
        return $ret;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM campos_valores WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     * 
     * @return \campo[]
     */
    public function all()
    {
        $campo = [];

        $data = $this->db->select("SELECT * FROM campos_valores ORDER BY id ASC;");
        if ($data) {
            foreach ($data as $d) {
                $campo[] = new \campo_valor($d);
            }
        }

        return $campo;
    }

    public function all_from_controlador($cod) {
        $data = $this->db->select("SELECT campos_valores.* "
                . " FROM campos_valores "
                . " Inner Join campos on campos.id = campos_valores.idcampo "
                . " WHERE lower(campos.controlador) = " . $this->var2str(mb_strtolower($cod, 'UTF8'))
                . " Order by campos.nombre;");

        //$data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \campo_valor($d);
            }
        }

        return $lista;
    }

    public function all_from_controlador_campo($cod, $campo) {
        $sql = "SELECT campos_valores.* "
                . " FROM campos_valores "
                . " Inner Join campos on campos.id = campos_valores.idcampo "
                . " WHERE lower(campos.controlador) = " . $this->var2str(mb_strtolower($cod, 'UTF8'))
                . " AND lower(campos.campo) = " . $this->var2str(mb_strtolower($campo, 'UTF8'))
                . " Order by campos.nombre;";

        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $lista[] = new \campo_valor($d);
            }
        }

        return $lista;
    }

}
