<?php
require_once('./index.php');


class Pokemon{

    public $nom;
    public $poids;
    public $taille;
    public $type;
    public $competence;
    public $faiblesse;

    public function __construct( string $nom, float $poids,  float $taille, array $type, array $competence, array $faiblesse)
    {
        $this->nom = $nom;
        $this->poids = $poids;
        $this->taille = $taille;
        $this->type = $type;
        $this->competence = $competence;
        $this->faiblesse = $faiblesse;
    }

}

?>