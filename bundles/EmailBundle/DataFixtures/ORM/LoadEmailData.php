<?php

namespace Autoborna\EmailBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\CoreBundle\Helper\CsvHelper;
use Autoborna\CoreBundle\Helper\Serializer;
use Autoborna\EmailBundle\Entity\Email;
use Autoborna\EmailBundle\Model\EmailModel;

class LoadEmailData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var EmailModel
     */
    private $emailModel;

    public function __construct(EmailModel $emailModel)
    {
        $this->emailModel = $emailModel;
    }

    public function load(ObjectManager $manager)
    {
        $emails = CsvHelper::csv_to_array(__DIR__.'/fakeemaildata.csv');

        foreach ($emails as $count => $rows) {
            $email = new Email();
            $email->setDateAdded(new \DateTime());
            $key = $count + 1;
            foreach ($rows as $col => $val) {
                if ('NULL' != $val) {
                    $setter = 'set'.ucfirst($col);
                    if (in_array($col, ['content', 'variantSettings'])) {
                        $val = Serializer::decode(stripslashes($val));
                    }
                    $email->$setter($val);
                }
            }
            $email->addList($this->getReference('lead-list'));

            $this->emailModel->getRepository()->saveEntity($email);
            $this->setReference('email-'.$key, $email);
        }
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return 9;
    }
}
