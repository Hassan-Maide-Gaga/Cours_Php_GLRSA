<?php 

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);
// Fonctions d'accès aux données
function selectClients():array {
    return [
        [
            "nom"=>"Wane",
            "prenom"=>"Baila",
            "telephone"=>"777661010",
            "adresse"=>"FO",
            "dettes" => []
        ],
        [
            "nom"=>"Wane1",
            "prenom"=>"Baila1",
            "telephone"=>"777661011",
            "adresse"=>"FO1",
            "dettes" => [
                [
                    "montdette" => 5000,
                    "datepret"=> "12-10-2012",
                    "echeance" => "12-10-2023",
                    "ref"=>"1234",
                    "montverse" => 2500,
                    "paiement"=>[
                        [
                            "ref"=>"1235",
                            "date"=> "12-12-2012",
                            "montantpaie"=> "2500"
                        ],
                        [
                            "ref"=>"123",
                            "date"=> "12-11-2015",
                            "montantpaie"=> "2500"
                        ]
                    ]
                ],
                [
                    "montdette" => 5000,
                    "datepret"=> "12-10-2012",
                    "echeance" => "12-10-2023",
                    "ref"=>"1234",
                    "montverse" => 2500,
                    "paiement"=>[
                        [
                            "ref"=>"1234",
                            "date"=> "12-11-2012",
                            "montantpaie"=> "2500"
                        ]
                    ]
                ]
            ]
        ]
    ];
}

function selectClientByTel(array $clients, string $tel):array|null {
    foreach ($clients as $client) {
        if ($client["telephone"] == $tel) {
            return $client;
        }
    }
    return null;
}

function insertClient(array &$tabClients, $client):void {
    $tabClients[] = $client;
}

// Fonctions Services ou Use Case ou Métier
function enregistrerClient(array &$tabClients, array $client):bool {
    $result = selectClientByTel($tabClients, $client["telephone"]);
    if ($result == null) {
        insertClient($tabClients, $client);
        return true;
    }
    return false;
}

function listerClient():array {
    return selectClients();
}

function estVide(string $value):bool {
    return empty($value);
}

// Fonctions Présentation
function saisieChampObligatoire(string $sms):string {
    do {
        $value = readline($sms);
    } while (estVide($value));
    return $value;
}

function telephoneIsUnique(array $clients, string $sms):string {
    do {
        $value = readline($sms);
    } while (estVide($value) || selectClientByTel($clients, $value) != null);
    return $value;
}

function afficheClient(array $clients):void {
    if (count($clients) == 0) {
        echo "Pas de client à afficher";
    } else {
        foreach ($clients as $client) {
            echo "\n-----------------------------------------\n";
            echo "Téléphone : " . $client["telephone"] . "\t";
            echo "Nom : " . $client["nom"] . "\t";
            echo "Prénom : " . $client["prenom"] . "\t";
            echo "Adresse : " . $client["adresse"] . "\t";
        }
    }
}

function saisieClient(array $clients):array {
    return [
        "telephone" => telephoneIsUnique($clients, "Entrer le Téléphone: "),
        "nom" => saisieChampObligatoire("Entrer le Nom: "),
        "prenom" => saisieChampObligatoire("Entrer le Prénom: "),
        "adresse" => saisieChampObligatoire("Entrer l'Adresse: "),
        "dettes" => []
    ];
}

function menu():int {
    echo "
    1. Ajouter client \n
    2. Lister les clients \n 
    3. Rechercher client par téléphone \n
    4. Quitter \n";
    return (int)readline("Faites votre choix: ");
}

function confirmer(string $sms):bool {
    do {
        $rep = readline($sms);
    } while ($rep != "O" && $rep != "N");
    return $rep == "O";
}

function verifMontant(string $sms) {
    do {
        $montant = (float)readline($sms);
    } while ($montant <= 0);
    return $montant;
}

function saisieDette():array {
    return [
        "montdette" => verifMontant("Entrer le montant de la dette: "),
        "datepret" => saisieChampObligatoire("Entrer la Date du prêt: "),
        "echeance" => saisieChampObligatoire("Entrer la Date de l'échéance: "),
        "ref" => saisieChampObligatoire("Entrer la Référence de la dette: "),
        "montverse" => verifMontant("Entrer le montant versé: "),
        "paiement" => []
    ];
}

function insertDettes(array &$tabClients, array $tabDette, int $index):void {
    $tabClients[$index]["dettes"][] = $tabDette;
}

function indexClientByTel(array $clients, string $tel):int {
    foreach ($clients as $index => $client) {
        if ($client["telephone"] == $tel) {
            return $index;
        }
    }
    return -1;
}

function listerDettesByClient(string $numero, array $tabDettes):void {
    foreach ($tabDettes as $dette) {
        echo "\n-----------------------------------------\n";
        echo "Téléphone : " . $numero . "\t";
        echo "Montant de la dette : " . $dette["montdette"] . "\t";
        echo "Date du prêt : " . $dette["datepret"] . "\t";
        echo "Date de l'échéance : " . $dette["echeance"] . "\t";
        echo "Référence : " . $dette["ref"] . "\t";
        echo "Montant versé : " . $dette["montverse"] . "\t";
    }
}

// Fonction principale
function principal():void {
    $clients = selectClients();
    do {
        $choix = menu();
        switch ($choix) {
            case 1:
                $client = saisieClient($clients);
                if (enregistrerClient($clients, $client)) {
                    echo "Client Enregistré avec succès \n";
                    if (confirmer("Voulez-vous enregistrer une dette (O/N)? ")) {
                        insertDettes($clients, saisieDette(), count($clients) - 1);
                    }
                } else {
                    echo "Le numéro de téléphone existe déjà \n";
                }
                break;
            case 2:
                afficheClient($clients);
                break;
            case 3:
                $tel = readline("Entrer le numéro de téléphone: ");
                if (indexClientByTel($clients, $tel) != -1) {
                    $dette = saisieDette();
                    insertDettes($clients, $dette, indexClientByTel($clients, $tel));
                } else {
                    echo "Le numéro de téléphone n'existe pas \n";
                }
                break;
            case 4:
                $numero = readline("Entrer le numéro: ");
                $client = selectClientByTel($clients, $numero);
                if ($client) {
                    listerDettesByClient($numero, $client["dettes"]);
                } else {
                    echo "Le numéro de téléphone n'existe pas \n";
                }
                break;
            default:
                echo "Veuillez faire un bon choix: ";
                break;
        }
    } while ($choix != 4);
}
principal();
