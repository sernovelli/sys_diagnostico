<?php
$v_params = $this -> getParams();
$vetor = $v_params['vetor'];

// echo "<pre>";
// print_r($vetor);
// echo "</pre>";
// break;
?>
<link rel="stylesheet" href="views/includes/css/estatisticas.css" />
<title>Estat&iacute;sticas :: Din&acirc;mica RH Merchandising</title>
</head>
<body>
	<header>
		<img class="logo" src="views/includes/images/logo160x71.jpg" alt="Din&acirc;mica RH Merchandising" />
        <h1>Estat&iacute;sticas - Fotos Postadas</h1>
	    <?php $i = 0;
			 foreach($vetor AS $item) { 
			 	if (get_class($item) == "EstatisticasModel") {
		?>
					<p><?php echo "<b>Per&iacute;odo:</b> " . implode("/", array_reverse(explode("-", $item -> getDataInicio()))) . " - " . implode("/", array_reverse(explode("-", $item -> getDataFinal()))); ?></p>
					<p><?php echo "<b>Cliente:</b> " . $item -> getFnomeCliente(); ?></p>
					<p><?php echo "<b>Filial:</b> " . $item -> getFnomeFilial(); ?></p>
			<?php 	$i++;
				 	if ($i >= 1) { break; }
				}
			 } 
		?>
		<div class="botoes">
			|| <a href="#" onclick="window.print();" title="Imprimir">Imprimir</a> || <!-- Exportar para: <a class="pdf" href="#" title="PDF">PDF</a> / <a class="xls" href="#" title="Excel">Excel</a> -->
		</div>
	</header>
	<article>
        <table style="width:100%;" id="table">
			<thead>
				<tr>
					<td style="width: 150px;">No. Contrato</td>
					<td>Cliente</td>
					<td style="width: 150px; text-align: center;">No. de Fotos</td>
				</tr>
			</thead>
			<tbody>
				<?php $totalFotos = 0;
					 foreach($vetor AS $item) {
					 	if (get_class($item) == "FilialModel") {
				?>
						<tr>
							<td colspan="3"><?php echo $item -> getFilial(); ?></td>
						</tr>
				<?php 	} else {
				?>
						<tr>
							<td><?php echo $item -> getContrato(); ?></td>
							<td><?php echo $item -> getNomeCliente(); ?></td>
							<td style="text-align: center;"><?php echo $item -> getQtdeFotos(); ?></td>
						</tr>
				<?php 	$totalFotos += $item -> getQtdeFotos();
						}
					} 
				?>
				<tr><td colspan="3"> </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Total Geral</td>
					<td style="text-align: center;"><?php echo $totalFotos; ?></td>
				</tr>
			</tfoot>
		</table>
	</article>
</body>
</html>