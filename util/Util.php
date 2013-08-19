<?php
/**
* Function Utilities.
*
* @author
*	Cássio Martins <cassioamartins07@gmail.com>
*	Douglas Cardinot <douglas_cardinot@ig.com.br>
*	Igor Frotté Pedro <igorfrotte@hotmail.com>
* @version	1.5.2
*/
 
require_once($_SERVER['DOCUMENT_ROOT']."/util/Foto.php");

abstract class Util {
	
	/**
	* Remove os caracteres especiais de um texto
	* 
	* @author Douglas
	*
	* @name removerCaracteresEspeciais
	* @example removerCaracteresEspeciais("Atenção");
	* @param string texto
	*	O texto para ser removido os caracteres
	* @return string texto
	*/
	public static function removerCaracteresEspeciais($texto){
		$texto = str_replace("á", "a", $texto);
		$texto = str_replace("Á", "a", $texto);
		$texto = str_replace("é", "e", $texto);
		$texto = str_replace("É", "E", $texto);
		$texto = str_replace("í", "i", $texto);
		$texto = str_replace("Í", "I", $texto);
		$texto = str_replace("ó", "o", $texto);
		$texto = str_replace("Ó", "O", $texto);
		$texto = str_replace("ú", "u", $texto);
		$texto = str_replace("Ú", "U", $texto);
		$texto = str_replace("ã", "a", $texto);
		$texto = str_replace("Ã", "A", $texto);
		$texto = str_replace("õ", "o", $texto);
		$texto = str_replace("Õ", "O", $texto);
		$texto = str_replace("â", "a", $texto);
		$texto = str_replace("Â", "A", $texto);
		$texto = str_replace("ê", "e", $texto);
		$texto = str_replace("Ê", "E", $texto);
		$texto = str_replace("ô", "o", $texto);
		$texto = str_replace("Ô", "O", $texto);
		$texto = str_replace("ç", "c", $texto);
		$texto = str_replace("Ç", "C", $texto);
		$texto = str_replace("Ì", "I", $texto);
		$texto = str_replace("ì", "i", $texto);
		$texto = str_replace('º','o',$texto);
		$texto = str_replace('à','a',$texto);
		$texto = str_replace("°","o",$texto);
		return $texto;
	}
	
	/**
	* Remove as pontuações de um texto
	* 
	* @author Douglas
	*
	* @name removerPontuacoes
	* @example removerCaracteresEspeciais("Olá! 100% de aproveitamento...");
	* @param string texto
	*	O texto para ser removido as pontuações
	* @return string texto
	*/
	public static function removerPontuacoes($texto){
		$texto = str_replace("%","",$texto);
		$texto = str_replace("$","",$texto);
		$texto = str_replace("#","",$texto);
		$texto = str_replace("\/","-",$texto);
		$texto = str_replace("\\","-",$texto);
		$texto = str_replace("@","",$texto);
		$texto = str_replace("*","",$texto);
		$texto = str_replace("\"","",$texto);
		$texto = str_replace("\'","",$texto);
		$texto = str_replace("{","",$texto);
		$texto = str_replace("}","",$texto);
		$texto = str_replace("[","",$texto);
		$texto = str_replace("]","",$texto);
		$texto = str_replace(":","",$texto);
		$texto = str_replace(";","",$texto);
		$texto = str_replace(">","",$texto);
		$texto = str_replace("<","",$texto);
		$texto = str_replace(",","",$texto);
		$texto = str_replace("?","",$texto);
		$texto = str_replace("|","",$texto);
		$texto = str_replace("¨","",$texto);
		$texto = str_replace("(","",$texto);
		$texto = str_replace(")","",$texto);
		return $texto;
	}
	
	/**
	* Formata o texto para utilização em URL
	* 
	* @author Douglas
	*
	* @name formatarParaUrl
	* @example formatarParaUrl("Atenção");
	* @param string texto
	*	O texto para ser removido os caracteres
	* @return string texto
	*/
	public static function formatarParaUrl($texto){
		$texto = html_entity_decode($texto, ENT_QUOTES, 'UTF-8');
		$texto = self::removerCaracteresEspeciais($texto);
		$texto = self::removerPontuacoes($texto);
		$texto = str_replace(" ", "-", $texto);
		$texto = str_replace(".", "-", $texto);
		$texto = str_replace("/", "-", $texto);
		$texto = strtolower($texto);
		return $texto;
	}
	
	/**
	* Monta um tipo _FILES em array
	* 
	* Essa função deve ser chamada sempre antes de percorrer um array de _FILES 
	* pelo foreach() para montar objetos do tipo Foto
	*
	* @author Douglas
	*
	* @name montarFiles
	* @example montarFiles($_FILES['fotos']);
	* @param array vector
	* @return array
	*	Um array de _FILES
	*/
	public static function montarFiles(array $vector) { 
		$result = array(); 
		foreach($vector as $key1 => $value1) 
			foreach($value1 as $key2 => $value2) 
				$result[$key2][$key1] = $value2; 
		return $result; 
	}
	
	/**
	* Monta um texto a partir de um array
	*
	* @author Douglas
	*
	* @name arrayParaTexto
	* @example arrayParaTexto($meuArray);
	* @param array vector
	* @return String
	*/
	public static function arrayParaTexto(array $vector) { 
		$texto = "Array( ";
		foreach($vector as $key => $v){
			if(is_array($v))
				$texto .= " [".$key."] => ".self::arrayParaTexto($v)." ";
			else $texto .= " [".$key."] => ".$v." ";
		}
		$texto .= ")";
		return $texto;
	}
	
	/**
	* Exclui a pasta e todos os arquivos da mesma
	* 
	* @author Douglas
	*
	* @name excluirArquivos
	* @example excluirArquivos("/img/dinamica/produto/3");
	* @param string dir
	*	Diretório a ser removido
	* @return bool
	*	false se houve algum erro ou true se funcionou como o esperado
	*/
	public static function excluirArquivos($dir1){
		if($dir1{0} != '/'){
			$dir1 = '/'.$dir1;
		}
		$dir = $_SERVER['DOCUMENT_ROOT'].$dir1;
		if(is_dir($dir)){
			if($handle = opendir($dir)){
				while(false !== ($file = readdir($handle))){
					if(($file == ".") or ($file == ".."))
						continue;
					if(is_dir($dir."/".$file))
						self::excluirArquivos($dir1."/".$file);
					else{
						chmod ($dir."/".$file, 0777);
						unlink($dir."/".$file);
					}
				}
			}else{
				return false;
			}
			closedir($handle);
			rmdir($dir);
		}else{
			return false;
		}
		return true;
	}
	
	/**
	 * Obtém todas as fotos dentro do diretório
	 * 
	 * @author Douglas
	 *
	 * @name todasAsFotosNaPasta
	 * @example todasAsFotosNaPasta("/img/dinamica/produto/3");
	 * @param string diretorio
	 *	Diretório onde estão as fotos
	 * @param bool fotoReal
	 *	Se a foto instanciada será aberta ou não em memória. (default to false)
	 * @return array Fotos
	 *	array de objetos do tipo Foto
	 */
	public static function todasAsFotosNaPasta($diretorio, $fotoReal = false){
		if(substr($diretorio, -1) == '/')
			$diretorio = substr($diretorio, 0, -1);
		$pasta = ($diretorio{0} == '/') ? $diretorio : '/'.$diretorio;
		$pasta = $_SERVER['DOCUMENT_ROOT'].$pasta;
		$fotos = array();
		if(is_dir($pasta)){
			if($pastaImagem = opendir($pasta)){
				while(false !== ($file = readdir($pastaImagem)))
					if($file!="." && $file!=".." && !is_dir($pasta."/".$file)){
						try{
							$foto = new Foto($diretorio."/".$file, $fotoReal);
							array_push($fotos, $foto);
						}catch(Exception $e){
						}
					}
				closedir($pastaImagem); 
			}
		}
		return $fotos;
	}
	
	/**
	* Obtém as coordenadas (latitude e longitude) de um endereço
	* 
	* @author Douglas
	*
	* @name obterCoordenadas
	* @example obterCoordenadas("Av. Antônio Mário de Azevedo, Nova Friburgo");
	* @param string endereco
	* @return array String
	*	array com 2 campos: Latitude(lat) e Longitude(lng)
	*/
	public static function obterCoordenadas($endereco){
		$endereco = str_replace(" ", "+", $endereco);
		$geocode = file_get_contents('http://maps.google.com/maps/api/geocode/json?address='.$endereco.'&sensor=false');
		$output= json_decode($geocode);
		$lat = $output->results[0]->geometry->location->lat;
		$lng = $output->results[0]->geometry->location->lng;
		$coordenadas = array(
				"lat" => $lat,
				"lng" => $lng
			);
		return $coordenadas;
	}
	
	/**
	* Função que gera uma senha composta de letras minusculas, maiusculas, numeros e codigos de acordo com os parametros enviados
	*
	* @author Douglas
	* @author Igor
	*
	* @name gerarCodigo
	* @example gerarCodigo(4,true,true,false,false);
	* @param int tamanho
	*	tamanho desejado da senha 
	* @param bool maiuscula
	*	define se a senha terá letras maiúsculas
	* @param bool minuscula
	*	define se a senha terá letras minúsculas
	* @param bool numeros
	*	define se a senha terá números
	* @param bool codigos
	*	define se a senha terá códigos
	* @return String senha
	*/
	public static function gerarCodigo($tamanho = 8, $maiuscula = true, $minuscula = true, $numeros = true, $codigos = false){
		$maius = "ABCDEFGHIJKLMNOPQRSTUWXYZ";
		$minus = "abcdefghijklmnopqrstuwxyz";
		$numer = "0123456789";
		$codig = '!@#$%&*()-+.,;?{[}]^><:|';
		
		$base = '';
		$base .= ($maiuscula) ? $maius : '';
		$base .= ($minuscula) ? $minus : '';
		$base .= ($numeros) ? $numer : '';
		$base .= ($codigos) ? $codig : '';
		
		srand((float) microtime() * 10000000);
		$senha = '';
		for($i = 0; $i < $tamanho; $i++) {
			$senha .= substr($base, rand(0, strlen($base)-1), 1);
		}
		return $senha;
	}
	
	/**
	* Transforma a data para uma data compatível com o banco de dados
	* 
	* @author Douglas
	*
	* @name dataParaBanco
	* @example dataParaBanco("24/03/1995");
	* @param string data
	* @return string data
	*/
	public static function dataParaBanco($data){
		return date("Y-m-d", strtotime(str_replace("/","-",$data)));
	}
	
	
	/**
	* Funções para validações
	*/
	//-------------------------- VALIDAÇÕES ---------------------------//
	
	/**
	* Valida data
	*
	* @author Douglas
	*
	* @name validarData
	* @example validarData("26/05/1991");
	* @param string dat
	*	 no formato dd/mm/aaaa ou dd-mm-aaaa
	* @return bool verificacao
	*/
	public static function validarData($data){
		$data = html_entity_decode($data);
		$data = str_replace('-','/',$data);
		$data = explode("/","$data"); // fatia a string $data em pedados, usando / como referência
		$d = $data[0];
		$m = $data[1];
		$y = $data[2];
		$res = checkdate($m,$d,$y);
		if ($res == 1)
			return true;
		return false;
	}
	
	/**
	* Valida email
	*
	* @author Douglas
	*
	* @name validarEmail
	* @example validarEmail("fulano@hotmail.com");
	* @param string email
	* @return bool verificacao
	*/
	public static function validarEmail($email) {
		$email = html_entity_decode($email);
		$conta = "^[a-zA-Z0-9\._-]+@";
		$domino = "[a-zA-Z0-9\._-]+.";
		$extensao = "([a-zA-Z]{2,4})$";
		$pattern = $conta.$domino.$extensao;
		if(ereg($pattern, $email))
			return true;
		return false;
	}
	
	/**
	* Valida CPF em qualquer formato
	*
	* @author Douglas
	*
	* @name validarCPF
	* @example validarCPF("157.657.300-15");
	* @param string cpf
	*	 exemplo: no formato xxx.xxx.xxx-xx ou xxxxxxxxxxx
	* @return bool verificacao 
	*/
	public static function validarCPF($cpf){
		$cpf = html_entity_decode($cpf);
		$j = 0;
		for($i = 0; $i < (strlen($cpf)); $i++){
			if(is_numeric($cpf[$i])){
				$num[$j] = $cpf[$i];
				$j++;
			}
		}
		if(count($num) != 11){
			$isCpfValid = false;
		}else{
			for($i = 0; $i < 10; $i++){
				if($num[0] == $i && $num[1] == $i && $num[2] == $i && $num[3] == $i && $num[4] == $i && $num[5] == $i && $num[6] == $i && $num[7] == $i && $num[8] == $i){
					$isCpfValid = false;
					break;
				}
			}
		}
		if(!isset($isCpfValid)){
			$j =10;
			for($i = 0; $i < 9; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
			if($resto < 2){
				$dg=0;
			}else{
				$dg = 11-$resto;
			}
			if($dg != $num[9]){
				$isCpfValid = false;
			}
		}
		if(!isset($isCpfValid)){
			$j=11;
			for($i = 0; $i < 10; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma%11;
			if($resto < 2){
				$dg = 0;
			}else{
				$dg = 11-$resto;
			}
			if($dg != $num[10]){
				$isCpfValid = false;
			}else{
				$isCpfValid = true;
			}
		}
		return $isCpfValid;
	}
	
	/**
	* Valida CNPJ em qualquer formato
	*
	* @author Douglas
	*
	* @name validarCNPJ
	* @example validarCNPJ("45.988.014/8246-22");
	* @param string cnpj
	*	 exemplo: no formato xx.xxx.xxx/xxxx-xx ou xxxxxxxxxxxxxx
	* @return bool verificacao 
	*/
	public static function validarCNPJ($cnpj){
		$cnpj = html_entity_decode($cnpj);
		$j = 0;
		for($i = 0; $i < (strlen($cnpj)); $i++){
			if(is_numeric($cnpj[$i])){
				$num[$j] = $cnpj[$i];
				$j++;
			}
		}
		if(count($num) != 14){
			$isCnpjValid = false;
		}
		if($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0){
			$isCnpjValid = false;
		}else{
			$j = 5;
			for($i = 0; $i < 4; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j = 9;
			for($i = 4; $i < 12; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);	
			$resto = $soma%11;			
			if($resto < 2){
				$dg = 0;
			}else{
				$dg = 11-$resto;
			}
			if($dg != $num[12]){
				$isCnpjValid = false;
			} 
		}
		if(!isset($isCnpjValid)){
			$j = 6;
			for($i = 0; $i < 5; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j = 9;
			for($i = 5; $i < 13; $i++){
				$multiplica[$i] = $num[$i]*$j;
				$j--;
			}
			$soma = array_sum($multiplica);	
			$resto = $soma%11;			
			if($resto < 2){
				$dg = 0;
			}else{
				$dg = 11-$resto;
			}
			if($dg != $num[13]){
				$isCnpjValid = false;
			}else{
				$isCnpjValid  =true;
			}
		}
		return $isCnpjValid;
	}
	
	/**
	* Valida um campo numério
	* Verifica se está preenchido o campo, se o valor é numérico e se é maior que 0
	*
	* @author Douglas
	*
	* @name validarCampoNumerico
	* @example validarCampoNumerico(47.76);
	* @param unknown_type valor
	* @return bool verificacao 
	*/
	public static function validarCampoNumerico($valor){
		if(strlen(html_entity_decode($valor)) < 0)
			return false;
		elseif(!is_numeric($valor))
			return false;
		elseif($valor < 0)
			return false;
		return true;
	}
	
	
	/**
	* Função que valida se um texto está no formato de um número hexadecimal
	*
	* @author Douglas
	*
	* @name validarHexadecimal
	* @example validarHexadecimal("#4466dd");
	* @param unknown_type hexadecimal
	* @return bool verificacao 
	*/
	public static function validarHexadecimal($hexadecimal){
		$hexadecimal = html_entity_decode($hexadecimal);
		if(preg_match('/^#(?:(?:[a-f\d]{3}){1,2})$/i', $hexadecimal)){
			return true;
		}
		return false;
	}
	
	//----------------------- END VALIDAÇÕES --------------------------//
}
?>