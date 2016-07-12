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

function sort_by_province ($a, $b) {
        $campi_a       = explode("|", $a);
        $campi_b       = explode("|", $b);

	$cmp = strcmp ($campi_a [2], $campi_b [2]);
	if ($cmp == 0)
		$cmp = strcmp ($campi_a [0], $campi_b [0]);

	return $cmp;
}

if (preg_match('/index\.php$/', $_SERVER["SCRIPT_NAME"])) { # se sono nel file index.php, allora sono stato invocato da /nome-regione/
  require_once ('../funzioni.php');
  $regione = substr(dirname($_SERVER["SCRIPT_NAME"]), 1); # estraggo la regione dal percorso

  if (array_key_exists ($regione, $elenco_regioni)) { # lasciamo il controllo, ma in ogni caso dovremmo ottenere un 404
    $db_file = '../db/'.$regione.'.txt';
    $db_regione = file($db_file);
    $title = 'LinuxSi: '. $elenco_regioni[$regione];
  }
  else {
    header("location: /");
  }
}
else { # qui se sono stato invocato alla vecchia maniera
  require_once ('funzioni.php');
  $db_regione = array ();

  foreach (glob ('./db/*.txt') as $db_file)
    $db_regione = array_merge ($db_regione, file ($db_file));

  $db_file = null;
  $regione = 'Italia';
  $title = 'LinuxSi: i negozi italiani';
}

lugheader ($title);
usort ($db_regione, 'sort_by_province');

?>

<div id="center">
  <h1 class="titoloregione"><?php echo substr($title, 8); print '&nbsp;<g:plusone size="small"></g:plusone>'; ?></h1>
  <p class="fromRegionLinks">
    <a href="/">&raquo; torna all'indice&nbsp;</a>
  </p>

  <?php if(count($db_regione) == 0): ?>

  <div style="text-align: center">
    <h2>Non sembrano esserci negozi Linux-friendly in questa regione!</h2>
    <p>Ne conosci qualcuno? <a href="/partecipa">Non esitare a contattarci</a>!</p>
  </div>

  <?php else: ?>

  <table id="lugListTable">
    <thead>
        <tr>
          <th>Provincia</th>
          <th>Zona</th>
          <th>Denominazione</th>
        </tr>
     </thead>
     <tfoot>
      <tr>
        <td colspan="3"></td>
        </tr>
    </tfoot>
    <tbody>
      <?php while (list ($nriga, $linea) = each ($db_regione)):
        $campi         = explode("|",$linea); # estrazione dei campi
        $provincia     = $campi[2];
        $denominazione = $campi[1];
        $zona          = $campi[0];
        $sito          = $campi[3];
        # stampa dei campi ?>
        <tr class="row_<?php echo ($nriga % 2); ?>">
         <td class="province"><?php echo $provincia ?></td>
         <td><?php echo $zona?></a></td>
         <td><a class="generalink" href="<?php echo $sito ?>"><?php echo $denominazione ?></a></td>
        </tr>
      <?php endwhile;?>
    </tbody>
   </table>

   <?php endif ?>

   <p class="fromRegionLinks">

   <?php if ($db_file != null) { ?>
      <a href="<?php echo $db_file ?>">&raquo; Elenco in formato CSV&nbsp;</a><br />
   <?php } else { ?>
   <br />
   <?php } ?>
   <?php ultimo_aggiornamento(); ?>

   </p>
</div>

<?php
  lugfooter ();
?>
