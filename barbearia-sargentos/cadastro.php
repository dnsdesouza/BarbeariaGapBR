<html> 
<head>

</head>
<body>
   
	<?php 
	
	session_start();
	$_SESSION['novo_militar'] = 0; // 0 indica que um novo militar está marcando pela primeira vez.
	
	include_once ("php/conecta.php");
	require_once("funcoes.php");
	$erro = "";
	//----------POSTO--------------
	//Cria a instrução SQL que vai selecionar os dados
	$query_posto = sprintf("SELECT * FROM posto");
	//executa a query
	$dados_posto = mysqli_query($conexao, $query_posto) or die (mysqli_error());
	//transforma os dados em um array
	$linha_posto = mysqli_fetch_assoc($dados_posto);
	$total_posto = mysqli_num_rows($dados_posto);

	
	function verifica_cadastro($saram,$conexao){
		
		$sql = "SELECT * FROM view_militar WHERE saram = '$saram';";
		$result = $conexao->query($sql);
		$row = $result->fetch_assoc();
		if($result->num_rows > 0){
			
			$dados_militar = array( $row['nome_guerra'], $row['descricao'], $row['om'], $row['secao'], $row['ramal'], $row['posto']);
			$_SESSION['novo_militar'] = $row['id_militar']; //Indica que o militar ja realizou outras marcações
			return $dados_militar;
		}
	
		
	}
	
	function verifica_15_dias($saram,$conexao){
	
		$data = date("Y-m-d");
		$d_15_dias = date("Y-m-d",strtotime('-5 days', strtotime($data)));

		$sql = "SELECT * FROM Militar_dia WHERE saram  = '$saram' AND  dia >= '$d_15_dias'";
		$result = $conexao->query($sql);
		$row = $result->fetch_assoc();
		if($result->num_rows > 0){
			#$result->close();
			#$conexao->close();
			return true; //retorna true se o militar ja possui marcacao no período

		}
		#$result->close();
		#$conexao->close();
		return false; // Militar não possui marcacao no período
	}

	function valida_saram($saram){
		//VALIDA SARAM COM DIGITO VERIFICADOR
			$saram_str = str_split($saram);
			$arrayNordem = $saram_str[0].$saram_str[1].$saram_str[2].$saram_str[3].$saram_str[4].$saram_str[5];
			$arrayNordem = str_split($arrayNordem);
			$soma = 0;
			if($saram == "0000000"){
				global $erro;
				$erro .= "<br> Você não informou um SARAM VÁLIDO!";
				return false;
			}
			if (strlen($saram) != 7) {
				global $erro;
				$erro .= "<br> Você não informou um SARAM VÁLIDO!";
				return false;
			}
			for($i = 1; $i <= count($arrayNordem); $i++){
				$soma = $soma + ($arrayNordem[count($arrayNordem) - $i]*($i+1));
			}
			$ultimo_digito = $soma % 11 > 1? 11 - $soma % 11: 0;
			
			if($saram_str[6] != $ultimo_digito ){ // VERIFICA SE O ÚLTIMO DÍGITO DO SARAM BATE COM O ALGORÍTIMO
		
				global $erro;
				$erro = "Você não informou um SARAM VÁLIDO!";
				return false;
				
			}else{
				
				return true;
			}
	}
	function valida_grad ($id_grad){
		if ($id_grad >= 16 ) // TESTE alterado de 16 para 14 em 29/08 
		return true;
		else
		return false;
		
	}

	$hora = $_GET['hora'];
	$dia_print = date('d/m/Y', strtotime($_GET['dia']));
	$dia = date('d-m-Y', strtotime($_GET['dia']));

	echo "<b> <font size=13>$dia_print as $hora </b></font><br><br><br><br>";
	
	if(isset($_POST['saram'])){
		
		$saram = $_POST['saram'];
		
		if(valida_saram($_POST['saram'])){
			//saram verdadeiro
		}else{
			//saram falso
			unset($_POST['verificar']);
			echo "<font color=\"red\"> Saram inválido </font><br>";
		}if(verifica_15_dias($_POST['saram'],$conexao )){
			//Militar ja marcaou corte nos ultimos 15 dias
			unset($_POST['verificar']);
			echo "<font color=\"red\"> Não foi possível agendar. Só é possível marcar uma vez a cada 5 dias. </font><br>";

		}
		//echo $dados_militar[5];
		
		$dados_militar = verifica_cadastro($_POST['saram'], $conexao);
		
		
	}
	$conexao->close();

	

	?> 
	
	<form method="post" action="<?php echo isset($_POST['verificar'])?"inserir.php":"" ?>">
		<table>
		
		<?php if(isset($_POST['verificar'])){ ?>
		<tr>
			<td>
				Dia
			</td>
			<td>
				<input type="text" name="dia" value="<?php echo $dia ?>" readonly>
			</td>
			<td>
				Hora
			</td>
			<td>
				<input type="text" name="hora" value="<?php echo $hora ?>" readonly>
			</td>
		</tr>
			<td>
				Nome
			</td>
			<td>
				<input type="text" maxlength="15" placeholder="Nome de Guerra" name="nome" <?php echo isset($dados_militar)?"value = \"$dados_militar[0]\" ":"value = \"\"" ?> required>
			</td>
			<td>
				Posto/Grad
				
			</td>
			<td>

			<?php ; 
			
			?> 
				<select id="posto" name="posto" required="required" <?php echo isset($dados_militar)?"":"" ?> >
					<option value="" >SELECIONE SEU POSTO</option>
					<?php 
							if($total_posto > 0) {
							do { 
								$complemento = "";
								if($dados_militar[5] == $linha_posto['id_posto'])
									$complemento = "selected";
								
								
								
								echo "<option  value=\"".$linha_posto['id_posto']."\" ".$complemento.">" .$linha_posto['descricao']."</option>";
								
								}while($linha_posto = mysqli_fetch_assoc($dados_posto));

							} ?>	
				</select>
				<!--input type="text" name="posto-grad" <?php echo isset($dados_militar)?"value = \"$dados_militar[1]\" ":"value = \"\"" ?>required-->
			</td>
		</tr>
		<tr>
			<td>
				Om
			</td>
			<td>
				<input type="text" name="om" <?php echo isset($dados_militar)?" value = \"$dados_militar[2]\" ":"value = \"\"" ?>required>
			</td>
			<td>
				Ramal
			</td>
			<td>
				<input type="text" name="ramal" <?php echo isset($dados_militar)?"value = \"$dados_militar[4]\" ":"value = \"\"" ?>required>
			</td>
		</tr>
		<tr>
			<td>
				Seção
			</td>
			<td>
				<input type="text" name="secao" <?php echo isset($dados_militar)?" value = \"$dados_militar[3]\"":"value = \"\"" ?>required>
			</td>
			
		<!--/tr>
		<?php } ?>
		<tr -->
			<td>
				Saram
			</td>
			<td>
				<input type="text" name="saram" maxlength="7" <?php echo isset($_POST['verificar'])?"value = \"$saram\"":"value = \"\"" ?>required>
			</td>
			<td colspan=2 align="center">
				<?php if(!isset($_POST['verificar'])){ ?>
					<input type="submit" value="verificar" name="verificar">
				<?php } ?>
			</td>
		</tr>
		<tr>
			<td colspan=4 align="center">
			<?php if(isset($_POST['verificar'])){
				
				//$dados_militar[5] = 0;
		//if(isset($dados_militar[5])){
		/* if ($dados_militar[5] > 14)
		{
			echo "<font color=\"red\"> Todos os cabos e soldados estão temporariamente proibidos de realizar agendamentos. </font><br>";
			//var_dump($dados_militar[5]);
		
		}
		else {
				} */
				?>
					<input type="submit" value="cadastrar">
			<?php  }?>
			</td>
		</tr>
		</table>
	</form>

</body>



</html>
