<?php

namespace App\StringHelpers;

use App\Entity\Ad;
use Doctrine\ORM\EntityManagerInterface;

class Slugifier
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setAdSlug(Ad $ad)
    {
        $this->em->persist($ad);
        $this->em->flush();

    }

}