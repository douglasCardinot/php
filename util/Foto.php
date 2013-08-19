<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/util/Util.php');

/**
 * Foto utilities
 *
 * @author Douglas Cardinot <douglas_cardinot@ig.com.br>
 * @version	2.0
 */

class Foto{
	
	private $id = 0;						//Id da foto
	private $nome = '';						//Nome da foto
	private $foto = null;					//Image (GD)
	private $pasta = '';					//Pasta onde a imagem está/será salva
	private $caminhoExibicao = '';			//Caminho para exibição
	private $caminhoExibicaoThumb = '';		//Caminho do thumb para exibição
	
	/**
	 * Filtros de efeitos de imagem. Devem ser utilizadas com o método setFiltro()
	 */
	
	/**
	 * @const SEM_FILTRO 0
	 *	Constante referente ao filtro negativo de imagem
	 */
	const SEM_FILTRO = 0;
	 
	/**
	 * @const FILTRO_NEGATIVO 1
	 *	Constante referente ao filtro negativo de imagem
	 */
	const FILTRO_NEGATIVO = 1;
	
	/**
	 * @const FILTRO_DESSATURACAO 1
	 *	Constante referente ao filtro de dessaturação de imagem
	 */
	const FILTRO_DESSATURACAO = 2;
	
	/**
	 * @const FILTRO_BRILHO 2
	 *	Constante referente ao filtro de brilho de imagem
	 */
	const FILTRO_BRILHO = 3;
	
	/**
	 * @const FILTRO_CONTRASTE 3
	 *	Constante referente ao filtro de contraste de imagem
	 */
	const FILTRO_CONTRASTE = 4;
	
	/**
	 * @const FILTRO_COLORIZACAO 4
	 *	Constante referente ao filtro de colorização de imagem
	 */
	const FILTRO_COLORIZACAO = 5;
	
	/**
	 * @const FILTRO_DETECCAO_BORDAS 5
	 *	Constante referente ao filtro de detecção de bordas de imagem
	 */
	const FILTRO_DETECCAO_BORDAS = 6;
	
	/**
	 * @const FILTRO_ENTALHE 6
	 *	Constante referente ao filtro entalhe de imagem
	 */
	const FILTRO_ENTALHE = 7;
	
	/**
	 * @const FILTRO_GAUSSIANO 7
	 *	Constante referente ao filtro gaussiano de imagem
	 */
	const FILTRO_GAUSSIANO = 8;
	
	/**
	 * @const FILTRO_SMOOTH 8
	 *	Constante referente ao filtro smooth de imagem
	 */
	const FILTRO_SMOOTH = 9;
	
	/**
	 * Construtor - Cria uma classe Foto que representa uma foto salva no servidor ou temporária
	 *
	 * @example new Foto('/img/dinamica/produto/suco-de-uva-101212-2.png');
	 * @param unknown_type foto
	 *	Caminho da foto no servidor ou $_FILES formatada (para isso, usar função montarFiles da classe Util)
	 * @param bool fotoReal
	 *	Se a foto instanciada será aberta ou não em memória. (default to false)
	 * @return void
	 */
	public function __construct($foto, $fotoReal = false) { //throws RuntimeException
		if(is_array($foto)){
			$extensoesPermitidas = array('jpg', 'jpeg', 'png', 'gif');
			$tamanhoMaximo = 1024 * 1024 * 2;
			
			if($foto['name'] != ''){ //Verifica se algum arquivo foi enviado
				if(is_uploaded_file($foto['tmp_name'])){ //Verifica se o upload do arquivo temporário foi realizado com sucesso
					$extensao = strtolower(end(explode('.', $foto['name'])));
					if (!(array_search($extensao, $extensoesPermitidas) === false)){ //Verifica se a extensão do arquivo é válida
						$temp = $foto['tmp_name'];
						if($extensao == 'jpg' || $extensao == 'jpeg')
							$this->foto = imagecreatefromjpeg($temp);
						else if($extensao == 'png')
							$this->foto = imagecreatefrompng($temp);
						else if($extensao == 'gif')
							$this->foto = imagecreatefromgif($temp);
						$campos = explode('.', $foto['name']);
						$this->nome = $campos[0].'.png';
					}else{ //A extensão da foto é inválida
						$erro = 'Formato de arquivo não permitido';
					}
				}else{ //A foto não foi carregada
					$erro = 'Houve uma falha durante o carregamento da foto';
				}
			}else{ //Não havia foto
				$erro = 'A foto não foi enviada';
			}
			
			if(isset($erro)){
				throw new RuntimeException($erro);
			}
		}
		else{
			if($foto{0} != '/'){
				$foto = '/'.$foto;
			}
			if(file_exists($_SERVER["DOCUMENT_ROOT"].$foto)){
				$campos = explode("/", $foto);
				$this->nome = array_pop($campos);
				$this->pasta = implode("/", $campos);
				if($fotoReal){
					$this->foto = imagecreatefrompng($_SERVER["DOCUMENT_ROOT"].$foto); 
					imageAlphaBlending($this->foto, true);
					imageSaveAlpha($this->foto, true);
				}
				$this->caminhoExibicaoThumb = RAIZ.'/'.$this->pasta.'/thumb/'.$this->nome;
				$this->caminhoExibicao = RAIZ.'/'.$this->pasta.'/'.$this->nome;
			}
			else throw new RuntimeException('O objeto foto &lt;'.$foto.'&gt; não pôde ser instanciado');
		}
	}
	
	/**
	 * Transforma a classe em string
	 * 
	 * @author Douglas
	 *
	 * @name __toString
	 * @example echo "A foto $foto é ótima";
	 * @return string
	 *	Caminho de exibição da foto
	 */
	public function __toString(){
		return $this->getCaminhoExibicao();
	}
	
	/**
	 * Redimensiona a foto
	 * 
	 * @author Douglas
	 *
	 * @name desativar
	 * @example desativar("produto", 5);
	 * @param float x
	 *	Nova largura para a imagem
	 * @param float y
	 *	Nova altura para a imagem
	 * @return void
	 */
	public function redimensionar($x, $y) {
		$nova = imagecreatetruecolor($x, $y);
		imagealphablending ($nova, true);
		$transparente = imagecolorallocatealpha($nova, 0, 0, 0, 127);
		imagefill($nova, 0, 0, $transparente);
		imagecopyresampled($nova, $this->foto, 0, 0, 0, 0, $x, $y, $this->getLargura(), $this->getAltura());
		imagesavealpha($nova, true);
		$this->foto = $nova;
	}
	
	/**
	 * Salva a foto que está no objeto na pasta atribuída e com o nome escolhido
	 * 
	 * @author Douglas
	 *
	 * @name salvar
	 * @example salvar();
	 * @return void
	 */
	public function salvar() { // throws RuntimeException 
		if($this->nome == ""){
			throw new RuntimeException("Nome não enviado");
		}
		else if($this->pasta == ""){
			throw new RuntimeException("Pasta não enviada");
		}
		if(!(imagepng($this->foto, $this->getCaminho())))
			throw new RuntimeException('Falha ao salvar');
	}
	
	/**
	 * Exclui a foto que está no objeto
	 * 
	 * @author Douglas
	 *
	 * @name excluir
	 * @example excluir();
	 * @return void
	 */
	public function excluir() { // throws RuntimeException
		//chmod($_SERVER["DOCUMENT_ROOT"].$this->pasta, 0777);
		if(!unlink($this->getCaminho())) {
			throw new RuntimeException('Erro ao excluir foto');
		}else{
			if(is_file($this->getCaminho(true))){
				if(!unlink($this->getCaminho(true))) {
					throw new RuntimeException('Erro ao excluir thumb da foto');
				}
			}
		}
	}
	
	/**
	 * Executa uma consulta no banco e retorna um array com os dados encontrados
	 * Detalhes em: http://www.php.net/manual/pt_BR/function.imagefilter.php
	 * 
	 * @author Douglas
	 *
	 * @name setFiltro
	 * @example setFiltro(Foto::FILTRO_BRILHO, 30);
	 * @param enum const filtro
	 *	Filtro a ser aplicado na imagem. Deve ser um dos tipos disponível nesta classe, definidos como constantes
	 * @param float valor
	 *	Valor para o filtro. Não são todos os filtros que utilizam este parâmetro. (default to null)
	 * @return true se filtro aplicado com sucesso
	 */
	public function setFiltro($filtro, $valor = null){ //Throws RuntimeException
		$sucesso = false;
		
		switch($filtro){
		
			case self::SEM_FILTRO : 
				$sucesso = true;
				break;
				
			case self::FILTRO_NEGATIVO : 
				$sucesso = imagefilter($this->foto, IMG_FILTER_NEGATE);
				break;
				
			case self::FILTRO_DESSATURACAO : 
				$sucesso = imagefilter($this->foto, IMG_FILTER_GRAYSCALE);
				break;
				
			case self::FILTRO_BRILHO : 
				if($valor === null){
					throw new RuntimeException("Envie a quantidade de brilho a ser aplicado");
				}
				$sucesso = imagefilter($this->foto, IMG_FILTER_BRIGHTNESS, $valor);
				break;
				
			case self::FILTRO_CONTRASTE : 
				if($valor === null){
					throw new RuntimeException("Envie a quantidade de brilho a ser aplicado");
				}
				$sucesso = imagefilter($this->foto, IMG_FILTER_CONTRAST, $valor);
				break;
				
			case self::FILTRO_COLORIZACAO : 
				if($valor === null || !is_array($valor)){
					throw new RuntimeException("Envie a cor a ser aplicada");
				}
				$sucesso = imagefilter($this->foto, IMG_FILTER_COLORIZE, $valor[0], $valor[1], $valor[2]);
				break;
				
			case self::FILTRO_DETECCAO_BORDAS : 
				$sucesso = imagefilter($this->foto, IMG_FILTER_EDGEDETECT);
				break;
				
			case self::FILTRO_ENTALHE : 
				$sucesso = imagefilter($this->foto, IMG_FILTER_EMBOSS);
				break;
				
			case self::FILTRO_GAUSSIANO : 
				$sucesso = imagefilter($this->foto, IMG_FILTER_GAUSSIAN_BLUR);
				break;
				
			case self::FILTRO_SMOOTH : 
				if($valor === null || !is_array($valor)){
					throw new RuntimeException("Envie a porcentagem para o filtro");
				}
				$sucesso = imagefilter($this->foto, IMG_FILTER_SMOOTH, $valor);
				break;
				
			default : 
				throw new RuntimeException("Filtro não suportado");
				break;
			
		}
		
		if(!$sucesso)
			throw new RuntimeException('Não foi possível aplicar o filtro');
			
		return true;
	}
	
	public static function getNomeFiltro($filtro){
		switch($filtro){
			case self::SEM_FILTRO : 
				return "Sem filtro";
				break;
			case self::FILTRO_NEGATIVO : 
				return "Negativo";
				break;
			case self::FILTRO_DESSATURACAO : 
				return "Preto e branco";
				break;
			case self::FILTRO_BRILHO : 
				return "Brilho";
				break;
			case self::FILTRO_CONTRASTE : 
				return "Contraste";
				break;
			case self::FILTRO_COLORIZACAO : 
				return "Colorização";
				break;
			case self::FILTRO_DETECCAO_BORDAS : 
				return "Detecção de bordas";
				break;
			case self::FILTRO_ENTALHE : 
				return "Entalhe";
				break;
			case self::FILTRO_GAUSSIANO : 
				return "Gaussiano";
				break;
			case self::FILTRO_SMOOTH : 
				return "Smooth";
				break;
		}
	}
	
	/**
	 * Gera um id único para a foto dentro da pasta onde será salva.
	 * 
	 * @author Douglas
	 *
	 * @name gerarId
	 * @example gerarId();
	 * @return int
	 *		Id da próxima foto a ser salva na pasta.
	 */
	private function gerarId() { //Throws RuntimeException
		if(!is_dir($_SERVER["DOCUMENT_ROOT"].$this->pasta)){
			mkdir($_SERVER["DOCUMENT_ROOT"].$this->pasta, 0777, true);
		}
		$idUltimaFoto = 0;
		if($pasta = opendir($_SERVER["DOCUMENT_ROOT"].$this->pasta)){ 
			while (false !== ($arquivo = readdir($pasta))) {
				if($arquivo != '.' && $arquivo != '..'){
					$valor = explode('.', $arquivo);
					$valor = explode('-',$valor[0]);
					if($idUltimaFoto < (int)$valor[count($valor)-1])
						$idUltimaFoto = (int)$valor[count($valor)-1];
				}
			}
			closedir($pasta); 
		}
		else throw new RuntimeException('Erro ao contar arquivos');
		return $idUltimaFoto+1;
	}
	
	
	/**
	 * Getters and setters
	 * ------------------------------------------
	 */
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
	public function getNome() {
		return $this->nome;
	}
	
	public function setNome($nome) { //Throws RuntimeException
		if($this->pasta == "")
			throw new RuntimeException('A pasta ainda não foi enviada');
		$nome = $this->formatarNome($nome);
		$this->nome = $nome.'-'.date('dmy').'-'.$this->gerarId().'.'.$this->getExtensao();
	}
	
	public function getPasta(){
		return $this->pasta;
	}
	
	public function setPasta($pasta) {
		if(stripos($pasta,'img/dinamica') === false){
			if($pasta{0} == '/') {
				$pasta = '/img/dinamica'.$pasta;
			}
			else {
				$pasta = '/img/dinamica/'.$pasta;
			}
		}
		if($pasta{0} != '/'){
			$pasta = '/'.$pasta;
		}
		if(substr($pasta, -1) == '/'){
			$pasta = substr($pasta, 0, strlen($pasta)-1);
		}
		$this->pasta = $pasta;
	}
	
	public function getLargura() {
		return imagesx($this->foto);
	}
	
	public function setLargura($x) {
		$y = ($x * $this->getAltura()) / $this->getLargura();
		$this->redimensionar($x, $y);
	}
	
	public function getAltura() {
		return imagesy($this->foto);
	}
	
	public function setAltura($y) {
		$x = ( $y * $this->getLargura() ) / $this->getAltura();
		$this->redimensionar($x, $y);
	}
	
	public function getExtensao() {
		$extensao = end(explode('.', $this->nome));
		return $extensao;
	}
	
	public function getCaminho($thumb = false) {
		if($thumb)
			return realpath($_SERVER["DOCUMENT_ROOT"].$this->pasta).'/thumb/'.$this->nome;
		return realpath($_SERVER["DOCUMENT_ROOT"].$this->pasta).'/'.$this->nome;
	}
	
	public function getCaminhoExibicao($thumb = false){
		if($thumb){
			return RAIZ.$this->pasta.'/thumb/'.$this->nome;
		}
		return RAIZ.$this->pasta.'/'.$this->nome;
	}
	
	public function getCaminhoExibicaoThumb(){
		return RAIZ.$this->pasta.'/thumb/'.$this->nome;
	}
	
	public function getCaminhoExibicaoPEB(){
		return RAIZ.$this->pasta.'/peb/'.$this->nome;
	}
	
	private function formatarNome($nome){
		$nome = html_entity_decode($nome);
		$nome = Util::formatarParaUrl($nome);
		return $nome;
	}
	
	/**
	 * End getters and setters
	 * --------------- --------------------------
	 */
	
	/**
	 * Destrutor - Destroi a classe, liberando os espaços de memória 
	 */
	public function __destruct() {
		@imagedestroy($this->foto);
	}
}
?>