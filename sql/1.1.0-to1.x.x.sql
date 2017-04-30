--
-- Structure de la table `pledge`
--

CREATE TABLE `pledge` (
  `id` int(11) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_amended` datetime DEFAULT NULL,
  `date_deleted` datetime DEFAULT NULL,
  `date_completed` datetime DEFAULT NULL,
  `civility` varchar(4) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `zipcode` varchar(10) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `key_edit` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL,
  `reference` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `pledge`
--
ALTER TABLE `pledge`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `pledge`
--
ALTER TABLE `pledge`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;


ALTER TABLE `group` ADD `iban` VARCHAR(255) NULL AFTER `bank_city`;