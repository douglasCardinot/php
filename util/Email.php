<?php

/**
 * Email sender.
 *
 * @author
 *	Douglas Cardinot <douglas_cardinot@ig.com.br>
 * @version	1.1
 *		Bug em servidores linux corrigido
 */

class Email{
	
	private $emailSender = '';			//Local de onde sairá o email
	private $lineBreak = '';			//Quebra de linha (diferente em servidores windows e outros)
	private $nomeRemetente = '';		//Nome do remetente do e-mail
	private $emailRemetente = '';		//E-mail do remetente (para Reply-To no e-mail)
	private $emailDestinatario = '';	//E-mail do destinatário
	private $comCopia = '';				//E-mail para cópia
	private $comCopiaOculta = '';		//E-mail para cópia oculta
	private $assunto = '';				//Assunto do e-mail
	private $mensagem = '';				//Mensagem
	private $type = null;				//Tipo do e-mail (EMAIL_IN ou EMAIL_OUT)
	private $charset = 'utf-8';			//Charset do E-mal (defaut to 'utf-8')
	private $formatado = true;			//Boolean - Formatado com HTML ou não o e-mail (default to true)
	
	/**
	 * @const EMAIL_IN 1
	 *	Constante referente a criação de um e-mail de entrada para o servidor
	 *
	 * @const EMAIL_OUT 2
	 *	Constante referente a criação de um e-mail de saída do servidor
	 */
	const EMAIL_IN = 1;
	const EMAIL_OUT = 2;
	
	/**
	 * Construtor - Cria uma classe E-mail definindo o tipo, a quebra de linha do servidor
	 *   e um dos nós do e-mail (remetente ou destinatário)
	 *
	 * @example new Email('contato', 'dzaine.net', Email::EMAIL_OUT);
	 * @param string usuario
	 *	Nome do usuário do e-mail
	 * @param string dominio
	 *	Domínio do site de onde sai o email (sem http e sem www)
	 * @param int type
	 *	Tipo de e-mail - Enumerado inserido na própria classe (default to self::EMAIL_IN)
	 * @return void
	 */
	public function __construct($usuario, $dominio, $type = self::EMAIL_IN){ //Throws
		
		switch($type){
			case self::EMAIL_IN :
				$this->emailSender = $usuario.'@'.$dominio;
				$this->emailDestinatario = $usuario.'@'.$dominio;
				break;
				
			case self::EMAIL_OUT :
				$this->emailSender = $usuario.'@'.$dominio;
				$this->emailRemetente = $usuario.'@'.$dominio;
				break;
				
			default:
				throw new RuntimeException('Tipo inválido');
		}
		$this->type = $type;
		
		if(PATH_SEPARATOR == ";")
			$this->lineBreak = "\r\n"; //If Windows
		else $this->lineBreak = "\n"; //If not Windows
		
	}
	
	/**
	 * Envia o email atual no objeto
	 *
	 * @name enviar
	 * @example $email->enviar();
	 * @return bool
	 * @throws RuntimeException IF e-mail não for enviado
	 */
	public function enviar(){
		if($this->emailSender == '')
			throw new RuntimeException('E-mail sender não informado');
		if($this->emailDestinatario == '')
			throw new RuntimeException('E-mail do destinatário não informado');
		
		$headers = "MIME-Version: 1.1".$this->lineBreak;
		if($this->formatado)
			$headers .= "Content-type: text/html; charset=".$this->charset.$this->lineBreak;
		$headers .= "From: ".$this->emailRemetente.$this->lineBreak;
		$headers .= "Cc: ".$this->comCopia.$this->lineBreak;
		$headers .= "Bcc: ".$this->comCopiaOculta.$this->lineBreak;
		$headers .= "Reply-To: ".$this->emailRemetente.$this->lineBreak;
		
		if(!mail($this->emailDestinatario, $this->assunto, $this->mensagem, $headers ,"-r".$this->emailSender)){ // Se for Postfix
			$headers .= "Return-Path: ".$this->emailSender.$this->lineBreak; // Se "não for Postfix"
			if(!mail($this->emailDestinatario, $this->assunto, $this->mensagem, $headers)){
				throw new RuntimeException('Não foi possível enviar o email');
			}
		}
		return true;
	}
	
	/**
	 * Retira a formatação html do corpo do e-mail
	 *
	 * @name removerFormatacao
	 * @example $email->removerFormatacao();
	 * @return void
	 */
	public function removerFormatacao(){
		$this->formatado = false;
	}
	
	/**
	 * Getters and Setters
	 */
	public function getEmailSender(){
		return $this->emailSender;
	}
	
	public function getNomeRemetente(){
		return $this->nomeRemetente;
	}
	
	public function setNomeRemetente($nomeRemetente){
		$this->nomeRemetente = $nomeRemetente;
	}
	
	public function getEmailRemetente(){
		return $this->emailRemetente;
	}
	
	public function setEmailRemetente($emailRemetente){
		$this->emailRemetente = $emailRemetente;
	}
	
	public function getEmailDestinatario(){
		return $this->emailDestinatario;
	}
	
	/*
	 * @throws RuntimeException IF tipo de e-mail for de entrada no servidor
	 */
	public function setEmailDestinatario($emailDestinatario){
		if($this->type == self::EMAIL_IN)
			throw new RuntimeException('Este é um email de entrada. Não é possível mudar o email do destinatário');
		$this->emailDestinatario = $emailDestinatario;
	}
	
	public function getComCopia(){
		return $this->comCopia;
	}
	
	public function setComCopia($comCopia){
		$this->comCopia = $comCopia;
	}
	
	public function getComCopiaOculta(){
		return $this->comCopiaOculta;
	}
	
	public function setComCopiaOculta($comCopiaOculta){
		$this->comCopiaOculta = $comCopiaOculta;
	}
	
	public function getAssunto(){
		return $this->assunto;
	}
	
	public function setAssunto($assunto){
		$this->assunto = $assunto;
	}
	
	public function getMensagem(){
		return $this->mensagem;
	}
	
	public function setMensagem($mensagem){
		$this->mensagem = $mensagem;
	}
	
	public function getType(){
		return $this->type;
	}
	
	public function getCharset(){
		return $this->charset;
	}
	
	public function setCharset($charset){
		$this->charset = $charset;
	}
	/* End Getters and Setters */
}
?>