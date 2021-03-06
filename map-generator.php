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

function init_geocache () {
	global $has_geocache;
	global $geocache;
	global $data_folder;

	$has_geocache = file_exists ($data_folder . '/geocache.txt');

	if ($has_geocache == true) {
		$geocache = file ($data_folder . '/geocache.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		list ($city, $coords) = explode ('|', $geocache [0]);
		list ($lat, $lon) = explode (',', $coords);
		if ($lat > 180) {
			unlink ($data_folder . '/geocache.txt');
			$geocache = array ();
		}
	}
	else {
		$geocache = array ();
	}
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
	$location = @file_get_contents ('http://nominatim.openstreetmap.org/search?format=xml&q=' . $c . ',Italia');
	if ($location == false)
		return null;

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

	return array ($lat, $lon);
}

function ask_geonames ($c) {
	$location = @file_get_contents ('http://api.geonames.org/search?username=madbob&q=' . $c . '&country=IT');
	if ($location == false)
		return null;

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

	return array ($lat, $lon);
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

	if (file_put_contents ($data_folder . '/' . $name, $contents) === false)
		log_mail ("Errore nel salvataggio del file");
}

function fetch_stats_file () {
	global $data_folder;

	$c = @file_get_contents ('https://h-node.org/download/notebooks/en');
	if ($c != false) {
		file_put_contents ($data_folder . '/hardware.xml', $c);
		unset ($c);
	}
	else {
		log_mail ("Impossibile scaricare file produttori PC da h-node");
	}
}

init_geocache ();
global $geocache;

$output = new stdClass ();
$output->type = "FeatureCollection";
$output->features = array ();

/**
 * @param $addr
 * @param $city
 * @param $prov
 *
 * @return array
 */
function generate_search_strings ($addr, $city, $prov) {
	$c = [];
	if ($addr !== '') {
		$c[] = rawurlencode ("$addr, $city, $prov");

		// Ultimo spazio nella stringa con l'indirizzo (e.g. "Via Di Test 12bis" => tra "Via Di Test" e "12bis"
		$lastspace = strrpos($addr, ' ', -1);
		if ($lastspace !== false) {
			$strada = substr ($addr, 0, $lastspace);
			$civico = substr ($addr, $lastspace);
			// Il civico è davvero un civico (12, 12bis, 12/14, etc... sono validi)
			// A volte c'è solo il nome di una strada perché è senza numero civico
			if (strpbrk ($civico, '1234567890') !== false) {
				// Prova con il nome della strada, senza il civico
				$c[] = rawurlencode ("$strada, $city, $prov");
			}
		}
	}
	// Per disperazione prova solo con la città
	$c[] = rawurlencode ("$city, $prov");
	$c[] = rawurlencode ("$city");

	return $c;
}

header ('Content-Type: text/plain; charset=utf-8');
foreach ($elenco_regioni as $region => $region_name) {
	$shops = file ('http://raw.github.com/madbob/LinuxSi/master/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	//$shops = file (__DIR__ . '/db/' . $region . '.txt', FILE_IGNORE_NEW_LINES);
	$found_cities = array ();

	foreach ($shops as $shop) {
		$result = null;

		$attr = explode ('|', trim ($shop));
		$coor = $attr [5];
		$addr = $attr [4];
		$site = $attr [3];
		$prov = $attr [2];
		$name = $attr [1];
		$city = $attr [0];

		if($coor === '') {
			$c = generate_search_strings ($addr, $city, $prov);

			foreach ($c as $askthis) {
				$result = ask_coordinates ($askthis);
				if ($result !== null) {
					break;
				}
			}

			if (substr_count ($askthis, ',') <= 1) {
				$doshift = true;
			}
		} else {
			$result = explode (',', $coor);
			$doshift = false;
		}

		if ($result != null) {
			list ($lat, $lon) = $result;

			// Una o zero virgole => è solo la città, bisogna shiftare
			if ($doshift) {
				$lon = shift_city ($city, $lon, $found_cities);
				$found_cities [] = $city;
			}

			$point = new stdClass ();
			$point->type = "Feature";
			$point->properties = new stdClass ();
			$point->properties->name = $name;
			$point->properties->website = $site;
			if($addr === '') {
				$point->properties->address = $city;
			} else {
				$point->properties->address = "$addr, $city";
			}
			$point->geometry = new stdClass ();
			$point->geometry->type = "Point";
			$point->geometry->coordinates = array ($lon, $lat);

			array_push ($output->features, $point);
		}
		else {
			log_mail ("Impossibile gestire la zona '$c[0]', si consiglia l'analisi manuale");
		}
	}
	echo '✅️ ' . $region . ".txt\r\n";
}

write_geo_file ('geo.txt', json_encode ($output));
save_geocache ();

fetch_stats_file ();

echo "✅️ cache\r\n";
