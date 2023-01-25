<?php 
//-----Onde está segunda ? -----------------------------------

function segunda($data){
	
	$data = date("d-m-Y",strtotime($data));
	$segunda = $data;	
	// Aqui podemos usar a data atual ou qualquer outra data no formato Ano-mês-dia (2014-02-28)
	#$data = date('Y-m-d');

	// Varivel que recebe o dia da semana (0 = Domingo, 1 = Segunda ...)
	$diasemana_numero = date('w', strtotime($data));
	if($diasemana_numero == 0){
		$segunda = date('d-m-Y', strtotime('+1 days', strtotime($segunda)));
	}elseif($diasemana_numero == 1){
			//esta é segunda
	}elseif($diasemana_numero == 2){
		$segunda = date('d-m-Y', strtotime('-1 days', strtotime($segunda)));
	}elseif($diasemana_numero == 3){
		$segunda = date('d-m-Y', strtotime('-2 days', strtotime($segunda)));
	}elseif($diasemana_numero == 4){
		$segunda = date('d-m-Y', strtotime('-3 days', strtotime($segunda)));
	}elseif($diasemana_numero == 5){
		$segunda = date('d-m-Y', strtotime('-4 days', strtotime($segunda)));
	}elseif($diasemana_numero == 6){
		$segunda = date('d-m-Y', strtotime('+2 days', strtotime($segunda)));
	}	
	// Exibe o dia da semana com o Array
	//echo $diasemana[$diasemana_numero];
	return $segunda;
	
}

?>