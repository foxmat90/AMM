<?php

include_once 'User.php';
include_once 'Utente.php';
include_once 'Amministratore.php';
include_once 'Docente.php';
include_once 'Studente.php';
include_once 'CorsoDiLaureaFactory.php';
include_once 'DipartimentoFactory.php';

/**
 * Classe per la creazione degli utenti del sistema
 *
 * @author Davide Spano
 */
class UserFactory {

    private static $singleton;

    private function __constructor() {
        
    }

    /**
     * Restiuisce un singleton per creare utenti
     * @return \UserFactory
     */
    public static function instance() {
        if (!isset(self::$singleton)) {
            self::$singleton = new UserFactory();
        }

        return self::$singleton;
    }

    /**
     * Carica un utente tramite username e password
     * @param string $username
     * @param string $password
     * @return \User|\Docente|\Studente
     */
    public function caricaUtente($username, $password) {


        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[loadUser] impossibile inizializzare il database");
            $mysqli->close();
            return null;
        }
        else {
            echo "OK";
        }

        // cerco prima nella tabella studenti
        $query = "select studenti.id studenti_id,
            studenti.nome studenti_nome,
            studenti.cognome studenti_cognome,
            studenti.matricola studenti_matricola,
            studenti.email studenti_email,
            studenti.citta studenti_citta,
            studenti.via studenti_via,
            studenti.cap studenti_cap,
            studenti.provincia studenti_provincia,
            studenti.numero_civico studenti_numero_civico,
            studenti.username studenti_username,
            studenti.password studenti_password,
            
            CdL.id CdL_id,
            CdL.nome CdL_nome,
            CdL.codice CdL_codice,
            
            dipartimenti.id dipartimenti_id,
            dipartimenti.nome dipartimenti_nome
            
            from studenti 
            join CdL on studenti.cdl_id = CdL.id
            join dipartimenti on CdL.dipartimento_id = dipartimenti.id
            where studenti.username = ? and studenti.password = ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[loadUser] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[loadUser] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $studente = self::caricaStudenteDaStmt($stmt);
        if (isset($studente)) {
            // ho trovato uno studente
            $mysqli->close();
            return $studente;
        }

        // ora cerco un docente
        $query = "select 
               docenti.id docenti_id,
               docenti.nome docenti_nome,
               docenti.cognome docenti_cognome,
               docenti.email docenti_email,
               docenti.citta docenti_citta,
               docenti.cap docenti_cap,
               docenti.via docenti_via,
               docenti.provincia docenti_provincia,
               docenti.numero_civico docenti_numero_civico,
               docenti.ricevimento docenti_ricevimento,
               docenti.username docenti_username,
               docenti.password docenti_password,
               dipartimenti.id dipartimenti_id,
               dipartimenti.nome dipartimenti_nome
               
               from docenti 
               join dipartimenti on docenti.dipartimento_id = dipartimenti.id
               where docenti.username = ? and docenti.password = ?";

        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[loadUser] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[loadUser] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $docente = self::caricaDocenteDaStmt($stmt);
        if (isset($docente)) {
            // ho trovato un docente
            $mysqli->close();
            return $docente;
        }

// cerco prima nella tabella studenti
        $query = "select 
				utenti.username utenti_username,
            utenti.password utenti_password,
            utenti.nome utenti_nome,
            utenti.cognome utenti_cognome,
            utenti.email utenti_email,
				utenti.numero_civico utenti_numero_civico,
            utenti.citta utenti_citta,
				utenti.provincia utenti_provincia,
				utenti.id utenti_id,
				utenti.cap utenti_cap,
            utenti.via utenti_via,
				utenti.codice_segreto utenti_codice_segreto
            
            from utenti 
            where utenti.username = ? and utenti.password = ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[loadUser] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[loadUser] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $utente = self::caricaUtenteDaStmt($stmt);
        if (isset($utente)) {
            // ho trovato uno studente
            $mysqli->close();
            return $utente;
        }

        // cerco prima nella tabella amministratori
        $query = "select 
				amministratori.username utenti_username,
            amministratori.password utenti_password,
            amministratori.nome utenti_nome,
            amministratori.cognome utenti_cognome,
            amministratori.email utenti_email,
				amministratori.id utenti_id,
				amministratori.telefono utenti_telefono,
            amministratori.CV utenti_CV,
				amministratori.titolo_studio utenti_titolo_studio
            
            from amministratori
            where amministratori.username = ? and amministratori.password = ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[loadUser] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('ss', $username, $password)) {
            error_log("[loadUser] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $amministratore = self::caricaAmministratoreDaStmt($stmt);
        if (isset($amministratore)) { echo " TROVATO ";
            // ho trovato uno studente
            $mysqli->close();
            return $amministratore;
        }
    }

    /**
     * Restituisce un array con i Docenti presenti nel sistema
     * @return array
     */
    public function &getListaDocenti() {
        $docenti = array();
        $query = "select 
               docenti.id docenti_id,
               docenti.nome docenti_nome,
               docenti.cognome docenti_cognome,
               docenti.email docenti_email,
               docenti.citta docenti_citta,
               docenti.cap docenti_cap,
               docenti.via docenti_via,
               docenti.provincia docenti_provincia,
               docenti.numero_civico docenti_numero_civico,
               docenti.ricevimento docenti_ricevimento,
               docenti.username docenti_username,
               docenti.password docenti_password,
               dipartimenti.id dipartimenti_id,
               dipartimenti.nome dipartimenti_nome
               
               from docenti 
               join dipartimenti on docenti.dipartimento_id = dipartimenti.id";
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getListaDocenti] impossibile inizializzare il database");
            $mysqli->close();
            return $docenti;
        }
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getListaDocenti] impossibile eseguire la query");
            $mysqli->close();
            return $docenti;
        }

        while ($row = $result->fetch_array()) {
            $docenti[] = self::creaDocenteDaArray($row);
        }

        $mysqli->close();
        return $docenti;
    }

    /**
     * Restituisce la lista degli studenti presenti nel sistema
     * @return array
     */
    public function &getListaStudenti() {
        $studenti = array();
        $query = "select * from studenti " .
                "join CdL on cdl_id = CdL.id" .
                "join dipartimenti on CdL.dipartimento_id = dipartimenti.id";
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[getListaStudenti] impossibile inizializzare il database");
            $mysqli->close();
            return $studenti;
        }
        $result = $mysqli->query($query);
        if ($mysqli->errno > 0) {
            error_log("[getListaStudenti] impossibile eseguire la query");
            $mysqli->close();
            return $studenti;
        }

        while ($row = $result->fetch_array()) {
            $studenti[] = self::creaStudenteDaArray($row);
        }

        return $studenti;
    }

    /**
     * Carica uno studente dalla matricola
     * @param int $matricola la matricola da cercare
     * @return Studente un oggetto Studente nel caso sia stato trovato,
     * NULL altrimenti
     */
    public function cercaStudentePerMatricola($matricola) {


        $intval = filter_var($matricola, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!isset($intval)) {
            return null;
        }

        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[cercaStudentePerMatricola] impossibile inizializzare il database");
            $mysqli->close();
            return null;
        }

        $query = "select studenti.id studenti_id,
            studenti.nome studenti_nome,
            studenti.cognome studenti_cognome,
            studenti.matricola studenti_matricola,
            studenti.email studenti_email,
            studenti.citta studenti_citta,
            studenti.via studenti_via,
            studenti.cap studenti_cap,
            studenti.provincia studenti_provincia,
            studenti.numero_civico studenti_numero_civico,
            studenti.username studenti_username,
            studenti.password studenti_password,
            
            CdL.id CdL_id,
            CdL.nome CdL_nome,
            CdL.codice CdL_codice,
            
            dipartimenti.id dipartimenti_id,
            dipartimenti.nome dipartimenti_nome
            
            from studenti 
            join CdL on studenti.cdl_id = CdL.id
            join dipartimenti on CdL.dipartimento_id = dipartimenti.id
            where studenti.matricola = ?";
        $stmt = $mysqli->stmt_init();
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[cercaStudentePerMatricola] impossibile" .
                    " inizializzare il prepared statement");
            $mysqli->close();
            return null;
        }

        if (!$stmt->bind_param('i', $intval)) {
            error_log("[cercaStudentePerMatricola] impossibile" .
                    " effettuare il binding in input");
            $mysqli->close();
            return null;
        }

        $toRet =  self::caricaStudenteDaStmt($stmt);
        $mysqli->close();
        return $toRet;
    }

    /**
     * Cerca uno studente per id
     * @param int $id
     * @return Studente un oggetto Studente nel caso sia stato trovato,
     * NULL altrimenti
     */
    public function cercaUtentePerId($id, $role) {echo "PARAMETRI "; echo $id; echo " "; echo $role;
        $intval = filter_var($id, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
        if (!isset($intval)) {
            return null;
        }
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[cercaUtentePerId] impossibile inizializzare il database");
            $mysqli->close();
            return null;
        }

        switch ($role) {
            case User::Studente:
                $query = "select 
            studenti.id studenti_id,
            studenti.nome studenti_nome,
            studenti.cognome studenti_cognome,
            studenti.matricola studenti_matricola,
            studenti.email studenti_email,
            studenti.citta studenti_citta,
            studenti.via studenti_via,
            studenti.cap studenti_cap,
            studenti.provincia studenti_provincia, 
            studenti.numero_civico studenti_numero_civico,
            studenti.username studenti_username,
            studenti.password studenti_password,
            
            CdL.id CdL_id,
            CdL.nome CdL_nome,
            CdL.codice CdL_codice,
            
            dipartimenti.id dipartimenti_id,
            dipartimenti.nome dipartimenti_nome
            
            from studenti 
            join CdL on studenti.cdl_id = CdL.id
            join dipartimenti on CdL.dipartimento_id = dipartimenti.id
            where studenti.id = ?";
                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                return self::caricaStudenteDaStmt($stmt);
                break;

				case User::Amministratore: echo " cerca utente per id ";
                $query = "select 
            amministratori.username amministratori_username,
            amministratori.password amministratori_password,
            amministratori.nome amministratori_nome,
            amministratori.cognome amministratori_cognome,
            amministratori.email amministratori_email,
				amministratori.id amministratori_id,
				amministratori.telefono amministratori_telefono,
            amministratori.CV amministratori_CV,
				amministratori.titolo_studio amministratori_titolo_studio
            
            from amministratori 
            
            where amministratori.id = ?";
                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaAmministratorePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[cercaAmministratorePerId] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                return self::caricaAmministratoreDaStmt($stmt);
                break;

				case User::Utente:
                $query = "select 
            utenti.username utenti_username,
            utenti.password utenti_password,
            utenti.nome utenti_nome,
            utenti.cognome utenti_cognome,
            utenti.email utenti_email,
				utenti.numero_civico utenti_numero_civico,
            utenti.citta utenti_citta,
				utenti.provincia utenti_provincia,
				utenti.id utenti_id,
				utenti.cap utenti_cap,
            utenti.via utenti_via,
				utenti.codice_segreto utenti_codice_segreto
            
            from utenti 
            
            where utenti.id = ?";
                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                return self::caricaUtenteDaStmt($stmt);
                break;

            case User::Docente:
                $query = "select 
               docenti.id docenti_id,
               docenti.nome docenti_nome,
               docenti.cognome docenti_cognome,
               docenti.email docenti_email,
               docenti.citta docenti_citta,
               docenti.cap docenti_cap,
               docenti.via docenti_via,
               docenti.provincia docenti_provincia,
               docenti.numero_civico docenti_numero_civico,
               docenti.ricevimento docenti_ricevimento,
               docenti.username docenti_username,
               docenti.password docenti_password,
               dipartimenti.id dipartimenti_id,
               dipartimenti.nome dipartimenti_nome
               
               from docenti 
               join dipartimenti on docenti.dipartimento_id = dipartimenti.id
               where docenti.id = ?";

                $stmt = $mysqli->stmt_init();
                $stmt->prepare($query);
                if (!$stmt) {
                    error_log("[cercaUtentePerId] impossibile" .
                            " inizializzare il prepared statement");
                    $mysqli->close();
                    return null;
                }

                if (!$stmt->bind_param('i', $intval)) {
                    error_log("[loadUser] impossibile" .
                            " effettuare il binding in input");
                    $mysqli->close();
                    return null;
                }

                $toRet =  self::caricaDocenteDaStmt($stmt);
                $mysqli->close();
                return $toRet;
                break;

            default: return null;
        }
    }

    /**
     * Crea uno studente da una riga del db
     * @param type $row
     * @return \Studente
     */
    public function creaStudenteDaArray($row) {
        $studente = new Studente();
        $studente->setId($row['studenti_id']);
        $studente->setNome($row['studenti_nome']);
        $studente->setCognome($row['studenti_cognome']);
        $studente->setCitta($row['studenti_citta']);
        $studente->setCap($row['studenti_cap']);
        $studente->setVia($row['studenti_via']);
        $studente->setMatricola($row['studenti_matricola']);
        $studente->setEmail($row['studenti_email']);
        $studente->setProvincia($row['studenti_provincia']);
        $studente->setNumeroCivico($row['studenti_numero_civico']);
        $studente->setRuolo(User::Studente);
        $studente->setUsername($row['studenti_username']);
        $studente->setPassword($row['studenti_password']);

        if (isset($row['CdL_id']))
            $studente->setCorsoDiLaurea(CorsoDiLaureaFactory::instance()->creaDaArray($row));
        return $studente;
    }


	public function creaAmministratoreDaArray($row) {echo " array ";
        $amministratore = new Amministratore();
		  $amministratore->setUsername($row['amministratori_username']);
        $amministratore->setPassword($row['amministratori_password']);
        $amministratore->setNome($row['amministratori_nome']);
        $amministratore->setCognome($row['amministratori_cognome']);
        $amministratore->setEmail($row['amministratori_email']);
        $amministratore->setId($row['amministratori_id']);

        $amministratore->setTelefono($row['amministratori_telefono']);
        $amministratore->setCV($row['amministratori_CV']);
        $amministratore->setTitoloStudio($row['amministratori_titolo_studio']);
        
       
        return $amministratore;
    }

	public function creaUtenteDaArray($row) {
        $utente = new Utente();
		  $utente->setUsername($row['utenti_username']);
        $utente->setPassword($row['utenti_password']);
        $utente->setNome($row['utenti_nome']);
        $utente->setCognome($row['utenti_cognome']);
        $utente->setEmail($row['utenti_email']);

		  $utente->setNumeroCivico($row['utenti_numero_civico']);
        $utente->setCitta($row['utenti_citta']);
        $utente->setProvincia($row['utenti_provincia']);

        $utente->setId($row['utenti_id']);

        $utente->setCap($row['utenti_cap']);
        $utente->setVia($row['utenti_via']);
        $utente->setCodiceSegreto($row['utenti_codice_segreto']);
        
       
        return $utente;
    }


    /**
     * Crea un docente da una riga del db
     * @param type $row
     * @return \Docente
     */
    public function creaDocenteDaArray($row) {
        $docente = new Docente();
        $docente->setId($row['docenti_id']);
        $docente->setNome($row['docenti_nome']);
        $docente->setCognome($row['docenti_cognome']);
        $docente->setEmail($row['docenti_email']);
        $docente->setCap($row['docenti_cap']);
        $docente->setCitta($row['docenti_citta']);
        $docente->setVia($row['docenti_via']);
        $docente->setProvincia($row['docenti_provincia']);
        $docente->setNumeroCivico($row['docenti_numero_civico']);
        $docente->setRicevimento($row['docenti_ricevimento']);
        $docente->setRuolo(User::Docente);
        $docente->setUsername($row['docenti_username']);
        $docente->setPassword($row['docenti_password']);

        $docente->setDipartimento(DipartimentoFactory::instance()->creaDaArray($row));
        return $docente;
    }

    /**
     * Salva i dati relativi ad un utente sul db
     * @param User $user
     * @return il numero di righe modificate
     */
    public function salva(User $user) { echo " salva ";
        $mysqli = Db::getInstance()->connectDb();
        if (!isset($mysqli)) {
            error_log("[salva] impossibile inizializzare il database");
            $mysqli->close();
            return 0;
        }

        $stmt = $mysqli->stmt_init();
        $count = 0;
        switch ($user->getRuolo()) {
            case User::Studente:
                $count = $this->salvaStudente($user, $stmt);
                break;
            case User::Docente:
                $count = $this->salvaDocente($user, $stmt);
				/*case User::Utente:
                $count = $this->salvaDocente($user, $stmt);
				case User::Amministratore:
                $count = $this->salvaDocente($user, $stmt);
				*/
        }

        $stmt->close();
        $mysqli->close();
        return $count;
    }

    /**
     * Rende persistenti le modifiche all'anagrafica di uno studente sul db
     * @param Studente $s lo studente considerato
     * @param mysqli_stmt $stmt un prepared statement
     * @return int il numero di righe modificate
     */
    private function salvaStudente(Studente $s, mysqli_stmt $stmt) {
        $query = " update studenti set 
                    password = ?,
                    nome = ?,
                    cognome = ?,
                    email = ?,
                    numero_civico = ?,
                    citta = ?,
                    provincia = ?,
                    matricola = ?,
                    cap = ?,
                    via = ?
                    where studenti.id = ?
                    ";
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[salvaStudente] impossibile" .
                    " inizializzare il prepared statement");
            return 0;
        }

        if (!$stmt->bind_param('ssssississi', $s->getPassword(), $s->getNome(), $s->getCognome(), $s->getEmail(), $s->getNumeroCivico(), $s->getCitta(), $s->getProvincia(), $s->getMatricola(), $s->getCap(), $s->getVia(), $s->getId())) {
            error_log("[salvaStudente] impossibile" .
                    " effettuare il binding in input");
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("[caricaIscritti] impossibile" .
                    " eseguire lo statement");
            return 0;
        }

        return $stmt->affected_rows;
    }
    
    /**
     * Rende persistenti le modifiche all'anagrafica di un docente sul db
     * @param Docente $d il docente considerato
     * @param mysqli_stmt $stmt un prepared statement
     * @return int il numero di righe modificate
     */
    private function salvaDocente(Docente $d, mysqli_stmt $stmt) {echo " salva docente ";
        $query = " update docenti set 
                    password = ?,
                    nome = ?,
                    cognome = ?,
                    email = ?,
                    citta = ?,
                    provincia = ?,
                    cap = ?,
                    via = ?,
                    ricevimento = ?,
                    numero_civico = ?,
                    dipartimento_id = ?
                    where docenti.id = ?
                    ";
        $stmt->prepare($query);
        if (!$stmt) {
            error_log("[salvaStudente] impossibile" .
                    " inizializzare il prepared statement");
            return 0;
        }

        if (!$stmt->bind_param('sssssssssiii', 
                $d->getPassword(), 
                $d->getNome(), 
                $d->getCognome(), 
                $d->getEmail(), 
                $d->getCitta(),
                $d->getProvincia(),
                $d->getCap(), 
                $d->getVia(), 
                $d->getRicevimento(),
                $d->getNumeroCivico(), 
                $d->getDipartimento()->getId(),
                $d->getId())) {
            error_log("[salvaStudente] impossibile" .
                    " effettuare il binding in input");
            return 0;
        }

        if (!$stmt->execute()) {
            error_log("[caricaIscritti] impossibile" .
                    " eseguire lo statement");
            return 0;
        }

        return $stmt->affected_rows;
    }

    /**
     * Carica un docente eseguendo un prepared statement
     * @param mysqli_stmt $stmt
     * @return null
     */
    private function caricaDocenteDaStmt(mysqli_stmt $stmt) {

        if (!$stmt->execute()) {
            error_log("[caricaDocenteDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['docenti_id'], 
                $row['docenti_nome'], 
                $row['docenti_cognome'], 
                $row['docenti_email'], 
                $row['docenti_citta'],
                $row['docenti_cap'],
                $row['docenti_via'],
                $row['docenti_provincia'], 
                $row['docenti_numero_civico'],
                $row['docenti_ricevimento'],
                $row['docenti_username'], 
                $row['docenti_password'], 
                $row['dipartimenti_id'], 
                $row['dipartimenti_nome']);
        if (!$bind) {
            error_log("[caricaDocenteDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();

        return self::creaDocenteDaArray($row);
    }

	private function caricaUtenteDaStmt(mysqli_stmt $stmt) {

        if (!$stmt->execute()) {
            error_log("[caricaUtenteDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['utenti_username'],
					 $row['utenti_password'],
                $row['utenti_nome'], 
                $row['utenti_cognome'], 
                $row['utenti_email'],
					 $row['utenti_numero_civico'],
                $row['utenti_citta'],
                $row['utenti_provincia'],
					 $row['utenti_id'],
                $row['utenti_cap'],
                $row['utenti_via'],
 					 $row['utenti_codice_segreto']
                );

        if (!$bind) {
            error_log("[caricaUtenteDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();

        return self::creaUtenteDaArray($row);
    }
	private function caricaAmministratoreDaStmt(mysqli_stmt $stmt) {echo " carica stmt ";

        if (!$stmt->execute()) {
            error_log("[caricaAmministratoreDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['amministratori_username'],
					 $row['amministratori_password'],
                $row['amministratori_nome'], 
                $row['amministratori_cognome'], 
                $row['amministratori_email'],
					 $row['amministratori_id'],
                $row['amministratori_telefono'],
                $row['amministratori_CV'],
                $row['amministratori_titolo_studio']
                );

        if (!$bind) {
            error_log("[caricaAmministratoreDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();

        return self::creaAmministratoreDaArray($row);
    }


    /**
     * Carica uno studente eseguendo un prepared statement
     * @param mysqli_stmt $stmt
     * @return null
     */
    private function caricaStudenteDaStmt(mysqli_stmt $stmt) {

        if (!$stmt->execute()) {
            error_log("[caricaStudenteDaStmt] impossibile" .
                    " eseguire lo statement");
            return null;
        }

        $row = array();
        $bind = $stmt->bind_result(
                $row['studenti_id'], $row['studenti_nome'], $row['studenti_cognome'], $row['studenti_matricola'], $row['studenti_email'], $row['studenti_citta'], $row['studenti_via'], $row['studenti_cap'], $row['studenti_provincia'], $row['studenti_numero_civico'], $row['studenti_username'], $row['studenti_password'], $row['CdL_id'], $row['CdL_nome'], $row['CdL_codice'], $row['dipartimenti_id'], $row['dipartimenti_nome']);
        if (!$bind) {
            error_log("[caricaStudenteDaStmt] impossibile" .
                    " effettuare il binding in output");
            return null;
        }

        if (!$stmt->fetch()) {
            return null;
        }

        $stmt->close();

        return self::creaStudenteDaArray($row);
    }

}

?>
