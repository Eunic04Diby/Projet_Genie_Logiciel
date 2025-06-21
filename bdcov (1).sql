-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 21 juin 2025 à 16:14
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdcov`
--

-- --------------------------------------------------------

--
-- Structure de la table `action`
--

CREATE TABLE `action` (
  `id_action` int(11) NOT NULL,
  `lib_action` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `affecter`
--

CREATE TABLE `affecter` (
  `id_ens` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `id_jurys` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `annee_academique`
--

CREATE TABLE `annee_academique` (
  `id_ac` int(11) NOT NULL,
  `dte_deb` date NOT NULL,
  `dte_fin` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `approuver`
--

CREATE TABLE `approuver` (
  `id_ens` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `dte_app` date NOT NULL,
  `com_appr` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `avoir`
--

CREATE TABLE `avoir` (
  `id_grade` int(11) NOT NULL,
  `id_ens` int(11) NOT NULL,
  `dte_grd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `compte_rendu`
--

CREATE TABLE `compte_rendu` (
  `id_cr` int(11) NOT NULL,
  `nom_cr` text NOT NULL,
  `dte_cr` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `deposer`
--

CREATE TABLE `deposer` (
  `num_etu` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `dte_dep` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ecue`
--

CREATE TABLE `ecue` (
  `id_ECUE` int(11) NOT NULL,
  `lib_ECUE` varchar(60) NOT NULL,
  `credit_ECUE` int(11) NOT NULL,
  `id_UE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignant`
--

CREATE TABLE `enseignant` (
  `id_ens` int(11) NOT NULL,
  `nom_ens` varchar(60) NOT NULL,
  `prenoms_ens` varchar(60) NOT NULL,
  `login_ens` varchar(60) NOT NULL,
  `mdp_ens` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE `entreprise` (
  `id_entr` int(11) NOT NULL,
  `lib_entr` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id_entr`, `lib_entr`) VALUES
(122, 'mpo'),
(125, 'GTBANK'),
(145, 'LABELLE');

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `Num_Etud` int(11) NOT NULL,
  `Nom_Etud` varchar(60) NOT NULL,
  `Prenom_Etud` varchar(60) NOT NULL,
  `Date_naiss_Etud` date NOT NULL,
  `Login_Etud` varchar(60) NOT NULL,
  `Mdp_Etud` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `evaluer`
--

CREATE TABLE `evaluer` (
  `num_etu` int(11) NOT NULL,
  `id_ens` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `dte_eval` date NOT NULL,
  `note` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `faire_stage`
--

CREATE TABLE `faire_stage` (
  `id_entr` int(11) NOT NULL,
  `num_etu` varchar(60) NOT NULL,
  `date_deb_stage` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `fonction`
--

CREATE TABLE `fonction` (
  `id_fonct` int(11) NOT NULL,
  `nom_fonct` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `fonction`
--

INSERT INTO `fonction` (`id_fonct`, `nom_fonct`) VALUES
(25, 'roi'),
(74, 'skek');

-- --------------------------------------------------------

--
-- Structure de la table `grade`
--

CREATE TABLE `grade` (
  `id_grade` int(11) NOT NULL,
  `nom_grade` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `grade`
--

INSERT INTO `grade` (`id_grade`, `nom_grade`) VALUES
(13, 'Grade3');

-- --------------------------------------------------------

--
-- Structure de la table `groupe_utilisateur`
--

CREATE TABLE `groupe_utilisateur` (
  `id_gu` int(11) NOT NULL,
  `lib_gu` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `groupe_utilisateur`
--

INSERT INTO `groupe_utilisateur` (`id_gu`, `lib_gu`) VALUES
(1, 'gu13');

-- --------------------------------------------------------

--
-- Structure de la table `inscrire`
--

CREATE TABLE `inscrire` (
  `num_etu` int(11) NOT NULL,
  `id_ac` int(11) NOT NULL,
  `id_niv_etu` int(11) NOT NULL,
  `dte_insc` date NOT NULL,
  `montant_insc` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveau_acces_donnees`
--

CREATE TABLE `niveau_acces_donnees` (
  `id_niv_acc` int(11) NOT NULL,
  `lib_niv_acc` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveau_approbation`
--

CREATE TABLE `niveau_approbation` (
  `id_approb` int(11) NOT NULL,
  `lib_approb` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveau_etude`
--

CREATE TABLE `niveau_etude` (
  `id_niv_etu` int(11) NOT NULL,
  `lib_niv_etu` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `niveau_etude`
--

INSERT INTO `niveau_etude` (`id_niv_etu`, `lib_niv_etu`) VALUES
(1, 'NE012');

-- --------------------------------------------------------

--
-- Structure de la table `occuper`
--

CREATE TABLE `occuper` (
  `id_fonct` int(11) NOT NULL,
  `id_ens` int(11) NOT NULL,
  `dte_occup` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personnel_admin`
--

CREATE TABLE `personnel_admin` (
  `id_pers` int(11) NOT NULL,
  `nom_pers` varchar(60) NOT NULL,
  `prenoms_pers` varchar(60) NOT NULL,
  `email_pers` varchar(60) NOT NULL,
  `date_naiss_pers` date NOT NULL,
  `genre_pers` varchar(30) NOT NULL,
  `poste_pers` varchar(30) NOT NULL,
  `date` date NOT NULL,
  `date_embauche_pers` date NOT NULL,
  `telephone_pers` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pister`
--

CREATE TABLE `pister` (
  `id_util` int(11) NOT NULL,
  `id_trait` int(11) NOT NULL,
  `dte_piste` int(11) NOT NULL,
  `acceder` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `posseder`
--

CREATE TABLE `posseder` (
  `id_util` int(11) NOT NULL,
  `id_gu` int(11) NOT NULL,
  `dte_poss` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rapport_etudiant`
--

CREATE TABLE `rapport_etudiant` (
  `id_rapport` int(11) NOT NULL,
  `nom_rapport` varchar(60) NOT NULL,
  `dte_rapport` date NOT NULL,
  `theme_mem` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rattacher`
--

CREATE TABLE `rattacher` (
  `id_gu` int(11) NOT NULL,
  `id_trait` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rendre`
--

CREATE TABLE `rendre` (
  `id_cr` int(11) NOT NULL,
  `id_ens` int(11) NOT NULL,
  `dte_env` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `rreclamation`
--

CREATE TABLE `rreclamation` (
  `ID_Recl` int(11) NOT NULL,
  `Num_Etud` int(11) NOT NULL,
  `Date_Recl` date NOT NULL,
  `Objet_Recl` varchar(100) NOT NULL,
  `Desc_Recl` text NOT NULL,
  `Stat_Recl` varchar(30) NOT NULL,
  `Reponse_Recl` text NOT NULL,
  `Date_Reponse` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `specialite`
--

CREATE TABLE `specialite` (
  `id_spe` int(11) NOT NULL,
  `lib_spe` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `specialite`
--

INSERT INTO `specialite` (`id_spe`, `lib_spe`) VALUES
(1, 'SPE012');

-- --------------------------------------------------------

--
-- Structure de la table `status_jury`
--

CREATE TABLE `status_jury` (
  `id_jury` int(11) NOT NULL,
  `lib_jury` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `status_jury`
--

INSERT INTO `status_jury` (`id_jury`, `lib_jury`) VALUES
(1, 'ST01');

-- --------------------------------------------------------

--
-- Structure de la table `traitement`
--

CREATE TABLE `traitement` (
  `id_trait` int(11) NOT NULL,
  `lib_trait` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `traitement`
--

INSERT INTO `traitement` (`id_trait`, `lib_trait`) VALUES
(1, 'TRAIT01');

-- --------------------------------------------------------

--
-- Structure de la table `type_utilisateur`
--

CREATE TABLE `type_utilisateur` (
  `id_tu` int(11) NOT NULL,
  `lib_tu` varchar(60) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `type_utilisateur`
--

INSERT INTO `type_utilisateur` (`id_tu`, `lib_tu`) VALUES
(1, 'TU012');

-- --------------------------------------------------------

--
-- Structure de la table `ue`
--

CREATE TABLE `ue` (
  `id_UE` int(11) NOT NULL,
  `lib_UE` varchar(60) NOT NULL,
  `credit_UE` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id_util` int(11) NOT NULL,
  `login_util` varchar(60) NOT NULL,
  `mdp_util` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `valider`
--

CREATE TABLE `valider` (
  `id_ens` int(11) NOT NULL,
  `id_rapport` int(11) NOT NULL,
  `dte_val` date NOT NULL,
  `com_val` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `action`
--
ALTER TABLE `action`
  ADD PRIMARY KEY (`id_action`);

--
-- Index pour la table `affecter`
--
ALTER TABLE `affecter`
  ADD PRIMARY KEY (`id_jurys`),
  ADD KEY `id_ens` (`id_ens`),
  ADD KEY `id_rapport` (`id_rapport`);

--
-- Index pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  ADD PRIMARY KEY (`id_ac`);

--
-- Index pour la table `approuver`
--
ALTER TABLE `approuver`
  ADD KEY `Contrainte_ens` (`id_ens`),
  ADD KEY `id_rapport` (`id_rapport`);

--
-- Index pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD KEY `id_ens` (`id_ens`),
  ADD KEY `id_grade` (`id_grade`);

--
-- Index pour la table `compte_rendu`
--
ALTER TABLE `compte_rendu`
  ADD PRIMARY KEY (`id_cr`);

--
-- Index pour la table `deposer`
--
ALTER TABLE `deposer`
  ADD KEY `Contrainte_etu` (`num_etu`),
  ADD KEY `Contrainte_rapport` (`id_rapport`);

--
-- Index pour la table `ecue`
--
ALTER TABLE `ecue`
  ADD PRIMARY KEY (`id_ECUE`),
  ADD KEY `id_UE` (`id_UE`);

--
-- Index pour la table `enseignant`
--
ALTER TABLE `enseignant`
  ADD PRIMARY KEY (`id_ens`);

--
-- Index pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD PRIMARY KEY (`id_entr`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`Num_Etud`);

--
-- Index pour la table `evaluer`
--
ALTER TABLE `evaluer`
  ADD KEY `num_etu` (`num_etu`),
  ADD KEY `id_ens` (`id_ens`),
  ADD KEY `id_rapport` (`id_rapport`);

--
-- Index pour la table `faire_stage`
--
ALTER TABLE `faire_stage`
  ADD KEY `Entreprise_etudiant` (`id_entr`);

--
-- Index pour la table `fonction`
--
ALTER TABLE `fonction`
  ADD PRIMARY KEY (`id_fonct`);

--
-- Index pour la table `grade`
--
ALTER TABLE `grade`
  ADD PRIMARY KEY (`id_grade`);

--
-- Index pour la table `groupe_utilisateur`
--
ALTER TABLE `groupe_utilisateur`
  ADD PRIMARY KEY (`id_gu`);

--
-- Index pour la table `inscrire`
--
ALTER TABLE `inscrire`
  ADD KEY `Etudian_inscrit` (`num_etu`),
  ADD KEY `Etudiant_année_academique` (`id_ac`),
  ADD KEY `Etudiant_niveau_etude` (`id_niv_etu`);

--
-- Index pour la table `niveau_acces_donnees`
--
ALTER TABLE `niveau_acces_donnees`
  ADD PRIMARY KEY (`id_niv_acc`);

--
-- Index pour la table `niveau_approbation`
--
ALTER TABLE `niveau_approbation`
  ADD PRIMARY KEY (`id_approb`);

--
-- Index pour la table `niveau_etude`
--
ALTER TABLE `niveau_etude`
  ADD PRIMARY KEY (`id_niv_etu`);

--
-- Index pour la table `occuper`
--
ALTER TABLE `occuper`
  ADD KEY `id_ens` (`id_ens`),
  ADD KEY `id_fonct` (`id_fonct`);

--
-- Index pour la table `personnel_admin`
--
ALTER TABLE `personnel_admin`
  ADD PRIMARY KEY (`id_pers`);

--
-- Index pour la table `pister`
--
ALTER TABLE `pister`
  ADD KEY `id_util` (`id_util`),
  ADD KEY `id_trait` (`id_trait`);

--
-- Index pour la table `posseder`
--
ALTER TABLE `posseder`
  ADD KEY `id_util` (`id_util`),
  ADD KEY `id_gu` (`id_gu`);

--
-- Index pour la table `rapport_etudiant`
--
ALTER TABLE `rapport_etudiant`
  ADD PRIMARY KEY (`id_rapport`);

--
-- Index pour la table `rattacher`
--
ALTER TABLE `rattacher`
  ADD KEY `id_gu` (`id_gu`),
  ADD KEY `id_trait` (`id_trait`);

--
-- Index pour la table `rendre`
--
ALTER TABLE `rendre`
  ADD KEY `Prof_rend` (`id_ens`);

--
-- Index pour la table `rreclamation`
--
ALTER TABLE `rreclamation`
  ADD PRIMARY KEY (`ID_Recl`),
  ADD KEY `Foreigh` (`Num_Etud`);

--
-- Index pour la table `specialite`
--
ALTER TABLE `specialite`
  ADD PRIMARY KEY (`id_spe`);

--
-- Index pour la table `status_jury`
--
ALTER TABLE `status_jury`
  ADD PRIMARY KEY (`id_jury`);

--
-- Index pour la table `traitement`
--
ALTER TABLE `traitement`
  ADD PRIMARY KEY (`id_trait`);

--
-- Index pour la table `type_utilisateur`
--
ALTER TABLE `type_utilisateur`
  ADD PRIMARY KEY (`id_tu`);

--
-- Index pour la table `ue`
--
ALTER TABLE `ue`
  ADD PRIMARY KEY (`id_UE`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id_util`);

--
-- Index pour la table `valider`
--
ALTER TABLE `valider`
  ADD KEY `id_ens` (`id_ens`),
  ADD KEY `id_rapport` (`id_rapport`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `action`
--
ALTER TABLE `action`
  MODIFY `id_action` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT pour la table `affecter`
--
ALTER TABLE `affecter`
  MODIFY `id_jurys` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  MODIFY `id_ac` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `compte_rendu`
--
ALTER TABLE `compte_rendu`
  MODIFY `id_cr` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `ecue`
--
ALTER TABLE `ecue`
  MODIFY `id_ECUE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `enseignant`
--
ALTER TABLE `enseignant`
  MODIFY `id_ens` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `entreprise`
--
ALTER TABLE `entreprise`
  MODIFY `id_entr` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT pour la table `fonction`
--
ALTER TABLE `fonction`
  MODIFY `id_fonct` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT pour la table `grade`
--
ALTER TABLE `grade`
  MODIFY `id_grade` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `groupe_utilisateur`
--
ALTER TABLE `groupe_utilisateur`
  MODIFY `id_gu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `niveau_acces_donnees`
--
ALTER TABLE `niveau_acces_donnees`
  MODIFY `id_niv_acc` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `niveau_approbation`
--
ALTER TABLE `niveau_approbation`
  MODIFY `id_approb` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `niveau_etude`
--
ALTER TABLE `niveau_etude`
  MODIFY `id_niv_etu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `personnel_admin`
--
ALTER TABLE `personnel_admin`
  MODIFY `id_pers` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `rapport_etudiant`
--
ALTER TABLE `rapport_etudiant`
  MODIFY `id_rapport` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `specialite`
--
ALTER TABLE `specialite`
  MODIFY `id_spe` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `status_jury`
--
ALTER TABLE `status_jury`
  MODIFY `id_jury` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `traitement`
--
ALTER TABLE `traitement`
  MODIFY `id_trait` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `type_utilisateur`
--
ALTER TABLE `type_utilisateur`
  MODIFY `id_tu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `ue`
--
ALTER TABLE `ue`
  MODIFY `id_UE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id_util` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `affecter`
--
ALTER TABLE `affecter`
  ADD CONSTRAINT `affecter_ibfk_1` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `affecter_ibfk_2` FOREIGN KEY (`id_rapport`) REFERENCES `rapport_etudiant` (`id_rapport`);

--
-- Contraintes pour la table `approuver`
--
ALTER TABLE `approuver`
  ADD CONSTRAINT `Contrainte_ens` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `approuver_ibfk_1` FOREIGN KEY (`id_rapport`) REFERENCES `rapport_etudiant` (`id_rapport`);

--
-- Contraintes pour la table `avoir`
--
ALTER TABLE `avoir`
  ADD CONSTRAINT `avoir_ibfk_1` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `avoir_ibfk_2` FOREIGN KEY (`id_grade`) REFERENCES `grade` (`id_grade`);

--
-- Contraintes pour la table `deposer`
--
ALTER TABLE `deposer`
  ADD CONSTRAINT `Contrainte_etu` FOREIGN KEY (`num_etu`) REFERENCES `etudiant` (`Num_Etud`),
  ADD CONSTRAINT `Contrainte_rapport` FOREIGN KEY (`id_rapport`) REFERENCES `rapport_etudiant` (`id_rapport`);

--
-- Contraintes pour la table `ecue`
--
ALTER TABLE `ecue`
  ADD CONSTRAINT `UE_ECUE` FOREIGN KEY (`id_UE`) REFERENCES `ue` (`id_UE`);

--
-- Contraintes pour la table `evaluer`
--
ALTER TABLE `evaluer`
  ADD CONSTRAINT `evaluer_ibfk_1` FOREIGN KEY (`num_etu`) REFERENCES `etudiant` (`Num_Etud`),
  ADD CONSTRAINT `evaluer_ibfk_2` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `evaluer_ibfk_3` FOREIGN KEY (`id_rapport`) REFERENCES `rapport_etudiant` (`id_rapport`);

--
-- Contraintes pour la table `faire_stage`
--
ALTER TABLE `faire_stage`
  ADD CONSTRAINT `Entreprise_etudiant` FOREIGN KEY (`id_entr`) REFERENCES `entreprise` (`id_entr`);

--
-- Contraintes pour la table `inscrire`
--
ALTER TABLE `inscrire`
  ADD CONSTRAINT `Etudian_inscrit` FOREIGN KEY (`num_etu`) REFERENCES `etudiant` (`Num_Etud`),
  ADD CONSTRAINT `Etudiant_année_academique` FOREIGN KEY (`id_ac`) REFERENCES `annee_academique` (`id_ac`),
  ADD CONSTRAINT `Etudiant_niveau_etude` FOREIGN KEY (`id_niv_etu`) REFERENCES `niveau_etude` (`id_niv_etu`);

--
-- Contraintes pour la table `occuper`
--
ALTER TABLE `occuper`
  ADD CONSTRAINT `occuper_ibfk_1` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `occuper_ibfk_2` FOREIGN KEY (`id_fonct`) REFERENCES `fonction` (`id_fonct`);

--
-- Contraintes pour la table `pister`
--
ALTER TABLE `pister`
  ADD CONSTRAINT `pister_ibfk_1` FOREIGN KEY (`id_util`) REFERENCES `utilisateur` (`id_util`),
  ADD CONSTRAINT `pister_ibfk_2` FOREIGN KEY (`id_trait`) REFERENCES `traitement` (`id_trait`);

--
-- Contraintes pour la table `posseder`
--
ALTER TABLE `posseder`
  ADD CONSTRAINT `posseder_ibfk_1` FOREIGN KEY (`id_util`) REFERENCES `utilisateur` (`id_util`),
  ADD CONSTRAINT `posseder_ibfk_2` FOREIGN KEY (`id_gu`) REFERENCES `groupe_utilisateur` (`id_gu`);

--
-- Contraintes pour la table `rattacher`
--
ALTER TABLE `rattacher`
  ADD CONSTRAINT `rattacher_ibfk_1` FOREIGN KEY (`id_gu`) REFERENCES `groupe_utilisateur` (`id_gu`),
  ADD CONSTRAINT `rattacher_ibfk_2` FOREIGN KEY (`id_trait`) REFERENCES `traitement` (`id_trait`);

--
-- Contraintes pour la table `rendre`
--
ALTER TABLE `rendre`
  ADD CONSTRAINT `Prof_rend` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`);

--
-- Contraintes pour la table `valider`
--
ALTER TABLE `valider`
  ADD CONSTRAINT `valider_ibfk_1` FOREIGN KEY (`id_ens`) REFERENCES `enseignant` (`id_ens`),
  ADD CONSTRAINT `valider_ibfk_2` FOREIGN KEY (`id_rapport`) REFERENCES `rapport_etudiant` (`id_rapport`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
