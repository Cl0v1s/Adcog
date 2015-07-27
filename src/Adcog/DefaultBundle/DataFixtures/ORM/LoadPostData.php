<?php

namespace Adcog\DefaultBundle\DataFixtures\ORM;

use Adcog\DefaultBundle\Entity\Post;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class LoadPostData
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class LoadPostData extends AbstractFixture
{
    /**
     * {@inheritdoc}
     */
    function load(ObjectManager $manager)
    {
        $posts = [
            ['2009-08-28 18:34:12', 'Bilan 2008 - 2009', 1, ['Bilan'], __DIR__ . '/../../Resources/public/img/demo/ID-10088965.jpg'],
            ['2010-08-28 18:35:31', 'Bilan 2009 - 2010', 2, ['Bilan'], __DIR__ . '/../../Resources/public/img/demo/ID-100215712.jpg'],
            ['2011-02-11 15:49:54', 'Le BAC : le p\'tit nouveau de l\'AdCog', 3],
            ['2011-02-11 15:52:43', 'Le développement de la 4G et ses promesses.', 4],
            ['2011-02-11 15:53:29', 'Florent André (promo 2007) : Témoignage...', 5],
            ['2011-02-24 13:42:18', 'Thibault de Buttet (\'08) : Témoignage...', 6],
            ['2011-03-23 23:20:06', 'Formation de l\'équipe vidéo', 7],
            ['2011-03-31 18:45:25', 'Interview vidéo - Pierre Salaün (\'09)', 8],
            ['2011-05-03 11:42:21', 'REX sur la tenue d\'entretiens', 9],
            ['2011-06-08 22:59:07', 'Modélisation comportementale du conducteur', 10],
            ['2011-07-12 11:26:44', 'C\'est l\'été sur le B.A.C...', 11],
            ['2011-07-18 14:01:50', 'Edgar Morin fête ses 90 ans...', 12],
            ['2011-07-26 14:58:33', 'Interview Stéphane Thévenel (\'08)', 13],
            ['2011-08-03 15:09:23', 'Statistiques sur les freelances, anciens de l\'ENSC', 14],
            ['2011-08-16 15:33:34', 'Conseils pratiques pour les freelances', 15],
            ['2011-08-28 18:41:01', 'Bilan 2010 - 2011', 16, ['Bilan'], __DIR__ . '/../../Resources/public/img/demo/ID-100110731.jpg'],
            ['2011-10-01 09:42:33', 'Information Design : quelques graphiques insolites', 17],
            ['2011-10-28 14:18:46', 'Interview Yannick Roy (\'07)', 18],
            ['2012-02-08 12:46:03', 'Ils sont frais mes cogniticiens !', 19],
            ['2012-03-12 16:50:52', 'Cognito\'Conf 2012 - Le planning', 20, ['Cognito\'Conf']],
            ['2012-05-09 19:05:23', 'Cognito\'Conf 2012 – Caroline et l\'AdCog', 21, ['Cognito\'Conf']],
            ['2012-05-16 10:45:13', 'Cognito\'Conf 2012 - Mehdi-Loup, jeune embauché !', '21-2', ['Cognito\'Conf']],
            ['2012-05-23 09:23:49', 'Cognito\'Conf 2012 - Julien : Human Centred Design', 22, ['Cognito\'Conf']],
            ['2012-05-31 15:19:37', 'Cognito\'Conf 2012 - Sophie : L\'ergonomique à long terme', 23, ['Cognito\'Conf']],
            ['2012-06-06 18:35:38', 'Cognito\'Conf 2012 - Benoit Leblanc : L’école, les anciens, les élèves', 24, ['Cognito\'Conf']],
            ['2012-06-13 11:13:42', 'Cognito\'Conf 2012 - Florent : Thesaurus in action', 25, ['Cognito\'Conf']],
            ['2012-06-20 11:30:24', 'Cognito\'Conf 2012 - Jade : Point de vue étudiant', 26, ['Cognito\'Conf']],
            ['2012-06-28 13:52:52', 'Cognito\'Conf 2012 - Thibault : Retour d\'expérience, semaine de conception mixte ENSC/Telecom Paris Sud', 27, ['Cognito\'Conf']],
            ['2012-07-17 15:44:04', 'Qu’est ce qu’une association d’anciens ?', 28],
            ['2012-07-29 22:18:16', 'Chronique de vie : Iyan Johnson', 29],
            ['2012-08-28 18:42:28', 'Bilan 2011 - 2012', 30, ['Bilan'], __DIR__ . '/../../Resources/public/img/demo/ID-100143636.jpg'],
            ['2012-08-28 18:51:46', 'Inventaire à la Prévert , des choses à faire', 31],
            ['2012-12-05 18:30:32', 'Chronique de vie : Silvio Figeac-Galindo', 32],
            ['2013-01-31 12:32:38', 'Cognito\'Conf\' 2011 !', 33, ['Cognito\'Conf']],
            ['2013-02-07 12:12:55', 'Cognito\'Conf 2013 sur bordeaux ! A vos agendas !', 34, ['Cognito\'Conf']],
            ['2013-02-07 12:13:56', 'Cognito\'Conf 2013 - Appel à présentations !', 35, ['Cognito\'Conf']],
            ['2013-02-18 22:01:28', 'Taylorisme et croyances sur l\'Homme', 36],
            ['2013-03-01 00:20:24', 'Cognito\'Conf 2013 - Le planning', 37, ['Cognito\'Conf']],
        ];

        foreach ($posts as $item) {
            $post = new Post();
            $post->setTitle($item[1]);
            $post->setCreated(\DateTime::createFromFormat('Y-m-d H:i:s', $item[0]));
            $post->setText(file_get_contents(__DIR__ . sprintf('/../../Resources/private/post/%s.html', $item[2])));
            /** @noinspection PhpParamsInspection */
            $post->setAuthor($this->getReference('cpi'));

            if (false === empty($item[3])) {
                foreach ($item[3] as $tag) {
                    /** @noinspection PhpParamsInspection */
                    $post->addTag($this->getReference($tag));
                }
            }

            if (false === empty($item[4])) {
                copy($item[4], $target = sys_get_temp_dir() . '/' . uniqid());

                $post->setFile(new UploadedFile($target, basename($item[4]), 'image/jpeg'));
            }

            $manager->persist($post);
        }

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2;
    }
}
