<?php

class PersonnageManager
{
	private $_bd;

	public function __construct($bdd)
	{
		$this->setDb($bdd);
	} 

	public function add(Personnage $person)
	{

		// cette method est charge d'ajoute un personnage dans la bd

		$q=$this->_bd->prepare('INSERT INTO personnage(nom,type,atout,degats,experience,niveau,forcePerso,nombreCoup,dateCoup,timeEndormi) VALUES(:nom,:type,:atout,:degats,:experience,:niveau,:forcePerso,:nombreCoup,:dateCoup,:timeEndormi)');
		$q->bindValue(':nom',$person->nom(),PDO::PARAM_STR);
		$q->bindValue(':type',$person->type(),PDO::PARAM_STR);
		$q->bindValue(':atout',$person->atout(),PDO::PARAM_INT);
		$q->bindValue(':degats',$person->degats(),PDO::PARAM_INT);
		$q->bindValue(':experience',$person->experience(),PDO::PARAM_INT);
		$q->bindValue(':niveau',$person->niveau(),PDO::PARAM_INT);
		$q->bindValue(':forcePerso',$person->forcePerso(),PDO::PARAM_INT);
		$q->bindValue(':nombreCoup',$person->nombreCoup(),PDO::PARAM_INT);
		$q->bindValue(':dateCoup',$person->dateCoup(),PDO::PARAM_INT);
		$q->bindValue(':timeEndormi',$person->timeEndormi(),PDO::PARAM_INT);
		$q->execute();

		$person->hydrate(["id"=>$this->_bd->lastInsertId(),"nom"=>$person->nom(),"degats"=>0,"experience"=>0]) ;
	}

	public function delete(Personnage $person)
	{
		// cette method est charge de supprimer un personnage dans notre bd
		$q=$this->_bd->prepare('DELETE  FROM personnage WHERE id=:id');
		$q->execute(array(':id'=>$person->id()));
	}

	public function get($info)
	{

		if(is_int($info))
		{	

			$q=$this->_bd->prepare('SELECT * FROM personnage WHERE id = :id');
			$q->execute(array('id'=>$info));
			$person = $q->fetch(PDO::FETCH_ASSOC);
		}
		else
		{
			$q = $this->_bd->prepare('SELECT * FROM personnage WHERE nom = :nom');
			$q ->execute(array('nom'=>$info));
			$person = $q ->fetch(PDO::FETCH_ASSOC);

		}

		switch ($person['type'])
		 {
			case 'guerrier':
				return new Guerrier($person) ;
				break;
			case 'magicien':
				return new Magicien($person) ;
				break;
			
			default:
				return null ;
				break;
		}
	}

	public function update(Personnage $person)
	{
		// cette method est charger de metre a jour un personnage.
		$q=$this->_bd->prepare('UPDATE personnage set nom = :nom ,type = :type, atout = :atout ,degats = :degats , experience = :experience, niveau = :niveau, forcePerso = :forcePerso,nombreCoup = :nombreCoup,dateCoup = :dateCoup , timeEndormi = :timeEndormi WHERE id = :id ');
		$q->bindValue(':nom',$person->nom(),PDO::PARAM_STR);
		$q->bindValue(':type',$person->type(),PDO::PARAM_STR);
		$q->bindValue(':atout',$person->atout(),PDO::PARAM_INT);
		$q->bindValue(':degats',$person->degats(),PDO::PARAM_INT);
		$q->bindValue(':experience',$person->experience(),PDO::PARAM_INT);
		$q->bindValue(':niveau',$person->niveau(),PDO::PARAM_INT);
		$q->bindValue(':forcePerso',$person->forcePerso(),PDO::PARAM_INT);
		$q->bindValue(':nombreCoup',$person->nombreCoup(),PDO::PARAM_INT);
		$q->bindValue(':dateCoup',$person->dateCoup(),PDO::PARAM_INT);
		$q->bindValue(':timeEndormi',$person->timeEndormi(),PDO::PARAM_INT);
		$q->bindValue(':id',$person->id(),PDO::PARAM_INT);
		$q->execute();
	}

	public function getList($nom)
	{
		// cette method renvoit la liste de tous les personnages. sauf pour celui passé en paramettre.

		$persos = [];

    	$q = $this->_bd->prepare('SELECT id,nom,degats FROM personnage WHERE nom != :nom ORDER BY nom');

    	$q->execute([':nom' => $nom]);


	    while ($donnees = $q->fetch(PDO::FETCH_ASSOC))
	    {

	       $persos[] = new Personnage($donnees);

	    }

    	return $persos;

	}
	public function setDb(PDO $bd)
	{
		$this->_bd = $bd ;
	}

	public function count()
	{
		// cette fonction renvoit le nombre d'enregistrement dans la bd.
		return $this->_bd->query('SELECT COUNT(*) FROM personnage')->fetchColumn();
	}

	public function exist($info)

  	{

    	// Si le paramètre est un entier, c'est qu'on a fourni un identifiant.

      // On exécute alors une requête COUNT() avec une clause WHERE, et on retourne un boolean.

	    if(is_int($info))
	    {
	    	return (bool) $this->_bd->query('SELECT * FROM personnage WHERE id='.$info)->fetchColumn();
	    }


    	// Sinon c'est qu'on a passé un nom.

    	// Exécution d'une requête COUNT() avec une clause WHERE, et retourne un boolean.

    	$q = $this->_bd->prepare('SELECT * FROM personnage WHERE nom=:nom');
    	$q->execute(array('nom'=>$info));
    	return (bool) $q->fetchColumn();

  	}

  

























}




?>