<?php

namespace Kcs\WatchdogBundle\Storage;

use Kcs\WatchdogBundle\Entity\AbstractError;
use Kcs\WatchdogBundle\Entity\Error;
use Doctrine\Bundle\DoctrineBundle\Registry;

/**
 * Doctrine ORM watchdog storage class
 *
 * @author Alessandro Chitolina <alekitto@gmail.com>
 */
class Doctrine implements StorageInterface
{
    /**
     * @var Registry
     */
    protected $doctrine;

    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    public function getNewEntity()
    {
        return new Error;
    }

    public function persist(AbstractError $error)
    {
        $this->doctrine->resetManager();
        $em = $this->doctrine->getManager();
        $em->persist($error);
        $em->flush();
    }

    public function find($id)
    {
        $em = $this->doctrine->getManager();
        return $em->find('KcsWatchdogBundle:Error', $id);
    }
}

