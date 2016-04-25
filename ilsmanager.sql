-- MySQL dump 10.13  Distrib 5.1.73, for debian-linux-gnu (i486)
--
-- Host: localhost    Database: ilsmanager
-- ------------------------------------------------------
-- Server version	5.1.73-1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `appunti`
--

DROP TABLE IF EXISTS `appunti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appunti` (
  `id_appunti` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `titolo` varchar(80) DEFAULT NULL,
  `contenuto` text,
  PRIMARY KEY (`id_appunti`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assemblee_elenco`
--

DROP TABLE IF EXISTS `assemblee_elenco`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assemblee_elenco` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` date DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `stato` varchar(20) DEFAULT NULL,
  `convocazione` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assemblee_log`
--

DROP TABLE IF EXISTS `assemblee_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assemblee_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_assemblea` int(10) unsigned DEFAULT NULL,
  `ora` int(10) unsigned DEFAULT NULL,
  `evento` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=230 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `assemblee_soci`
--

DROP TABLE IF EXISTS `assemblee_soci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assemblee_soci` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_assemblea` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_delega` int(10) unsigned DEFAULT NULL,
  `presenza` varchar(10) DEFAULT NULL,
  `voto` varchar(3) DEFAULT NULL,
  `aggiornamento` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=246 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `associazioni`
--

DROP TABLE IF EXISTS `associazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `associazioni` (
  `id_associazione` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nominativo` varchar(40) DEFAULT NULL,
  `sigla` varchar(20) DEFAULT NULL,
  `dcreazione` date DEFAULT NULL,
  `sede` text,
  `email` varchar(50) DEFAULT NULL,
  `contatti` text,
  `web` varchar(50) DEFAULT NULL,
  `note` text,
  `aggiornamento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_associazione`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banche`
--

DROP TABLE IF EXISTS `banche`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banche` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sottoconto` int(10) unsigned DEFAULT NULL,
  `nomebanca` varchar(20) DEFAULT NULL,
  `iban` varchar(27) DEFAULT NULL,
  `note` text,
  `type` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `banche_righe`
--

DROP TABLE IF EXISTS `banche_righe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `banche_righe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_banca` int(10) unsigned DEFAULT NULL,
  `id_riga` int(10) unsigned DEFAULT NULL,
  `contabile` date DEFAULT NULL,
  `valuta` date DEFAULT NULL,
  `importo` decimal(10,2) DEFAULT NULL,
  `causale` varchar(4) DEFAULT NULL,
  `descrizione` varchar(160) DEFAULT NULL,
  `commissioni` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1832 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conti_conti`
--

DROP TABLE IF EXISTS `conti_conti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conti_conti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_mastro` int(10) unsigned DEFAULT NULL,
  `conto` varchar(40) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=31 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conti_mastri`
--

DROP TABLE IF EXISTS `conti_mastri`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conti_mastri` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ordine` int(10) unsigned DEFAULT NULL,
  `tipo` varchar(10) DEFAULT NULL,
  `mastro` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conti_movimenti`
--

DROP TABLE IF EXISTS `conti_movimenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conti_movimenti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `data` date DEFAULT NULL,
  `descrizione` varchar(100) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1730 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conti_righe`
--

DROP TABLE IF EXISTS `conti_righe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conti_righe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_sottoconto` int(10) unsigned DEFAULT NULL,
  `id_movimento` int(10) unsigned DEFAULT NULL,
  `valuta` date DEFAULT NULL,
  `dare` decimal(10,2) DEFAULT NULL,
  `avere` decimal(10,2) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7255 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `conti_sottoconti`
--

DROP TABLE IF EXISTS `conti_sottoconti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `conti_sottoconti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_conto` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `sottoconto` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=673 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `domains`
--

DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `owner` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventi`
--

DROP TABLE IF EXISTS `eventi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventi` (
  `id_lug` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_md5` char(40) DEFAULT NULL,
  `id_luganagrafe` int(10) unsigned DEFAULT NULL,
  `lug_sigla` varchar(60) DEFAULT NULL,
  `lug_nome` varchar(60) DEFAULT NULL,
  `lug_url` varchar(60) DEFAULT NULL,
  `lug_email` varchar(60) DEFAULT NULL,
  `lug_citta` varchar(40) DEFAULT NULL,
  `lug_prov` char(2) DEFAULT NULL,
  `lug_regione` varchar(21) DEFAULT NULL,
  `lug_resp_nome` varchar(60) DEFAULT NULL,
  `lug_resp_email` varchar(60) DEFAULT NULL,
  `ld_resp_nome` varchar(60) DEFAULT NULL,
  `ld_resp_email` varchar(60) DEFAULT NULL,
  `ld_url` varchar(120) DEFAULT NULL,
  `ld_luogo` text NOT NULL,
  `ld_indirizzo` varchar(100) DEFAULT NULL,
  `ld_citta` varchar(40) DEFAULT NULL,
  `ld_prov` char(2) DEFAULT NULL,
  `ld_regione` varchar(21) DEFAULT NULL,
  `ld_ok_pubblica` char(1) DEFAULT NULL,
  `ld_invio_materiale` varchar(100) DEFAULT NULL,
  `confermalg` date DEFAULT NULL,
  `datiok` char(1) DEFAULT NULL,
  `note` text,
  `X` text NOT NULL,
  `Y` text NOT NULL,
  `anno` int(4) DEFAULT NULL,
  `inserted` date NOT NULL,
  PRIMARY KEY (`id_lug`)
) ENGINE=MyISAM AUTO_INCREMENT=509 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventi_redirld`
--

DROP TABLE IF EXISTS `eventi_redirld`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventi_redirld` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pubblica` char(1) DEFAULT NULL,
  `responsabile` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `host` varchar(50) DEFAULT NULL,
  `url` varchar(120) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventi_update1`
--

DROP TABLE IF EXISTS `eventi_update1`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventi_update1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ora` int(10) unsigned DEFAULT NULL,
  `query` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=275 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `eventi_update2`
--

DROP TABLE IF EXISTS `eventi_update2`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eventi_update2` (
  `id` int(10) unsigned DEFAULT NULL,
  `exitcode` int(10) unsigned DEFAULT NULL,
  `outmsg` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `it_comuni`
--

DROP TABLE IF EXISTS `it_comuni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_comuni` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `id_provincia` int(10) unsigned DEFAULT NULL,
  `comune` varchar(32) NOT NULL,
  `codice_comune` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8102 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `it_province`
--

DROP TABLE IF EXISTS `it_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_province` (
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  `id_zona` int(10) unsigned DEFAULT NULL,
  `id_regione` int(10) unsigned DEFAULT NULL,
  `provincia` varchar(32) NOT NULL,
  `sigla_prov` varchar(2) DEFAULT NULL,
  `codice_provincia` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `it_regioni`
--

DROP TABLE IF EXISTS `it_regioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_regioni` (
  `id` int(10) unsigned DEFAULT NULL,
  `id_zona` int(10) unsigned DEFAULT NULL,
  `regione` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `it_zone`
--

DROP TABLE IF EXISTS `it_zone`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `it_zone` (
  `id` int(10) unsigned DEFAULT NULL,
  `zona` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lug_anagrafe`
--

DROP TABLE IF EXISTS `lug_anagrafe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lug_anagrafe` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_ld2008` int(10) unsigned DEFAULT NULL,
  `id_lug` int(10) unsigned DEFAULT NULL,
  `id_md5` char(40) DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `nome` varchar(60) DEFAULT NULL,
  `sigla` varchar(20) DEFAULT NULL,
  `provincia` char(2) DEFAULT NULL,
  `zona` varchar(100) DEFAULT NULL,
  `associazione` char(1) DEFAULT NULL,
  `fsug` char(1) DEFAULT NULL,
  `url` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `resp_nome` varchar(50) DEFAULT NULL,
  `resp_email` varchar(50) DEFAULT NULL,
  `fondazione` date DEFAULT NULL,
  `soci` int(10) unsigned DEFAULT NULL,
  `simpatizzanti` int(10) unsigned DEFAULT NULL,
  `flags` varchar(50) DEFAULT NULL,
  `aggiornamento` date DEFAULT NULL,
  `pubblica` char(2) NOT NULL DEFAULT 'N',
  `descrizione` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=80 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lug_eventi`
--

DROP TABLE IF EXISTS `lug_eventi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lug_eventi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_lug` int(10) unsigned DEFAULT NULL,
  `data` date DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `titolo` varchar(60) DEFAULT NULL,
  `url` varchar(80) DEFAULT NULL,
  `descrizione` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lug_soci`
--

DROP TABLE IF EXISTS `lug_soci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lug_soci` (
  `id_lug` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `note` varchar(80) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lug_territorio`
--

DROP TABLE IF EXISTS `lug_territorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lug_territorio` (
  `id_lug` int(10) unsigned DEFAULT NULL,
  `provincia` char(2) DEFAULT NULL,
  `dettagli` varchar(100) DEFAULT NULL,
  `note` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_eventi`
--

DROP TABLE IF EXISTS `old_eventi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_eventi` (
  `id_lug` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id` int(10) unsigned DEFAULT NULL,
  `lug_sigla` varchar(60) DEFAULT NULL,
  `lug_nome` varchar(60) DEFAULT NULL,
  `lug_url` varchar(60) DEFAULT NULL,
  `lug_email` varchar(60) DEFAULT NULL,
  `lug_citta` varchar(40) DEFAULT NULL,
  `lug_prov` char(2) DEFAULT NULL,
  `lug_regione` varchar(21) DEFAULT NULL,
  `lug_resp_nome` varchar(60) DEFAULT NULL,
  `lug_resp_email` varchar(60) DEFAULT NULL,
  `ld_resp_nome` varchar(60) DEFAULT NULL,
  `ld_resp_email` varchar(60) DEFAULT NULL,
  `ld_url` varchar(120) DEFAULT NULL,
  `ld_luogo` text NOT NULL,
  `ld_indirizzo` varchar(100) DEFAULT NULL,
  `ld_citta` varchar(40) DEFAULT NULL,
  `ld_prov` char(2) DEFAULT NULL,
  `ld_regione` varchar(21) DEFAULT NULL,
  `ld_ok_pubblica` char(1) DEFAULT NULL,
  `ld_invio_materiale` varchar(100) DEFAULT NULL,
  `confermalg` date DEFAULT NULL,
  `datiok` char(1) DEFAULT NULL,
  `note` text,
  `X` text NOT NULL,
  `Y` text NOT NULL,
  `anno` int(4) DEFAULT NULL,
  `inserted` date NOT NULL,
  PRIMARY KEY (`id_lug`)
) ENGINE=MyISAM AUTO_INCREMENT=377 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_lug_tipi`
--

DROP TABLE IF EXISTS `old_lug_tipi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_lug_tipi` (
  `id` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_lugld2006`
--

DROP TABLE IF EXISTS `old_lugld2006`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_lugld2006` (
  `id` int(10) unsigned DEFAULT NULL,
  `id_lug` int(10) unsigned NOT NULL,
  `lug_sigla` varchar(24) DEFAULT NULL,
  `lug_nome` varchar(60) DEFAULT NULL,
  `lug_url` varchar(60) DEFAULT NULL,
  `lug_email` varchar(60) DEFAULT NULL,
  `lug_citta` varchar(40) DEFAULT NULL,
  `lug_prov` char(2) DEFAULT NULL,
  `lug_regione` varchar(20) DEFAULT NULL,
  `lug_resp_nome` varchar(60) DEFAULT NULL,
  `lug_resp_email` varchar(60) DEFAULT NULL,
  `ld_resp_nome` varchar(60) DEFAULT NULL,
  `ld_resp_email` varchar(60) DEFAULT NULL,
  `ld_url` varchar(80) DEFAULT NULL,
  `ld_indirizzo` varchar(100) DEFAULT NULL,
  `ld_citta` varchar(40) DEFAULT NULL,
  `ld_prov` char(2) DEFAULT NULL,
  `ld_regione` varchar(20) DEFAULT NULL,
  `ld_ok_pubblica` char(1) DEFAULT NULL,
  `ld_invio_materiale` varchar(200) DEFAULT NULL,
  `confermalg` date DEFAULT NULL,
  `datiok` char(1) DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id_lug`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_members`
--

DROP TABLE IF EXISTS `old_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_members` (
  `nick` varchar(32) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `sessionid` varchar(32) DEFAULT NULL,
  `ipaddress` varchar(80) DEFAULT NULL,
  `activated` varchar(32) DEFAULT 'NO',
  `datereg` datetime DEFAULT NULL,
  `logins` int(11) DEFAULT '0',
  `lastlogin` datetime DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `email_pub` varchar(80) DEFAULT NULL,
  `askpass` datetime DEFAULT NULL,
  `real_name` varchar(80) DEFAULT NULL,
  `lug_flag` tinyint(4) NOT NULL DEFAULT '0',
  `lug_nome` varchar(40) DEFAULT NULL,
  `lug_tipo` int(11) DEFAULT NULL,
  `lug_regione` int(11) DEFAULT NULL,
  `lug_area` varchar(40) DEFAULT NULL,
  `lug_fondazione` date DEFAULT NULL,
  `lug_homepage` varchar(80) DEFAULT NULL,
  `lug_summary` text,
  `lug_info` text,
  `lug_n_friends` int(11) NOT NULL DEFAULT '0',
  `lug_n_soci` int(11) NOT NULL DEFAULT '0',
  `lug_adesivi` text NOT NULL,
  `azienda_piva` varchar(32) DEFAULT NULL,
  `azienda_nome` varchar(40) DEFAULT NULL,
  `azienda_regione` int(11) DEFAULT NULL,
  `azienda_homepage` varchar(80) DEFAULT NULL,
  `azienda_summary` text,
  `azienda_info` text,
  `lug_data1` int(11) NOT NULL DEFAULT '0',
  `lug_data2` varchar(5) NOT NULL DEFAULT '  /',
  `lug_reality` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`nick`),
  KEY `jmember1` (`nick`),
  KEY `jmember2` (`email`),
  KEY `jmembers5` (`lug_regione`,`lug_nome`),
  KEY `jmembers6` (`azienda_piva`),
  KEY `jmembers7` (`azienda_regione`,`azienda_nome`),
  KEY `jmember4` (`email_pub`),
  KEY `lug_flag` (`lug_flag`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_regioni`
--

DROP TABLE IF EXISTS `old_regioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_regioni` (
  `id` int(11) NOT NULL DEFAULT '0',
  `nome` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `old_sondaggio2008`
--

DROP TABLE IF EXISTS `old_sondaggio2008`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `old_sondaggio2008` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lugnome` varchar(255) DEFAULT NULL,
  `lugtipo` varchar(255) DEFAULT NULL,
  `lugzona` varchar(255) DEFAULT NULL,
  `lugurl` varchar(255) DEFAULT NULL,
  `lugemail` varchar(255) DEFAULT NULL,
  `lugnote` text,
  `ldurl` varchar(255) DEFAULT NULL,
  `ldcitta` varchar(255) DEFAULT NULL,
  `ldvolontari` int(10) unsigned DEFAULT NULL,
  `ldpubblico` int(10) unsigned DEFAULT NULL,
  `ldnote` text,
  `sponsor` text,
  `patrocinireg` text,
  `patrociniprov` text,
  `patrocinicom` text,
  `patrocini` text,
  `rassegne` text,
  `cronaca` text,
  `altracomunicazione` text,
  `databuona` varchar(255) DEFAULT NULL,
  `datacambia` varchar(255) DEFAULT NULL,
  `datanoprob` varchar(255) DEFAULT NULL,
  `dataproposta` varchar(255) DEFAULT NULL,
  `logobuono` varchar(255) DEFAULT NULL,
  `logocambia` varchar(255) DEFAULT NULL,
  `logonoprob` varchar(255) DEFAULT NULL,
  `logoproposto` text,
  `nomeattualeok` varchar(255) DEFAULT NULL,
  `nomegnupiace` varchar(255) DEFAULT NULL,
  `nomegnuok` varchar(255) DEFAULT NULL,
  `nomefreeswpiace` varchar(255) DEFAULT NULL,
  `nomefreeswok` varchar(255) DEFAULT NULL,
  `sponsorok` varchar(255) DEFAULT NULL,
  `sponsorsh` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=461 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `patrocini`
--

DROP TABLE IF EXISTS `patrocini`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `patrocini` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_zona` int(10) unsigned DEFAULT NULL,
  `id_regione` int(10) unsigned DEFAULT NULL,
  `id_provincia` int(10) unsigned DEFAULT NULL,
  `data` date DEFAULT NULL,
  `evento` varchar(80) DEFAULT NULL,
  `url` varchar(80) DEFAULT NULL,
  `descrizione` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prestazionisoci`
--

DROP TABLE IF EXISTS `prestazionisoci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prestazionisoci` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `data` date DEFAULT NULL,
  `ore` decimal(10,2) DEFAULT NULL,
  `km` int(10) unsigned DEFAULT NULL,
  `spesa` decimal(10,2) DEFAULT NULL,
  `descrizione` varchar(60) DEFAULT NULL,
  `dettagli` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prj_affi`
--

DROP TABLE IF EXISTS `prj_affi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prj_affi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo` int(10) unsigned DEFAULT NULL,
  `affiliazione` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prj_eventi`
--

DROP TABLE IF EXISTS `prj_eventi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prj_eventi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_progetto` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_zona` int(10) unsigned DEFAULT NULL,
  `id_regione` int(10) unsigned DEFAULT NULL,
  `id_provincia` int(10) unsigned DEFAULT NULL,
  `data` date DEFAULT NULL,
  `evento` varchar(40) DEFAULT NULL,
  `url` varchar(80) DEFAULT NULL,
  `descrizione` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prj_lista`
--

DROP TABLE IF EXISTS `prj_lista`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prj_lista` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo` int(10) unsigned DEFAULT NULL,
  `id_comune` int(10) unsigned DEFAULT NULL,
  `id_provincia` int(10) unsigned DEFAULT NULL,
  `id_regione` int(10) unsigned DEFAULT NULL,
  `id_zona` int(10) unsigned DEFAULT NULL,
  `progetto` varchar(40) DEFAULT NULL,
  `creazione` date DEFAULT NULL,
  `descrizione` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prj_soci`
--

DROP TABLE IF EXISTS `prj_soci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prj_soci` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_progetto` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_affi` int(10) unsigned DEFAULT NULL,
  `ping` date DEFAULT NULL,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `prj_tipi`
--

DROP TABLE IF EXISTS `prj_tipi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `prj_tipi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ricevute`
--

DROP TABLE IF EXISTS `ricevute`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ricevute` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_movimento` int(10) unsigned DEFAULT NULL,
  `numero` int(10) unsigned DEFAULT NULL,
  `importo` decimal(10,2) DEFAULT NULL,
  `email` varchar(80) DEFAULT NULL,
  `intestazione` varchar(80) DEFAULT NULL,
  `indirizzo` text,
  `causale` text,
  `note` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=319 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ricevute_conti`
--

DROP TABLE IF EXISTS `ricevute_conti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ricevute_conti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_conto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `segnalazioni`
--

DROP TABLE IF EXISTS `segnalazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `segnalazioni` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `oggetto` varchar(100) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `ora` int(10) unsigned DEFAULT NULL,
  `reply` date DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  `browser` varchar(120) DEFAULT NULL,
  `referer` varchar(80) DEFAULT NULL,
  `testo` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=69 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soci_domande`
--

DROP TABLE IF EXISTS `soci_domande`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soci_domande` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo` int(10) unsigned DEFAULT '1',
  `ip_remoto` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `comune_nasc` varchar(100) NOT NULL,
  `prov_nasc` char(2) DEFAULT NULL,
  `data_nasc` date NOT NULL,
  `indirizzo_resid` varchar(100) NOT NULL,
  `comune_resid` varchar(100) NOT NULL,
  `prov_resid` char(2) DEFAULT NULL,
  `cap_resid` char(5) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `codfis` char(16) DEFAULT NULL,
  `data_domanda` date NOT NULL DEFAULT '0000-00-00',
  `nickname` varchar(50) DEFAULT NULL,
  `note` text,
  `type` varchar(20) DEFAULT NULL,
  `members` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=298 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soci_iscritti`
--

DROP TABLE IF EXISTS `soci_iscritti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soci_iscritti` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_tipo` int(10) unsigned DEFAULT '1',
  `nome` varchar(100) NOT NULL,
  `cognome` varchar(100) NOT NULL,
  `comune_nasc` varchar(100) NOT NULL,
  `prov_nasc` char(2) DEFAULT NULL,
  `data_nasc` date NOT NULL,
  `indirizzo_resid` varchar(100) NOT NULL,
  `comune_resid` varchar(100) NOT NULL,
  `prov_resid` char(2) DEFAULT NULL,
  `cap_resid` char(5) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `codfis` char(16) NOT NULL,
  `data_domanda` date NOT NULL DEFAULT '0000-00-00',
  `data_approvazione` date NOT NULL DEFAULT '0000-00-00',
  `data_ammissione` date NOT NULL DEFAULT '0000-00-00',
  `data_espulsione` date NOT NULL DEFAULT '0000-00-00',
  `anno_iscrizione` int(11) DEFAULT NULL,
  `nickname` varchar(50) DEFAULT NULL,
  `note` text,
  `type` varchar(20) DEFAULT NULL,
  `members` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=561 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soci_quote`
--

DROP TABLE IF EXISTS `soci_quote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soci_quote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `id_riga` int(10) unsigned DEFAULT NULL,
  `anno` int(11) NOT NULL,
  `data_versamento` date NOT NULL,
  `note` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2345 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `soci_tipi`
--

DROP TABLE IF EXISTS `soci_tipi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `soci_tipi` (
  `id` int(10) unsigned DEFAULT NULL,
  `quota` decimal(10,2) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_accesslog`
--

DROP TABLE IF EXISTS `users_accesslog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_accesslog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) DEFAULT NULL,
  `ora` int(10) unsigned DEFAULT NULL,
  `ip` int(11) DEFAULT NULL,
  `browser` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3413 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_chat`
--

DROP TABLE IF EXISTS `users_chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_chat` (
  `nickname` varchar(100) DEFAULT NULL,
  `chatmd5` varchar(40) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_extra`
--

DROP TABLE IF EXISTS `users_extra`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_extra` (
  `nome` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `attivo` char(1) DEFAULT 'N',
  `cookie_pw` varchar(40) DEFAULT NULL,
  `cookie_time` int(10) unsigned DEFAULT NULL,
  `sospeso` date DEFAULT NULL,
  `cambiopw` date DEFAULT NULL,
  `note` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_passwlog`
--

DROP TABLE IF EXISTS `users_passwlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_passwlog` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` varchar(100) DEFAULT NULL,
  `action` varchar(20) DEFAULT NULL,
  `ora` int(10) unsigned DEFAULT NULL,
  `ip` int(10) unsigned DEFAULT NULL,
  `browser` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=113 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_perm`
--

DROP TABLE IF EXISTS `users_perm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_perm` (
  `id_rules` int(10) unsigned DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_picard`
--

DROP TABLE IF EXISTS `users_picard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_picard` (
  `nome` varchar(100) DEFAULT NULL,
  `cognome` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `fw_email` varchar(100) DEFAULT NULL,
  `attivo` char(1) DEFAULT 'N',
  `cookie_pw` varchar(40) DEFAULT NULL,
  `cookie_time` int(10) unsigned DEFAULT NULL,
  `sospeso` date DEFAULT NULL,
  `cambiopw` date DEFAULT NULL,
  `homepage` varchar(100) DEFAULT NULL,
  `blog_feed` varchar(100) DEFAULT NULL,
  `note` text,
  `pw_picard` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`nickname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_picard_hardwire`
--

DROP TABLE IF EXISTS `users_picard_hardwire`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_picard_hardwire` (
  `nome` varchar(100) DEFAULT NULL,
  `cognome` varchar(100) DEFAULT NULL,
  `nickname` varchar(100) DEFAULT NULL,
  `pw_picard` varchar(100) DEFAULT NULL,
  `fw_email` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `users_rules`
--

DROP TABLE IF EXISTS `users_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_rules` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rule` varchar(20) DEFAULT NULL,
  `descrizione` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votazioni`
--

DROP TABLE IF EXISTS `votazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votazioni` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_assemblea` int(10) unsigned DEFAULT NULL,
  `apertura` int(10) unsigned DEFAULT NULL,
  `chiusura` int(10) unsigned DEFAULT NULL,
  `descrizione` varchar(30) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT NULL,
  `maxitem` int(11) DEFAULT NULL,
  `testo` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votazioni_soci`
--

DROP TABLE IF EXISTS `votazioni_soci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votazioni_soci` (
  `id_votazione` int(10) unsigned DEFAULT NULL,
  `id_socio` int(10) unsigned DEFAULT NULL,
  `votato` varchar(5) DEFAULT NULL,
  `scheda` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `votazioni_voci`
--

DROP TABLE IF EXISTS `votazioni_voci`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `votazioni_voci` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_votazione` int(10) unsigned DEFAULT NULL,
  `voti` int(10) unsigned DEFAULT NULL,
  `label` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=115 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-04-25 20:34:57
