-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.5.25a


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema loja
--

CREATE DATABASE IF NOT EXISTS loja;
USE loja;

--
-- Definition of table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE `cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(14) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `rg` varchar(15) DEFAULT NULL,
  `rua` varchar(45) DEFAULT NULL,
  `numero` varchar(5) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `estado` varchar(25) DEFAULT NULL,
  `cep` varchar(8) DEFAULT NULL,
  `telefone_residencial` varchar(11) DEFAULT NULL,
  `telefone_comercial` varchar(11) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 COMMENT='Tabela que armazena os dados do cliente.';

--
-- Dumping data for table `cliente`
--

/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` (`id_cliente`,`documento`,`nome`,`rg`,`rua`,`numero`,`bairro`,`cidade`,`estado`,`cep`,`telefone_residencial`,`telefone_comercial`,`email`) VALUES 
 (1,'39418088851','Cesar Augusto Vieira Giovani',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
 (2,'99999955512','Maria',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;


--
-- Definition of table `compra`
--

DROP TABLE IF EXISTS `compra`;
CREATE TABLE `compra` (
  `id_compra` int(11) NOT NULL AUTO_INCREMENT,
  `id_fornecedor` int(11) NOT NULL,
  `num_nota_fiscal` varchar(20) NOT NULL,
  `valor_total_nota` float NOT NULL DEFAULT '0',
  `descricao` varchar(30) DEFAULT NULL,
  `data` datetime NOT NULL,
  PRIMARY KEY (`id_compra`),
  KEY `FOR_COM_FOR` (`id_fornecedor`),
  CONSTRAINT `FOR_COM_FOR` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `compra`
--

/*!40000 ALTER TABLE `compra` DISABLE KEYS */;
/*!40000 ALTER TABLE `compra` ENABLE KEYS */;


--
-- Definition of table `despesa`
--

DROP TABLE IF EXISTS `despesa`;
CREATE TABLE `despesa` (
  `id_despesa` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipodespesa` int(11) NOT NULL,
  `valor_despesa` float NOT NULL DEFAULT '0',
  `descricao` varchar(45) NOT NULL,
  PRIMARY KEY (`id_despesa`),
  KEY `FOR_DES_TIP` (`id_tipodespesa`),
  CONSTRAINT `FOR_DES_TIP` FOREIGN KEY (`id_tipodespesa`) REFERENCES `tipodespesa` (`id_tipodespesa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `despesa`
--

/*!40000 ALTER TABLE `despesa` DISABLE KEYS */;
/*!40000 ALTER TABLE `despesa` ENABLE KEYS */;


--
-- Definition of table `duplicata`
--

DROP TABLE IF EXISTS `duplicata`;
CREATE TABLE `duplicata` (
  `id_duplicata` int(11) NOT NULL AUTO_INCREMENT,
  `id_venda` int(11) NOT NULL,
  `data_vencimento` datetime NOT NULL,
  `data_pagamento` datetime DEFAULT NULL,
  `valor_total` float NOT NULL,
  `valor_pago` float DEFAULT '0',
  PRIMARY KEY (`id_duplicata`),
  KEY `FOR_DUP_VEN` (`id_venda`),
  CONSTRAINT `FOR_DUP_VEN` FOREIGN KEY (`id_venda`) REFERENCES `venda` (`id_venda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `duplicata`
--

/*!40000 ALTER TABLE `duplicata` DISABLE KEYS */;
/*!40000 ALTER TABLE `duplicata` ENABLE KEYS */;


--
-- Definition of table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
CREATE TABLE `fornecedor` (
  `id_fornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `documento` varchar(14) NOT NULL,
  `razao_social` varchar(45) NOT NULL,
  `nome_fantasia` varchar(45) DEFAULT NULL,
  `representante_comercial` varchar(45) NOT NULL,
  `telefone` varchar(11) NOT NULL,
  `email` varchar(30) DEFAULT NULL,
  `rua` varchar(45) DEFAULT NULL,
  `numero` varchar(5) DEFAULT NULL,
  `bairro` varchar(45) DEFAULT NULL,
  `cidade` varchar(45) DEFAULT NULL,
  `estado` varchar(25) DEFAULT NULL,
  `cep` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`id_fornecedor`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `fornecedor`
--

/*!40000 ALTER TABLE `fornecedor` DISABLE KEYS */;
/*!40000 ALTER TABLE `fornecedor` ENABLE KEYS */;


--
-- Definition of table `itemcompra`
--

DROP TABLE IF EXISTS `itemcompra`;
CREATE TABLE `itemcompra` (
  `id_itemcompra` int(11) NOT NULL AUTO_INCREMENT,
  `id_mercadoria` int(11) NOT NULL,
  `id_compra` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT '0',
  `valor_unitario` float DEFAULT '0',
  PRIMARY KEY (`id_itemcompra`),
  KEY `FOR_ITEC_MER` (`id_mercadoria`),
  KEY `FOR_ITEC_COM` (`id_compra`),
  CONSTRAINT `FOR_ITEC_COM` FOREIGN KEY (`id_compra`) REFERENCES `compra` (`id_compra`),
  CONSTRAINT `FOR_ITEC_MER` FOREIGN KEY (`id_mercadoria`) REFERENCES `mercadoria` (`id_mercadoria`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `itemcompra`
--

/*!40000 ALTER TABLE `itemcompra` DISABLE KEYS */;
/*!40000 ALTER TABLE `itemcompra` ENABLE KEYS */;


--
-- Definition of table `itemvenda`
--

DROP TABLE IF EXISTS `itemvenda`;
CREATE TABLE `itemvenda` (
  `id_itemvenda` int(11) NOT NULL AUTO_INCREMENT,
  `id_mercadoria` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL DEFAULT '0',
  `valor_unitario` float NOT NULL,
  PRIMARY KEY (`id_itemvenda`),
  KEY `FOR_ITEV_VEN` (`id_venda`),
  KEY `FOR_ITEV_MER` (`id_mercadoria`),
  CONSTRAINT `FOR_ITEV_MER` FOREIGN KEY (`id_mercadoria`) REFERENCES `mercadoria` (`id_mercadoria`),
  CONSTRAINT `FOR_ITEV_VEN` FOREIGN KEY (`id_venda`) REFERENCES `venda` (`id_venda`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `itemvenda`
--

/*!40000 ALTER TABLE `itemvenda` DISABLE KEYS */;
/*!40000 ALTER TABLE `itemvenda` ENABLE KEYS */;


--
-- Definition of table `mercadoria`
--

DROP TABLE IF EXISTS `mercadoria`;
CREATE TABLE `mercadoria` (
  `id_mercadoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_subtipomercadoria` int(11) NOT NULL,
  `descricao` varchar(45) NOT NULL,
  `qtde_estoque` int(11) NOT NULL DEFAULT '0',
  `qtde_minima_estoque` int(11) NOT NULL DEFAULT '0',
  `preco_venda_unitario` float NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_mercadoria`),
  KEY `FOR_MER_SUB` (`id_subtipomercadoria`),
  CONSTRAINT `FOR_MER_SUB` FOREIGN KEY (`id_subtipomercadoria`) REFERENCES `subtipomercadoria` (`id_subtipomercadoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mercadoria`
--

/*!40000 ALTER TABLE `mercadoria` DISABLE KEYS */;
INSERT INTO `mercadoria` (`id_mercadoria`,`id_subtipomercadoria`,`descricao`,`qtde_estoque`,`qtde_minima_estoque`,`preco_venda_unitario`) VALUES 
 (6,1,'CAMISETA P MARCA X',10,3,30.99),
 (7,2,'BERMUNA G MARCA Y',15,5,45.5),
 (8,3,'CALÇA 45 MARCA Z',4,2,79.9),
 (9,4,'SHAPE MARCA AB',7,2,120),
 (10,5,'JOELHEIRA COR C MARCA R',5,1,80.5);
/*!40000 ALTER TABLE `mercadoria` ENABLE KEYS */;


--
-- Definition of table `parcela`
--

DROP TABLE IF EXISTS `parcela`;
CREATE TABLE `parcela` (
  `id_parcela` int(11) NOT NULL,
  `id_despesa` int(11) NOT NULL,
  `data_vencimento` date NOT NULL,
  `data_pagamento` date DEFAULT NULL,
  `valor_total_parcela` float NOT NULL,
  `valor_pago` float DEFAULT NULL,
  `moeda_pagamento` char(2) NOT NULL,
  PRIMARY KEY (`id_parcela`),
  KEY `FK_PAR_DES` (`id_despesa`),
  CONSTRAINT `FK_PAR_DES` FOREIGN KEY (`id_despesa`) REFERENCES `despesa` (`id_despesa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `parcela`
--

/*!40000 ALTER TABLE `parcela` DISABLE KEYS */;
/*!40000 ALTER TABLE `parcela` ENABLE KEYS */;


--
-- Definition of table `subtipomercadoria`
--

DROP TABLE IF EXISTS `subtipomercadoria`;
CREATE TABLE `subtipomercadoria` (
  `id_subtipomercadoria` int(11) NOT NULL AUTO_INCREMENT,
  `id_tipomercadoria` int(11) NOT NULL,
  `descricao` varchar(45) NOT NULL,
  PRIMARY KEY (`id_subtipomercadoria`),
  KEY `FOR_SUB_TIP` (`id_tipomercadoria`),
  CONSTRAINT `FOR_SUB_TIP` FOREIGN KEY (`id_tipomercadoria`) REFERENCES `tipomercadoria` (`id_tipomercadoria`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=dec8;

--
-- Dumping data for table `subtipomercadoria`
--

/*!40000 ALTER TABLE `subtipomercadoria` DISABLE KEYS */;
INSERT INTO `subtipomercadoria` (`id_subtipomercadoria`,`id_tipomercadoria`,`descricao`) VALUES 
 (1,1,'CAMISETAS'),
 (2,1,'BERMUDAS'),
 (3,1,'CALÇAS'),
 (4,2,'SHAPES'),
 (5,2,'ACESSÓRIOS');
/*!40000 ALTER TABLE `subtipomercadoria` ENABLE KEYS */;


--
-- Definition of table `tipodespesa`
--

DROP TABLE IF EXISTS `tipodespesa`;
CREATE TABLE `tipodespesa` (
  `id_tipodespesa` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(45) NOT NULL,
  PRIMARY KEY (`id_tipodespesa`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tipodespesa`
--

/*!40000 ALTER TABLE `tipodespesa` DISABLE KEYS */;
/*!40000 ALTER TABLE `tipodespesa` ENABLE KEYS */;


--
-- Definition of table `tipomercadoria`
--

DROP TABLE IF EXISTS `tipomercadoria`;
CREATE TABLE `tipomercadoria` (
  `id_tipomercadoria` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(45) NOT NULL,
  PRIMARY KEY (`id_tipomercadoria`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tipomercadoria`
--

/*!40000 ALTER TABLE `tipomercadoria` DISABLE KEYS */;
INSERT INTO `tipomercadoria` (`id_tipomercadoria`,`descricao`) VALUES 
 (1,'VESTUÁRIO'),
 (2,'MATERIAL ESPORTIVO');
/*!40000 ALTER TABLE `tipomercadoria` ENABLE KEYS */;


--
-- Definition of table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(30) NOT NULL,
  `senha` varchar(15) NOT NULL,
  `tipo_usuario` varchar(1) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuario`
--

/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` (`id_usuario`,`login`,`senha`,`tipo_usuario`) VALUES 
 (1,'a','a','A'),
 (3,'r','r','A'),
 (4,'cesar','cesar','C'),
 (5,'cecec','ce','A'),
 (6,'cesar_admin','cesar12345','C'),
 (7,'luíz','a','A');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;


--
-- Definition of table `venda`
--

DROP TABLE IF EXISTS `venda`;
CREATE TABLE `venda` (
  `id_venda` int(11) NOT NULL AUTO_INCREMENT,
  `id_cliente` int(11) NOT NULL,
  `forma_pagamento` varchar(2) NOT NULL,
  `valor_total_venda` float NOT NULL,
  `valor_desconto` float DEFAULT '0',
  `data_venda` date NOT NULL,
  `tipo_pagamento` varchar(1) NOT NULL,
  PRIMARY KEY (`id_venda`),
  KEY `FOR_VEN_CLI` (`id_cliente`),
  CONSTRAINT `FOR_VEN_CLI` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `venda`
--

/*!40000 ALTER TABLE `venda` DISABLE KEYS */;
INSERT INTO `venda` (`id_venda`,`id_cliente`,`forma_pagamento`,`valor_total_venda`,`valor_desconto`,`data_venda`,`tipo_pagamento`) VALUES 
 (1,2,'D',11.11,1.12,'2013-03-16','V'),
 (2,2,'CC',145.78,12.22,'2013-03-17','V'),
 (3,1,'CC',12.33,4.56,'2013-03-18','P');
/*!40000 ALTER TABLE `venda` ENABLE KEYS */;




/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
