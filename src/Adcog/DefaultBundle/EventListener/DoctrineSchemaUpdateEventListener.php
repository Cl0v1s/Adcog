<?php

namespace Adcog\DefaultBundle\EventListener;

use Doctrine\DBAL\Schema\Column;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DoctrineSchemaUpdateEventListener
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class DoctrineSchemaUpdateEventListener
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * On console command
     *
     * @param ConsoleCommandEvent $event
     */
    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        if ('doctrine:schema:update' !== $event->getCommand()->getName()) {
            return;
        }

        $event->getOutput()->writeln('<info>Migrating database ...</info>');

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $co = $em->getConnection();
        $co->beginTransaction();

        // Migrate files
        $fileTable = $this->getTable('File');
		
        // Migrate user table
        $userTable = $this->getTable('User');
		# If table doesn't exist
		$schemaManager = $co->getSchemaManager();
		if ($schemaManager->tablesExist(array('User')) == false) {
			return;
		}
        $userColumns = $this->getColumns('User');
        if (false === array_key_exists('filename', $userColumns)) {
            $event->getOutput()->writeln('<info>Migrating user table ...</info>');

            $co->query(sprintf('ALTER TABLE %s ADD filename varchar(255) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD extension varchar(255) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD mime varchar(255) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD path varchar(255) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD size int(11) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD uniqid VARCHAR(13) DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD version INT DEFAULT NULL', $userTable));
            $co->query(sprintf('ALTER TABLE %s ADD uri varchar(255) DEFAULT NULL', $userTable));

            // Migrate files for user
            $statement = $co->prepare(sprintf('SELECT u.id AS user_id,f.id AS file_id,f.filename,f.extension,f.mime,f.path,f.uri,f.size FROM %s u INNER JOIN %s f ON u.file_id = f.id', $userTable, $fileTable));
            $statement->execute();
            $co->query(sprintf('UPDATE %s SET file_id = NULL', $userTable));
            foreach ($statement->fetchAll() as $user) {
                // Delete file
                $co
                    ->prepare(sprintf('DELETE FROM %s WHERE id = :id', $fileTable))
                    ->execute(['id' => $user['file_id']]);

                // Add file info in user
                $co
                    ->prepare(sprintf('UPDATE %s SET filename = :filename, extension = :extension, mime = :mime, path = :path, uri = :uri, size = :size WHERE id = :id', $userTable))
                    ->execute([
                        'filename' => $user['filename'],
                        'extension' => $user['extension'],
                        'mime' => $user['mime'],
                        'path' => $user['path'],
                        'uri' => $user['uri'],
                        'size' => $user['size'],
                        'id' => $user['user_id'],
                    ]);
            }
        }

        // Migrate post table
        $postTable = $this->getTable('Post');
        $postColumns = $this->getColumns('Post');
        if (false === array_key_exists('filename', $postColumns)) {
            $event->getOutput()->writeln('<info>Migrating post table ...</info>');

            $co->query(sprintf('ALTER TABLE %s ADD filename varchar(255) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD extension varchar(255) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD mime varchar(255) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD path varchar(255) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD size int(11) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD uniqid VARCHAR(13) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD version INT DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s CHANGE uri slug varchar(255) DEFAULT NULL', $postTable));
            $co->query(sprintf('ALTER TABLE %s ADD uri varchar(255) DEFAULT NULL', $postTable));

            // Migrate files for post
            $statement = $co->prepare(sprintf('SELECT p.id AS post_id,f.id AS file_id,f.filename,f.extension,f.mime,f.path,f.uri,f.size FROM %s p INNER JOIN %s f ON p.file_id = f.id', $postTable, $fileTable));
            $statement->execute();
            $co->query(sprintf('UPDATE %s SET file_id = NULL', $postTable));
            foreach ($statement->fetchAll() as $post) {
                // Delete file
                $co
                    ->prepare(sprintf('DELETE FROM %s WHERE id = :id', $fileTable))
                    ->execute(['id' => $post['file_id']]);

                // Migrate file info in post
                $co
                    ->prepare(sprintf('UPDATE %s SET filename = :filename, extension = :extension, mime = :mime, path = :path, uri = :uri, size = :size WHERE id = :id', $postTable))
                    ->execute([
                        'filename' => $post['filename'],
                        'extension' => $post['extension'],
                        'mime' => $post['mime'],
                        'path' => $post['path'],
                        'uri' => $post['uri'],
                        'size' => $post['size'],
                        'id' => $post['post_id'],
                    ]);
            }
        }

        // Update array structures in payments
        $this->getEm()->getConnection()->prepare('UPDATE adcog_payment SET token = "a:0:{}" WHERE token IS NULL AND discr = "paypal"')->execute();
        if (in_array('payment', $this->getColumns('PaymentPaypal'))) {
            $this->getEm()->getConnection()->prepare('UPDATE adcog_payment SET payment = "a:0:{}" WHERE payment IS NULL AND discr = "paypal"')->execute();
        }

        $co->commit();
    }

    /**
     * On console terminate
     *
     * @param ConsoleTerminateEvent $event
     */
    public function onConsoleTerminate(ConsoleTerminateEvent $event)
    {
        if ('doctrine:schema:update' !== $event->getCommand()->getName()) {
            return;
        }

        $event->getOutput()->writeln('<info>Migrating data ...</info>');

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $co = $em->getConnection();
        $co->beginTransaction();

        // Migrate default static contents
        $staticContentTable = $this->getTable('StaticContent');
		# If table doesn't exist
		$schemaManager = $co->getSchemaManager();
		if ($schemaManager->tablesExist(array($staticContentTable)) == false) {
			return;
		}
        $statement = $co->executeQuery(sprintf('SELECT COUNT(*) AS count FROM %s', $staticContentTable));
        $count = (int)$statement->fetch()['count'];
        if (0 === $count) {
            $event->getOutput()->writeln('<info>Add default static contents ...</info>');

            $sql = <<<EOF
INSERT INTO `%s` (`id`, `title`, `content`, `created`, `updated`, `type`) VALUES
(1, 'Bienvenue sur le site de l''ADCOG !', '<p>L''association des Diplômés en Cognitique est heureuse de vous présenter la première mise à jour de son système d''information. Nouveau format. Nouveau design. Nouvelles fonctionnalités. Ces améliorations sont les premières d''une longue liste pour fournir de plus en plus d''outils à la hauteur des attentes des anciens de l'' <a href="/ensc">École Nationale Supérieure de Cognitique</a>.</p>\r\n\r\n<p>Pour nous soutenir dans cette démarche il vous suffit de vous inscrire via la page <a href="/inscription">Inscription</a> puis d''adhérer via la page <a href="/utilisateur/adhesion">Mon adhésion</a>. Si vous êtes déjà adhérent à l''ADCOG, connectez vous sur le site avec l''identifiant et le mot de passe que nous vous avons fourni sur la page <a href="/connexion">Connexion</a>.</p>', '2014-09-28 13:45:32', '2014-09-28 13:48:52', 'TYPE_DEFAULT_INDEX_WELCOME'),
(2, 'Le mot de l''association', '<p>Depuis 2007, l''ADCOG, association des anciens élèves de l''<a href="/ensc">École Nationale Supérieure de Cognitique</a> et de l''<a href="/ensc">Institut de Cognitique</a>, facilite le rapprochement des anciens élèves et souhaite promouvoir la prise en compte de l''Homme dans la conception de produits et de services au sein des entreprises. Cela se concrétise notamment par l''organisation d''évènements tels que la Cognito''Conf, où des anciens échangent sur des sujets liés à la Cognitique. De plus, l''ADCOG participe chaque année au Gala de l''ENSC pour rencontrer les nouveaux diplômés et les soutenir lors de leurs premiers pas dans le monde professionnel.</p>\r\n\r\n<p>Convaincue par l''importance de l''échange entre anciens, l''ADCOG souhaite ralier les jeunes diplômés, à la recherche d''une première expérience professionnelle, tout comme les ingénieurs confirmés souhaitant échanger sur des pratiques ou des concepts auprès de leurs pairs. L''année 2012 marque un tournant dans l''histoire de l''association puisque désormais, le nombre d''ancien est supérieur au nombre d''étudiants au sein de l''ENSC. Il est grand temps d''organiser un réseau de qualité, efficace et solide. Mais cela est impossible sans votre soutien et votre engagement, alors n''attendez plus pour adhérer !</p>', '2014-09-28 13:49:37', NULL, 'TYPE_DEFAULT_INDEX_ORGANIZATION'),
(3, 'Adhérez', '<p>Adhérer à l''ADCOG en 2014, c''est avant tout affirmer sa volonté d''animer le réseau des Ingénieurs diplômés de l''ENSC. En adhérant, vous soutenez le <strong>développement de l''association</strong>, rendez possible <strong>l''organisation d''événements</strong> et aidez à promouvoir le diplôme d''Ingénieur en Cognitique.</p>', '2014-09-28 13:50:34', NULL, 'TYPE_DEFAULT_INDEX_JOIN'),
(4, 'Intervenez', '<p>\r\n                        Depuis sa création en 2007, l''ADCOG favorise les échanges entre anciens élèves de l''ENSC en\r\n                        maintenant notamment sa liste de diffusion. Cette liste est ouverte à tous, n''hésitez donc pas à\r\n                        vous abonner à la <strong>Cognitiliste</strong> pour être tenu informé de l''actualité de l''association des anciens et\r\n                        de l''ENSC.\r\n                    </p>', '2014-09-28 13:50:58', NULL, 'TYPE_DEFAULT_INDEX_SPEEK'),
(5, 'Diffusez', '<p>\r\n                        Vous souhaitez en savoir plus sur l''association des anciens, obtenir des informations sur les\r\n                        services qu''elle propose ou tout simplement la présenter à d''autres personnes ? N''hésitez pas à\r\n                        télécharger nos <strong>brochures</strong> et nos <strong>supports de communication</strong>. Ils sont à diffuser avec excès.\r\n                    </p>', '2014-09-28 13:51:23', NULL, 'TYPE_DEFAULT_INDEX_SHARE'),
(6, 'L''association', '<p>\r\n        Elle a pour buts :\r\n    </p>\r\n<ul>\r\n        <li>d''établir un lien de solidarité et d''amitié entre tous les anciens élèves,</li>\r\n        <li>de relier successivement les promotions nouvelles aux promotions antérieures,</li>\r\n        <li>d''utiliser les rapports ainsi créés aussi bien dans l''intérêt général qu''au profit des membres eux-mêmes,</li>\r\n        <li>de faire en sorte que le diplôme d''Ingénieur Cogniticien conserve toute sa valeur en contribuant avec le Conseil d''Administration et la Direction de l''École à ce que l''enseignement prodigué à l''École s''adapte en permanence aux besoins de la société.</li>\r\n    </ul>\r\n<p>\r\n        Peuvent être membres de l''Association les anciens élèves de l''<a href="/ensc" title="École Nationale Supérieure de Cognitique" class="">École Nationale Supérieure de Cognitique</a> ou de l''<a href="/ensc" title="École Nationale Supérieure de Cognitique" class="">Institut de Cognitique</a>\r\n        ayant obtenu le diplôme d''ingénieur en cognitique ou le master 2 délivrés par cette\r\n        école. Toute autre personne qui en fait la demande, sur autorisation du Conseil d''Administration, peut\r\n        également être membre.\r\n    </p>', '2014-09-28 13:54:55', NULL, 'TYPE_DEFAULT_PRESENTATION_ORGANIZATION'),
(7, 'Liste de diffusion', '<p>\r\n                L''association met à disposition une liste de diffusion par emails.\r\n                Cette liste permet aux membres de communiquer rapidement les uns avec les autres.\r\n            </p>\r\n<p class="text-left">Pour vous inscrire a la liste de diffusion cogniticiens, envoyez un email à :</p>\r\n<p class="well text-center">\r\n                cogniticiens-subscribe@adcog.fr\r\n            </p>\r\n<p class="text-left">Pour communiquer avec les membres de la liste de diffusion, envoyer un email à :</p>\r\n<p class="well text-center">\r\n                cogniticiens@adcog.fr\r\n            </p>\r\n<p class="text-left">Pour retirer votre adresse de la liste, envoyez simplement un email a :</p>\r\n<p class="well text-center">\r\n                cogniticiens-unsubscribe@adcog.fr\r\n            </p>', '2014-09-28 13:55:55', NULL, 'TYPE_DEFAULT_PRESENTATION_LIST'),
(8, 'Le bureau et le CA', '<p>La composition du bureau et du CA est la suivante</p>', '2014-09-28 13:56:45', NULL, 'TYPE_DEFAULT_PRESENTATION_OFFICE'),
(9, 'École Nationale Supérieure de Cognitique', '<blockquote>\r\n<p>L''ENSC est une école publique d''ingénieurs de l''<a href="/inp-bordeaux">Institut National Polytechnique de Bordeaux</a>. Elle forme des ingénieurs diplômés en "cognitique" spécialistes de la cognition artificielle ou augmentée, des technologies numériques et de leurs usages, du facteur humain, de l''ergonomie et de l''intégration homme-systèmes.</p>\r\n\r\n<footer>Site officiel de l''ENSC : <cite><a href="http://www.ensc.fr" target="_blank">http://www.ensc.fr</a></cite></footer>\r\n</blockquote>\r\n\r\n<p><img alt="ENSC" src="/bundles/adcogdefault/img/ensc256.png?1399642099" style="float:left; margin:15px 15px 15px 0px" /></p>\r\n\r\n<p>En 1983, Jean-Michel Truong associe "connaissance" et "automatique" pour former le mot "cognitique". Il désigne ainsi la science du traitement automatique de la connaissance et des relations entre l''Homme et les technologies de l''information et de la communication.</p>\r\n\r\n<p>L''ENSC a pour vocation de former les étudiants et les élèves ingénieurs en cognitique et en ingénierie humaine, et à valoriser une recherche appliquée avec les entreprises du domaine. Membre fondateur du Pôle de compétitivité "Aerospace Valley", l''ENSC bénéficie d''un partenariat étroit avec des entreprises, des organisations professionnelles, un réseau de laboratoires de recherche et d''autres écoles d''ingénieurs nationales et universités étrangères. Anciennement institut interne de l''Université Bordeaux 2, l''Institut de Cognitique a été créé par le décret du 20 août 2003. Transféré à l''<a href="/inp-bordeaux">Institut Polytechnique de Bordeaux</a> le 1er avril 2009, par le décret du 25 mars 2009, l''IDC a été dissout et recréé sous le nom d''Ecole Nationale Supérieure de Cognitique (ENSC).</p>', '2014-09-28 14:00:18', '2014-09-28 14:17:07', 'TYPE_DEFAULT_ENSC'),
(10, 'Institut National Polytechnique de Bordeaux', '<p><img alt="INP" src="/bundles/adcogdefault/img/inp256.png?1399642099" style="float:right; margin:15px 0px 15px 15px" /></p>\r\n\r\n<blockquote>\r\n<p>L’Institut National Polytechnique de Bordeaux (INP) est un établissement d’enseignement supérieur, sous tutelle du ministère chargé de l’enseignement supérieur et de la recherche, et constitué en grand établissement au sens de l''article L. 717-1 du Code de l''éducation.</p>\r\n\r\n<p>Créé en 2009 sous le nom Institut Polytechnique de Bordeaux (IPB), il regroupe toutes les formations d’ingénieurs de l’Université de Bordeaux ; son siège est situé sur le campus Talence Pessac Gradignan. En septembre 2014, l’institut polytechnique de Bordeaux adopte comme nom de marque « Bordeaux INP » et change son logo afin d’affirmer son appartenance au réseau des INP.</p>\r\n\r\n<footer>Site officiel de l''INP de Bordeaux : <cite><a href="http://www.bordeaux-inp.fr/" target="_blank">http://www.bordeaux-inp.fr/</a></cite></footer>\r\n</blockquote>', '2014-09-28 14:02:56', '2014-09-28 14:16:55', 'TYPE_DEFAULT_INP_BORDEAUX'),
(11, 'Pi''Cognitique', '<p>\r\n                    Les Pi''Cognitiques sont des pique-niques prenant part dans les principales villes où travaillent les cogniticiens.\r\n                    Ils sont organisés par les membres de l''association pour que les cogniticiens puissent se retrouver et échanger\r\n                    lors d''un moment convivial tout en profitant d''un bol d''air frais.\r\n                </p>', '2014-09-28 14:17:30', NULL, 'TYPE_DEFAULT_EVENT_PICOGNITIQUE'),
(12, 'Cognito''Conf', '<p>\r\n                    La Cognito''Conf est une demi-journée de présentations et de discussions autour des thèmes rencontrés par les\r\n                    anciens dans leur quotidien professionnel. L''objectif de cet évènement est de permettre à tous les cogniticiens,\r\n                    provenant de toutes les promotions, de partager des idées, des problèmes, des astuces, des bon plans, des connaissances\r\n                    en tout genre, et des expériences, le tout dans une atmosphère cordiale et détendue. La Cognito''Conf est organisée\r\n                    une fois par an par l''ADCOG.\r\n                </p>', '2014-09-28 14:17:46', NULL, 'TYPE_DEFAULT_EVENT_COGNITOCONF'),
(13, 'Cog''Out', '<p>\r\n                    Les Cog''Out sont des afterwork organisés par les ambassadeurs de l''ADCOG situés dans les principales villes où sont\r\n                    regroupés les cogniticiens. Ces soirées sont l''occasion de décompresser après une journée de travail et de partager\r\n                    son quotidien professionnel auprès des autres cogniticiens.\r\n                </p>', '2014-09-28 14:18:08', NULL, 'TYPE_DEFAULT_EVENT_COGOUT'),
(14, 'N''hésitez plus !', '<p class="margin-bottom">\r\n                <span class="label label-warning pull-left bigger">\r\n                    <span class="fa fa-exclamation-circle"></span>\r\n                </span>\r\n                En moins de 30 secondes vous pouvez créer votre compte sur le site de l''ADCOG.\r\n                C''est simple et en plus <strong>c''est gratuit</strong>.\r\n                Alors, pourquoi s''en priver ?\r\n            </p>', '2014-09-28 14:24:26', NULL, 'TYPE_DEFAULT_REGISTER_GO'),
(15, 'À quoi sert l''inscription ?', '<p class="margin-bottom">\r\n                <span class="label label-info pull-left bigger">\r\n                    <span class="fa fa-question-circle"></span>\r\n                </span>\r\n                Avec votre compte, vous serez en mesure de participer aux événements de l''association\r\n                et de renseigner vos diplômes, stages, thèses et expériences professionnelles\r\n                pour <strong>l''ensemble du réseau des diplômés</strong> de l''<a href="/ensc" title="École Nationale Supérieure de Cognitique" class="">École Nationale Supérieure de Cognitique</a>.\r\n            </p>', '2014-09-28 14:24:47', NULL, 'TYPE_DEFAULT_REGISTER_WHY'),
(16, 'Pour en profiter encore plus ...', '<p class="margin-bottom">\r\n                <span class="label label-success pull-left bigger">\r\n                    <span class="fa fa-unlock-alt"></span>\r\n                </span>\r\n                Une fois inscrit, il vous sera possible d''adhérer à l''association des anciens. Vous aurez\r\n                alors accès à la <strong>messagerie</strong>, à l''<strong>annuaire</strong> et aux <strong>statistiques</strong> concernant les anciens diplômés.\r\n            </p>', '2014-09-28 14:25:10', '2014-09-28 14:25:33', 'TYPE_DEFAULT_REGISTER_MORE');
EOF;

            $co->executeQuery(sprintf($sql, $staticContentTable));
        }

        $co->commit();
    }

    /**
     * Get columns
     *
     * @param string $entity
     *
     * @return string[]
     */
    private function getColumns($entity)
    {
        return array_map(function (Column $column) {
            return $column->getName();
        }, $this->getEm()->getConnection()->getSchemaManager()->listTableColumns($this->getTable($entity)));
    }

    /**
     * Get entity manager
     *
     * @return EntityManager
     */
    private function getEm()
    {
        return $this->container->get('doctrine.orm.default_entity_manager');
    }

    /**
     * Get table
     *
     * @param string $entity
     *
     * @return string
     */
    private function getTable($entity)
    {
        return $this->getEm()->getClassMetadata(sprintf('Adcog\DefaultBundle\Entity\%s', $entity))->getTableName();
    }
}
