<?php
/*
  Codice della mappa dei negozi Linux-friendly italiani
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
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once ('../funzioni.php');
lugheader ('Mappa', null, array ('http://openlayers.org/api/OpenLayers.js', 'mappa.js'));

$transformed = false;

if (array_key_exists ('zoom', $_GET)) {
	$found = false;
	$contents = file ('../' . $data_folder . '/geo.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach ($contents as $row) {
		list ($lat, $lon, $lug, $useless) = explode ("\t", $row, 4);
		$lug = str_replace (' ', '_', $lug);

		if ($lug == $_GET ['zoom']) {
			$found = true;
			break;
		}
	}

	if ($found == true) {
		$transformed = true;

		?>

		<input type="hidden" name="zooming_lat" value="<?php echo $lat ?>" />
		<input type="hidden" name="zooming_lon" value="<?php echo $lon ?>" />
		<input type="hidden" name="default_zoom" value="12" />

		<?php
	}
}

if ($transformed == false) {
	?>
	<input type="hidden" name="default_zoom" value="5" />
	<?php
}

?>

<input type="hidden" name="coords_file" value="../<?php echo $data_folder ?>/geo.txt" />
<div id="map"></div>

<?php lugfooter (); ?>

