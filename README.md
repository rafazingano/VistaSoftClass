# VistaSoft_Class
Vista Soft Class

Exemplo de uso:<br>
include 'VistaSoft_Class.php';<br>
$g = new VistaSoft('c9fdd79584fb8d369a6a579af1a8f681', 'http://sandbox-rest.vistahost.com.br/');<br>
print_r($g->select('*')->from('imoveis')->limit(1,2)->get('listar'));<br>
print_r($g->getUrl());<br>
print_r($g->getFields('imoveis'));<br>
