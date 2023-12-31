<?php

namespace Autoborna\FormBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Autoborna\CoreBundle\Helper\CsvHelper;
use Autoborna\CoreBundle\Helper\Serializer;
use Autoborna\FormBundle\Entity\Action;
use Autoborna\FormBundle\Entity\Field;
use Autoborna\FormBundle\Entity\Form;
use Autoborna\FormBundle\Model\ActionModel;
use Autoborna\FormBundle\Model\FieldModel;
use Autoborna\FormBundle\Model\FormModel;

class LoadFormData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * @var FormModel
     */
    private $formModel;

    /**
     * @var FieldModel
     */
    private $formFieldModel;

    /**
     * @var ActionModel
     */
    private $actionModel;

    public function __construct(FormModel $formModel, FieldModel $formFieldModel, ActionModel $actionModel)
    {
        $this->formModel      = $formModel;
        $this->formFieldModel = $formFieldModel;
        $this->actionModel    = $actionModel;
    }

    public function load(ObjectManager $manager)
    {
        $forms        = CsvHelper::csv_to_array(__DIR__.'/fakeformdata.csv');
        $formEntities = [];
        foreach ($forms as $count => $rows) {
            $form = new Form();
            $key  = $count + 1;
            foreach ($rows as $col => $val) {
                if ('NULL' != $val) {
                    $setter = 'set'.ucfirst($col);

                    if (in_array($col, ['dateAdded'])) {
                        $form->$setter(new \DateTime($val));
                    } elseif (in_array($col, ['cachedHtml'])) {
                        $val = stripslashes($val);
                        $form->$setter($val);
                    } else {
                        $form->$setter($val);
                    }
                }
            }
            $this->formModel->getRepository()->saveEntity($form);
            $formEntities[] = $form;
            $this->setReference('form-'.$key, $form);
        }

        //import fields
        $fields = CsvHelper::csv_to_array(__DIR__.'/fakefielddata.csv');
        foreach ($fields as $count => $rows) {
            $field = new Field();
            foreach ($rows as $col => $val) {
                if ('NULL' != $val) {
                    $setter = 'set'.ucfirst($col);

                    if (in_array($col, ['form'])) {
                        $form = $this->getReference('form-'.$val);
                        $field->$setter($form);
                        $form->addField($count, $field);
                    } elseif (in_array($col, ['customParameters', 'properties'])) {
                        $val = Serializer::decode(stripslashes($val));
                        $field->$setter($val);
                    } else {
                        $field->$setter($val);
                    }
                }
            }
            $this->formFieldModel->getRepository()->saveEntity($field);
        }

        //import actions
        $actions = CsvHelper::csv_to_array(__DIR__.'/fakeactiondata.csv');
        foreach ($actions as $rows) {
            $action = new Action();
            foreach ($rows as $col => $val) {
                if ('NULL' != $val) {
                    $setter = 'set'.ucfirst($col);

                    if (in_array($col, ['form'])) {
                        $action->$setter($this->getReference('form-'.$val));
                    } elseif (in_array($col, ['properties'])) {
                        $val = Serializer::decode(stripslashes($val));
                        if ('settings' == $col) {
                            $val['callback'] = stripslashes($val['callback']);
                        }

                        $action->$setter($val);
                    } else {
                        $action->$setter($val);
                    }
                }
            }
            $this->actionModel->getRepository()->saveEntity($action);
        }

        //create the tables
        foreach ($formEntities as $form) {
            //create the HTML
            $this->formModel->generateHtml($form);

            //create the schema
            $this->formModel->createTableSchema($form, true, true);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 8;
    }
}
