<?php

namespace Autoborna\LeadBundle\Tests\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\PageBundle\Entity\Hit;

class LoadPageHitData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $hits = [
            [
                'ipAddress'  => $this->getReference('ipAddress-1'),
                'url'        => 'http://test.com',
                'urlTitle'   => 'Test Title',
                'referer'    => 'http://autoborna.com',
                'alias'      => 'hit-1',
                'contact'    => $this->getReference('lead-1'),
                'dateHit'    => new \DateTime('-1 day'),
                'code'       => 200,
                'trackingId' => 'asdf',
            ],
        ];

        foreach ($hits as $hitConfig) {
            $this->createHit($hitConfig, $manager);
        }
    }

    protected function createHit($hitConfig, ObjectManager $manager)
    {
        $hit = new Hit();

        $hit->setIpAddress($hitConfig['ipAddress']);
        $hit->setUrl($hitConfig['url']);
        $hit->setReferer($hitConfig['referer']);
        $hit->setUrlTitle($hitConfig['urlTitle']);
        $hit->setLead($hitConfig['contact']);
        $hit->setDateHit($hitConfig['dateHit']);
        $hit->setCode($hitConfig['code']);
        $hit->setTrackingId($hitConfig['trackingId']);

        $this->setReference($hitConfig['alias'], $hit);

        $manager->persist($hit);
        $manager->flush();
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 6;
    }
}
