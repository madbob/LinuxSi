<?php

/*
	Codice della mappa dei negozi Linux-friendly italiani
	Copyright (C) 2010-2017  Italian Linux Society - http://www.ils.org/

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU Affero General Public License as
	published by the Free Software Foundation, either version 3 of the
	License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU Affero General Public License for more details.

	You should have received a copy of the GNU Affero General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function sort_by_province ($a, $b) {
	$campi_a       = explode("|", $a);
	$campi_b       = explode("|", $b);

	$cmp = strcmp ($campi_a [2], $campi_b [2]);
	if ($cmp == 0)
		$cmp = strcmp ($campi_a [0], $campi_b [0]);

	return $cmp;
}

if ($_SERVER["SCRIPT_NAME"] === '/regioni/index.php') { # qui se sono stato invocato alla vecchia maniera
	require_once (__DIR__ . '/../funzioni.php');
	$db_regione = array();

	foreach(glob(__DIR__ . '/../db/*.txt') as $db_file) {
		$db_regione = array_merge($db_regione, file($db_file));
	}

	$db_file = null;
	$regione = 'Italia';
	$title = 'LinuxSi: i negozi italiani';
}
else { # qui sono stato invocato da /nome-regione/
	require_once (__DIR__ . '/../funzioni.php');
	$regione = explode('/', dirname($_SERVER["SCRIPT_NAME"]))[2]; # estraggo la regione dal percorso

	if(array_key_exists($regione, $elenco_regioni)) { # lasciamo il controllo, ma in ogni caso dovremmo ottenere un 404
		$db_file = __DIR__ . '/../db/' . $regione . '.txt';
		$db_regione = file($db_file);
		$title = 'LinuxSi: ' . $elenco_regioni[$regione];
	} else {
		header("location: /");
	}
}

lugheader ($title);
usort ($db_regione, 'sort_by_province');

?>

<div id="center">
	<h1 class="titoloregione"><?php echo substr($title, 8) ?></h1>
	<p class="pull-right text-right">
		<a href="/">&raquo; torna all'indice&nbsp;</a>
	</p>

	<?php if(count($db_regione) == 0): ?>

	<div style="text-align: center">
		<h2>Non sembrano esserci negozi Linux-friendly in questa regione!</h2>
		<p>Ne conosci qualcuno? <a href="/partecipa">Non esitare a contattarci</a>!</p>
	</div>

	<?php else: ?>

	<table id="lugListTable" class="table">
		<thead>
			<tr>
				<th>Provincia</th>
				<th>Zona</th>
				<th>Denominazione</th>
				<th>Indirizzo</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4"></td>
			</tr>
		</tfoot>
		<tbody>
			<?php while (list ($nriga, $linea) = each ($db_regione)):
				if (empty($linea))
					continue;

				$campi         = explode("|", trim($linea)); # estrazione dei campi
				$comune        = $campi[0];
				$denominazione = $campi[1];
				$provincia     = $campi[2];
				$sito          = $campi[3];
				$indirizzo     = $campi[4];
				# stampa dei campi ?>
				<tr class="row_<?php echo ($nriga % 2); ?>">
					<td class="province"><?php echo $provincia ?></td>
					<td><?php echo $comune?></a></td>
					<?php if($sito === ''): ?>
					<td><?php echo $denominazione ?></td>
					<?php else: ?>
					<td><a href="<?php echo $sito ?>"><?php echo $denominazione ?></a></td>
					<?php endif ?>
					<?php if($indirizzo === ''): ?>
					<td></td>
					<?php else: ?>
					<td><?php echo $indirizzo ?>, <?php echo $comune ?></td>
					<?php endif ?>
				</tr>
			<?php endwhile;?>
		</tbody>
	</table>

	<?php endif ?>

	<p class="pull-right text-right">
		<?php if ($db_file != null) { ?>
		<a href="<?php echo $db_file ?>">&raquo; Elenco in formato CSV&nbsp;</a><br />
		<?php } else { ?>
		<br />
		<?php } ?>
		<?php ultimo_aggiornamento(); ?>
	</p>
</div>

<?php lugfooter (); ?>

