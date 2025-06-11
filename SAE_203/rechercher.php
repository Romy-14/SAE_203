<?php
	echo '<!DOCTYPE html>
	<html lang="fr">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>Accueil - Lycée professionnel Joigny - Section automobile</title>
		<link rel="icon" href="./img/2016_page_accueil_internat_de_la_reussite.png" />
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
		<link rel="stylesheet" href="./css/style.css" />
	</head>
	<body>
	<main>
	<header class="text-center my-4">
		<img src="./img/2016_page_accueil_internat_de_la_reussite.png" class="logo1" alt="Logo du lycée" />
		<img src="./img/logo-république-française.png" class="logo2" alt="Logo République française" />
		<h1>Lycée professionnel de Joigny</h1>
		<h2>Section automobile</h2>

		<nav class="navbar navbar-expand">
			<div class="container-fluid">
				<ul class="navbar-nav">
					<li class="nav-item"><a class="nav-link" href="accueil.html">Accueil</a></li>
					<li class="nav-item dropdown">
						<a class="nav-link dropdown-toggle" href="#" id="afficherDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
							Afficher une table
						</a>
						<ul class="dropdown-menu" aria-labelledby="afficherDropdown">
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="details_fournitures" class="btn btn-primary w-100 text-start">Détails fournitures</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="details_interventions" class="btn btn-primary w-100 text-start">Détails interventions</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="fournitures" class="btn btn-primary w-100 text-start">Fournitures</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="interventions" class="btn btn-primary w-100 text-start">Interventions</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="operations" class="btn btn-primary w-100 text-start">Opérations</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="proprietaires" class="btn btn-primary w-100 text-start">Propriétaires</button>
								</form>
							</a></li>
							<li><a class="dropdown-item">
								<form action="" method="post">										
									<button type="submit" name="table" value="voitures" class="btn btn-primary w-100 text-start">Voitures</button>
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
		$bdd = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
		]);
	} catch (PDOException $e) { // gestion des erreurs
		die("<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>");
	}

	$tables = [
		"fournitures" => "Fournitures",
		"interventions" => "Interventions",
		"operations" => "Opérations",
		"proprietaires" => "Propriétaires",
		"voitures" => "Voitures"
	];

	$step = $_POST['step'] ?? '1'; // sert à initialiser la variable $step avec la valeur du champ step envoyée en méthode POST, ou à défaut, à lui attribuer la valeur '1' si $_POST['step'] n’existe pas
	$tableSelected = $_POST['table'] ?? null;
	$entrySelected = $_POST['entry'] ?? null;

	function getPrimaryKey(PDO $bdd, string $table): ?string { // Elle interroge la base de données dans la table système INFORMATION_SCHEMA pour récupérer la colonne marquée comme clé primaire
		$req_ord = $bdd->prepare("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND COLUMN_KEY = 'PRI'");
		$req_ord->execute(['table' => $table]);
		return $req_ord->fetchColumn() ?: null;
	}

	function getForeignKeys(PDO $bdd, string $table): array { // Elle interroge INFORMATION_SCHEMA.KEY_COLUMN_USAGE pour savoir quelles colonnes référencent d’autres tables.
															  // Renvoie un tableau associatif où chaque clé est le nom d’une colonne clé étrangère, et chaque valeur donne la table et colonne référencée.
		$sql = "SELECT COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table AND REFERENCED_TABLE_NAME IS NOT NULL";
		$req_ord = $bdd->prepare($sql);
		$req_ord->execute(['table' => $table]);
		return $req_ord->fetchAll(PDO::FETCH_UNIQUE);
	}

	function getLabelForEntry(PDO $bdd, string $table, $id): string { // Elle prend l’ID, trouve la clé primaire, récupère une colonne "significative" (souvent la 2e colonne de la table), et retourne cette valeur avec l’ID
		$pk = getPrimaryKey($bdd, $table);
		if (!$pk) return "ID $id";

		$req_ordCols = $bdd->prepare("DESCRIBE `$table`");
		$req_ordCols->execute();
		$cols = $req_ordCols->fetchAll(PDO::FETCH_COLUMN);
		$labelCol = $cols[1] ?? $pk;

		$req_ord = $bdd->prepare("SELECT `$labelCol` FROM `$table` WHERE `$pk` = :id LIMIT 1");
		$req_ord->execute(['id' => $id]);
		$label = $req_ord->fetchColumn();
		return $label ? "$label (ID: $id)" : "ID $id";
	}

	function getEnrichedLabel(PDO $bdd, string $table, array $entry): string { // Elle récupère les clés étrangères dans cette entrée et pour chaque valeur étrangère récupère son libellé (via getLabelForEntry
		$pk = getPrimaryKey($bdd, $table);
		if (!$pk || !isset($entry[$pk])) return '';

		$foreignKeys = getForeignKeys($bdd, $table);
		$label = $entry[array_keys($entry)[1]] ?? "ID {$entry[$pk]}";

		$details = [];
		foreach ($entry as $col => $val) {
			if (isset($foreignKeys[$col]) && $val !== null) {
				$refTable = $foreignKeys[$col]['REFERENCED_TABLE_NAME'];
				$details[] = "$col → " . getLabelForEntry($bdd, $refTable, $val);
			}
		}
		$detailText = $details ? ' [' . implode(' | ', $details) . ']' : '';
		return htmlspecialchars($label . $detailText . " (ID: {$entry[$pk]})");
	}

	function displayEntryDetails(PDO $bdd, string $table, $id, int $level = 0) { // Elle récupère toutes les colonnes de l’entrée, et pour chaque clé étrangère, remplace l’ID par un libellé compréhensible (avec la fonction getLabelForEntry)
		global $tables;
		if ($level > 2) return;

		$pk = getPrimaryKey($bdd, $table);
		if (!$pk) return;

		$req_ord = $bdd->prepare("SELECT * FROM `$table` WHERE `$pk` = :id");
		$req_ord->execute(['id' => $id]);
		$data = $req_ord->fetch();
		if (!$data) return;

		$foreignKeys = getForeignKeys($bdd, $table);

		echo '<h5 class="mt-4">Détails de l\'entrée dans <strong>' . htmlspecialchars($tables[$table] ?? $table) . "</strong> (ID: $id)</h5>";
		echo '<table class="table table-bordered">';
		echo '<thead><tr><th>Champ</th><th>Valeur</th></tr></thead><tbody>';
		foreach ($data as $col => $val) {
			if (isset($foreignKeys[$col]) && $val !== null) {
				$refTable = $foreignKeys[$col]['REFERENCED_TABLE_NAME'];
				$label = getLabelForEntry($bdd, $refTable, $val);
				echo '<tr><td>' . htmlspecialchars($col) . '</td><td>' . htmlspecialchars($label) . '</td></tr>';
			} else {
				echo '<tr><td>' . htmlspecialchars($col) . '</td><td>' . htmlspecialchars($val) . '</td></tr>';
			}
		}
		echo '</tbody></table>';
	}

// Ces fonctions travaillent ensemble pour :

// 		Identifier les clés primaires et étrangères des tables.

// 		Afficher des entrées de manière lisible (pas juste des IDs).

// 		Afficher les détails des entrées en enrichissant l’affichage avec les données liées (liens entre tables).

// 		Faciliter la navigation et la compréhension des données dans l’application.


#######################################################################################################################


	echo '<div class="container my-4">';
	echo '<h3>Rechercher des informations</h3>';

	if ($step === '1' || $step === '2') { // compare la valeur ET le type des expressions
		// Formulaire sélection table
		echo '<form method="post">';
		echo '<input type="hidden" name="step" value="2">';
		echo '<label for="table" class="form-label">Choisissez une table :</label>';
		echo '<select name="table" id="table" class="form-select mb-3" onchange="this.form.submit()">';
		echo '<option value="">-- Choisissez une table --</option>';
		foreach ($tables as $t => $label) {
			$selected = ($tableSelected === $t) ? 'selected' : ''; // compare la valeur ET le type des expressions
			echo '<option value="' . htmlspecialchars($t) . '" ' . $selected . '>' . htmlspecialchars($label) . '</option>';
		}
		echo '</select>';
		echo '</form>';
	
		// Formulaire sélection entrée
		if ($step === '2' && $tableSelected && array_key_exists($tableSelected, $tables)) { // compare la valeur ET le type des expressions
			$pk = getPrimaryKey($bdd, $tableSelected);
			if ($pk) {
				$req_ord = $bdd->query("SELECT * FROM `$tableSelected` ORDER BY `$pk` ASC");
				$entries = $req_ord->fetchAll();
	
				if ($entries) {
					echo '<form method="post">';
					echo '<input type="hidden" name="step" value="3">';
					echo '<input type="hidden" name="table" value="' . htmlspecialchars($tableSelected) . '">';
					echo '<label for="entry" class="form-label">Sélectionnez une entrée :</label>';
					echo '<select name="entry" id="entry" class="form-select mb-3" required>';
					foreach ($entries as $entry) {
						$label = getEnrichedLabel($bdd, $tableSelected, $entry);
						$selected = ($entrySelected == $entry[$pk]) ? 'selected' : '';
						echo '<option value="' . htmlspecialchars($entry[$pk]) . '" ' . $selected . '>' . $label . '</option>';
					}
					echo '</select>';
					echo '<button type="submit" class="btn btn-primary">Afficher les détails</button>';
					echo '</form>';
				}
			}
		}
	}
	
	// Affichage détails si entrée choisie
	if ($step === '3' && $tableSelected && $entrySelected) { // compare la valeur ET le type des expressions
		displayEntryDetails($bdd, $tableSelected, $entrySelected);
	
		echo '<form method="post" class="mt-4">';
		echo '<input type="hidden" name="step" value="1">';
		echo '<button type="submit" class="btn btn-secondary">⬅ Retour au choix de la table et de l\'entrée</button>';
		echo '</form>';
	}
	
	echo '</div>'; // fermeture container


	echo '<footer class="text-center mt-5">
		<p>Lycée professionnel Joigny - Section automobile<br/>
		Créé par TRANQUILLE Romain & NIBERT-SIBER Jauris</p>
		</footer>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	</main>
	</body>
	</html>';
?>
