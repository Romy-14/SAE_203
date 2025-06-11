<?php
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
				<main>

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
						
									<li class="nav-item"><a class="nav-link" href="ajouter.php
									">Ajouter une table</a></li>
									<li class="nav-item"><a class="nav-link" href="supprimer.php">Supprimer une table</a></li>
									<li class="nav-item"><a class="nav-link" href="rechercher.php">Rechercher une table</a></li>
								</ul>
							</div>
						</nav>
						
						
					</header>';


#################################################################################################################################################

					// Connexion BDD 
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



					if ($_SERVER['REQUEST_METHOD'] === 'POST') { // compare la valeur ET le type des expressions
						// Si on clique sur "Retour", on réaffiche les boutons, peu importe si les champs ont été remplis ou non
						if (isset($_POST['retour'])) {
							afficherBoutonsAjout();
						}

						// formulaire propriétaires
						elseif (isset($_POST['validerPropriétaires'])) {
							$nom = $_POST['nom'] ?? '';
							$prenom = $_POST['prenom'] ?? '';
							$adresse = $_POST['adresse'] ?? '';
							$code_postal = $_POST['code_postal'] ?? '';
							$ville = $_POST['ville'] ?? '';
							$telephone = $_POST['telephone'] ?? '';
							$email = $_POST['email'] ?? '';

							if ($nom && $prenom && $adresse && $code_postal && $ville && $telephone && $email) { // si tous les formulaires sont remplis
								try {
									$requete = $bdd->prepare("INSERT INTO proprietaires (nom, prenom, adresse, `code postal`, ville, telephone, email) VALUES (?, ?, ?, ?, ?, ?, ?)");
									$requete->execute([$nom, $prenom, $adresse, $code_postal, $ville, $telephone, $email]);
									echo '<div class="alert alert-success">Propriétaire ajouté(e) avec succès</div>';
								} catch (PDOException $e) {
									echo '<div class="alert alert-danger">Problème présent dans la BDD : ' . htmlspecialchars($e->getMessage()) . '<br>Veuillez relancer votre requête</div>';
								}
							} else {
								echo '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
							}

							afficherBoutonsAjout();
						}

						// formulaire voiture
						elseif (isset($_POST['validerVoiture'])) {
							$marque = $_POST['marque'] ?? '';
							$modele = $_POST['modele'] ?? '';
							$immatriculation = $_POST['immatriculation'] ?? '';
							$id_proprietaire = $_POST['id_proprietaire'] ?? '';

							if ($marque && $modele && $immatriculation && $id_proprietaire) { // si tous les formulaires sont remplis
								try {
									$requete = $bdd->prepare("INSERT INTO voitures (marque, modele, immatriculation, id_proprietaire) VALUES (?, ?, ?, ?)");
									$requete->execute([$marque, $modele, $immatriculation, $id_proprietaire]);
									echo '<div class="alert alert-success">Voiture ajoutée avec succès</div>';
								} catch (PDOException $e) {
									echo '<div class="alert alert-danger">Problème présent dans la BDD : ' . htmlspecialchars($e->getMessage()) . '<br>Veuillez relancer votre requête</div>';
								}
							} else {
								echo '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
							}

							afficherBoutonsAjout();
						}

						// formulaire fourniture
						elseif (isset($_POST['validerFourniture'])) {
							$nom = $_POST['produit'] ?? '';
							$prix = $_POST['prix_TTC'] ?? '';

							if ($nom && $prix) { // si tous les formulaires sont remplis
								try {
									$requete = $bdd->prepare("INSERT INTO fournitures (produit, prix_TTC) VALUES (?, ?)");
									$requete->execute([$nom, $prix]);
									echo '<div class="alert alert-success">Fourniture ajoutée avec succès</div>';
								} catch (PDOException $e) {
									echo '<div class="alert alert-danger">Problème présent dans la BDD : ' . htmlspecialchars($e->getMessage()) . '<br>Veuillez relancer votre requête</div>';
								}
							} else {
								echo '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
							}

							afficherBoutonsAjout();
						}

						// formulaire intervention
						elseif (isset($_POST['validerIntervention'])) {
							$id_voiture = $_POST['id_voiture'] ?? '';
							$date_rdv = $_POST['date_rdv'] ?? '';
							$kimometrage = $_POST['kimometrage'] ?? '';
							$id_operation = $_POST['id_operation'] ?? '';

							if ($id_voiture && $date_rdv && $kimometrage && $id_operation) { // si tous les formulaires sont remplis
								try {
									$requete = $bdd->prepare("INSERT INTO interventions (id_voiture, date_rdv, kimometrage, id_operation) VALUES (?, ?, ?, ?)");
									$requete->execute([$id_voiture, $date_rdv, $kimometrage, $id_operation]);
									echo '<div class="alert alert-success">Intervention ajoutée avec succès</div>';
								} catch (PDOException $e) {
									echo '<div class="alert alert-danger">Problème présent dans la BDD : ' . htmlspecialchars($e->getMessage()) . '<br>Veuillez relancer votre requête</div>';
								}
							} else {
								echo '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
							}

							afficherBoutonsAjout();
						}

						// formulaire opération
						elseif (isset($_POST['validerOperation'])) {
							$type = $_POST['type_operation'] ?? '';
							$temps = $_POST['temps_operation'] ?? '';
							$prix = $_POST['prix_operation'] ?? '';

							if ($type && $temps && $prix) { // si tous les formulaires sont remplis
								try {
									$requete = $bdd->prepare("INSERT INTO operations (type_operation, temps_operation, prix_operation) VALUES (?, ?, ?)");
									$requete->execute([$type, $temps, $prix]);
									echo '<div class="alert alert-success">Opération ajoutée avec succès</div>';
								} catch (PDOException $e) {
									echo '<div class="alert alert-danger">Problème présent dans la BDD : ' . htmlspecialchars($e->getMessage()) . '<br>Veuillez relancer votre requête</div>';
								}
							} else {
								echo '<div class="alert alert-warning">Veuillez remplir tous les champs</div>';
							}

							afficherBoutonsAjout();
						}




	##################################################################################################################################

						
						// Affichage du formulaire propriétaires
						elseif (isset($_POST['ajouterPropriétaires'])) {
							echo '<h2>Ajouter un(e) propriétaire</h2>
							<form method="post">

								<div class="mb-3">
									<label class="form-label">Nom</label>
									<input type="text" class="form-control" name="nom">
								</div>

								<div class="mb-3">
									<label class="form-label">Prénom</label>
									<input type="text" class="form-control" name="prenom">
								</div>

								<div class="mb-3">
									<label class="form-label">Adresse</label>
									<input type="text" class="form-control" name="adresse">
								</div>

								<div class="mb-3">
									<label class="form-label">Code Postal</label>
									<input type="number" class="form-control" name="code_postal">
								</div>

								<div class="mb-3">
									<label class="form-label">Ville</label>
									<input type="text" class="form-control" name="ville">
								</div>

								<div class="mb-3">
									<label class="form-label">Téléphone</label>
									<input type="text" class="form-control" name="telephone">
								</div>

								<div class="mb-3">
									<label class="form-label">E-mail</label>
									<input type="email" class="form-control" name="email">
								</div>

								<button type="submit" name="validerPropriétaires" class="btn btn-success">Valider</button>
								<button type="submit" name="retour" class="btn btn-secondary">Retour</button>
							</form>';
						}
						
						// Affichage du formulaire voiture
						elseif (isset($_POST['ajouterVoiture'])) {
						echo '<h2>Ajouter une voiture</h2>
						<form method="post">

							<div class="mb-3">
								<label class="form-label">Marque</label>
								<input type="text" class="form-control" name="marque">
							</div>

							<div class="mb-3">
								<label class="form-label">Modèle</label>
								<input type="text" class="form-control" name="modele">
							</div>

							<div class="mb-3">
								<label class="form-label">Immatriculation</label>
								<input type="text" class="form-control" name="immatriculation">
							</div>

							<div class="mb-3">
								<label class="form-label">Propriétaire</label>
								<select class="form-control" name="id_proprietaire" required>';
								
									// Récupération des propriétaires existants (triés)
									$req_ord = $bdd->query("SELECT id_proprietaire, nom, prenom FROM proprietaires ORDER BY id_proprietaire ASC");
									while ($row = $req_ord->fetch(PDO::FETCH_ASSOC)) {
										$id = htmlspecialchars($row['id_proprietaire']);
										$nom = htmlspecialchars($row['nom']);
										$prenom = htmlspecialchars($row['prenom']);
										echo "<option value=\"$id\">$nom $prenom (ID: $id)</option>";
									}

								echo '</select>
							</div>

							<button type="submit" name="validerVoiture" class="btn btn-success">Valider</button>
							<button type="submit" name="retour" class="btn btn-secondary">Retour</button>
						</form>';
						}


						// Affichage du formulaire fourniture
						elseif (isset($_POST['ajouterFourniture'])) {
							echo '<h2>Ajouter une fourniture</h2>
							<form method="post">
								<div class="mb-3">
									<label class="form-label">Nom du produit</label>
									<input type="text" class="form-control" name="produit">
								</div>
								
								<div class="mb-3">
									<label class="form-label">Prix (€)</label>
									<input type="number" step="0.01" class="form-control" name="prix_TTC" min="0.00">
								</div>
								<button type="submit" name="validerFourniture" class="btn btn-success">Valider</button>
								<button type="submit" name="retour" class="btn btn-secondary">Retour</button>
							</form>';
						}

						

						// Affichage du formulaire intervention
						elseif (isset($_POST['ajouterIntervention'])) {
							echo '<h2>Ajouter une intervention</h2>
							<form method="post">

								<div class="mb-3">
									<label class="form-label">Voiture</label>
									<select class="form-control" name="id_voiture" required>';
										
										// Récupération des voitures (triées)
										$req_ord = $bdd->query("SELECT id_voiture, marque, modele, immatriculation, id_proprietaire FROM voitures ORDER BY id_proprietaire ASC");
										while ($row = $req_ord->fetch(PDO::FETCH_ASSOC)) {
											$id = htmlspecialchars($row['id_voiture']);
											$label = htmlspecialchars($row['marque'] . ' ' . $row['modele'] . ' (' . $row['immatriculation'] . ') - Propriétaire ID: ' . $row['id_proprietaire']);
											echo "<option value=\"$id\">$label</option>";
										}
									echo '</select>
								</div>

								<div class="mb-3">
									<label class="form-label">Date du rendez-vous</label>
									<input type="date" class="form-control" name="date_rdv" required>
								</div>

								<div class="mb-3">
									<label class="form-label">Kilométrage</label>
									<input type="number" class="form-control" name="kimometrage" required>
								</div>

								<div class="mb-3">
									<label class="form-label">Opération</label>
									<select class="form-control" name="id_operation" required>';
										
										// Récupération des opérations (triées)
										$req_ord = $bdd->query("SELECT id_operation, type_operation, temps_operation, prix_operation FROM operations ORDER BY id_operation ASC");
										while ($row = $req_ord->fetch(PDO::FETCH_ASSOC)) {
											$id = htmlspecialchars($row['id_operation']);
											$type = htmlspecialchars($row['type_operation']);
											$temps = htmlspecialchars($row['temps_operation']);
											$prix = htmlspecialchars($row['prix_operation']);
											$label = "$type - $temps mn - $prix €";
											echo "<option value=\"$id\">$label (ID: $id)</option>";
										}

									echo '</select>
								</div>

								<button type="submit" name="validerIntervention" class="btn btn-success">Valider</button>
								<button type="submit" name="retour" class="btn btn-secondary">Retour</button>
							</form>';
						}

						// Affichage du formulaire opération
						elseif (isset($_POST['ajouterOperation'])) {
							echo '<h2>Ajouter une opération</h2>
							<form method="post">

								<div class="mb-3">
									<label class="form-label">Type d\'opération</label>
									<input type="text" class="form-control" name="type_operation">
								</div>

								<div class="mb-3">
									<label class="form-label">Temps (minutes)</label>
									<input type="number" class="form-control" name="temps_operation" min="0">
								</div>

								<div class="mb-3">
									<label class="form-label">Prix (€)</label>
									<input type="number" step="0.01" class="form-control" name="prix_operation" min="0">
								</div>

								<button type="submit" name="validerOperation" class="btn btn-success">Valider</button>
								<button type="submit" name="retour" class="btn btn-secondary">Retour</button>
							</form>';
						}

					} else {
						afficherBoutonsAjout();
					}

					

					

				echo '</main>
				</body>
			</html>';

		

			// Fonction d’affichage des boutons initiaux
			function afficherBoutonsAjout() {
				echo '<h2>Ajouter des éléments dans une table</h2>
				<form method="post" class="d-flex gap-3">
					<button type="submit" name="ajouterVoiture" class="btn btn-primary">Ajouter une voiture</button>
					<button type="submit" name="ajouterFourniture" class="btn btn-primary">Ajouter une fourniture</button>
					<button type="submit" name="ajouterPropriétaires" class="btn btn-primary">Ajouter un(e) propriétaire</button>
					<button type="submit" name="ajouterIntervention" class="btn btn-primary">Ajouter une intervention</button>
					<button type="submit" name="ajouterOperation" class="btn btn-primary">Ajouter une opération</button>

				</form>';
			}



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