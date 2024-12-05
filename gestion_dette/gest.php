<?php
// Fonctions d'accès aux données
function selectClients():array {
    return [
        [
            "nom" => "Wane",
            "prenom" => "Baila",
            "telephone" => "777661010",
            "adresse" => "FO"
        ],
        [
            "nom" => "Wane1",
            "prenom" => "Baila1",
            "telephone" => "777661011",
            "adresse" => "FO1"
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

function selectDettes():array {
    return [
        [
            "telephone" => "777661010",
            "montant" => 100.50,
            "date_due" => "2024-12-31"
        ]
    ];
}

function insertDette(array &$tabDettes, $dette):void {
    $tabDettes[] = $dette;
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

function enregistrerDette(array &$tabDettes, array $dette):bool {
    insertDette($tabDettes, $dette);
    return true;
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
        "adresse" => saisieChampObligatoire("Entrer l'Adresse: ")
    ];
}

function afficheDette(array $dettes):void {
    if (count($dettes) == 0) {
        echo "Pas de dettes à afficher";
    } else {
        foreach ($dettes as $dette) {
            echo "\n-----------------------------------------\n";
            echo "Téléphone : " . $dette["telephone"] . "\t";
            echo "Montant : " . $dette["montant"] . "€\t";
            echo "Date Due : " . $dette["date_due"] . "\t";
        }
    }
}

function saisieDette(array $clients):array {
    return [
        "telephone" => telephoneIsUnique($clients, "Entrer le numero Téléphone: "),
        "montant" => (float)saisieChampObligatoire("Entrer le Montant: "),
        "date_due" => saisieChampObligatoire("Entrer la Date Due (YYYY-MM-DD): ")
    ];
}

function menu():int {
    echo "
     1. Ajouter client \n
     2. Lister les clients \n 
     3. Ajouter une dette \n
     4. Lister les dettes \n
     5. Quitter \n";
    return (int)readline("Faites votre choix: ");
}

function principal() {
    $clients = selectClients();
    $dettes = selectDettes();
    do {
        $choix = menu();
        switch ($choix) {
            case 1:
                $client = saisieClient($clients);
                if (enregistrerClient($clients, $client)) {
                    echo "Client Enregistré avec succès \n";
                } else {
                    echo "Le numéro de téléphone existe déjà \n";
                }
                break;
            case 2:
                afficheClient($clients);
                break;
            case 3:
                $dette = saisieDette($clients);
                enregistrerDette($dettes, $dette);
                echo "Dette Enregistrée avec succès \n";
                break;
            case 4:
                afficheDette($dettes);
                break;
            case 5:
                echo "Au revoir ! \n";
                break;
            default:
                echo "Veuillez faire un bon choix \n";
                break;
        }
    } while ($choix != 5);
}
principal();
