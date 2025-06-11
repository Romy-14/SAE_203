<!-- Code d'affichage de la table propriétaires, inutilisé car non-demandé dans les consignes -->

<?php
    // Connexion à la base
    $host = 'localhost';
    $dbname = 'BUTRT1_rt989650';
    $username = 'rt989650';
    $password = 'MDP_rt989650';

    // Nom de la table à afficher
    $table = 'proprietaires'; 
    echo '<!DOCTYPE html>
	<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title> Accueil - Lycée professionnel Joigny - Section automobile</title>
		<link rel="icon" href="./img/2016_page_accueil_internat_de_la_reussite.png">
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" href="./css/style.css">
	</head>

	<body>

		<header class="text-center my-4">
			<img src="./img/2016_page_accueil_internat_de_la_reussite.png" class="logo1" alt="Logo du lycée">
			<img src="./img/logo-république-française.png" class="logo2" alt="Logo République française">
			<h1>Lycée professionnel de Joigny</h1>
			<h2>Section automobile</h2>

			<nav class="navbar navbar-expand">
				<div class="container-fluid">
					<ul class="navbar-nav">
						<li class="nav-item">
							<a class="nav-link" href="accueil.html">Accueil</a>
						</li>
			
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="afficherDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
								Afficher une table
							</a>
							<ul class="dropdown-menu" aria-labelledby="afficherDropdown">
								<li><a class="dropdown-item">
									<form action="afficherDetailsFournitures.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Détails fournitures
										</button>
									</form>
								</a></li>

								<li><a class="dropdown-item">
									<form action="afficherDetailsInterventions.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Détails interventions
										</button>
									</form>
								</a></li>

								<li><a class="dropdown-item">
									<form action="afficherFournitures.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Fournitures
										</button>
									</form>
								</a></li>

								<li><a class="dropdown-item">
									<form action="afficherInterventions.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Interventions
										</button>
									</form>
								</a></li>
								
								<li><a class="dropdown-item">
									<form action="afficherOperations.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Opérations
										</button>
									</form>
								</a></li>

								<li><a class="dropdown-item">
									<form action="afficherVoitures.php" method="post">										
										<button type="submit" class="btn btn-primary">
											Voitures
										</button>
									</form>
								</a></li>
																						
							</ul>
						</li>
			
						<li class="nav-item"><a class="nav-link" href="ajouter.php">Ajouter une table</a></li>
						<li class="nav-item"><a class="nav-link" href="supprimer.php">Supprimer une table</a></li>
						<li class="nav-item"><a class="nav-link" href="rechercher.php">Rechercher une table</a></li>
					</ul>
				</div>
			</nav>
			
			
		</header>';


###################################################################################################


    try {
        $bdd = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Requête pour récupérer toutes les données de la table
        $result = $bdd->query("SELECT * FROM `$table`");

        echo '<h1>Table : ' . htmlspecialchars($table) . '</h1>';
        echo '<table class="table table-bordered mx-auto">';

        // Affiche les noms des colonnes
        echo '<tr>';
        for ($i = 0; $i < $result->columnCount(); $i++) { // lance une boucle qui parcourt toutes les colonnes du résultat pour afficher les noms des colonnes dans l’entête
            $col = $result->getColumnMeta($i); // permet d'obtenir les infos sur la colonne $i
            echo '<th>' . htmlspecialchars($col['name']) . '</th>';
        }
        echo '</tr>';

        // Affiche les données
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo '<tr>';
            foreach ($row as $val) {
                echo '<td>' . htmlspecialchars($val) . '</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }

    catch (PDOException $e) { // gestion des erreurs
        echo "Erreur : " . $e->getMessage();
    }

    finally	{
		// Ferme la connexion à la base de données
		$bdd = null;
	}


###########################################################################################


			echo '<footer class="text-center mt-5">
				<p>Lycée professionnel Joigny - Section automobile<br/>
					Crée par TRANQUILLE Romain & NIBERT-SIBER Jauris
				</p>
			</footer>

			<!-- Scripts Bootstrap -->
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

			<!-- Include all compiled plugins (below), or include individual files as needed -->
			<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
		</body>
	</html>';
?>
