<!DOCTYPE html>
<html lang="en">
<head>
	<?php include('config.php'); ?>
	<meta charset="UTF-8">
	<title>Conferência de dominios</title>

	<link href="<?php echo BASE ?>favicon.fw.png" rel="icon" />
	<link href="<?php echo BASE ?>css/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link href="<?php echo BASE ?>css/style.css" rel="stylesheet">
	<base href="<?php echo BASE ?>" />
	
</head>
<body>
	<i class='fa fa-check'></i> = <b>Hospeda algo com a Web Thomaz</b><br>
	<i class='fa fa-exclamation-circle'></i> = <b>Domínio existente, mas NÃO hospeda com a Web Thomaz</b><br>
	<i class='fa fa-times-circle'></i> = <b>Domínio inexistente</b><br>
	<i class='fa fa-globe'></i> = <b>Possui Website</b><br>
	<i class='fa fa-envelope'></i> = <b>Possui e-mail</b><br>
	<i class='fa fa-plus-circle'></i> = <b>Possui Outro Serviço Web</b><br>

	<br><br>

	<form action="." name="lista" method="post">
		<h2>Digite o IP do seu servidor que gostaria de fazer a conferência.</h2>
		<input type="text" name="ipParaConferencia" placeholder="Digite o IP (ex: 122.52.123.123)" required="required" value='<?php echo @$_POST['ipParaConferencia'] ? $_POST['ipParaConferencia'] : "186.226.57.113,186.226.56.195,186.226.57.111,186.226.56.32" ?>' style="width: 100%;">
		<h2>Digite a lista de domínios abaixo separados por vírgula, espaço ou quebra de linha.</h2>
		<textarea name="dominios" placeholder="Lista de servidores" required="required"><?php echo @$_POST['dominios'] ? $_POST['dominios'] : "" ?></textarea>
		<button type="submit">Enviar Lista</button>
	</form>

	<br>
	<br>
	<br>

	<?php if (@$_POST['dominios']) { ?>
		<?php
			$adaptando = str_replace("\n", ";", $_POST['dominios']);
			$adaptando = str_replace(" ", ";", $adaptando);
			$adaptando = str_replace(",", ";", $adaptando);
			
			$dominios = explode(";" , $adaptando);

			foreach ($dominios as $key => $dominio) {
				$testes = ["www", "mail", "ftp"];

				echo "<div class='dominio'>";
				echo "<b>#" . ($key + 1) . "</b> - ";
				$hospedaAlgo = 0;
				$oQueHospeda = "";
				$output = "";
				$dominioOkay = "";
				$mensagem = "";

				//faz a primeira verificação pra ver se o domínio está pelo menos existe
				exec("ping -n 1 -w 1 " . $dominio , $dominioOkay, $result);

				if (!$dominioOkay[0]) {
					foreach ($testes as $key => $teste) {
						$disparo = $teste . "." . $dominio;
						exec("ping -n 1 -w 1 " . $disparo , $output, $result);
						if (!$output[0]) {
							$ip = explode("[", $output[1]);
							$ip = explode("]", $ip[1]);
							$ip = $ip[0];
							
							$ipParaConferencia = [];
							$ipParaConferencia = explode(",", $_POST['ipParaConferencia']);

							if (in_array($ip, $ipParaConferencia)) {
								$hospedaAlgo++;
								if ($teste == "www") {
									$oQueHospeda .= "<i class='fa fa-globe'></i> ";
								} else if ($teste == "mail") {
									$oQueHospeda .= "<i class='fa fa-envelope'></i> ";
								} else if ($teste == "ftp") {
									$oQueHospeda .= "<i class='fa fa-folder-open-o'></i> ";
								} else {
									$oQueHospeda .= "<i class='fa fa-plus-circle'></i> ";
								}
							}
						}
						$output = "";
					}

					if ($hospedaAlgo) {
						$mensagem .= "<i class='fa fa-check'></i> <br>";
						$mensagem .= "Hospeda: " . $oQueHospeda;
					} else {
						$mensagem .= "<i class='fa fa-exclamation-circle'></i> <br>";
					}
				} else { //domínio não existe
						$mensagem .= "<i class='fa fa-times-circle'></i> <br>";
				}
				echo "<b><a href='$dominio' target='_blank'> {$dominio} </a> ({$ip})</b> ";
				echo $mensagem;
				echo "</div>";
			}
		?>	
	<?php } else {  ?>
		<h1>Nenhum domínio buscado.</h1>
	<?php } ?>
	
</body>
</html>

