<?php


class Guerrier extends Personnage
{
	public function recevoirDegats($forceFrappeur)

	{


		if($this->degats>=0 && $this->degats <= 25)
		{
			$this->setAtout(4);
		}
		elseif($this->degats>25 && $this->degats <= 50)
		{
			$this->setAtout(3);
		}
		elseif($this->degats>50 && $this->degats <= 75)
		{
			$this->setAtout(2);
		}
		elseif($this->degats>75 && $this->degats <= 100)
		{
			$this->setAtout(1);
		}
		else
		{
			$this->setAtout(0) ;
		}

		// on augmente les degats avec la force du frappeur moins son atout.
		$this->degats += $forceFrappeur - $this->atout ;

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

}













?>