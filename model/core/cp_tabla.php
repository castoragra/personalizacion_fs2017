<?php

namespace FacturaScripts\model;

/**
 * Tabla para campos personalizados
 *
 * @author Cástor Agra <castor.agra@gmail.com>
 */
class cp_tabla extends \fs_model {

    /**
     * Clave primaria.
     * @var string
     */
    public $id;
    public $idgrupo;
    public $tabla;
    public $controlador;
    public $nombre_controlador;
    public $columna_id;
    public $id_columna;
    public $nombre;
    public $nombre_columna;
    public $numero;
    public $numero_columna;
    public $fecha;
    public $fecha_columna;
    public $importe;
    public $importe_euros;
    public $importe_columna;
    public $sumatorio_importe;
    private $modelo = 'cp_tablas';

    public function __construct($data = FALSE) {
        parent::__construct($this->modelo);
        if ($data) {
            $this->id = $data['id'];
            $this->idgrupo = $data['idgrupo'];
            $this->tabla = $data['tabla'];
            $this->controlador = $data['controlador'];
            $this->nombre_controlador = $data['nombre_controlador'];
            $this->columna_id = $data['columna_id'];
            $this->id_columna = $data['id_columna'];
            $this->nombre = $data['nombre'];
            $this->nombre_columna = $data['nombre_columna'];
            $this->numero = $data['numero'];
            $this->numero_columna = $data['numero_columna'];
            $this->fecha = $data['fecha'];
            $this->fecha_columna = $data['fecha_columna'];
            $this->importe = $data['importe'];
            $this->importe_euros = $data['importe_euros'];
            $this->importe_columna = $data['importe_columna'];
            $this->sumatorio_importe = $data['sumatorio_importe'];
        } else {
            $this->id = 0;
            $this->idgrupo = NULL;
            $this->tabla = NULL;
            $this->controlador = NULL;
            $this->nombre_controlador = NULL;
            $this->columna_id = NULL;
            $this->id_columna = NULL;
            $this->nombre = NULL;
            $this->nombre_columna = NULL;
            $this->numero = NULL;
            $this->numero_columna = NULL;
            $this->fecha = NULL;
            $this->fecha_columna = NULL;
            $this->importe = NULL;
            $this->importe_euros = NULL;
            $this->importe_columna = NULL;
            $this->sumatorio_importe = NULL;
        }
    }

    protected function install() {
        return "Insert into cp_tablas (idgrupo, tabla, controlador, nombre_controlador, columna_id, id_columna, nombre, nombre_columna, numero, numero_columna, fecha, fecha_columna, importe, importe_euros, importe_columna, sumatorio_importe)
                select cp_grupos.id, 'facturascli', 'ventas_factura', 'Ventas > Facturas', 'idfactura', 'id', 'nombrecliente', 'Cliente', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'ventas'
                union
                select cp_grupos.id, 'albaranescli', 'ventas_albaran', 'Ventas > Albaranes', 'idalbaran', 'id', 'nombrecliente', 'Cliente', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'ventas'
                union
                select cp_grupos.id, 'pedidoscli', 'ventas_pedido', 'Ventas > Pedidos', 'idpedido', 'id', 'nombrecliente', 'Cliente', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'ventas'

                Union
                select cp_grupos.id, 'facturasprov', 'compras_factura', 'Compras > Facturas', 'idfactura', 'id', 'nombre', 'Proveedor', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'compras'
                union
                select cp_grupos.id, 'albaranesprov', 'compras_albaran', 'Compras > Albaranes', 'idalbaran', 'id', 'nombre', 'Proveedor', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'compras'
                union
                select cp_grupos.id, 'pedidosprov', 'compras_pedido', 'Compras > Pedidos', 'idpedido', 'id', 'nombre', 'Proveedor', 'codigo', 'Número', 'fecha', 'Fecha', 'total', 'totaleuros', 'Importe', TRUE
                from cp_grupos where grupo = 'compras'
                Union

                select cp_grupos.id, 'clientes', 'ventas_cliente', 'Clientes', 'codcliente', 'cod', 'nombre', 'Cliente', 'cifnif', 'Número', 'fechaalta', 'Fecha Alta', '', '', '', FALSE
                from cp_grupos where grupo = 'ventas'
                Union
                select cp_grupos.id, 'proveedores', 'compras_proveedor', 'Proveedores', 'codproveedor', 'cod', 'nombre', 'Proveedor', 'cifnif', 'Número', '', '', '', '', '', FALSE
                from cp_grupos where grupo = 'compras'
                Union
                select cp_grupos.id, 'articulos', 'ventas_articulo', 'Artículos', 'referencia', 'ref', 'descripcion', 'Artículo', 'referencia', 'Referencia', '', '', '', '', '', FALSE
                from cp_grupos where grupo = 'articulos';";
    }

    public function url() {
        return 'index.php?page=' . $this->modelo . '&id=' . urlencode($this->id);
    }

    public function get($id) {
        $data = $this->db->select("SELECT * FROM " . $this->modelo . " WHERE id = " . $this->var2str($id) . ";");
        if ($data) {
            return new \cp_tabla($data[0]);
        }

        return FALSE;
    }

    public function get_from_grupo($code) {
        $obj = [];
        $sql = "SELECT " . $this->modelo . ".* FROM " . $this->modelo . " inner join cp_grupos on cp_grupos.id = " . $this->modelo . ".idgrupo WHERE cp_grupos.grupo = " . $this->var2str($code) . " ORDER BY nombre_controlador;";
        //print_r($sql);
        $data = $this->db->select($sql);
        if ($data) {
            foreach ($data as $d) {
                $obj[] = new \cp_tabla($d);
            }
        }

        return $obj;
    }

    public function get_controladores() {
        $obj = [];
        $data = $this->db->select("SELECT distinct controlador, nombre_controlador FROM " . $this->modelo . " ORDER BY nombre_controlador;");
        if ($data) {
            foreach ($data as $d) {
                $obj[] = array('controlador' => $d['controlador'], 'nombre_controlador' => $d['nombre_controlador']);
            }
        }
        return $obj;
    }

    public function get_controladores_from_grupo($code) {
        $obj = [];
        $data = $this->db->select("SELECT distinct controlador, nombre_controlador FROM " . $this->modelo . " inner join cp_grupos on cp_grupos.id = " . $this->modelo . ".idgrupo WHERE cp_grupos.grupo = " . $this->var2str($code) . " ORDER BY nombre_controlador;");
        if ($data) {
            foreach ($data as $d) {
                $obj[] = array('controlador' => $d['controlador'], 'nombre_controlador' => $d['nombre_controlador']);
            }
        }
        return $obj;
    }

    public function exists() {
        if (is_null($this->controlador)) {
            return FALSE;
        }

        return $this->db->select("SELECT * FROM " . $this->modelo . " WHERE controlador = " . $this->var2str($this->grupo) . ";");
    }

    public function save() {
        $this->grupo = $this->no_html($this->grupo);
        $this->nombre_grupo = $this->no_html($this->nombre_grupo);

        if ($this->exists()) {
            $sql = "UPDATE " . $this->modelo . " SET "
                    . "  idgrupo = " . $this->var2str($this->no_html($this->idgrupo))
                    . ", tabla = " . $this->var2str($this->no_html($this->tabla))
                    . ", controlador = " . $this->var2str($this->no_html($this->controlador))
                    . ", nombre_controlador = " . $this->var2str($this->no_html($this->nombre_controlador))
                    . ", columna_id = " . $this->var2str($this->no_html($this->columna_id))
                    . ", id_columna = " . $this->var2str($this->no_html($this->id_columna))
                    . ", nombre = " . $this->var2str($this->no_html($this->nombre))
                    . ", nombre_columna = " . $this->var2str($this->no_html($this->nombre_columna))
                    . ", numero = " . $this->var2str($this->no_html($this->numero))
                    . ", numero_columna = " . $this->var2str($this->no_html($this->numero_columna))
                    . ", fecha = " . $this->var2str($this->no_html($this->fecha))
                    . ", fecha_columna = " . $this->var2str($this->no_html($this->fecha_columna))
                    . ", importe = " . $this->var2str($this->no_html($this->importe))
                    . ", importe_euros = " . $this->var2str($this->no_html($this->importe_euros))
                    . ", importe_columna = " . $this->var2str($this->no_html($this->importe_columna))
                    . ", sumatorio_importe = " . $this->var2str($this->no_html($this->sumatorio_importe))
                    . " WHERE id = " . $this->var2str($this->no_html($this->id)) . ";";

            $ret = $this->db->exec($sql);
        } else {
            $sql = "INSERT INTO " . $this->modelo . " (idgrupo, tabla, controlador, nombre_controlador, columna_id, id_columna, nombre, nombre_columna, numero, numero_columna, fecha, fecha_columna, importe, importe_euros, importe_columna, sumatorio_importe) VALUES "
                    . "( " . $this->var2str($this->no_html($this->idgrupo))
                    . ", " . $this->var2str($this->no_html($this->tabla))
                    . ", " . $this->var2str($this->no_html($this->controlador))
                    . ", " . $this->var2str($this->no_html($this->nombre_controlador))
                    . ", " . $this->var2str($this->no_html($this->columna_id))
                    . ", " . $this->var2str($this->no_html($this->id_columna))
                    . ", " . $this->var2str($this->no_html($this->nombre))
                    . ", " . $this->var2str($this->no_html($this->nombre_columna))
                    . ", " . $this->var2str($this->no_html($this->numero))
                    . ", " . $this->var2str($this->no_html($this->numero_columna))
                    . ", " . $this->var2str($this->no_html($this->fecha))
                    . ", " . $this->var2str($this->no_html($this->fecha_columna))
                    . ", " . $this->var2str($this->no_html($this->importe))
                    . ", " . $this->var2str($this->no_html($this->importe_euros))
                    . ", " . $this->var2str($this->no_html($this->importe_columna))
                    . ", " . $this->str2bool($this->no_html($this->sumatorio_importe))
                    . ");";
            $ret = $this->db->exec($sql);
            $this->id = $this->db->lastval();
            header('Location: ' . $this->url());
        }

        return $ret;
    }

    public function delete() {
        return $this->db->exec("DELETE FROM " . $this->modelo . " WHERE id = " . $this->var2str($this->id) . ";");
    }

    /**
     *
     * @return \cp_tabla[]
     */
    public function all() {
        $obj = [];

        $data = $this->db->select("SELECT * FROM " . $this->modelo . " ORDER BY LOWER(tabla), nombre_controlador ASC;");
        if ($data) {
            foreach ($data as $d) {
                $obj[] = new \cp_tabla($d);
            }
        }

        return $obj;
    }

}
