-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

INSERT INTO `wo_config` (`name`, `value`)
VALUES ('private_groups', '1'),
('link_expiry', '1 hour'),
('creation_fee', '5'),
('joining_fee', '10');

DROP TABLE IF EXISTS `wo_private_groupadmins`;
CREATE TABLE `wo_private_groupadmins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `group_id` int(11) NOT NULL DEFAULT 0,
  `general` int(11) NOT NULL DEFAULT 1,
  `privacy` int(11) NOT NULL DEFAULT 1,
  `avatar` int(11) NOT NULL DEFAULT 1,
  `members` int(11) NOT NULL DEFAULT 0,
  `analytics` int(11) NOT NULL DEFAULT 1,
  `delete_group` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `group_id` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


SET NAMES utf8mb4;

DROP TABLE IF EXISTS `wo_private_groups`;
CREATE TABLE `wo_private_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `payment_id` int(11) NOT NULL DEFAULT 0,
  `group_name` varchar(32) NOT NULL DEFAULT '',
  `group_title` varchar(40) NOT NULL DEFAULT '',
  `address` varchar(120) NOT NULL DEFAULT '',
  `avatar` varchar(120) NOT NULL DEFAULT 'upload/photos/d-group.jpg ',
  `cover` varchar(120) NOT NULL DEFAULT 'upload/photos/d-cover.jpg  ',
  `about` varchar(550) NOT NULL DEFAULT '',
  `category` int(11) NOT NULL DEFAULT 1,
  `active` tinyint NOT NULL DEFAULT '0',
  `registered` varchar(32) NOT NULL DEFAULT '0/0000',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `wo_private_categories`;
CREATE TABLE `wo_private_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang_key` varchar(160) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `wo_private_members`;
CREATE TABLE `wo_private_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT 0,
  `group_id` int(11) NOT NULL DEFAULT 0,
  `category` int(11) NOT NULL DEFAULT 0,
  `time` int(11) NOT NULL DEFAULT 0,
  `active` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `wo_private_jobcategories`;
CREATE TABLE `wo_private_jobcategories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL DEFAULT '0',
  `lang_key` varchar(160) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wo_private_allowedcategories`;
CREATE TABLE `wo_private_allowedcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL DEFAULT '0',
  `category_id` int NOT NULL DEFAULT '0',
  `jobcategory_id` int NOT NULL DEFAULT '0',
  `active` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `wo_private_groupinvites`;
CREATE TABLE `wo_private_groupinvites` (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL DEFAULT '0',
  `recipient_id` int NOT NULL DEFAULT '0',
  `group_id` int NOT NULL DEFAULT '0',
  `token` varchar(160) NOT NULL DEFAULT '',
  `active` tinyint NOT NULL DEFAULT '0',
  `time` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `wo_posts`
ADD `private_id` int(11) NOT NULL DEFAULT '0' AFTER `group_id`;

ALTER TABLE `wo_reports`
ADD `private_id` int(11) NOT NULL DEFAULT '0' AFTER `group_id`,
ADD INDEX `private_id` (`private_id`);

ALTER TABLE `wo_notifications`
ADD `private_id` int(11) NOT NULL DEFAULT '0' AFTER `group_id`,
ADD `private_chat_id` int(11) NOT NULL DEFAULT '0' AFTER `group_chat_id`;

INSERT INTO `wo_langs` (`lang_key`, `type`, `english`, `arabic`, `dutch`, `french`, `german`, `italian`, `portuguese`, `russian`, `spanish`, `turkish`) VALUES
('private',	'',	'Private',	'خاص',	'Private',	'Privé',	'Privat',	'Privato',	'Privado',	'Закрытая группа',	'Privado',	'Özel'),
('my_private_groups',	'',	'My Private Groups',	'مجموعاتي الخاصة',	'Mijn Privégroepen',	'Mes Groupes Privés',	'Meine Privaten Gruppen',	'I Miei Gruppi Privati',	'Meus Grupos Privados',	'Мои частные группы',	'Mis Grupos Privados',	'Özel Gruplarım'),
('private_groups',	'',	'Private Groups',	'المجموعات الخاصة',	'Privégroepen',	'Groupes Privés',	'Private Gruppen',	'Gruppi Privati',	'Grupos Privados',	'Частные группы',	'Grupos Privados',	'Özel Gruplar'),
('job_category', '', 'Job Category', 'تصنيف الوظيفة', 'Functiecategorie', 'catégorie d\'emploi', 'Stellenkategorie', 'Categoria di lavoro', 'Categoria de Emprego', 'Тип деятельности', 'Categoría de Trabajo', 'iş kategorisi'),
('creation_fee',	'',	'Pay {currency} {amount} to create this private group',	'ادفع {amount} {currency} لإنشاء هذه المجموعة الخاصة',	'Betaal {currency} {amount} om deze privégroep te creëren',	'Payez {amount} {currency} pour créer ce groupe privé',	'Zahlen Sie {amount} {currency}, um diese private Gruppe zu erstellen',	'Paga {currency} {amount} per creare questo gruppo privato',	'Pague R {currency} {amount} para criar este grupo privado',	'Заплатите {currency} {amount}, чтобы создать эту частную группу',	'Paga {currency} {amount} para crear este grupo privado',	'Bu özel grubu oluşturmak için {amount} {currency} ödeyin '),
('joining_fee',	'',	'Pay {currency} {amount} to join this private group',	'ادفع {amount} {currency} للانضمام إلى هذه المجموعة الخاصة',	'Betaal {currency} {amount} om lid te worden van deze privégroep',	'Payez {amount} {currency} pour rejoindre ce groupe privé',	'Zahlen Sie {amount} {currency}, um diese private Gruppe beizutreten',	'Paga {currency} {amount} per unirti a questo gruppo privato',	'Pague R {currency} {amount} para ingressar neste grupo privado',	'Заплатите {currency} {amount}, чтобы присоединиться к этой частной группе',	'Paga {currency} {amount} para unirte a este grupo privado',	'Bu özel grubu katılmak için {amount} {currency} ödeyin'),
('employee_category',	'',	'Employee Job Categories',	'فئات عمل الموظف',	'Functiecategorieën werknemer',	'Catégories d\'emploi des employés',	'Jobkategorien für Mitarbeiter',	'Categorie di lavoro dei dipendenti',	'Categorias de trabalho dos funcionários',	'Категории работника',	'Categorías de trabajo del empleado',	'Çalışan İş Kategorileri'),
('creation_error',	'',	'Please pay the creation fee before creating private group.',	'يرجى دفع رسوم الإنشاء قبل إنشاء مجموعة خاصة.',	'Betaal de aanmaakkosten voordat u een privégroep maakt.',	'Veuillez payer les frais de création avant de créer un groupe privé.',	'Bitte zahlen Sie die Erstellungsgebühr, bevor Sie eine private Gruppe erstellen.',	'Si prega di pagare la quota di creazione prima di creare un gruppo privato.',	'Pague a taxa de criação antes de criar um grupo privado.',	'Пожалуйста, внесите плату за создание, прежде чем создавать частную группу.',	'Pague la tarifa de creación antes de crear un grupo privado.',	'Lütfen özel grup oluşturmadan önce oluşturma ücretini ödeyin.'),
('joining_error',	'',	'Please pay the joining fee before joining private group.',	'يرجى دفع رسوم الانضمام قبل الانضمام إلى مجموعة خاصة.',	'Betaal het inschrijfgeld voordat u lid wordt van een privégroep.',	'Veuillez payer les frais d\'inscription avant de rejoindre un groupe privé.',	'Bitte zahlen Sie die Teilnahmegebühr, bevor Sie einer privaten Gruppe beitreten.',	'Si prega di pagare la quota di iscrizione prima di aderire al gruppo privato.',	'Pague a taxa de inscrição antes de ingressar no grupo privado.',	'Пожалуйста, оплатите вступительный взнос до присоединения к частной группе.',	'Pague la tarifa de inscripción antes de unirse al grupo privado.',	'Lütfen özel gruba katılmadan önce katılım ücretini ödeyin.'),
('group_created',	'',	'Private group created, It\'be approved once the you receipt gets approved.',	'تم إنشاء مجموعة خاصة ، يتم اعتمادها بمجرد الموافقة على الإيصال.',	'Privégroep gemaakt, wordt goedgekeurd zodra de ontvangstbevestiging is goedgekeurd.',	'Groupe privé créé, il sera approuvé une fois le reçu approuvé.',	'Private Gruppe erstellt, wird genehmigt, sobald die Quittung genehmigt wurde.',	'Creazione di un gruppo privato, viene approvato dopo l\'approvazione della ricevuta.',	'Grupo privado criado. É aprovado assim que o recebimento é aprovado.',	'Создана частная группа, она будет утверждена после подтверждения вашей квитанции.',	'Grupo privado creado, se aprobará una vez que se apruebe su recibo.',	'Özel grup oluşturuldu, makbuz onaylandıktan sonra onaylanır.'),
('approval_error',	'',	'User hasn\'t created a private group for this payment yet!',	'لم يقم المستخدم بإنشاء مجموعة خاصة لهذه الدفعة حتى الآن!',	'Gebruiker heeft nog geen privégroep gemaakt voor deze betaling!',	'L\'utilisateur n\'a pas encore créé de groupe privé pour ce paiement!',	'Der Benutzer hat noch keine private Gruppe für diese Zahlung erstellt!',	'L\'utente non ha ancora creato un gruppo privato per questo pagamento!',	'O usuário ainda não criou um grupo privado para este pagamento!',	'Пользователь еще не создал приватную группу для этого платежа!',	'¡El usuario aún no ha creado un grupo privado para este pago!',	'Kullanıcı bu ödeme için henüz özel bir grup oluşturmadı!'),
('invite_your_friends',	'',	'Invite your friends to this group',	'قم بدعوة أصدقائك إلى هذه المجموعة',	'Nodig je vrienden uit voor deze groep',	'Invitez vos amis dans ce groupe',	'Laden Sie Ihre Freunde zu dieser Gruppe ein',	'Invita i tuoi amici a questo gruppo',	'Convide seus amigos para este grupo',	'Пригласите своих друзей в эту группу',	'Invita a tus amigos a este grupo',	'Arkadaşlarınızı bu gruba davet edin'),
('join_group',	'',	'Join Group',	'إنضم للمجموعة',	'Deelnemen aan groep',	'Rejoindre le groupe',	'Gruppe beitreten',	'Unirsi al gruppo',	'Juntar-se ao grupo',	'Вступить в группу',	'Unirse al grupo',	'Gruba katılmak'),
('group_joined',	'',	'Private group joined, It will be approved once the you receipt gets approved.',	'انضمت مجموعة خاصة ، ستتم الموافقة عليه بمجرد الموافقة على الإيصال.',	'Privé-groep is lid geworden. Het wordt goedgekeurd zodra de ontvangstbevestiging is goedgekeurd.',	'Groupe privé rejoint, il sera approuvé une fois le reçu approuvé.',	'Private Gruppe ist beigetreten. Sie wird genehmigt, sobald die Quittung genehmigt wurde.',	'Gruppo privato iscritto, sarà approvato una volta ricevuta la ricevuta.',	'Grupo privado ingressado. Ele será aprovado assim que o recibo for aprovado.',	'Присоединилась частная группа, она будет одобрена после подтверждения вашей квитанции.',	'Grupo privado unido, se aprobará una vez que se apruebe su recibo.',	'Özel grup katıldı, makbuz onaylandıktan sonra onaylanacaktır.');

ALTER TABLE `bank_receipts`
ADD `type` enum('subscription','group') COLLATE 'utf8_general_ci' NOT NULL DEFAULT 'subscription' AFTER `description`,
ADD `group_id` int NOT NULL DEFAULT '0' AFTER `fund_id`;

ALTER TABLE `wo_pinnedposts`
ADD `private_id` int(11) NOT NULL DEFAULT '0' AFTER `group_id`;

-- 2020-07-21 10:35:04