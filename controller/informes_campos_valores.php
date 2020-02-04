<?php

require_once 'plugins/facturacion_base/extras/fbase_controller.php';
require_once 'plugins/facturacion_base/extras/fs_pdf.php';
require_once 'extras/xlsxwriter.class.php';


/**
 * Description of informes_campos_valores
 *
 * @author Cástor Agra
 */
class informes_campos_valores extends fs_controller {

    /**
     *
     * @var string
     */
    public $b_url;

    /**
     *
     * @var campo
     */
    public $campo;

    /**
     *
     * @var lista_valor
     */
    public $valor;

    /**
     *
     * @var int
     */
    public $numresultados;

    /**
     *
     * @var int
     */
    public $offset;

    /**
     *
     * @var cliente
     */
    public $cliente;

    /**
     *
     * @var articulo
     */
    public $articulo;

    /**
     *
     * @var string
     */
    public $desde;

    /**
     *
     * @var string
     */
    public $documento;

    /**
     *
     * @var string
     */
    public $hasta;

    /**
     *
     * @var int
     */
    public $idtipo;

    /**
     *
     * @var proveedor
     */
    public $proveedor;

    /**
     *
     * @var string
     */
    public $mostrar;

    /**
     *
     * @var array
     */
    public $resultados;
    public $controlador_filtro;
    public $campos;
    public $valores;

    /**
     *
     * @var int
     */
    public $totalresultados;
    public $tabla;
    public $controlador;
    public $id;
    public $nombre;
    public $numero;
    public $nombre_columna;
    public $numero_columna;
    public $tablas;
    public $controladores;
    public $grupos;
    public $totales_resultados;
    public $totales_resultados_txt;

    public function __construct() {
        parent::__construct(__CLASS__, 'Campos personalizados', 'informes');
    }

    function findKey($array, $keySearch) {
        foreach ($array as $key => $item) {
            if ($item == $keySearch) {
                //echo 'yes, it exists';
                return true;
            } elseif (is_array($item) && $this->findKey($item, $keySearch)) {
                return true;
            }
        }
        return false;
    }

    public function get_grupos() {
        $grupo = new cp_grupo();

        $this->grupos = $grupo->all();

        return $this->grupos;

//        $this->grupos = [];
//        foreach ($this->tablas as &$tabla) {
//            //print_r($tabla['grupo']);
//            if (!$this->findKey($this->grupos, $tabla['grupo'])) {
//                //print_r($this->grupos . '<br/>') . '<br/>';
//                $this->grupos[] = array('grupo' => $tabla['grupo'], 'nombre_grupo' => $tabla['nombre_grupo']);
//            }
//        }
//
//        asort($this->grupos);
//        return $this->grupos;
    }

    public function get_tablas($grupo) {
        $tabla = new cp_tabla();

        $this->tablas = $tabla->get_from_grupo($grupo);
        return $this->tablas;
    }

    public function get_nombres_controladores($grupo) {
        $tabla = new cp_tabla();
        $this->controladores = $tabla->get_controladores_from_grupo($grupo);

        return $this->controladores;
//        $this->controladores = [];
//        foreach ($this->tablas as &$tabla) {
//            if ($tabla['grupo'] == $grupo) {
//                $this->controladores[] = array('controlador' => $tabla['controlador'], 'nombre_controlador' => $tabla['nombre_controlador']);
//            }
//        }
//        asort($this->controladores);
//        return $this->controladores;
    }

    public function get_campos() {
        $this->campos = [];
        $campo = new campo();

        $this->campos = $campo->all_from_controlador($this->tipo);

        return $this->campos;
    }

    public function get_valores_campo($campo) {
        $this->valores = [];
        $campo_valor = new lista_valor();

        $this->valores = $campo_valor->all_from_codlista_controlador($this->tipo, $campo);

        return $this->valores;
    }

    protected function private_core() {
        parent::private_core();

        $tbl = new \cp_tabla();

        /*
          $this->tablas = array(
          array(
          'tabla' => 'facturascli', //tablas
          'controlador' => 'ventas_factura', //tablas
          'grupo' => 'ventas', //grupo
          'nombre_grupo' => 'Ventas', //grupo
          'nombre_controlador' => 'Facturas', //tablas
          'id' => 'idfactura', //tablas
          'nombre_id' => 'id', //tablas
          'nombre' => 'nombrecliente', //tablas
          'numero' => 'codigo', //tablas
          'fecha' => 'fecha',
          'nombre_columna' => 'Cliente',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'facturasprov',
          'controlador' => 'compras_factura',
          'grupo' => 'compras',
          'nombre_grupo' => 'Compras',
          'nombre_controlador' => 'Facturas',
          'id' => 'idfactura',
          'nombre_id' => 'id',
          'nombre' => 'nombre',
          'numero' => 'codigo',
          'fecha' => 'fecha',
          'nombre_columna' => 'Proveedor',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'albaranescli',
          'controlador' => 'ventas_albaran',
          'grupo' => 'ventas',
          'nombre_grupo' => 'Ventas',
          'nombre_controlador' => 'Albaranes',
          'id' => 'idalbaran',
          'nombre_id' => 'id',
          'nombre' => 'nombrecliente',
          'numero' => 'codigo',
          'fecha' => 'fecha',
          'nombre_columna' => 'Cliente',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'albaranesprov',
          'controlador' => 'compras_albaran',
          'grupo' => 'compras',
          'nombre_grupo' => 'Compras',
          'nombre_controlador' => 'Albaranes',
          'id' => 'idalbaran',
          'nombre_id' => 'id',
          'nombre' => 'nombre',
          'numero' => 'codigo',
          'fecha' => 'fecha',
          'nombre_columna' => 'Proveedor',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'pedidoscli',
          'controlador' => 'ventas_pedido',
          'grupo' => 'ventas',
          'nombre_grupo' => 'Ventas',
          'nombre_controlador' => 'Pedidos',
          'id' => 'idpedido',
          'nombre_id' => 'id',
          'nombre' => 'nombrecliente',
          'numero' => 'codigo',
          'fecha' => 'fecha',
          'nombre_columna' => 'Cliente',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'pedidosprov',
          'controlador' => 'compras_pedido',
          'grupo' => 'compras',
          'nombre_grupo' => 'Compras',
          'nombre_controlador' => 'Pedidos',
          'id' => 'idpedido',
          'nombre_id' => 'id',
          'nombre' => 'nombre',
          'numero' => 'codigo',
          'fecha' => 'fecha',
          'nombre_columna' => 'Proveedor',
          'numero_columna' => 'Número',
          'fecha_columna' => 'Fecha',
          'importe' => 'total',
          'importe_euros' => 'totaleuros',
          'importe_columna' => 'Importe',
          'sumatorio_importe' => TRUE
          ),
          array(
          'tabla' => 'articulos',
          'controlador' => 'ventas_articulo',
          'grupo' => 'articulos',
          'nombre_grupo' => 'Artículos',
          'nombre_controlador' => 'Artículos',
          'id' => 'referencia',
          'nombre_id' => 'ref',
          'nombre' => 'descripcion',
          'numero' => 'referencia',
          'fecha' => '',
          'nombre_columna' => 'Artículo',
          'numero_columna' => 'Referencia',
          'fecha_columna' => '',
          'importe' => '',
          'importe_euros' => '',
          'importe_columna' => '',
          'sumatorio_importe' => FALSE
          ),
          array(
          'tabla' => 'clientes',
          'controlador' => 'ventas_cliente',
          'grupo' => 'ventas',
          'nombre_grupo' => 'Ventas',
          'nombre_controlador' => 'Clientes',
          'id' => 'codcliente',
          'nombre_id' => 'cod',
          'nombre' => 'nombre',
          'numero' => 'cifnif',
          'fecha' => 'fechaalta',
          'nombre_columna' => 'Cliente',
          'numero_columna' => 'CIF/NIF',
          'fecha_columna' => 'Fecha Alta',
          'importe' => '',
          'importe_euros' => '',
          'importe_columna' => '',
          'sumatorio_importe' => FALSE
          )
          );
         */

        //asort($this->tablas);



        if (isset($_REQUEST['buscar_cliente'])) {
            $this->buscar_cliente();
        } else if (isset($_REQUEST['buscar_proveedor'])) {
            $this->buscar_proveedor();
        } else if (isset($_REQUEST['buscar_articulo'])) {
            $this->buscar_articulo();
        } else {
            $this->mostrar = isset($_REQUEST['mostrar']) ? $_REQUEST['mostrar'] : 'articulos';
            $this->desde = isset($_REQUEST['desde']) ? $_REQUEST['desde'] : '';
            $this->hasta = isset($_REQUEST['hasta']) ? $_REQUEST['hasta'] : '';
            $this->offset = isset($_REQUEST['offset']) ? (int) $_REQUEST['offset'] : 0;

            $this->cliente = new cliente();
            $this->proveedor = new proveedor();
            $this->articulo = new articulo();
            $this->campo = new campo();
            if (isset($_REQUEST['codcliente']) && $_REQUEST['codcliente'] != '') {
                $cli0 = new cliente();
                $this->cliente = $cli0->get($_REQUEST['codcliente']);
            }
            if (isset($_REQUEST['codproveedor']) && $_REQUEST['codproveedor'] != '') {
                $pro0 = new proveedor();
                $this->proveedor = $pro0->get($_REQUEST['codproveedor']);
            }
            if (isset($_REQUEST['referencia']) && $_REQUEST['referencia'] != '') {
                $art0 = new articulo();
                $this->articulo = $art0->get($_REQUEST['referencia']);
            }
            if (isset($_REQUEST['campo']) && $_REQUEST['campo'] != '') {
                $cam0 = new campo();
                $this->campo = $cam0->get_from_campo($_REQUEST['campo']);
            }
            if (isset($_REQUEST['valor']) && $_REQUEST['valor'] != '') {
                $this->valor = $_REQUEST['valor'];
            } else {
                $this->valor = '-1';
            }

//            $this->tipo = 'facturascli';
//            $this->idtipo = 'idfactura';
//            $this->documento = 'factura_cliente';

            if (isset($_REQUEST['tipo'])) {
                $this->tipo = $_REQUEST['tipo'];
            } else {
                $this->tipo = $this->get_nombres_controladores($this->mostrar)[0]['controlador'];
            }

            //$tablas[] = new cp_tabla();
            $this->get_tablas($this->mostrar);
//            var_dump($tbl->get_from_grupo($this->mostrar));

            //var_dump($this->tablas);

            foreach ($this->tablas as $tabla) {
                if ($tabla->controlador == $this->tipo) {

                    $this->controlador_filtro = $this->tipo;

                    $this->idtipo = $tabla->columna_id;
                    $this->documento = $tabla->tabla;
                }
            }
            $this->resultados = $this->find();

            /// url para paginacion y descarga
            $this->b_url = $this->url() . "&mostrar=" . $this->mostrar
                    . "&codcliente=" . $this->cliente->codcliente
                    . "&codproveedor=" . $this->proveedor->codproveedor
                    . "&referencia=" . $this->articulo->referencia
                    . "&campo=" . $this->campo->campo
                    . "&tipo=" . $this->tipo
                    . "&desde=" . $this->desde
                    . "&hasta=" . $this->hasta
                    . "&offset=" . $this->offset;

            if (isset($_REQUEST['generar']) && $_REQUEST['generar'] == 'pdf') {
                $this->generar_pdf();
            }
        }
    }

    /**
     * Buscamos los campos_valores con adjuntos.
     * 
     * @return array
     */
    public function find() {
        $resultados = [];

//        /// inicio y fin
//        $inicio = intval($this->offset);
//        $fin = intval($inicio + FS_ITEM_LIMIT);
//
//        if ($this->mostrar == 'ventas') {
//            $nombre = 'nombrecliente';
//            $prov = '';
//            $num2 = 'numero2';
//        } else {
//            $nombre = 'nombre';
//            $prov = 'prov';
//            $num2 = 'numproveedor';
//        }
        //$t = array_filter($tablas, "$this->filter_tabla");
        $t = [];
        foreach ($this->tablas as &$tabla) {
            if ($tabla->controlador == $this->controlador_filtro) {
                //print_r($tabla);
                $resultados = $tabla;
            }
        }

        $select = "";
        $join = "";

        if ($resultados) {
            $select = ", "
                    . $resultados->tabla . "." . $resultados->nombre . " as columna_nombre, "
                    . $resultados->tabla . "." . $resultados->numero . " as columna_numero, "
                    . $resultados->tabla . "." . $resultados->columna_id . " as columna_id "
                    . ($resultados->fecha != '' ? (", " . $resultados->tabla . "." . $resultados->fecha . " as columna_fecha") : "")
                    . ($resultados->importe != '' ? (", " . $resultados->tabla . "." . $resultados->importe . " as columna_importe, coddivisa, tasaconv") : "")
                    . ($resultados->importe_euros != '' ? (", " . $resultados->tabla . "." . $resultados->importe_euros . " as columna_importe_euros") : "");
            $join = " INNER JOIN " . $resultados->tabla . " on " . $resultados->tabla . "." . $resultados->columna_id . " = cv.idrelacion";
        }

        /// filtros.
        $sql = '';
        $where = '';
        if ($this->controlador_filtro != '') {
            $where .= " AND c.controlador = '" . $this->db->escape_string($this->controlador_filtro) . "' ";
        }
        if ($this->campo->campo != '') {
            $where .= " AND campo = '" . $this->db->escape_string($this->campo->campo) . "' ";
        }
        if ($this->valor != '-1') {
            $where .= " AND cv.valor = '" . $this->db->escape_string($this->valor) . "' ";
        }

        if ($this->desde != '' && $resultados->fecha != '') {
            $where .= " AND " . $resultados->tabla . "." . $resultados->fecha . " >= " . $this->empresa->var2str($this->desde);
        }

        if ($this->hasta != '' && $resultados->fecha != '') {
            $where .= " AND " . $resultados->tabla . "." . $resultados->fecha . " <= " . $this->empresa->var2str($this->hasta);
        }

        if ($this->cliente->codcliente) {
            $where .= " AND " . $resultados->tabla . ".codcliente = " . $this->empresa->var2str($this->cliente->codcliente);
        }

        if ($this->proveedor->codproveedor) {
            $where .= " AND " . $resultados->tabla . ".codproveedor = " . $this->empresa->var2str($this->proveedor->codproveedor);
        }
//
//        if ($this->cliente->codcliente) {
//            $sql .= " AND codcliente = " . $this->empresa->var2str($this->cliente->codcliente);
//        }
//
//        if ($this->proveedor->codproveedor) {
//            $sql .= " AND codproveedor = " . $this->empresa->var2str($this->proveedor->codproveedor);
//        }

        $sql = "SELECT cv.id, cv.idcampo, case when c.tipo = 'list' then lv.valor else cv.valor end as valor, cv.idrelacion, c.tipo, lv_controlador.nombre_controlador, c.controlador, c.campo, c.nombre as nombre_campo " . $select
                . " FROM campos_valores cv "
                . " inner join campos c on c.id = cv.idcampo "
                . $join
                . " LEFT JOIN listas_valores lv on lv.codigo = cv.valor "
                . " LEFT JOIN listas l on l.id = lv.idlista and l.codlista = c.campo"
                . " INNER JOIN cp_tablas lv_controlador on lv_controlador.controlador = c.controlador "
                //. " INNER JOIN listas l_controlador on l_controlador.id = lv_controlador.idlista and l_controlador.codlista = 'cboControlador'"
                . " WHERE 1=1 "
                . " AND CASE WHEN c.tipo = 'list' THEN l.id ELSE 0 END = CASE WHEN c.tipo = 'list' THEN lv.idlista ELSE 0 END "
                . $where
                . " ORDER BY c.controlador, c.nombre desc, " . $resultados->tabla . "." . $resultados->numero . ", case when c.tipo = 'list' then lv.valor else cv.valor end;";
        $data = $this->db->select($sql);
        //print_r($sql);
//        return;


        $this->totales_resultados = array();
        if ($resultados->sumatorio_importe) {
            $this->totales_resultados_txt = 'Suma total de esta página (agrupado por tipo de documento):';
        }
        if ($data) {
            foreach ($data as $d) {

                $this->resultados[] = $d;

                if ($resultados->sumatorio_importe) {
                    if (!isset($this->totales_resultados[$d['coddivisa']])) {
                        $this->totales_resultados[$d['coddivisa']] = array(
                            'coddivisa' => $d['coddivisa'],
                            'total' => '0',
                            'items' => []
                        );
                        $this->totales_resultados[$d['coddivisa']]['items'][$d['columna_numero']] = array(
                            'numero' => $d['columna_numero'],
                            'total' => $d['columna_importe']
                        );
                        //print_r('columna_importe' . $d['columna_importe']);
                    } else {
                        if (!isset($this->totales_resultados[$d['coddivisa']]['items']['numero'][$d['columna_numero']])) {
                            $this->totales_resultados[$d['coddivisa']]['items'][$d['columna_numero']] = array(
                                'numero' => $d['columna_numero'],
                                'total' => $d['columna_importe']
                            );
                        } else {
                            //$this->totales_resultados[$d['coddivisa']]['items']['numero'][$d['columna_numero']]['total'] = floatval($this->totales_resultados[$d['coddivisa']]['items']['numero'][$d['columna_numero']]['total']) + floatval($d['columna_importe']);
                        }
                    }
                }
            }
        }
        $this->totalresultados = $resultados;
        $this->numresultados = count($data);
        //print_r($this->totales_resultados);
        foreach ($this->totales_resultados as $divisa) {
            foreach ($divisa['items'] as $item) {
//                if (!isset($this->totales_resultados[$item->coddivisa])) {
//                    $this->totales_resultados[$item->coddivisa] = array(
//                        'coddivisa' => $item->coddivisa,
//                        'total' => 0
//                    );
//                }
                //print_r('tiem' . $item['total']);
                $this->totales_resultados[$divisa['coddivisa']]['total'] = floatval($this->totales_resultados[$divisa['coddivisa']]['total']) + floatval($item['total']);
            }
        }

//        var_dump($this->totales_resultados);
        //debug_zval_dump($this->totales_resultados);
        return;
//        return array_slice($resultados, $inicio, $fin);
    }

    /**
     * buscamos los clientes autocomplete
     */
    private function buscar_cliente() {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        $cli0 = new cliente();
        $json = [];
        foreach ($cli0->search($_REQUEST['buscar_cliente']) as $cli) {
            $json[] = array('value' => $cli->nombre, 'data' => $cli->codcliente);
        }

        header('Content-Type: application/json');
        echo json_encode(array('query' => $_REQUEST['buscar_cliente'], 'suggestions' => $json));
    }

    /**
     * Buscamos el proveedor autocomplete
     */
    private function buscar_proveedor() {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        $pro0 = new proveedor();
        $json = [];
        foreach ($pro0->search($_REQUEST['buscar_proveedor']) as $pro) {
            $json[] = array('value' => $pro->nombre, 'data' => $pro->codproveedor);
        }

        header('Content-Type: application/json');
        echo json_encode(array('query' => $_REQUEST['buscar_proveedor'], 'suggestions' => $json));
    }

    /**
     * Buscamos el articulo autocomplete
     */
    private function buscar_articulo() {
        /// desactivamos la plantilla HTML
        $this->template = FALSE;

        $pro0 = new articulo();
        $json = [];
        foreach ($pro0->search($_REQUEST['buscar_articulo']) as $pro) {
            $json[] = array('value' => $pro->descripcion, 'data' => $pro->referencia);
        }

        header('Content-Type: application/json');
        echo json_encode(array('query' => $_REQUEST['buscar_articulo'], 'suggestions' => $json));
    }

    protected function generar_pdf($tipo = 'compra') {
        /// desactivamos el motor de plantillas
        $this->template = FALSE;

        $resultados = [];
        foreach ($this->tablas as &$tabla) {
            if ($tabla->controlador == $this->controlador_filtro) {
                //print_r($tabla);
                $resultados = $tabla;
            }
        }

        $pdf_doc = new fs_pdf('a4', 'landscape', 'Courier');
        $titulo = 'Valores personalizados de ' . $resultados->nombre_controlador;
        if ($this->desde != '') {
            $titulo .= ' desde ' . $this->desde;
        }
        if ($this->hasta != '') {
            $titulo .= ' hasta ' . $this->hasta;
        }
        $pdf_doc->pdf->addInfo('Title', 'Valores personalizados');
        $pdf_doc->pdf->addInfo('Subject', $titulo);
        $pdf_doc->pdf->addInfo('Author', fs_fix_html($this->empresa->nombre));

        $this->find();

//        $tabla = $this->table_compras;
//        if ($tipo == 'venta') {
//            $cliente = 'Cliente';
//            $num2 = FS_NUMERO2;
//            $tabla = $this->table_ventas;
//        }


        $encabezado = $this->empresa->nombre . ' - ' . $resultados->nombre_controlador;
                if ($this->desde != '') {
            $encabezado .= ', desde: ' . $this->desde;
        }
        if ($this->hasta != '') {
            $encabezado .= ', hasta: ' . $this->hasta;
        }

        if ($this->cliente->nombre != '') {
            $encabezado .= ', cliente: ' . $this->cliente->nombre;
        }

        if ($this->proveedor->nombre != '') {
            $encabezado .= ', proveedor: ' . $this->proveedor->nombre;
        }

        if ($this->articulo->descripcion != '') {
            $encabezado .= ', artículo: ' . $this->articulo->descripcion;
        }

        if ($this->campo->nombre != '') {
            $encabezado .= ', campo: ' . $this->campo->nombre;
        }

        if ($this->valor != '-1') {
            $encabezado .= ', valor: ' . $this->valor;
        }

        $encabezado = fs_fix_html($encabezado);

        $documentos = $this->resultados;
        if (!empty($documentos)) {
            $total_lineas = count($documentos);
            $linea_actual = 0;
            $lppag = 72;
            $pagina = 1;
            $totales = [];


            while ($linea_actual < $total_lineas) {
                if ($linea_actual > 0) {
                    $pdf_doc->pdf->ezNewPage();
                    $pagina++;
                }

                /// encabezado
                $pdf_doc->pdf->ezText($encabezado . ".\n\n");

                /// tabla principal
                $pdf_doc->new_table();
                $table_header = array(
                    'doc' => '<b>' . $resultados->numero_columna . '</b>',
                            'fecha' => '<b>' . $resultados->fecha_columna . '</b>',
                            'nombre' => '<b>' . $resultados->nombre_columna . '</b>',
                            'campo' => '<b>Campo</b>',
                    'valor' => '<b>Valor</b>',
                    'importe' => '<b>' . $resultados->importe_columna . '</b>'
                        );
                if ($resultados->fecha == '') {
                            unset($table_header['fecha']);
                }
                if ($resultados->importe == '') {
                            unset($table_header['importe']);
                }

                $pdf_doc->add_table_header($table_header);

                for ($i = 0; $i < $lppag && $linea_actual < $total_lineas; $i++) {
                    $linea = array(
                        'doc' => $documentos[$linea_actual]['columna_numero'],
                        'fecha' => ($resultados->fecha != '') ? date('d-m-Y', strtotime($documentos[$linea_actual]['columna_fecha'])) : '',
                                'nombre' => $documentos[$linea_actual]['columna_nombre'],
                        'campo' => $documentos[$linea_actual]['nombre_campo'],
                        'valor' => $documentos[$linea_actual]['valor'],
                        'importe' => ($resultados->importe != '') ? $this->show_precio($documentos[$linea_actual]['columna_importe'], $documentos[$linea_actual]['coddivisa']) : ''
                            );
                    if ($resultados->fecha == '') {
                                unset($linea['fecha']);
                    }
                    if ($resultados->importe == '') {
                                unset($linea['importe']);
                    }

//                    if ($tipo == 'compra') {
//                        $linea['num2'] = fs_fix_html($documentos[$linea_actual]->numproveedor);
//                        $linea['cliente'] = fs_fix_html($documentos[$linea_actual]->nombre);
//                    } else {
//                        $linea['num2'] = fs_fix_html($documentos[$linea_actual]->numero2);
//                        $linea['cliente'] = fs_fix_html($documentos[$linea_actual]->nombrecliente);
//                    }

                    $pdf_doc->add_table_row($linea);
                    if ($resultados->importe != '') {
                                $divisa = $documentos[$linea_actual]['coddivisa'];


                        if (array_key_exists($divisa, $totales)) {
                            $totales[$divisa] += floatval($documentos[$linea_actual]['columna_importe']);
                        } else {
                            $totales[$divisa] = floatval($documentos[$linea_actual]['columna_importe']);
                        }
                    }

                    $i++;
                    $linea_actual++;
                }

                if ($this->campo->nombre != '') {
                    /// añadimos el subtotal
                    $i = 0;
                    foreach ($totales as $key => $value) {
                        $linea = array(
                            'doc' => '',
                            'fecha' => '',
                            'nombre' => '',
                            'campo' => '',
                            'valor' => '',
                            'importe' => '<b>' . $this->show_precio($value, $key, FALSE) . '</b>'
                        );
                        if ($resultados->fecha == '') {
                            unset($linea['fecha']);
                        }
                        if ($resultados->importe == '') {
                                    unset($linea['importe']);
                        }
                        $pdf_doc->add_table_row($linea);
                    }
                }

                $pdf_doc->save_table(
                        array(
                            'fontSize' => 10,
                            'cols' => array(
                                'fecha' => array('justification' => 'center', 'width' => '80'),
                                'importe' => array('justification' => 'right')
                            ),
                            'shaded' => 0,
                            'width' => 780
                        )
                );
            }

            //$this->desglose_impuestos_pdf($pdf_doc, $tipo);
        } else {
            $pdf_doc->pdf->ezText($encabezado . '.');
            $pdf_doc->pdf->ezText("\nSin resultados.", 14);
        }

        $pdf_doc->show();
    }

}
