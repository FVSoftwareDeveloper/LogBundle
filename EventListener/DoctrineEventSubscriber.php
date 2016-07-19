<?php

namespace NTI\LogBundle\EventListener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Symfony\Component\CssSelector\Parser\Token;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\SecurityContext;
use NTI\LogBundle\Entity\Log;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

use JMS\Serializer\SerializerBuilder;
use NTI\LogBundle\Logger;

class DoctrineEventSubscriber implements EventSubscriber
{
    private $container;
    /** @var Logger $logger */
    private $logger;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            'postPersist',
            'postUpdate',
            'preRemove',
        );
    }
    
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->logger = new Logger($this->container);
        $entity = $args->getEntity();
        if(is_string($entity)) {
            $entity = null;
        }
        $this->logger->logSuccess("Created ".get_class($entity).".", Log::ACTION_CREATE, $entity);
    }
    
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->logger = new Logger($this->container);
        $entity = $args->getEntity();
        if(is_string($entity)) {
            $entity = null;
        }
        $this->logger->logSuccess("Updated ".get_class($entity).".", Log::ACTION_UPDATE, $entity);
    }    
    
    public function preRemove(LifecycleEventArgs $args)
    {
        $this->logger = new Logger($this->container);
        $entity = $args->getEntity();
        if(is_string($entity)) {
            $entity = null;
        }
        $this->logger->logWarning("Removed ".get_class($entity).".", Log::ACTION_DELETE, $entity);
    }  
}