<?php

namespace Adcog\DefaultBundle\DependencyInjection;

use Adcog\DefaultBundle\Routing\RouteLoader;
use Doctrine\ORM\EntityManager;
use EB\DoctrineBundle\Converter\StringConverter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class AdcogDefaultExtension
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class AdcogDefaultExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /** @var Definition[] $services */
        $services = [
            'doctrine.orm.default_entity_manager' => new Definition(EntityManager::class),
            'router' => new Definition(RouterInterface::class),
            'security.authorization_checker' => new Definition(AuthorizationCheckerInterface::class),
            'eb_string' => new Definition(StringConverter::class),
            'translator' => new Definition(TranslatorInterface::class),
        ];

        // Load other services
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('transformer.xml');
        $loader->load('form.xml');
        $loader->load('static_content.xml');

        // Routing
        $container->setParameter('sensio_framework_extra.routing.loader.annot_class.class', RouteLoader::class);

        // Load forms
        $services = array_merge($services, $container->getDefinitions());
        $files = glob(__DIR__ . '/../Form/*Type.php');
        foreach ($files as $file) {
            if (0 !== strpos($name = mb_substr(pathinfo($file, PATHINFO_FILENAME), 0, -4), 'Abstract')) {
                $camel = ContainerBuilder::underscore($name);
                $alias = sprintf('adcog_%s', $camel);

                $form = new Definition($class = sprintf('Adcog\DefaultBundle\Form\%sType', $name));
                $form->addMethodCall('setName', [$alias]);
                $form->addTag('form.type', ['alias' => $alias]);
                $container->setDefinition(sprintf('adcog_default.form.%s', $camel), $form);

                // Add constructor values
                $ref = new \ReflectionClass($class);
                if (null !== $constructor = $ref->getConstructor()) {
                    $parameters = $constructor->getParameters();
                    foreach ($parameters as $parameter) {
                        $namespace = $parameter->getClass()->getName();
                        foreach ($services as $key => $service) {
                            $class = $service->getClass();
                            if (0 === strpos($class, '%')) {
                                $class = $container->getParameter(mb_strcut($class, 1, -1));
                            }
                            if ($class === $namespace) {
                                $form->addArgument(new Reference($key));
                            }
                        }
                    }
                }
            }
        }
    }
}
