<?php

namespace Adcog\DefaultBundle\Controller;

use Adcog\DefaultBundle\Entity\Comment;
use Adcog\DefaultBundle\Entity\Post;
use Adcog\DefaultBundle\Entity\Tag;
use Adcog\DefaultBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class BlogController
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 * @Route("/blog")
 */
class BlogController extends Controller
{
    /**
     * Index
     *
     * @return array
     * @Route("/{page}", requirements={"page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->getPaginator($paginatorHelper, ['not_validated' => false]);

        return [
            'paginator' => $paginator,
        ];
    }

    /**
     * RSS
     *
     * @return array
     * @Route("/feed.xml")
     * @Method("GET")
     */
    public function rssAction()
    {
        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->getPaginator($paginatorHelper, ['not_validated' => false]);

        $lines = [];
        $lines[] = <<<EOF
<?xml version="1.0" encoding="utf-8"?>
<rss version="2.0">
    <channel>
        <title>BAC</title>
        <link>http://www.adcog.fr</link>
        <description>Blog de Anciens Diplômés en Cognitique</description>
EOF;
        foreach ($paginator as $post) {
            $line = <<<EOF
        <item>
            <title>%1\$s</title>
            <link>%2\$s</link>
            <guid>%2\$s</guid>
            <pubDate>%3\$s</pubDate>
            <description>%4\$s</description>
        </item>
EOF;

            $lines[] = sprintf(
                $line,
                $post->getTitle(),
                $this->generateUrl('blog_read', [
                    'post' => $post->getId(),
                    'slug' => $post->getSlug(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                $post->getCreated()->format(\DateTime::COOKIE),
                $this->get('eb_string')->cut(str_replace(PHP_EOL, ' ', strip_tags($post->getText())), 200)
            );
        }
        $lines[] = <<<EOF
    </channel>
</rss>
EOF;

        return new Response(implode(PHP_EOL, $lines), 200, [
            'Content-Type' => 'text/xml',
        ]);
    }

    /**
     * Tag
     *
     * @param Tag    $tag  Tag
     * @param string $slug Slug
     *
     * @return RedirectResponse|array
     * @Route("/tag/{tag}-{slug}/{page}", requirements={"tag":"\d+","page":"\d+"}, defaults={"page":1})
     * @Method("GET")
     * @Template()
     */
    public function tagAction(Tag $tag, $slug)
    {
        if ($slug !== $tag->getSlug()) {
            return $this->redirect($this->generateUrl('blog_tag', [
                'tag' => $tag->getId(),
                'slug' => $tag->getSlug(),
                'page' => 1,
            ]));
        }

        $paginatorHelper = $this->get('eb_paginator_helper');
        $paginator = $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->getPaginator($paginatorHelper, ['not_validated' => false, 'tag' => $tag]);

        return [
            'tag' => $tag,
            'paginator' => $paginator,
        ];
    }

    /**
     * Read
     *
     * @param Request $request Request
     * @param Post    $post    Post
     * @param string  $slug    Slug
     *
     * @return array
     * @throws NotFoundHttpException
     * @Route("/{post}-{slug}", requirements={"id":"\d+"})
     * @ParamConverter("post", class="AdcogDefaultBundle:Post")
     * @Method("GET|POST")
     * @Template()
     */
    public function readAction(Request $request, Post $post, $slug)
    {
        if ($slug !== $post->getSlug()) {
            return $this->redirect($this->generateUrl('blog_read', [
                'post' => $post->getId(),
                'slug' => $post->getSlug(),
            ]));
        }

        if (false === $post->isValid() && false === $this->get('security.authorization_checker')->isGranted(User::ROLE_BLOGGER)) {
            throw new NotFoundHttpException();
        }

        if (null !== $user = $this->getUser()) {
            $comment = new Comment();
            $comment->setAuthor($user);
            $comment->setPost($post);
            $form = $this->createForm('adcog_comment_add', $comment);
            if (true === $form->handleRequest($request)->isValid()) {
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($comment);
                $em->flush();

                $session = $request->getSession();
                if ($session instanceof Session) {
                    $session->getFlashBag()->add('warning', 'Votre commentaire doit être validé par un administrateur pour être visible.');
                }

                return $this->redirect($this->generateUrl('blog_read', [
                        'post' => $post->getId(),
                        'slug' => $post->getSlug(),
                    ]) . '#comments');
            }
        }

        return [
            'post' => $post,
            'comments' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Comment')->findForPost($post),
            'form' => isset($form) ? $form->createView() : null,
        ];
    }

    /**
     * Display recent tags in twig
     *
     * @return array
     * @Template()
     */
    public function popularTagsAction()
    {
        $statement = <<<EOF
SELECT t.id
FROM adcog_tag t
INNER JOIN post_tag a ON a.tag_id = t.id
INNER JOIN adcog_post p ON p.id = a.post_id
WHERE p.validated IS NOT NULL
GROUP BY t.id
ORDER BY COUNT(DISTINCT(p.id)) DESC
EOF;

        /** @var EntityManager $em */
        $em = $this->get('doctrine.orm.default_entity_manager');
        $query = $em->getConnection()->prepare($statement);
        $query->execute();

        return [
            'tags' => array_map(function (array $item) use ($em) {
                return $em->getRepository('AdcogDefaultBundle:Tag')->find((int)$item['id']);
            }, $query->fetchAll()),
        ];
    }

    /**
     * Display recent comment in twig
     *
     * @return array
     * @Template()
     */

    public function popularCommentsAction()
    {
        return [
            'comments' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Comment')->findPopular(),
        ];
    }

    /**
     * Display recent article in twig
     *
     * @return array
     * @Template()
     */
    public function recentArticlesAction()
    {
        return [
            'posts' => $this->get('doctrine.orm.default_entity_manager')->getRepository('AdcogDefaultBundle:Post')->findRecent(),
        ];
    }
}
