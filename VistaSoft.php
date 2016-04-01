<?php
/**
 * Description of VistaSoft_Class 
 * @author Zingano
 **/
class VistaSoft {
    private $fields     = null;
    private $key        = null; //'c9fdd79584fb8d369a6a579af1a8f681'; 
    private $url        = null; //'http://sandbox-rest.vistahost.com.br/';
    private $select     = null;
    private $from       = null;
    private $where      = null;
    private $get        = null;
    private $filter     = null;
    private $order      = null;
    private $pagination = null;
    
    /*
     * Contrutor
     * classe deve ser instanciada com a chave e 
     * ex: $vs = new VistaSoft('c9fdd79584fb8d369a6a579af1a8f681', 'sandbox-rest');
    */
    function __construct($key = null, $url = null){
        $this->key = $key;
        $this->url = $url;
        if(!isset($this->key)){ die('Falta informar uma chave'); }
        if(!isset($this->url)){ die('Falta informar uma url'); }
        //if(!in_array('ok', $this->getCurl($this->url . 'reloadcache?key=' . $this->key))){
        //    die('Url e/ou chave invalidos');
        //}
    }
    
    function clear(){
        $this->fields = null;
        $this->filter = null;
        $this->order = null;
        $this->pagination = null;
        $this->select = null;
    }
    
    //function getKey() {
    //    return $this->key;
    //}

    //function setKey($key) {
    //    $this->key = $key;
    //}
    
    //function getUrl() {
    //    return $this->url;
    //}

    //function setUrl($key) {
    //    $this->url = $key;
    //}

        
    /*
     * Função curl para conectar
    */
    private function getCurl($url = null){
        $ch = curl_init(isset($url)? $url : $this->getUrl());
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER , array( 'Accept: application/json' ) );
        return json_decode( curl_exec( $ch ), true );
    }
    
    /*
     * Monta a url
    */
    public function getUrl(){
        $this->fields = isset($this->fields)? $this->fields : $this->getFields($this->from)[$this->from];
        $url = $this->url . $this->from . '/' . $this->get . '?key=' . $this->key;
        if($this->get == 'detalhes'){
            $url .= '&imovel=' . $this->filter['Codigo'];            
            $foto = ["Foto"=>["Foto","FotoPequena","Destaque"]];
            if(!in_array('Foto', $this->fields)){ array_push($this->fields, $foto); }
            if(!in_array('Caracteristicas', $this->fields)){ array_push($this->fields, "Caracteristicas"); }
            if(!in_array('InfraEstrutura', $this->fields)){ array_push($this->fields, "InfraEstrutura"); }
        }else{        
            if(isset($this->filter)){ $data['filter'] = $this->filter; }
            if(isset($this->order)){ $data['order'] = $this->order; }
            if(isset($this->pagination)){ $data['paginacao'] = $this->pagination; }
        }
        if(isset($this->fields)){ $data['fields'] = $this->fields; }
        if(isset($this->select)){ $url .= '&pesquisa=' . json_encode( $data ); }
        $this->clear();
        return $url;
    }
    
    /*
     * Function select()
     * Quais campos serão utilizados na pesquisa
     * se nenhum campo for selecionado retorna todos disponiveis
    */
    public function select($select = '*'){
        $this->select = $select;
        return $this;
    }
    
    /*
     * Function From()
     * Buscar por imoveis ou clientes
     * Default imoveis
    */
    public function from($from = 'imoveis'){
        $this->from = $from;
        return $this;
    }
    
    /*
     * Metodo where()
     * faz o filtro
     * $filter['Cidade'] = 'Porto Alegre'
     * $filter['bairro'] = array('Guaruja', 'São+Pedro')
     * pode se usar um array com os simbolos
     *  ">" - Maior que x
        "<" - Menor que x
        ">=" - Maior ou igual à x
        "<=" - Menor ou igual à x
        "like" - Similar ao texto
        "!=" - Diferente de x
     * $filter['valorVenda'] = array(">=", 500000)
    */
    public function where($filter = null){
        $this->filter = $filter;
        return $this;
    }
    
    /*
     * Metodo order()
     * ordena a busca
     * order('Bairro', 'asc')
     * order(array('Bairro' => 'asc', 'ValorVenda'  => 'asc'))
    */
    public function order($order = null, $by = 'asc'){
        $this->order = array( is_array($order)? $order : $order . '=>' . $by);
        return $this;
    }
    
    /*
     * Function limit()
     * metodo para selecionar a pagina e quantidade por pagina
    */
    public function limit($quantidade = 50, $pagina = 1){
        $this->pagination = array('pagina' => isset($pagina)? $pagina : 1, 'quantidade' => ($quantidade > 50)? 50 : $quantidade);
        return $this;
    }
    
    /*
     * Function get()
     * retorna o resultado final
     * basta definir o get
     * get default listar
     * listar, listarConteudo, detalhes
    */
    public function get($get = 'listar'){
        $this->get = $get;
        return $this->getCurl();      
    }
    
    /*
     * Function getFields()
     * Busca campos disponoveis
     * Basta informar o from
     * from default imoveis
     * imoveis, clientes
    */
    public function getFields($from = 'imoveis') {
        return $this->getCurl($this->url . $from . '/listarcampos?key=' . $this->key);
    }
}