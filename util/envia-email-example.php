<?php
	require_once($_SERVER['DOCUMENT_ROOT'].'/util/Email.php');
	
	$nomeremetente     = $_POST['nome'];
	$emailremetente    = $_POST['email'];
	$telefone		   = $_POST['telefone'];
	$assunto           = $_POST['assunto'];
	$mensagem          = $_POST['mensagem'];
	
	// pega a data do servidor
	$date = date("d/m/Y | H:i:s");	

	/* Montando a mensagem a ser enviada no corpo do e-mail. */
	$mensagemHTML = "	
	Mensagem enviada por: ".utf8_decode($nomeremetente)."<br />
	Telefone: $telefone <br />
	Enviado às $date <br />
	<br />	
	Mensagem:<br />
	------------------------------------<br />
	".utf8_decode($mensagem)."
	<br />
	------------------------------------
	";
	
	try{
		$email = new Email('contato', 'seudominio.com.br', Email::EMAIL_IN);
		$email->setNomeRemetente($nomeremetente);
		$email->setEmailRemetente($emailremetente);
		//$email->setEmailSender($emailremetente);
		$email->setAssunto($assunto);
		$email->setMensagem($mensagemHTML);
		$email->enviar();
?>
		<script type="text/javascript">
			alert('Contato enviado com sucesso. Aguarde nosso feedback.'); 
			location.href='/contato';
		</script>
<?php 
	}catch(Exception $e){
?>
		<script type="text/javascript">
			alert('<?php echo $e->getMessage(); ?>'); 
			location.href='/contato';
		</script>
<?php
	}
?>