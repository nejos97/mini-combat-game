<?php

require 'Class/Autoload.php';

spl_autoload_register('ChargerClasse') ;

// demarage de la session 

session_start(); 

if(isset($_GET['deconnexion']))
{
	session_destroy();
	header('location:index.php');
	exit();
}

if(isset($_SESSION['person']))
{
	$person = $_SESSION['person'] ;
}

$bd = new PDO('mysql:host=localhost;dbname=Mini-Combat', 'root', '');

$bd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING); 

$manager = new PersonnageManager($bd);

if(isset($_POST['creer']) && isset($_POST['nom']))
{
	$donnees = ["nom"=>$_POST['nom'],"atout"=>0,"degats"=>0,"experience"=>0,"niveau"=>1,"forcePerso"=>5,"nombreCoup"=>0,"dateCoup"=>0,"timeEndormi"=>0] ;

	switch ($_POST['type']) {
		case 'magicien':
			$person = new Magicien($donnees) ;
			break;
		case 'guerrier':
			$person = new Guerrier($donnees) ;
			break;
		
		default:
			$message = "type personnage invalide" ;
			break;
	}

	if(isset($person))
	{
		if(!$person->nomValide())
		{
			$message = 'votre nom n\'est pas valide';
			unset($person);
		}
		elseif($manager->exist($person->nom()))
		{
			$message = 'ce nom existe deja';
			unset($person);
		}
		else
		{	
			$manager->add($person);
		}
	}


}
elseif (isset($_POST['utiliser']) && isset($_POST['nom'])) 
{
	if(!empty($_POST['nom']))
	{
		if($manager->exist($_POST['nom']))
		{
			$person=$manager->get($_POST['nom']);
		}
		else
		{
			$message = 'ce personnage n\'existe pas ';
		}
	}
	else
	{
		$message = "Votre nom ne doit pas etre vide" ;
	}
}

elseif(isset($_GET['frapper']))
{
	if(!isset($person))
	{
		$message = ' Merci de vous identifier ' ; 
	}
	else
	{
		if(!$manager->exist( (int) $_GET['frapper']))
		{
			$message = 'Ce personnage n\'existe pas' ;
		}
		else
		{
			$victime = $manager->get((int) $_GET['frapper']) ;

			$retour = $person->frapper($victime) ;

			switch ($retour) 
			{
				case personnage::CEST_MOI:
					$message = 'Impossible de vous frapper vous meme' ;
					break;
				case personnage::PERSONNAGE_FRAPPER :
					$message = 'Le personnage a ete bien frapper hahahah' ;
					$manager->update($person);
					$manager->update($victime);
					break ;
				
				case personnage::PERSONNAGE_TUE :
					$message = 'vous avez tue ce personnage heeeeee' ;
					$person->gagnerExperience();
					$manager->update($person);
					$manager->delete($victime);
					break;
				case personnage::PERSONNAGE_ENDORMI :
					$message = 'vous etes endormi impossible de frapper' ;
					break;
				case personnage::PAS_FRAPPER :
					$message = "Desole !!! vous avez atteint votre cota journalier de frappe" ;

			}
		}
	}
}
elseif(isset($_GET['ensorceler']))
{
	if(!isset($person))
	{
		$message = "Merci de vous identifier" ;
	}
	else
	{
		if($person->type() != 'magicien')
		{
			$message = 'desole vous n\'etes pas magicien ' ;
		}
		else
		{
			if(!$manager->exist((int) $_GET['ensorceler']))
			{
				$message = 'le personnage a ensorceler n\existe pas ' ;
			}
			else
			{
				$personAensorceler = $manager->get((int) $_GET['ensorceler']) ;

				$retour = $person->lancerUnSort($personAensorceler) ;

				switch ($retour) {
					case personnage::PERSONNAGE_ENSORCELE:
						$message = 'le personnage a bien ete ensorceler' ;
						$manager->update($person);
						$manager->update($personAensorceler);
						break;
					case personnage::CEST_MOI:
						$message = 'impossible de vous ensorceler.' ;
						break;
					case personnage::PAS_DE_MAGIE:
						$message = 'vous n\'avez pas de magie' ;
						break;
					case personnage::PERSONNAGE_ENDORMI:
						$message = 'vous etes endormi empossible de jeter un sort.' ;
						break;
				}
			}
		}
	}

}

?>
<!DOCTYPE html>
<html>
<head>

	<meta charset="utf-8">

	<!-- Loading bootstrap -->
	<link href="asset/dist/css/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Loading Flat UI -->
    <link href="asset/dist/css/flat-ui.min.css" rel="stylesheet">

    <!-- Loading Personal style-->
    <link href="asset/style.css" rel="stylesheet">

	<title>Mini-Combat-v1.2.0</title>
</head>
<body>
	<h3><font color="white"><center> MINI-COMBAT GAME </center> </font></h3>
	<div class="col-md-offset-3 col-md-6 ">
		<p class="well">Nombre de personnage creer : <?= $manager->count();  ?> </p>
	</div>
	
	<div class="col-md-offset-3 col-md-6">
		
		<?php 
			if(isset($message))
			{
				echo '<p class="alert alert-info"><b>'. $message .'</b></p>' ;
			}
		?>
	</div>



	

	<?php
	// chargement des new perso
	if(isset($person))
	{
	?>
		<div class="row">
			<div class="col-md-offset-3 col-md-6">
				<div class="well">
					<fieldset>
						<legend>Mes information</legend>
						<a href="?deconnexion"> <i class="fui-power"></i> Deconnexion</a><br>
						Nom : <?= htmlspecialchars($person->nom()); ?>
						<br />
						Type : <?= ucfirst($person->type()) ?>
						<br />
						Degats : <?= $person->degats() ?>
						<br />
						Experience : <?= $person->experience() ?>
						<br />
						Niveau : <?= $person->niveau() ?>
						<br />
						Force : <?= $person->forcePerso() ?>
						<br />
						Coup/jour : <?= $person->nombreCoup() ?>
						<br />
						<?php

						switch ($person->type()) {
							case 'magicien':
								echo 'Magie : ' ;
								break;
							case 'guerrier':
								echo 'Protection : ' ;
								break ;
						}

						echo $person->atout() ;

						?>
					</fieldset>
				</div>
			</div>			
		</div>

		<div class="row">
			<div class="col-md-offset-3 col-md-6">
				<div class="well">
					<fieldset>
						<legend>Qui frapper ?</legend>
						<?php 

						$persons=$manager->getList($person->nom());

						if(empty($persons))
						{
							echo 'pas d\'enemis a frapper';
						}
						else
						{
							if($person->estEndormi())
							{
								echo 'un magicien vous a endormi !! votre reveil est dans : '.$person->reveil();
							}
							else
							{


								foreach ($persons as $p) 
								{

								?>


									<div class="row">
										<div class="col-sm-4"><?= htmlspecialchars($p->nom()) ?></div>
										<div class="col-sm-4">Degats : <?= $p->degats() ?></div>
										<div class="col-sm-4">
											<?= '<a href="?frapper=', $p->id(), '">','<button class="btn btn-embossed btn-success btn-sm">Frapper</button></a>' ?>
											<?php
												if($person->type() == 'magicien')
												{
													echo '<a href="?ensorceler=', $p->id(), '">','<button class="btn btn-embossed btn-success btn-sm">Ensorceler</button></a>' ;
												}
											?>
										</div>
									</div>
									<hr>

								<?php

								}
							}
						}

						?>
					</fieldset>
				</div>
			</div>
		</div>

	<?php
	}
	else
	{

	?>
		<div class="row">
			<div class="col-md-offset-3 col-md-6 ">
				<div class="tab1">
					<form method="POST" action="#" class="well">
					<input type="text" name="nom" class="form-control" placeholder="Nom Personnage">
					<br>
					<select name="type" class="form-control">
						<optgroup label="Type personnage">
							<option value="magicien">Magicien</option>
							<option value="guerrier">Guerrier</option>
						</optgroup>
					</select>
					<br>
					<center>
						<button type="submit" name="creer" class="btn btn-info btn-lg"><i class="fui-plus"></i> Creer Personnage</button>
						<button type="submit" name="utiliser" class="btn btn-info btn-lg"><i class="fui-user"></i> Utiliser Personnage</button>
					</center>
				</form> 
				</div>
			</div>
		</div> 

	<?php
	}
	?>
	<div class="col-md-offset-3 col-md-6 ">
		<div class="well">
			<p><a href="http://www.github.com/nejostar"><i class="fui-github"></i> @nejostar</a></p>
			<p><a href="http://www.github.com/sdmg15"><i class="fui-github"></i> @sdmg15</a></p>
			<p><a href="http://www.google.com/plus/jonathannenba"><i class="fui-google-plus"></i> @jonathannenba</a></p>
			<p><a href="http://www.twitter.com/nejostar"><i class="fui-twitter"></i> @nejostar</a></p>
			<p><a href="mailto:jonathannenba@gmail.com"><i class="fui-mail"></i> jonathannenba@gmail.com</a></p>
			<p><a><i class="fui-location"></i> Guider , Cameroun </a> </p>
			<hr>
			<p> <center>CopyRight  2016</center> </p>

		</div>
	</div>
	<script src="asset/dist/js/vendor/jquery.min.js"></script>
    <script src="asset/dist/js/vendor/video.js"></script>
    <script src="asset/dist/js/flat-ui.min.js"></script>
    <script src="asset/dist/js/application.js"></script>
</body>
</html>

<?php
if (isset($person))
{
	$_SESSION['person'] = $person ;
}

?>
