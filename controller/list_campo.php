<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class list_campo extends fs_list_controller {

    public function __construct() {
        parent::__construct(__CLASS__, 'Campos personalizados', 'Personalización');
    }

    protected function create_tabs() {
        $this->create_tab_campos();
    }

    function create_tab_campos($tab_name = 'campos') {
        $this->add_tab($tab_name, 'Campos', 'vcampos');

        $this->add_button($tab_name, "Nuevo", 'index.php?page=campos', 'fa-plus', 'btn-success');

        $this->decoration->add_column($tab_name, 'nombre_controlador', 'string', 'Controlador', 'text-left');
        $this->decoration->add_column($tab_name, 'nombre', 'string', 'Nombre', 'text-left');
        $this->decoration->add_column($tab_name, 'campo', 'string', 'Campo', 'text-left');
        $this->decoration->add_column($tab_name, 'nombre_tipo', 'string', 'Tipo', 'text-left');
        $this->decoration->add_column($tab_name, 'nombre_requerido', 'string', 'Requerido', 'text-center');

        $this->decoration->add_row_url($tab_name, 'index.php?page=campos&cod=', 'id');

        $this->add_search_columns($tab_name, ['controlador', 'Nombre']);
        $this->add_sort_option($tab_name, ['controlador'], 1);
        $this->add_sort_option($tab_name, ['campo']);
        $this->add_sort_option($tab_name, ['Nombre']);

        //$controladores = $this->db->select("select codigo, valor from listas l inner join listas_valores lv on l.id = lv.idlista where l.codlista = 'cboControlador' order by lv.orden");
        //$controladores = $this->get_valores_lista('cboControlador');
        //$cc = new cp_tabla();
        $controladores = $this->get_controladores();

        $this->add_filter_select($tab_name, 'controlador', 'Controlador', $controladores);

        $tipos = $this->get_valores_lista('cboTiposCampos');
        $this->add_filter_select($tab_name, 'tipo', 'Tipo', $tipos);

//        $requeridos = $this->get_valores_lista('cboRequerido');
//        $this->add_filter_select($tab_name, 'requerido', 'Requerido', $requeridos);
    }

    public function get_valores_lista($codlista) {


        $final = [];
        $sql = "select codigo, valor from listas l inner join listas_valores lv on l.id = lv.idlista where l.codlista = '" . $codlista . "' order by lv.orden;";
        $data = $this->db->select($sql);
        if (!empty($data)) {
            foreach ($data as $d) {
                $final[$d['codigo']] = $d['valor'];
            }
        }

        return $final;
    }

    public function get_controladores() {

        $cc = new cp_tabla();

        $final = [];
        
        $data = $cc->get_controladores();
        if (!empty($data)) {
            foreach ($data as $d) {
                $final[$d['controlador']] = $d['nombre_controlador'];
            }
        }

        return $final;
    }

}
