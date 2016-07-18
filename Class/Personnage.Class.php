<?php

class Personnage
{
 
	protected $id;
	protected $nom;
	protected $type ;
	protected $atout;
	protected $degats;
	protected $experience;
	protected $niveau;
	protected $forcePerso;
	protected $nombreCoup;
	protected $dateCoup;
	protected $timeEndormi ;

	// les constante de classe

	const CEST_MOI = 1;
	const PERSONNAGE_TUE = 2;
	const PERSONNAGE_FRAPPER = 3;
	const PAS_FRAPPER = 4 ;
	const PERSONNAGE_ENSORCELE = 5 ;
	const PAS_DE_MAGIE = 6 ;
	const PERSONNAGE_ENDORMI = 7 ;

	public function __construct(array $donnees)
	{
		$this->hydrate($donnees);
		$this->type = strtolower(get_class($this)) ;

	}

	public function hydrate(array $donnees)
	{
		foreach ($donnees as $key => $value) 
		{
			$method = 'set'.ucfirst($key);

			if(method_exists($this, $method))
			{
				$this->$method($value);
			}
		}
	}

	// les gettes pour nos attribut
	public function id()
	{
		return $this->id ;
	}

	public function nom()
	{
		return $this->nom ;
	}

	public function type()
	{
		return $this->type ;
	}

	public function atout()
	{
		return $this->atout ;
	}
	public function degats()
	{
		return $this->degats ;
	}
	public function experience()
	{
		return $this->experience ;
	}
	public function niveau()
	{
		return $this->niveau ;
	}
	public function forcePerso()
	{
		return $this->forcePerso ;
	}
	public function nombreCoup()
	{
		return $this->nombreCoup ;
	}
	public function dateCoup()
	{
		return $this->dateCoup ;
	}
	public function timeEndormi()
	{
		return $this->timeEndormi ;
	}


	// les setters de nos attributs

	public function setId($id)
	{
		$id = (int) $id ;

		if($id > 0)
		{
			$this->id = $id ; 
		}
	} 

	public function setNom($nom)
	{
		if(is_string($nom) and strlen($nom) <= 30)
		{
			$this->nom = $nom ;
		}
	} 

	public function setAtout($atout)
	{
		$atout = (int) $atout ;

		if($atout>=0 && $atout <=100)
		{
			$this->atout = $atout ;
		}
	}

	public function setDegats($degats)
	{
		$degats = (int) $degats ; 
		if($degats>=0 && $degats <=100)
		{
			$this->degats = $degats ;
		}
	}

	public function setExperience($experience)
	{
		$experience = (int) $experience ;

		if($experience >= 0)
		{
			$this->experience = $experience ;
		}

	}

	public function setNiveau($niveau)
	{	
		$niveau = (int) $niveau ;
		if($niveau >= 1)
		{
			$this->niveau = $niveau ;
		}
	}

	public function setForcePerso($force)
	{	
		$force = (int) $force ;

		if($force > 0)
		{
			$this->forcePerso = $force ;
		}
	}

	public function setNombreCoup($coup)
	{
		$coup = (int) $coup ;

		if($coup>=0)
		{
			$this->nombreCoup = $coup ;
		}

	}

	public function setDateCoup($date)
	{	
		$this->dateCoup = $date ;
	}

	public function setTimeEndormi($time)
	{
		$this->timeEndormi = (int) $time ;
	}



	// les method qu'utilise la classe

	public function frapper(Personnage $person)
	{

		if($this->nombreCoup<3)
		{
			//on verifie qu'on ne se frappe pas soit meme 
			if($person->id() == $this->id)
			{
				return self::CEST_MOI;
			}
			else
			{
				if($this->estEndormi())
				{
					return self::PERSONNAGE_ENDORMI ;
				}
				else
				{
					//on envoit un signe pour dire k le personnage a ete bien frapper
					$this->gagnerCoup();
					$this->updateDate();
					return $person->recevoirDegats($this->forcePerso);
				}

			}
		}
		else
		{
			if($this->heureRepos()>24)
			{
				if($this->estEndormi())
				{
					return self::PERSONNAGE_ENDORMI ;
				}
				else
				{
					$this->restoreCoup();
					$this->restoreDate();
					$this->gagnerCoup();
					$this->updateDate();
					return $person->recevoirDegats($this->forcePerso);
				}

			}
			else
			{
				return self::PAS_FRAPPER ;
			}

		}
	}

	public function recevoirDegats($forceFrappeur)
	{

		// on augmente les degats avec la force du frappeur.
		$this->degats += $forceFrappeur ;

		// on voit si les degats ne depasse pas 100
		if($this->degats>=100)
		{
			return self::PERSONNAGE_TUE ;
		}
		else
		{
			// on renvoit la valeur signifiant que la personne a ete frapper.
			return self::PERSONNAGE_FRAPPER ;
		
		}
	}

	public function nomValide()
	{
		if(!empty($this->nom))
		{
			return true ;
		}
		else
		{
			return false  ;
		}
	}

	public function gagnerExperience()
	{
		$exp = $this->experience += 1;

		if($exp==5)
		{
			$this->gagnerNiveau();
			$this->gagnerForcePerso();
			$this->setExperience(0);
		}


	}
	public function gagnerNiveau()
	{
		$this->niveau += 1;
	}
	public function gagnerForcePerso()
	{
		$this->forcePerso += 5 ;
	}

	public function gagnerCoup()
	{
		$this->nombreCoup ++ ;
	}

	public function heureRepos()
	{
		return (time()-$this->dateCoup)/3600 ;
	}
	public function restoreDate()
	{
		$this->dateCoup = 0 ;
	}
	public function restoreCoup()
	{
		$this->nombreCoup = 0 ;
	}
	public function updateDate()
	{
		$this->dateCoup = time() ;
	}

	public function estEndormi()
	{
		return $this->timeEndormi > time() ;
	} 

	public function reveil()
	{
		$seconde = $this->timeEndormi ;

		$seconde -= time() ;

		$heur = floor($seconde / 3600) ;
		$seconde -= $heur * 3600 ;
		$minute = floor($seconde / 60) ;
		$seconde -= $minute * 60 ;

		$heur .= $heur <= 1 ? 'heure': ' heures' ;
		$minute .= $minute <= 1 ? 'minute': ' minutes' ;
		$seconde .= $seconde <= 1 ? 'seconde': ' seconde' ;

		return $heur.', '.$minute. ' et '.$seconde ;
	}


}


?>