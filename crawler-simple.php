<?php

	//Variaveis
	$url 	= 	isset($_GET['url'])		? $_GET['url'] 		: NULL;
	$depth 	= 	isset($_GET['depth'])	? $_GET['depth'] 	: NULL;
	
	$max_entries = 100;
	
	// Desabilitando o limite de tempo
	set_time_limit(0);
	
	//função que pega  os links das páginas
	function get_links($url, $max_entries, $depth=null)
	{
		$domains_tmp_stack=array();
		$domains_final_stack=array();
		if (!@file_get_contents($url))
			 return NULL;
		
		$content_url = file_get_contents($url);
		$tags_a = strip_tags($content_url, "<a>");
		preg_match_all("/<a(?:[^>]*)href=\"([^\"]*)\"(?:[^>]*)>(?:[^<]*)<\/a>/is", $tags_a, $result);
		foreach($result[1] as $res)
		{
			//Faz tratamento 
			$res = str_replace('http://', "", $res);
			$res = str_replace('https://', "", $res);
			$tmp = explode("/", $res);
			$reg = '/^(.+).(.+)$/';
			//valida o dado
			if($tmp[0] != '' && preg_match($reg,$tmp[0]) && strpos($tmp[0],"html") === false && strpos($tmp[0],"htm") === false)
				$domains_tmp_stack[] = $tmp[0]; //Adiciona dado ao array se for validado
		}

		//Retirada links duplicados
		$domains_tmp_stack = array_unique($domains_tmp_stack);
		
		//Verifica tamanho máximo
		$max_value = 0;
		if($depth != null)
			$max_value = $depth;
		else
			$max_value = $max_entries;
		
		$c=0;
		foreach($domains_tmp_stack as $dts)
		{
			if($c<$max_value)
				$domains_final_stack[] = $dts;
			else
				break;
			$c++;
		}
		
		//Retorna o array depois de tratado, validado e calculado
		return $domains_final_stack;
	}
	
	//Começo do script que chama a função principal
	if($url != null && $depth!=null)
	{
		$test = array();
		$domain_stack = get_links($url, $max_entries, $depth);
		foreach($domain_stack as $ds)
		{
			$url_new = 'http://'.$ds;
			$test[$ds] = get_links($url_new, $max_entries);
		}
		$test = json_encode($test, JSON_PRETTY_PRINT);
		echo $test;
	}
?>