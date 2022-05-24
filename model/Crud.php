<?php

require_once('Pokemon.php');

class CRUD{

    private $database;
     
    public function __construct($dns,$ndc,$mdp)
    {
        $this->database = new PDO($dns,$ndc,$mdp);
    }

    /* return toutes nos functions (5) */

    private function idType($idType){
        $req = $this->database->prepare('SELECT `type` FROM `type` WHERE id = ? ');
        $req->execute([$idType]);
        $resultIdType = $req->fetch(PDO::FETCH_ASSOC);
        

        return $resultIdType['type'];
    }
     

    private function idFaiblesse($idFaiblesse){
        $req = $this->database->prepare('SELECT `faiblesse` FROM `faiblesse` WHERE id = ? ');
        $req->execute([$idFaiblesse]);
        $resultIdFaiblesse = $req->fetch(PDO::FETCH_ASSOC);

        return $resultIdFaiblesse['faiblesse'];
    }
    private function idCompetence($idCompetence){
        $req = $this->database->prepare('SELECT `competence` FROM `competence` WHERE id = ? ');
        $req->execute([$idCompetence]);
        $resultIdCompetences = $req->fetch(PDO::FETCH_ASSOC);

        return $resultIdCompetences['competence'];
    }

    // 1) trouver tous les pokemon
    public function getAll(){


        $tabloPokemon = [];

        $stats = $this->database->prepare('SELECT nom  FROM pokemon ');
        $stats->execute();
        $resultAll = $stats->fetchAll(PDO::FETCH_ASSOC);

        foreach($resultAll as $data){
            $pokemon = $this->getOneByName($data['nom']);
            array_push($tabloPokemon, $pokemon);
        }
        return $tabloPokemon;
     
    }
    
    // 2) trouver un pokemon par nom
    
    public function getOneByName($name){


        
        $stats = $this->database->prepare('SELECT * FROM  pokemon WHERE nom = ?');
        $stats->execute([$name]);
        $resultByName = $stats->fetch(PDO::FETCH_ASSOC);

        if($resultByName === false){
            return 'aucun pokemon trouver';
        }else{
     
        $tabloType = [];
        $stats = $this->database->prepare('SELECT id_type FROM pokemon_type WHERE id_pokemon = ?');
        $stats->execute([$resultByName['id']]);
        $resultIdType = $stats->fetchAll(PDO::FETCH_ASSOC); 
        foreach($resultIdType as $data){
            $funcType = $this->idType($data['id_type']);
            array_push($tabloType, $funcType);
        }

        $tabloFaiblesse = [];
        $stats = $this->database->prepare('SELECT id_faiblesse FROM pokemon_faiblesse WHERE id_pokemon = ?');
        $stats->execute([$resultByName['id']]);
        $resultIdFaiblesse = $stats->fetchAll(PDO::FETCH_ASSOC); 
        foreach($resultIdFaiblesse as $data){
            $funcFaiblesse = $this->idFaiblesse($data['id_faiblesse']);
            array_push($tabloFaiblesse, $funcFaiblesse);
        }

        $tabloCompetence = [];
        $stats = $this->database->prepare('SELECT id_competence FROM pokemon_competence WHERE id_pokemon = ?');
        $stats->execute([$resultByName['id']]);
        $resultIdCompetence = $stats->fetchAll(PDO::FETCH_ASSOC); 
        foreach($resultIdCompetence as $data){
            $funcCompetence = $this->idCompetence($data['id_competence']);
            array_push($tabloCompetence, $funcCompetence);
        }
        
        return new Pokemon($resultByName['nom'],$resultByName['poids'],$resultByName['taille'],$tabloType,$tabloCompetence,$tabloFaiblesse);
        }

       
        

    }

    // 5) trouver un pokemon par plusieurs faiblesses
    public function getByMultiWeak($Faiblesse){

        $idFaiblesse =[];
        
        foreach($Faiblesse as $data){
            $requete = 'SELECT id FROM faiblesse WHERE faiblesse = ?';
            $stats = $this->database->prepare($requete);
            $stats->execute([$data]);
            $result = $stats->fetch(PDO::FETCH_COLUMN);
            array_push($idFaiblesse, $result);

        }

        $reformat =  implode(',',$idFaiblesse);

        $request = "SELECT id_pokemon FROM `pokemon_faiblesse` 
        WHERE  id_faiblesse IN ($reformat)
        GROUP BY id_pokemon HAVING COUNT(id_faiblesse)=:count";

        $stats = $this->database->prepare($request);
        $stats->execute([':count'=>count($idFaiblesse)]);
        $result = $stats->fetchAll(PDO::FETCH_COLUMN);

        $tabloNamePokemon = [];
        foreach($result as $data){
            $rocket = "SELECT nom FROM pokemon WHERE id = ? ";
            $stats = $this->database->prepare($rocket);
            $stats->execute([$data]);
            $resultName = $stats->fetch(PDO::FETCH_COLUMN);
            
            array_push($tabloNamePokemon, $resultName);

        }
        
        $tabloFinal = [];

        foreach($tabloNamePokemon as $data){
            $pokemon = $this->getOneByName($data);
            array_push($tabloFinal, $pokemon);
        }
        return $tabloFinal;

    }

    // 6) trouver un pokemon par plusieurs types
    

    public function getByMultiType($type){

        $idType =[];
        
        foreach($type as $typeData){
            $requete = 'SELECT `id` FROM `type` WHERE type = ?';
            $stats = $this->database->prepare($requete);
            $stats->execute([$typeData]);
            $result = $stats->fetch(PDO::FETCH_COLUMN);
            array_push($idType, $result);

        }


        $reformat =  implode(',',$idType);


        $request = "SELECT id_pokemon FROM `pokemon_type` 
        WHERE  id_type IN ($reformat)
        GROUP BY id_pokemon HAVING COUNT(id_type)=:count";

        $stats = $this->database->prepare($request);
        $stats->execute([':count'=>count($idType)]);
        $result = $stats->fetchAll(PDO::FETCH_COLUMN);

        $tabloNamePokemon = [];
        foreach($result as $data){
            $rocket = "SELECT nom FROM pokemon WHERE id = ? ";
            $stats = $this->database->prepare($rocket);
            $stats->execute([$data]);
            $resultName = $stats->fetch(PDO::FETCH_COLUMN);
            
            array_push($tabloNamePokemon, $resultName);

        }
        
        $tabloFinal = [];

        foreach($tabloNamePokemon as $data){
            $pokemon = $this->getOneByName($data);
            array_push($tabloFinal, $pokemon);
        }
        return $tabloFinal;

    }
    
}
