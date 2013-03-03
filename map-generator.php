<?php

require_once ('funzioni.php');

/*
	Scopiazzato da http://www.phpbuilder.com/board/showthread.php?t=10287962
*/
function howMany ($needle, $haystack) {
	$exists = array_search ($needle, $haystack);
	if ($exists !== FALSE)
		return 1 + howMany ($needle, array_slice ($haystack, ($exists + 1)));

	return 0;
}

function shift_city ($city, $lon, $found_cities) {
	/*
		Questo e' per evitare che due punti si sovrappongano, quelli che vengono
		trovati nella stessa citta' (e dunque alle stesse coordinate) vengono
		arbitrariamente shiftati
	*/
	$occurrences = howMany ($city, $found_cities);
	if ($occurrences != 0)
		$lon = $lon + (3000 * $occurrences);

	return $lon;
}

function latlon_magic ($lat, $lon) {
	/*
		Formule per la conversione delle coordinate brutalmente scopiazzate da linuxday.it
	*/
	$lat = (log (tan ((90 + $lat) * pi () / 360)) / (pi () / 180)) * 20037508.34 / 180;
	$lon = $lon * 20037508.34 / 180;
	return array ($lat, $lon);
}

function init_geocache () {
	global $has_geocache;
	global $geocache;
	global $data_folder;

	$has_geocache = file_exists ($data_folder . '/geocache.txt');

	if ($has_geocache == true)
		$geocache = file ($data_folder . '/geocache.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	else
		$geocache = array ();
}

function ask_geocache ($c) {
	global $has_geocache;
	global $geocache;

	if ($has_geocache == true) {
		foreach ($geocache as $row) {
			list ($city, $coords) = explode ('|', $row);

			if ($city == $c)
				return explode (',', $coords);
			else if (strcmp ($city, $c) > 0)
				break;
		}
	}

	return null;
}

function ask_nominatim ($c) {
	$location = file_get_contents ('http://nominatim.openstreetmap.org/search?format=xml&q=' . $c . ',Italia');

	$doc = new DOMDocument ();
	if ($doc->loadXML ($location, LIBXML_NOWARNING) == false)
		return null;

	$xpath = new DOMXPath ($doc);

	/*
		I risultati restituiti da Nominatim sono molteplici, e non sempre coerenti,
		qui cerchiamo il riferimento esplicito a diversi tipi (credo che li usi
		a seconda delle dimensioni del centro abitato) e se non si trova nulla
		passera' all'interrogazione di GeoNames. Attenzione: non usare i nodi di tipo
		"administrative", sono veramente troppo poco precisi
	*/

	$found = false;
	$accepted_nodes = array ('city', 'town', 'village', 'hamlet', 'suburb');

	foreach ($accepted_nodes as $accept) {
		$results = $xpath->query ("/searchresults/place[@type='$accept']", $doc);
		if ($results->length > 0) {
			$found = true;
			break;
		}
	}

	if ($found == false)
		return null;

	$node = $results->item (0);
	$lat = $node->getAttribute ('lat');
	$lon = $node->getAttribute ('lon');

	return latlon_magic ($lat, $lon);
}

function ask_geonames ($c) {
	$location = file_get_contents ('http://api.geonames.org/search?username=madbob&q=' . $c . '&country=IT');

	$doc = new DOMDocument ();
	if ($doc->loadXML ($location, LIBXML_NOWARNING) == false)
		return null;

	$xpath = new DOMXPath ($doc);

	$results = $xpath->query ("/geonames/geoname/lat", $doc);
	if ($results->length < 1)
		return null;
	$lat = $results->item (0);
	$lat = $lat->nodeValue;

	$results = $xpath->query ("/geonames/geoname/lng", $doc);
	if ($results->length < 1)
		return null;
	$lon = $results->item (0);
	$lon = $lon->nodeValue;

	return latlon_magic ($lat, $lon);
}

function ask_coordinates ($c) {
	global $geocache;

	$result = ask_geocache ($c);

	if ($result == null) {
		/*
			Questo e' per evitare i limiti imposti dal server OpenStreetMap
			http://wiki.openstreetmap.org/wiki/Nominatim_usage_policy
			Non dubito che GeoNames abbia qualcosa di analogo
		*/
		sleep (1);

		$result = ask_geonames ($c);

		if ($result == null) {
			$result = ask_nominatim ($c);
			if ($result == null)
				return null;
		}

		list ($lat, $lon) = $result;
		$geocache [] = "$c|$lat,$lon";
		sort ($geocache);
	}

	return $result;
}

function save_geocache () {
	global $geocache;
	global $data_folder;

	sort ($geocache);
	file_put_contents ($data_folder . '/geocache.txt', join ("\n", $geocache));
}

function write_geo_file ($name, $contents) {
	global $data_folder;

	/*
		Attenzione: e' necessario mettere un newline anche al fondo dell'ultima
		riga del file, la quale viene altrimenti ignorata da OpenLayer
	*/
	if (file_put_contents ($data_folder . '/' . $name, join ("\n", $contents) . "\n") === false)
		echo "Errore nel salvataggio del file\n";
	else
		echo "I dati sono stati scritti nel file '$name'\n";
}

init_geocache ();
global $geocache;

/*
	Per dettagli sul formato del file accettato da OpenLayer.Layer.Text
	http://dev.openlayers.org/apidocs/files/OpenLayers/Layer/Text-js.html
*/
$rows = array ("lat\tlon\ttitle\tdescription\ticonSize\ticonOffset\ticon");

foreach ($elenco_regioni as $region => $region_name) {
        $shops = file ('http://raw.github.com/madbob/LinuxSi/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$found_cities = array ();

        foreach ($shops as $shop) {
		$result = null;

		$attr = explode ('|', $shop);
		$site = $attr [3];
		$prov = $attr [2];
		$name = $attr [1];
		$city = $attr [0];

		$c = str_replace (' ', '%20', $city) . ',' . str_replace (' ', '%20', $prov);

		$result = ask_coordinates ($c);
		if ($result == null) {
			$c = str_replace (' ', '%20', $city);
			$result = ask_coordinates ($c);
		}

		if ($result != null) {
			list ($lat, $lon) = $result;
			$lon = shift_city ($city, $lon, $found_cities);
			$found_cities [] = $city;

			$rows [] = "$lat\t$lon\t$name\t<a href=\"$site\">$site</a>\t16,19\t-8,-19\thttp://lugmap.it/images/icon.png";
		}
		else {
			echo "Impossibile gestire la zona '$city', si consiglia l'analisi manuale\n";
		}
	}
}

write_geo_file ('geo.txt', $rows);
save_geocache ();

?>
