<?php
require 'flight/Flight.php';
require 'jsonindent.php';

Flight::register('db', 'Database', array('rest'));
Flight::register('db_pom', 'Database', array('rest'));
$json_podaci = file_get_contents("php://input");
flight::set('json_podaci', $json_podaci);

Flight::route('/', function () {
    echo 'hello world!';
});

Flight::route('GET /novosti', function () {
    header("Content-Type:application/json; charset-utf-8");
    $db = Flight::db();
    $db->select();
    $niz = array();
    while ($red = $db->getResult()->fetch_object()) {
        $niz[] = $red;
    }

    $json_niz = json_encode($niz, JSON_UNESCAPED_UNICODE);
    echo indent($json_niz);
    return false;
});


Flight::route('GET /novosti/@id', function () {
});

Flight::route('POST /novosti', function () {
    header("Content-Type:application/json; charset-utf-8");
    $db = Flight::db();
    $podaci = json_decode(Flight::get('json_podaci'));
    if ($podaci == null) {
        $odg["poruka"] = "Niste prosledili nista";
        $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
        echo $json_odg;
    } else {
        if (!property_exists($podaci, 'naslov') || !property_exists($podaci, 'tekst') || !property_exists($podaci, 'kategorija_id')) {
            $odg["poruka"] = "Niste prosledili tacne podatke";
            $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
            echo $json_odg;
        } else {
            $podaci_q = array();
            foreach ($podaci as $k => $v) {
                $v = "'" . $v . "'";
                $podaci_q[$k] = $v;
            }
            $niz_vrednosti = array($podaci_q["naslov"], $podaci_q["tekst"], $podaci_q["kategorija_id"], 'NOW()');
            if ($db->insert("novosti", "naslov, tekst, kategorija_id, datumVreme", $niz_vrednosti)) {
                $odg["poruka"] = "Uspesno uneta novost";
                $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
                echo $json_odg;
            } else {
                $odg["poruka"] = "Greska pri unosu novosti";
                $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
                echo $json_odg;
            }
        }
    }
});


Flight::route('PUT /novosti/@id', function ($id) {
    header("Content-Type:application/json; charset-utf-8");
    $db = Flight::db();
    $podaci = json_decode(Flight::get('json_podaci'));
    if ($podaci == null) {
        $odg["poruka"] = "Niste prosledili nista";
        $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
        echo $json_odg;
    } else {
        if (!property_exists($podaci, 'naslov') || !property_exists($podaci, 'tekst') || !property_exists($podaci, 'kategorija_id')) {
            $odg["poruka"] = "Niste prosledili tacne podatke";
            $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
            echo $json_odg;
        } else {
            $podaci_q = array();
            foreach ($podaci as $k => $v) {
                $v = "'" . $v . "'";
                $podaci_q[$k] = $v;
            }
            $kljucevi = array('naslov', 'tekst', 'kategorija_id');
            $vrednosti = array($podaci->naslov, $podaci->tekst, $podaci->kategorija_id);
            if ($db->update("novosti", $id, $kljucevi, $vrednosti)) {
                $odg["poruka"] = "Uspesno azurirana novost";
                $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
                echo $json_odg;
            } else {
                $odg["poruka"] = "Greska pri azuriranju novosti";
                $json_odg = json_encode($odg, JSON_UNESCAPED_UNICODE);
                echo $json_odg;
            }
        }
    }
});

Flight::route('DELETE /novosti/@id', function () {
});

Flight::route('GET /kategorije', function () {
    header("Content-Type: application/json; charset=utf-8");
    $db = Flight::db();
    $db->select("kategorije", "*", null, null, null, null, null);
    $niz = array();
    $i = 0;
    while ($red = $db->getResult()->fetch_object()) {

        $niz[$i]["id"] = $red->id;
        $niz[$i]["kategorija"] = $red->kategorija;
        $db_pomocna = new Database("rest");
        $db_pomocna->select("novosti", "*", null, null, null, "novosti.kategorija_id = " . $red->id, null);
        while ($red_pomocna = $db_pomocna->getResult()->fetch_object()) {
            $niz[$i]["novosti"][] = $red_pomocna;
        }
        $i++;
    }
    //JSON_UNESCAPED_UNICODE parametar je uveden u PHP verziji 5.4
    //Omogućava Unicode enkodiranje JSON fajla
    //Bez ovog parametra, vrši se escape Unicode karaktera
    //Na primer, slovo č će biti \u010
    $json_niz = json_encode($niz, JSON_UNESCAPED_UNICODE);
    echo indent($json_niz);
    return false;
});


Flight::route('GET /kategorije/@id', function () {
});

Flight::route('POST /kategorije', function () {
});

Flight::route('PUT /kategorije/@id', function () {
});

Flight::route('DELETE /kategorije/@id', function () {
});


Flight::start();
