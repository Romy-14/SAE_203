<?php
	echo'<!DOCTYPE html>
	<html lang="fr">
		<head>
			<meta charset="UTF-8">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
			<title> Supprimer - Lycée professionnel Joigny - Section automobile</title>
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

			$host = 'localhost';
			$dbname = 'BUTRT1_rt989650';
			$username = 'rt989650';
			$password = 'MDP_rt989650';

			try {
				$bdd = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
				$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (PDOException $e) { // gestion des erreurs
				die("<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>");
			}

			function deleteDependencies(PDO $bdd, string $table, string $primaryKey, $id) { // supprime les dépendances lors des suppressions des entrées
				// Chercher les contraintes de clé étrangère pointant vers cette table
				$foreignKeyQuery = "
					SELECT TABLE_NAME, COLUMN_NAME 
					FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
					WHERE REFERENCED_TABLE_NAME = :table 
					AND REFERENCED_COLUMN_NAME = :primaryKey 
					AND TABLE_SCHEMA = DATABASE()
				";
			
				$req_ord = $bdd->prepare($foreignKeyQuery);
				$req_ord->execute(['table' => $table, 'primaryKey' => $primaryKey]);
				$foreignKeys = $req_ord->fetchAll(PDO::FETCH_ASSOC);
			
				// Pour chaque table liée, supprimer les lignes dépendantes
				foreach ($foreignKeys as $fk) {
					$childTable = $fk['TABLE_NAME'];
					$childColumn = $fk['COLUMN_NAME'];
			
					// Appel récursif pour supprimer les sous-dépendances
					$childPKStmt = $bdd->query("DESCRIBE $childTable");
					$childColumns = $childPKStmt->fetchAll(PDO::FETCH_COLUMN);
					$childPrimaryKey = $childColumns[0];
			
					// Récupère les IDs enfants dépendants
					$idStmt = $bdd->prepare("SELECT $childPrimaryKey FROM $childTable WHERE $childColumn = :id");
					$idStmt->execute(['id' => $id]);
					$childIds = $idStmt->fetchAll(PDO::FETCH_COLUMN);
			
					// Supprime chaque dépendance trouvée (et ses propres dépendances)
					foreach ($childIds as $childId) {
						deleteDependencies($bdd, $childTable, $childPrimaryKey, $childId);
			
						$delChildStmt = $bdd->prepare("DELETE FROM $childTable WHERE $childPrimaryKey = :id");
						$delChildStmt->execute(['id' => $childId]);
					}
				}
			}
			

			// Liste des tables autorisées
			$tables = [
				"details_fournitures",
				"details_interventions",
				"fournitures",
				"interventions",
				"operations",
				"proprietaires",
				"voitures"
			];

			// Variables pour les messages
			$message = "";
			$error = "";

			// Récupération table sélectionnée (via POST ou GET)
			$tableSelected = $_POST['table'] ?? null;

			// Traitement de la suppression
			if (isset($_POST['delete']) && $tableSelected && in_array($tableSelected, $tables)) {
				$idToDelete = $_POST['entry'] ?? null;

				if (!$idToDelete) {
					$error = "Veuillez sélectionner une entrée à supprimer.";
				} else {
					// On récupère la clé primaire de la table pour construire la requête
					try {
						$req_ord = $bdd->query("DESCRIBE $tableSelected");
						$columns = $req_ord->fetchAll(PDO::FETCH_COLUMN);
						$primaryKey = $columns[0]; // première colonne comme clé primaire

						// Préparation et exécution de la suppression
						deleteDependencies($bdd, $tableSelected, $primaryKey, $idToDelete);

						$delStmt = $bdd->prepare("DELETE FROM $tableSelected WHERE $primaryKey = :id");
						$delStmt->execute(['id' => $idToDelete]);

						if ($delStmt->rowCount() > 0) {
							$message = "Entrée supprimée avec succès de la table '$tableSelected'.";
						} else {
							$error = "Aucune entrée supprimée. Vérifiez que la sélection est correcte.";
						}
					} catch (Exception $e) {
						$error = "Erreur lors de la suppression : " . htmlspecialchars($e->getMessage());
					}
				}
			}

			// Fonction pour récupérer les entrées à afficher dans la liste déroulante de suppression
			function getEntries($bdd, $table) {
				// Récupérer les colonnes de la table
				$req_ord = $bdd->query("DESCRIBE $table");
				$columns = $req_ord->fetchAll(PDO::FETCH_ASSOC);
				$primaryKey = $columns[0]['Field'];
				$secondColumn = $columns[1]['Field'] ?? $primaryKey;
			
				// Vérifie si la 2e colonne est une clé étrangère
				$fkQuery = "
					SELECT REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
					FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
					WHERE TABLE_NAME = :table 
					AND COLUMN_NAME = :column 
					AND TABLE_SCHEMA = DATABASE()
				";
			
				$fkReq_ord = $bdd->prepare($fkQuery);
				$fkReq_ord->execute(['table' => $table, 'column' => $secondColumn]);
				$fk = $fkReq_ord->fetch(PDO::FETCH_ASSOC);
			
				if ($fk) {
					$refTable = $fk['REFERENCED_TABLE_NAME'];
					$refColumn = $fk['REFERENCED_COLUMN_NAME'];
			
					// Récupère colonne lisible de la table référencée
					$refDescStmt = $bdd->query("DESCRIBE $refTable");
					$refCols = $refDescStmt->fetchAll(PDO::FETCH_COLUMN);
					$displayCol = $refCols[1] ?? $refCols[0];
			
					if ($table === 'details_fournitures') { // compare la valeur ET le type des expressions
						// Ajout de la quantité dans l'affichage du label
						$query = "
							SELECT t.$primaryKey AS id, 
								   CONCAT(t.$primaryKey, ' - ', r.$displayCol, ' - Quantité: ', t.quantite) AS label
							FROM $table t
							LEFT JOIN $refTable r ON t.$secondColumn = r.$refColumn
							ORDER BY t.$primaryKey ASC
						";
					} else {
						$query = "
							SELECT t.$primaryKey AS id, 
								   CONCAT(t.$primaryKey, ' - ', r.$displayCol) AS label
							FROM $table t
							LEFT JOIN $refTable r ON t.$secondColumn = r.$refColumn
							ORDER BY t.$primaryKey ASC
						";
					}
				} else {
					// Affichage classique si pas de clé étrangère
					if ($table === 'voitures') { // compare la valeur ET le type des expressions
						$query = "
							SELECT id_voiture AS id,
							CONCAT(id_voiture, ' - ', marque, ' ', modele, ' (', immatriculation, ') - Propriétaire: ', id_proprietaire, ' (infos confidentielles)') AS label
							FROM voitures
							ORDER BY id_voiture ASC
						";
					} else {
						$query = "
							SELECT $primaryKey AS id, CONCAT($primaryKey, ' - ', $secondColumn) AS label
							FROM $table
							ORDER BY $primaryKey ASC
						";
					}
				}
			
				$req_ord = $bdd->query($query);
				return [$primaryKey, $req_ord->fetchAll(PDO::FETCH_ASSOC)];
			}
			
			

			// Formulaire de sélection de la table
			echo '<div class="container my-4">';
			echo '<h3>Choisissez une table pour supprimer une entrée :</h3>';
			echo '<form method="post" class="mb-3">';
			echo '<select name="table" class="form-select" onchange="this.form.submit()">';
			echo '<option value="">-- Sélectionnez une table --</option>';
			foreach ($tables as $table) {
				$selected = ($table === $tableSelected) ? "selected" : ""; // compare la valeur ET le type des expressions
				echo "<option value=\"$table\" $selected>" . ucfirst(str_replace('_', ' ', $table)) . "</option>";
			}
			echo '</select>';
			echo '</form>';

			// Si une table est sélectionnée, afficher formulaire de suppression avec les entrées
			if ($tableSelected && in_array($tableSelected, $tables)) {
				list($primaryKey, $entries) = getEntries($bdd, $tableSelected);

				if (count($entries) === 0) { // compare la valeur ET le type des expressions
					echo "<p>Aucune entrée trouvée dans la table '$tableSelected'.</p>";
				} else {
					echo '<form method="post">';
					echo '<input type="hidden" name="table" value="' . htmlspecialchars($tableSelected) . '"/>';
					echo '<label for="entry" class="form-label">Choisissez l\'entrée à supprimer :</label>';
					echo '<select name="entry" id="entry" class="form-select mb-3">';
					foreach ($entries as $entry) {
						echo '<option value="' . htmlspecialchars($entry['id']) . '">' . htmlspecialchars($entry['label']) . '</option>';
					}
					
					echo '</select>';
					echo '<button type="submit" name="delete" class="btn btn-danger">Supprimer</button>';
					echo '</form>';
				}
			}

			// Affichage des messages
			if ($message) {
				echo '<div class="alert alert-success mt-3">' . htmlspecialchars($message) . '</div>';
			}
			if ($error) {
				echo '<div class="alert alert-danger mt-3">' . htmlspecialchars($error) . '</div>';
			}

			echo '</div>';
		






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