﻿<?php
$v_params = $this -> getParams();
$vetor = $v_params['vetor'];
?>
<link rel="stylesheet" href="views/includes/css/estatisticas.css" />
<title>Estat&iacute;sticas :: Din&acirc;mica RH Merchandising</title>
</head>
<body>
	<header>
		<img class="logo" src="views/includes/images/logo160x71.jpg" alt="Din&acirc;mica RH Merchandising" />
        <h1>Estat&iacute;sticas - Fotos Postadas</h1>
	    <?php
			 foreach($vetor AS $item) { ?>
				<p><?php echo "<b>Per&iacute;odo:</b> " . implode("/", array_reverse(explode("-", $item -> getDataInicio()))) . " - " . implode("/", array_reverse(explode("-", $item -> getDataFinal()))); ?></p>
				<p><?php echo "<b>Cliente:</b> " . $item -> getNomeCliente(); ?></p>
				<p><?php echo "<b>Filial:</b> " . $item -> getNomeFilial(); ?></p>
		<?php } ?>
		<div class="botoes">
			<a href="#" title="Imprimir">Imprimir</a> || Exportar para: <a class="pdf" href="#" title="PDF">PDF</a> / <a class="xls" href="#" title="Excel">Excel</a>
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
					 foreach($vetor AS $item) { ?>
						<tr>
							<td><?php echo $item -> getContrato(); ?></td>
							<td><?php echo $item -> getNomeCliente(); ?></td>
							<td style="text-align: center;"><?php echo $item -> getQtdeFotos(); ?></td>
						</tr>
				<?php $totalFotos += $item -> getQtdeFotos();
					} 
				?>
				<tr><td colspan="3"> </td></tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="2">Total</td>
					<td style="text-align: center;"><?php echo $totalFotos; ?></td>
				</tr>
			</tfoot>
		</table>
	</article>
</body>
</html>