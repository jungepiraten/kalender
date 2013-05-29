#!/usr/bin/php
<?php

$hostname = php_uname('n');
$filename = basename(__FILE__);

$colors = array(
	-1	=> array("filename" => "kalender"),
	0	=> array("background" => "#FFAD46", "border" => "#CB7403", "text" => "#1D1D1D", "filename" => "bund", "title" => "Bund"), # Default
	520	=> array("background" => "#CA2AE6", "border" => "#CA2AE6", "text" => "#1D1D1D", "filename" => "be", "title" => "BE"), # Berlin
	550	=> array("background" => "#92E1C0", "border" => "#33B694", "text" => "#1D1D1D", "filename" => "hh", "title" => "HH"), # Hamburg
	590	=> array("background" => "#7BD148", "border" => "#4DB810", "text" => "#1D1D1D", "filename" => "nw", "title" => "NRW"), # NRW
	600	=> array("background" => "#B99AFF", "border" => "#6733DD", "text" => "#1D1D1D", "filename" => "rp", "title" => "RLP"), # RLP
	620	=> array("background" => "#9FE1E7", "border" => "#0BBCB2", "text" => "#1D1D1D", "filename" => "sn", "title" => "SN"), # SN
	640	=> array("background" => "#CCA6AC", "border" => "#8A404D", "text" => "#1D1D1D", "filename" => "sh", "title" => "SH"), # SH
	650	=> array("background" => "#F691B2", "border" => "#D21E5B", "text" => "#1D1D1D", "filename" => "th", "title" => "TH"), # TH
);

$icals = array();
$events = array();
foreach ($colors as $color) {
	$filename = $color["filename"];
	$icals[$filename] = <<<EOT
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//{$hostname}//{$filename}//EN

EOT;
	$events[$filename] = array();
}

$baseurl = "https://wiki.junge-piraten.de/w/api.php?action=query&list=categorymembers&cmtitle=Kategorie:Termin&cmprop=title|sortkeyprefix&format=php";
do {
	$data = unserialize(file_get_contents($baseurl . (isset($data) ? "&cmcontinue=" . urlencode($data["query-continue"]["categorymembers"]["cmcontinue"]) : "")));
	foreach ($data["query"]["categorymembers"] as $member) {
		$categories = array();
		$url = "http://wiki.junge-piraten.de/wiki/" . rawurlencode($member["title"]);
		list($start, $ende, $titel) = explode(";", $member["sortkeyprefix"], 3);

		$color = null;
		$prefix = "";
		if (isset($colors[$member["ns"]])) {
			$color = $colors[$member["ns"]];
			$prefix = "[" . $color["title"] . "] ";
			$categories[] = $color["title"];
		}

		$unixstart = strtotime($start);
		// Sometimes we get "unusal" Timestamps
		if ($unixstart < 12*60*60) {
			continue;
		}
		if (strpos($start, " ")) {
			$allDay = false;
			if (empty($ende)) {
				$unixende = $unixstart + 2*60*60;
			} else {
				$unixende = strtotime($ende);
			}
			$dtstart = ":" . gmdate("Ymd\THis\\Z", $unixstart);
			$dtend = ":" . gmdate("Ymd\THis\\Z", $unixende);
			$jsonstart = date("r", $unixstart);
			$jsonend = date("r", $unixende);
		} else {
			$allDay = true;
			if (empty($ende)) {
				$unixende = $unixstart;
			} else {
				$unixende = strtotime($ende);
			}
			$dtstart = ";VALUE=DATE:" . date("Ymd", $unixstart);
			$dtend = ";VALUE=DATE:" . date("Ymd", $unixende);
			$jsonstart = date("Y-m-d", $unixstart);
			$jsonend = date("Y-m-d", $unixende);
		}

		$categories = implode(",", $categories);
		$dtstamp = gmdate("Ymd\\THis\\Z");

		$ical = <<<EOT
BEGIN:VEVENT
DTSTAMP:{$dtstamp}
DTSTART{$dtstart}
DTEND{$dtend}
CATEGORIES:{$categories}
SUMMARY:{$prefix}{$titel}
URL;VALUE=URI:{$url}
DESCRIPTION:{$url}
END:VEVENT

EOT;

		$event = array(
			"title" => $titel,
			"allDay" => $allDay,
			"start" => $jsonstart,
			"end" => $jsonend,
			"url" => $url
		);
		if ($color != null) {
			$event["backgroundColor"] = $color["background"];
			$event["borderColor"] = $color["border"];
			$event["textColor"] = $color["text"];
		}

		$icals["kalender"] .= $ical;
		$events["kalender"][] = $event;
		if ($color != null) {
			$icals[$color["filename"]] .= $ical;
			$events[$color["filename"]][] = $event;
		}
	}
} while (isset($data["query-continue"]));

foreach ($colors as $color) {
	$filename = $color["filename"];
	$icals[$filename] .= <<<EOT
END:VCALENDAR
EOT;

	file_put_contents("/var/www/kalender/data/" . $filename . ".ics", str_replace("\n", "\r\n", $icals[$filename]));
	file_put_contents("/var/www/kalender/data/" . $filename . ".json", json_encode($events[$filename]));
}

?>
