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

# l'array utilizza come chiave la richiesta in input
# (utilizzata anche per identificare il file da leggere)
# e come valore la stringa da visualizzare
$elenco_regioni = array (
	"abruzzo"               => "Abruzzo",
	"basilicata"            => "Basilicata",
	"calabria"              => "Calabria",
	"campania"              => "Campania",
	"emilia-romagna"        => "Emilia Romagna",
	"friuli-venezia-giulia" => "Friuli Venezia Giulia",
	"lazio"                 => "Lazio",
	"liguria"               => "Liguria",
	"lombardia"             => "Lombardia",
	"marche"                => "Marche",
	"molise"                => "Molise",
	"piemonte"              => "Piemonte",
	"puglia"                => "Puglia",
	"sardegna"              => "Sardegna",
	"sicilia"               => "Sicilia",
	"toscana"               => "Toscana",
	"trentino-alto-adige"   => "Trentino Alto Adige",
	"umbria"                => "Umbria",
	"valle-daosta"          => "Valle d'Aosta",
	"veneto"                => "Veneto"
);

$data_folder = 'data';

function conf($name) {
	require('config.php');
	
	if (!isset($$name)) {
		echo 'parametro ' . $name . ' non esistente';
	}

	return $$name;
}

function lugheader ($title, $extracss = null, $extrajs = null, $reduced = false) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="language" content="italian" />
	<meta name="robots" content="noarchive" />
	<link href="https://www.linux.it/shared/?f=bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="https://www.linux.it/shared/?f=main.css" rel="stylesheet" type="text/css" />

	<meta name="dcterms.creator" content="Italian Linux Society" />
	<meta name="dcterms.type" content="Text" />
	<link rel="publisher" href="http://www.ils.org/" />

	<meta name="twitter:title" content="LinuxSi" />
	<meta name="twitter:creator" content="@ItaLinuxSociety" />
	<meta name="twitter:card" content="summary" />
	<meta name="twitter:url" content="https://linuxsi.com/" />
	<meta name="twitter:image" content="https://linuxsi.com/immagini/tw.png" />

	<meta property="og:site_name" content="LinuxSi" />
	<meta property="og:title" content="LinuxSi" />
	<meta property="og:url" content="https://linuxsi.com/" />
	<meta property="og:image" content="https://linuxsi.com/immagini/fb.png" />
	<meta property="og:type" content="website" />
	<meta property="og:country-name" content="Italy" />
	<meta property="og:email" content="webmaster@linux.it" />
	<meta property="og:locale" content="it_IT" />
	<meta property="og:description" content="La mappa dei negozi Linux friendly" />

	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

	<script src="//code.jquery.com/jquery-1.9.1.min.js"></script>

	<?php

	if ($extracss != null)
		foreach ($extracss as $e) {
			?>
			<link href="<?php echo $e; ?>" rel="stylesheet" type="text/css" />
			<?php
		}

	if ($extrajs != null)
		foreach ($extrajs as $e) {
			?>
			<script type="text/javascript" src="<?php echo $e; ?>"></script>
			<?php
		}

	?>

	<title><?php echo $title; ?></title>
</head>

<body>

<div id="header">
	<img src="/immagini/logo.png" alt="" />
	<div id="maintitle"><?php echo conf('website_name') ?></div>
	<div id="payoff"><?php echo conf('website_tagline') ?></div>

	<div class="menu">
		<a class="generalink" href="/">Home</a>
		<a class="generalink" href="/mappa">Mappa</a>
		<a class="generalink" href="/produttori">Produttori</a>
		<a class="generalink" href="/partecipa">Partecipa</a>

		<p class="social mt-2">
			<a href="https://twitter.com/ItaLinuxSociety"><img src="https://www.linux.it/shared/?f=immagini/twitter.svg"></a>
			<a href="https://www.facebook.com/ItaLinuxSociety/"><img src="https://www.linux.it/shared/?f=immagini/facebook.svg"></a>
			<a href="https://www.instagram.com/ItaLinuxSociety"><img src="https://www.linux.it/shared/?f=immagini/instagram.svg"></a>
			<a href="https://mastodon.uno/@ItaLinuxSociety/"><img src="https://www.linux.it/shared/?f=immagini/mastodon.svg"></a>
			<a href="https://github.com/madbob/LinuxSi"><img src="https://www.linux.it/shared/?f=immagini/github.svg"></a>
		</p>
	</div>
</div>

<?php
}

function lugfooter () {
?>

<div id="ils_footer" class="mt-5">
	<div class="container">
		<div class="row">
			<div class="col-md-3">
				<span style="text-align: center; display: block">
					<a rel="nofollow" href="https://www.gnu.org/licenses/agpl-3.0-standalone.html" rel="license">
						<img src="https://www.linux.it/shared/index.php?f=immagini/agpl3.svg" style="border-width:0" loading="lazy" alt="AGPLv3 License">
					</a>

					<a rel="nofollow" href="https://creativecommons.org/publicdomain/zero/1.0/deed.en_US" rel="license">
						<img src="https://www.linux.it/shared/index.php?f=immagini/cczero.png" style="border-width:0" loading="lazy" alt="Creative Commons License">
					</a>
				</span>
			</div>

			<div class="col-md-3">
				<h2>RESTA AGGIORNATO!</h2>
				<iframe title="Newsletter ILS" src="https://crm.linux.it/form/1" width="100%" height="430" frameBorder="0"><p>Your browser does not support iframes.</p></iframe>
			</div>

			<div class="col-md-3">
				<h2>Amici</h2>
				<p style="text-align: center">
					<a href="https://www.ils.org/info#aderenti">
						<img src="https://www.ils.org/external/getrandlogo.php" border="0" loading="lazy" alt="Aderenti a Italian Linux Society" /><br />
						Scopri tutte le associazioni e le aziende che hanno aderito a Italian Linux Society.
					</a>
				</p>
			</div>

			<div class="col-md-3">
				<h2>Network</h2>
				<script type="text/javascript" src="https://www.ils.org/external/widgetils.js" defer></script>
    				<div id="widgetils"></div>
			</div>
		</div>
	</div>

	<div style="clear: both"></div>
</div>

<!-- Matomo -->
<script>
	var _paq = window._paq = window._paq || [];
	_paq.push(["setDoNotTrack", true]);
	_paq.push(["disableCookies"]);
	_paq.push(['trackPageView']);
	_paq.push(['enableLinkTracking']);
	(function() {
		var u="//stats.linux.it/";
		_paq.push(['setTrackerUrl', u+'matomo.php']);
		_paq.push(['setSiteId', '10']);
		var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
		g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
	})();
</script>
<noscript><p><img src="//stats.linux.it/matomo.php?idsite=10&amp;rec=1" style="border:0;" alt="" /></p></noscript>
<!-- End Matomo Code -->

</body>
</html>

<?php
}

function ultimo_aggiornamento () {
	try {
		$path = '../.ultimo_commit';
		if (file_exists($path) == false) {
			$path = '../../.ultimo_commit';
		}

		$last_update = file_get_contents($path);
	}
	catch(Exception $e) {
		$last_update = '???';
	}

	?>
	<a href="https://github.com/madbob/LinuxSi/commits/master">&raquo; Aggiornato al <?php print $last_update ?>&nbsp;</a><br />
	<a href="mailto:webmaster@linux.it?subject=LinuxSi: segnalazione aggiornamento/errore/refuso">&raquo; Segnala&nbsp;</a>
	<?php
}

function log_mail ($message) {
	mail ('webmaster@linux.it', 'errore su linuxsi.com', $message . "\n", 'From: linux.it <webmaster@linux.it>' . "\r\n");
}
