-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le :  sam. 22 avr. 2023 à 19:25
-- Version du serveur :  5.7.17
-- Version de PHP :  7.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `sae2.03-2023-voitures-garage`
--

-- --------------------------------------------------------

--
-- Structure de la table `proprietaires`
--

CREATE TABLE `proprietaires` (
  `id_proprietaire` int(11) NOT NULL,
  `nom` varchar(250) DEFAULT NULL,
  `prenom` varchar(250) DEFAULT NULL,
  `adresse` varchar(250) DEFAULT NULL,
  `code postal` varchar(250) DEFAULT NULL,
  `ville` varchar(250) DEFAULT NULL,
  `telephone` varchar(15) DEFAULT NULL,
  `email` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `proprietaires`
--

INSERT INTO `proprietaires` (`id_proprietaire`, `nom`, `prenom`, `adresse`, `code postal`, `ville`, `telephone`, `email`) VALUES
(1, 'toto', 'toto', 'toto', '89000', 'Auxerre', '0304050607', 'toto@gmail.com');

-- --------------------------------------------------------

--
-- Structure de la table `voitures`
--

CREATE TABLE `voitures` (
  `id_voiture` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `immatriculation` varchar(12) NOT NULL,
  `id_proprietaire` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `interventions`
--

CREATE TABLE `interventions` (
  `id_intervention` int(11) NOT NULL,
  `id_voiture` int(11) NOT NULL,
  `date_rdv` date NOT NULL,
  `kimometrage` int(12) NOT NULL,
  `id_operation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `details_interventions`
--

CREATE TABLE `details_interventions` (
  `id_intervention` int(11) NOT NULL,
  `id_operation` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `operations`
--

CREATE TABLE `operations` (
  `id_operation` int(11) NOT NULL,
  `type_operation` varchar(255) NOT NULL,
  `temps_operation` float(6.2) NOT NULL,
  `prix_operation` float(8.2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `details_fournitures`
--

CREATE TABLE `details_fournitures` (
  `id_operation` int(11) NOT NULL,
  `id_fourniture` int(11) NOT NULL,
  `quantite` float(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------


--
-- Structure de la table `fournitures`
--

CREATE TABLE `fournitures` (
  `id_fourniture` int(11) NOT NULL,
  `produit` varchar(255) NOT NULL,
  `prix_TTC` float(8.2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `fournitures` (`id_fourniture`, `produit`, `prix_TTC`) VALUES
(1, 'huile ELF Evolution C4 5w30 bidon 5l', 53.40),
(2, 'huile ELF Evolution C4 5w30 bidon 1l', 13.80),
(3, 'huile ELF Evolution C3 5w30 bidon 5l', 54.20),
(4, 'huile ELF Evolution C3 5w30 bidon 1l', 11.90);


-- --------------------------------------------------------

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `proprietaires`
--
ALTER TABLE `proprietaires`
  ADD PRIMARY KEY (`id_proprietaire`);

--
-- Index pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD PRIMARY KEY (`id_voiture`),
  ADD KEY `id_proprietaire` (`id_proprietaire`);

--
-- Index pour la table `interventions`
--
ALTER TABLE `interventions`
  ADD PRIMARY KEY (`id_intervention`),
  ADD KEY `id_voiture` (`id_voiture`),
  ADD KEY `id_operation` (`id_operation`);
  
--
-- Index pour la table `details_interventions`
--
ALTER TABLE `details_interventions`
  ADD KEY `id_intervention` (`id_intervention`),
  ADD KEY `id_operation` (`id_operation`);

--
-- Index pour la table `operations`
--
ALTER TABLE `operations`
  ADD PRIMARY KEY (`id_operation`);

--
-- Index pour la table `details_fournitures`
--
ALTER TABLE `details_fournitures`
  ADD KEY `id_operation` (`id_operation`),
  ADD KEY `id_fourniture` (`id_fourniture`);

--
-- Index pour la table `fournitures`
--
ALTER TABLE `fournitures`
  ADD PRIMARY KEY (`id_fourniture`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `proprietaires`
--
ALTER TABLE `proprietaires`
  MODIFY `id_proprietaire` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
  
--
-- AUTO_INCREMENT pour la table `voitures`
--
ALTER TABLE `voitures`
  MODIFY `id_voiture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT pour la table `interventions`
--
ALTER TABLE `interventions`
  MODIFY `id_intervention` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT pour la table `interventions`
--
ALTER TABLE `operations`
  MODIFY `id_operation` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=0;

--
-- AUTO_INCREMENT pour la table `fournitures`
--
ALTER TABLE `fournitures`
  MODIFY `id_fourniture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
  
--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `voitures`
--
ALTER TABLE `voitures`
  ADD CONSTRAINT `voitures_ibfk_1` FOREIGN KEY (`id_proprietaire`) REFERENCES `proprietaires` (`id_proprietaire`);

--
-- Contraintes pour la table `interventions`
--
ALTER TABLE `interventions`
  ADD CONSTRAINT `interventions_ibfk_1` FOREIGN KEY (`id_voiture`) REFERENCES `voitures` (`id_voiture`);

--
-- Contraintes pour la table `detail_interventions`
--
ALTER TABLE `details_interventions`
  ADD CONSTRAINT `details_interventions_ibfk_1` FOREIGN KEY (`id_intervention`) REFERENCES `interventions` (`id_intervention`),
  ADD CONSTRAINT `details_interventions_ibfk_2` FOREIGN KEY (`id_operation`) REFERENCES `operations` (`id_operation`);

--
-- Contraintes pour la table `operations`
--
/*ALTER TABLE `operations`
ADD CONSTRAINT `operations_ibfk_1` FOREIGN KEY (`id_fourniture`) REFERENCES `fournitures` (`id_fourniture`);
*/
  
--
-- Contraintes pour la table `detail_fournitures`
--
ALTER TABLE `details_fournitures`
  ADD CONSTRAINT `details_fournitures_ibfk_1` FOREIGN KEY (`id_operation`) REFERENCES `operations` (`id_operation`),
  ADD CONSTRAINT `details_fournitures_ibfk_2` FOREIGN KEY (`id_fourniture`) REFERENCES `fournitures` (`id_fourniture`);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
