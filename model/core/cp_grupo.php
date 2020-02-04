<?php

namespace FacturaScripts\model;

/**
 * Grupos de campos personalizados
 *
 * @author Cástor Agra
 */
class cp_grupo extends \fs_model {

    /**
     * Clave primaria.
     * @var string 
     */
    
    public $id;
    public $grupo;
    public $nombre_grupo;
    private $modelo = 'cp_grupos';

    public function __construct($data = FALSE) {
        parent::__construct($this->modelo);
        if ($data) {
            $this->id = $data['id'];
            $this->grupo = $data['grupo'];
            $this->nombre_grupo = $data['nombre_grupo'];
        } else {
            $this->id = 0;
            $this->grupo = NULL;
            $this->nombre_grupo = NULL;
        }
    }

    protected function install()
    {
        return "insert into cp_grupos (grupo, nombre_grupo) values ('ventas', 'Ventas'), ('compras', 'Compras'), ('articulos', 'Artículos');";
    }

    public function url()
    {
        return 'index.php?page=' . $this->modelo . '&id=' . urlencode($this->id);
    }

    public function get($id) {
        $data = $this->db->select("SELECT * FROM " . $this->modelo . " WHERE id = " . $this->var2str($id) . ";");
        if ($data) {
            return new \cp_grupo($data[0]);
        }

        return FALSE;
    }

    public function get_from_grupo($code) {
        $data = $this->db->select("SELECT * FROM " . $this->modelo . " WHERE grupo = " . $this->var2str($code) . ";");
        if ($data) {
            return new \cp_grupo($data[0]);
        }

        return FALSE;
    }

    public function exists()
    {
        if (is_null($this->grupo)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->modelo . " WHERE grupo = " . $this->var2str($this->grupo) . ";");
    }
    

    public function save()
    {
        $this->grupo = $this->no_html($this->grupo);
        $this->nombre_grupo = $this->no_html($this->nombre_grupo);

        if ($this->exists()) {
            $sql = "UPDATE " . $this->modelo . " SET "
                    . "  grupo = " . $this->var2str($this->grupo)
                    . ", nombre_grupo = " . $this->var2str($this->nombre_grupo)
                    . " WHERE id = " . $this->var2str($this->id) . ";";

            $ret = $this->db->exec($sql);
        } else {
            $sql = "INSERT INTO " . $this->modelo . " (grupo, nombre_grupo) VALUES "
                    . "(" . $this->var2str($this->grupo)
                    . "," . $this->var2str($this->nombre_grupo)
                    . ");";
            $ret = $this->db->exec($sql);
            $this->id = $this->db->lastval();
            header('Location: ' . $this->url());
        }
        
        return $ret;
    }

    public function delete()
    {
        return $this->db->exec("DELETE FROM " . $this->modelo . " WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     *
     * @return \cp_grupo[]
     */
    public function all() {
        $obj = [];

        $data = $this->db->select("SELECT * FROM " . $this->modelo . " ORDER BY LOWER(nombre_grupo) ASC;");
        if ($data) {
            foreach ($data as $d) {
                $obj[] = new \cp_grupo($d);
            }
        }

        return $obj;
    }

}
