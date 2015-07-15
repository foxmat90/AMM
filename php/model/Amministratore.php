<?php

include_once 'User.php';
include_once 'Dipartimento.php';

/**
 * Classe che rappresenta un Docente
 *
 * @author Davide Spano
 */
class Amministratore extends User {

    /**
     * Il Dipartimento di afferenza
     * @var Dipartimento $dipartimento 
     */
    private $dipartimento;
    /**
     * Descrizione dell'orario di ricevimento
     * @var string
     */
    private $ricevimento;
    private $telefono;
    private $cv;
    private $titolo_studio;


    /**
     * Costruttore
     */
    public function __construct() {
        // richiamiamo il costruttore della superclasse
        parent::__construct();
        $this->setRuolo(User::Amministratore);
    }

    /**
     * Restituisce il Dipartimento di afferenza
     * @return Dipartimento
     */
    public function getDipartimento() {
        return $this->dipartimento;
    }

    /**
     * Imposta un nuovo Dipartimento di afferenza
     * @param Dipartimento $dipartimento il nuovo Dipartimento di afferenza
     */
    public function setDipartimento(Dipartimento $dipartimento) {
        $this->dipartimento = $dipartimento;
    }

    /**
     * Imposta un nuovo valore per l'orario di ricevimento
     * @param string $ricevimento il nuovo orario di ricevimento
     * @return boolean true se impostato correttamente, false altrimenti
     */
    public function setRicevimento($ricevimento) {
        $this->ricevimento = $ricevimento;
        return true;
    }

    /**
     * Restituisce la descrizione dell'orario di ricevimento
     * @return string
     */
    public function getRicevimento() {
        return $this->ricevimento;
    }


 	 public function setTelefono($telefono) {
        $this->telefono= $telefono;
        return true;
    }

  
    public function getTelefono() {
        return $this->telefono;
    }

	 public function setCV($cv) {
        $this->cv= $cv;
        return true;
    }

  
    public function getCV() {
        return $this->cv;
    }

	  public function setTitoloStudio($titolo_studio) {
        $this->titolo_studio = $titolo_studio;
        return true;
    }

  
    public function getTitoloStudio() {
        return $this->titolo_studio;
    }

}

?>
