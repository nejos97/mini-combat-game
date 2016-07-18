<?php

class Magicien extends Personnage
{
 
 	public function lancerUnSort(Personnage $person)
 	{
 		if($this->degats>=5 && $this->degats <= 25)
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

		if($this->id == $person->id)
		{
			return self::CEST_MOI ;
		}
		if($this->atout == 0)
		{
			return self::PAS_DE_MAGIE ;
		}
		if($this->estEndormi())
		{
			return self::PERSONNAGE_ENDORMI ;
		}
		else
		{
			$person->timeEndormi = time() + ($this->atout * 6 ) * 3600 ;

			return self::PERSONNAGE_ENSORCELE ;
		}
 	}




































	
}
?>