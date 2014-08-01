<?php
/*
  Codice della mappa dei negozi Linux-friendly italiani
  Copyright (C) 2010-2014  Italian Linux Society - http://www.ils.org/

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

require_once ('../funzioni.php');
lugheader ('Produttori Linux-friendly',
		array ('produttori.css'),
		array ('produttori.js'));

?>

<div class="contentsBox">
	<p>
		Che fare se non si trova un negozio Linux-friendly vicino casa? Ci si può rivolgere al resto del mercato, ma
		badando a scegliere un modello di computer i cui componenti siano compatibili con Linux!
	</p>
	<p>
		Qui di seguito, i produttori per i quali è più o meno facile trovare prodotti pienamente supportati: clicca
		sulle icone per maggiori dettagli.
	</p>
</div>

<?php

function sort_vendors ($a, $b) {
	if ($a->average > $b->average)
		return -1;
	else
		return 1;
}

/*
	Questo file deve essere periodicamente aggiornato coi contenuti di
	http://h-node.org/download/notebooks/en
*/
$url = '../data/hardware.xml';

$vendors = array (
	'google' => array (
		'icon' => 'google.png',
		'website' => 'http://www.chromebook.com/',
	),
	'zareason-inc' => array (
		'icon' => 'zareason.png',
		'website' => 'http://zareason.com/',
	),
	'gateway' => array (
		'icon' => 'gateway.png',
		'website' => 'http://www.gateway.com/',
	),
	'lg' => array (
		'icon' => 'lg.png',
		'website' => 'http://www.lg.com/',
	),
	'garlach44' => array (
		'icon' => 'garlach.png',
		'website' => 'https://www.garlach44.eu/',
	),
	'lemote' => array (
		'icon' => 'lemote.png',
		'website' => 'http://www.lemote.com/en/',
	),
	'asus' => array (
		'icon' => 'asus.png',
		'website' => 'http://www.asus.com/it/',
	),
	'acer' => array (
		'icon' => 'acer.png',
		'website' => 'http://www.acer.it/',
	),
	'compaq' => array (
		'icon' => 'compaq.png',
		'website' => 'http://www.compaq.com/',
	),
	'ibm' => array (
		'icon' => 'ibm.png',
		'website' => 'http://www.ibm.com/it/',
	),
	'toshiba' => array (
		'icon' => 'toshiba.png',
		'website' => 'http://www.toshiba.it/',
	),
	'fujitsu' => array (
		'icon' => 'fujitsu.png',
		'website' => 'http://www.fujitsu.com/it/',
	),
	'samsung' => array (
		'icon' => 'samsung.png',
		'website' => 'http://www.samsung.com/it/',
	),
	'sony' => array (
		'icon' => 'sony.png',
		'website' => 'http://www.sony.it/',
	),
	'clevo' => array (
		'icon' => 'clevo.png',
		'website' => 'http://www.clevo.com.tw/',
	),
	'dell' => array (
		'icon' => 'dell.png',
		'website' => 'http://www.dell.it/',
	),
	'lenovo' => array (
		'icon' => 'lenovo.png',
		'website' => 'http://www.lenovo.com/it/',
	),
	'hewlett-packard' => array (
		'icon' => 'hp.png',
		'website' => 'http://www.hp.com/',
	),
	'packard-bell' => array (
		'icon' => 'packard.png',
		'website' => 'http://www.packardbell.it/',
	),
	'oracle' => array (
		'icon' => 'oracle.png',
		'website' => 'http://www.oracle.com/it/',
	),
	'lanix' => array (
		'icon' => 'lanix.png',
		'website' => 'http://www.lanix.com/',
	),
	'compal-electronics' => array (
		'icon' => 'compal.png',
		'website' => 'http://www.compal.com/',
	),
	'msi' => array (
		'icon' => 'msi.png',
		'website' => 'http://it.msi.com/',
	),
	'emachines' => array (
		'icon' => 'emachines.png',
		'website' => 'http://it.emachines.com/',
	),
	'apple' => array (
		'icon' => 'apple.png',
		'website' => 'https://www.apple.com/it/',
	),
);

/*
	Metodo di massima:
	- parso tutto l'XML per contare quanti e quali modelli ci sono per ogni produttore
	- calcolo una media per produttore considerando compatibilita' e numero di modelli
	- ordino i produttori per la media
	- traslo tutto in una scala che va dalla massima alla minima compatibilita'
	- la griglia e' divisa in 10 colonne indipendenti entro le quali ci stanno i produttori
	  il cui indice di bonta' (a questo punto espresso in percentuale) e' compreso in ogni
	  decina

	In questo modo allargo i contenuti sull'intera estensione della griglia, e in ogni
	colonna si possono allineare verticalmente gli elementi
*/

$data = array ();

$c = file_get_contents ($url);
$contents = simplexml_load_string ($c);

foreach ($contents->device as $device) {
	$vendor = (string) $device->vendor;
	$compatibility = (string) $device->compatibility;

	if (array_key_exists ($vendor, $data) == false) {
		$node = new stdClass ();
		$node->lname = strtolower ($vendor);
		$node->name = $vendor;
		$node->num_items = 0;
		$node->average = 0;
		$node->items = array ('A-platinum' => 0, 'B-gold' => 0, 'C-silver' => 0, 'D-bronze' => 0, 'E-garbage' => 0);
		$data [$vendor] = $node;
	}

	$data [$vendor]->num_items += 1;
	$data [$vendor]->items [$compatibility] += 1;
}

$min = 100;
$max = 0;

foreach ($data as $name => $vendor) {
	$sum = $vendor->items ['A-platinum'] * 4 + $vendor->items ['B-gold'] * 3 + $vendor->items ['C-silver'] * 2 + $vendor->items ['D-bronze'] * 1 + $vendor->items ['E-garbage'] * 0;
	$average = $sum / $vendor->num_items;

	if ($average < $min)
		$min = $average;
	if ($average > $max)
		$max = $average;

	$vendor->average = $average;
}

$managed = array ();
uasort ($data, 'sort_vendors');

?>

<div class="scale">
	<div class="section">

		<?php

		$current_stop = 0;
		$next_stop = 10;

		foreach ($data as $name => $vendor) {
			$n = $vendor->lname;

			/*
				Salto i produttori con meno di 2 prodotti recensiti, solitamente
				sono realta' inesistenti in Italia dunque non vale la pena
			*/
			if ($vendor->num_items < 3)
				continue;

			if (array_key_exists ($vendor->lname, $vendors) == false) {
				log_mail ('Nuovo produttore non gestito: ' . $vendor->name);
				continue;
			}

			if (file_exists ('icone/' . $vendors [$n]['icon']) == false) {
				log_mail ('Icona non disponibile per ' . $vendor->name);
				continue;
			}

			$tot = 100 - ((($vendor->average - $min) * 100) / ($max - $min));
			while ($tot > $next_stop) {
				$current_stop = $next_stop;
				$next_stop += 10;

				?>

				</div>
				<div class="section">

				<?php
			}

			$tot = ($tot - $current_stop) * 10;
			$icon = $vendors [$n]['icon'];

			?>

			<div class="place" style="margin-left: <?php echo $tot ?>%">
				<img src="icone/<?php echo $icon ?>">
				<input type="hidden" name="target" value="<?php echo $n ?>" />
			</div>

			<?php

			$managed [] = $vendor;
		}
		?>

	</div>
</div>

<div class="contentsBox">
	<?php foreach ($managed as $m): ?>
		<?php

		$n = $m->lname;
		$icon = $vendors [$n]['icon'];
		$web = $vendors [$n]['website'];

		$scale = array (
			'Pienamente Compatibile' => ($m->items ['A-platinum'] * 100) / $m->num_items,
			'Con Problemi Minori' => ($m->items ['B-gold'] * 100) / $m->num_items,
			'Appena Usabile' => ($m->items ['D-bronze'] * 100) / $m->num_items,
			'Con Problemi Maggiori' => ($m->items ['C-silver'] * 100) / $m->num_items,
			'Da Buttare!' => ($m->items ['E-garbage'] * 100) / $m->num_items,
		);

		?>

		<div class="vendorsummary" id="<?php echo $n ?>">
			<img src="icone/<?php echo $icon ?>">
			<div class="info">
				<h2><?php echo $m->name ?></h2>
				<p><a href="<?php echo $web ?>" target="_blank"><?php echo $web ?></a></p>

				<div class="details">
					<div class="row">
						I modelli testati per questo produttore sono:
					</div>
					<?php foreach ($scale as $level => $s): ?>
						<div class="row">
							<div class="label"><?php echo $level ?></div>
							<div class="barwrap">
								<div class="bar" style="width: <?php echo $s ?>%"></div>
							</div>
						</div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	<?php endforeach ?>
</div>

<div class="contentsBox">
	<p>
		Le statistiche sono elaborate a partire dai dati disponibili su <a href="http://h-node.org/">h-node</a>,
		piattaforma di valutazione della compatibilità dell'hardware con Linux cui vi invitiamo a partecipare per
		arricchire le informazioni a disposizione.
	</p>
</div>

<div class="contentsBox">
	<p>
		Quando intendi acquistare un nuovo PC su cui installare una distribuzione Linux, ti raccomandiamo di verificare sempre:
	</p>

	<ul>
		<li>
			<p>
				<b>la compatibilità dell'hardware</b>: non per tutti i componenti si trovano sempre drivers utilizzabili
				su Linux, o che lo facciano funzionare perfettamente. E' consigliato prima dare una occhiata alle recensioni
				aggregate su
			</p>
			<ul>
				<li><a href="http://tuxmobil.org/mylaptops.html">Tux Mobil</a></li>
				<li><a href="http://www.linuxcompatible.org/compatdb/lists/hardware_linux.html">Linux Compatible</a></li>
				<li><a href="http://www.linuxquestions.org/hcl/index.php?cat=507">Linux Questions</a></li>
				<li><a href="http://linux-laptop.net/">Linux on Laptops</a></li>
			</ul>
			<p>
				oppure consultare l'elenco di
				<a href="http://www.ubuntu.com/certification/desktop/">computer certificati per Ubuntu</a>.
			</p>
		</li>

		<li>
			<p>
				<b>la presenza di SecureBoot</b>: negli ultimi anni l'introduzione del cosiddetto "SecureBoot", velleitaria e
				contestata misura di sicurezza applicata direttamente nel firmware dei PC dei maggiori produttori su indicazione
				e supervisione di Microsoft, ha reso difficile l'installazione di Linux sui nuovi dispositivi. A volte non si
				riesce ad ottenere un dual boot con un altro sistema operativo, altre volte la procedura di installazione fallisce
				o neppure si avvia. Esistono varie sfumature di complessità, in alcuni casi tutto funziona al primo colpo ed in
				altri occorre modificare alcuni parametri appunto del pannello di configurazione del firmware: documentarsi prima
				sulla difficoltà di tale setup sul modello di computer scelto può far risparmiare parecchi grattacapi!
			</p>
		</li>
	</ul>
</div>

<?php
lugfooter ();
?>
