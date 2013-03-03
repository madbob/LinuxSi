<?php
/*Codice della mappa dei negozi Linux-friendly italiani
  Copyright (C) 2010-2013  Italian Linux Society - http://www.ils.org/

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as
  published by the Free Software Foundation, either version 3 of the
  License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.*/
?>
<?php

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

function lugheader ($title, $extracss = null, $extrajs = null) {
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="it">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="language" content="italian" />
  <meta name="robots" content="noarchive" />
  <link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Open+Sans|Nobile|Nobile:b" />
  <link href="/css/main.css" rel="stylesheet" type="text/css" />

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

  <script type="text/javascript" src="https://apis.google.com/js/plusone.js">
    {lang: 'it'}
  </script>

  <title><?php echo $title; ?></title>
</head>
<body>

<div id="header">
  <img src="/immagini/logo.png" width="66" height="79" alt="" />
	<div id="maintitle">LinuxSi</div>
	<div id="payoff">La mappa dei negozi Linux friendly</div>

	<div class="menu">
		<a class="generalink" href="/">LinuxSi</a>
		<a class="generalink" href="/mappa/">Mappa</a>
		<a class="generalink" href="/partecipa/">Partecipa</a>
		<a class="generalink" href="/contatti/">Contatti</a>

		<p class="social">
			<!-- Icone prese da http://kooc.co.uk/need-some-up-to-date-social-media-icons -->
			<a href="https://github.com/madbob/LinuxSi/commits/master.atom"><img src="/immagini/rss.png"></a>
			<a href="https://github.com/madbob/LinuxSi"><img src="/immagini/github.png"></a>
		</p>
	</div>
</div>

<?php
}

function lugfooter () {
?>
<div id="footer">
</div>
<!-- Piwik -->
<!--

<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://pergamena.lugbs.linux.it/" : "http://pergamena.lugbs.linux.it/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 1);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://pergamena.lugbs.linux.it/piwik.php?idsite=1" style="border:0" alt="" /></p></noscript>
-->
<!-- End Piwik Tracking Code -->
</body>
</html>

<?php
}

function ultimo_aggiornamento () {
?>
   <a href="http://github.com/madbob/LinuxSi/commits/">&raquo; Aggiornato al <?php print file_get_contents('../.ultimo_commit') ?>&nbsp;</a><br />
   <a href="mailto:roberto.guido@linux.it?subject=LinuxSi: segnalazione aggiornamento/errore/refuso">&raquo; Segnala&nbsp;</a>

<?php
}

?>
