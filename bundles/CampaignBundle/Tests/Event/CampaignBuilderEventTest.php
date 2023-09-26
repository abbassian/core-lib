<?php

namespace Autoborna\CampaignBundle\Tests\Event;

use Autoborna\AssetBundle\Form\Type\PointActionAssetDownloadType;
use Autoborna\CampaignBundle\Event\CampaignBuilderEvent;
use Autoborna\CampaignBundle\Tests\CampaignTestAbstract;
use Autoborna\CoreBundle\Translation\Translator;
use Autoborna\FormBundle\Form\Type\CampaignEventFormFieldValueType;

class CampaignBuilderEventTest extends CampaignTestAbstract
{
    public function testAddGetDecision()
    {
        $decisionKey = 'email.open';
        $decision    = [
            'label'                  => 'autoborna.email.campaign.event.open',
            'description'            => 'autoborna.email.campaign.event.open_descr',
            'eventName'              => 'autoborna.email.on_campaign_trigger_decision',
            'connectionRestrictions' => [
                'source' => [
                    'action' => [
                        'email.send',
                    ],
                ],
            ],
        ];
        $event = $this->initEvent();
        $event->addDecision(
            $decisionKey,
            $decision
        );

        $decisions = $event->getDecisions();
        $this->assertSame([$decisionKey => $decision], $decisions);
    }

    public function testEventDecisionSort()
    {
        $decision = [
            'label'                  => 'autoborna.email.campaign.event.open',
            'description'            => 'autoborna.email.campaign.event.open_descr',
            'eventName'              => 'autoborna.email.on_campaign_trigger_decision',
            'connectionRestrictions' => [
                'source' => [
                    'action' => [
                        'email.send',
                    ],
                ],
            ],
        ];
        $event = $this->initEvent();

        // add 3 unsorted decisions
        $event->addDecision('email.open1', $decision);
        $decision['label'] = 'autoborna.email.campaign.event.open.3';
        $event->addDecision('email.open3', $decision);
        $decision['label'] = 'autoborna.email.campaign.event.open.2';
        $event->addDecision('email.open2', $decision);

        $decisions = $event->getDecisions();

        $this->assertCount(3, $decisions);

        $shouldBe = 1;
        foreach ($decisions as $key => $resultDecision) {
            $this->assertSame('email.open'.$shouldBe, $key);
            ++$shouldBe;
        }
    }

    public function testEventConditionSort()
    {
        $condition = [
            'label'       => 'autoborna.form.campaign.event.field_value',
            'description' => 'autoborna.form.campaign.event.field_value_descr',
            'formType'    => CampaignEventFormFieldValueType::class,
            'formTheme'   => 'AutobornaFormBundle:FormTheme\FieldValueCondition',
            'eventName'   => 'autoborna.form.on_campaign_trigger_condition',
        ];
        $event = $this->initEvent();

        // add 3 unsorted conditions
        $event->addCondition('form.field_value1', $condition);
        $condition['label'] = 'autoborna.form.campaign.event.field_value.3';
        $event->addCondition('form.field_value3', $condition);
        $condition['label'] = 'autoborna.form.campaign.event.field_value.2';
        $event->addCondition('form.field_value2', $condition);

        $conditions = $event->getConditions();

        $this->assertCount(3, $conditions);

        $shouldBe = 1;
        foreach ($conditions as $key => $resultCondition) {
            $this->assertSame('form.field_value'.$shouldBe, $key);
            ++$shouldBe;
        }
    }

    public function testEventActionSort()
    {
        $action = [
            'group'       => 'autoborna.asset.actions',
            'label'       => 'autoborna.asset.point.action.download',
            'description' => 'autoborna.asset.point.action.download_descr',
            'callback'    => ['\\Autoborna\\AssetBundle\\Helper\\PointActionHelper', 'validateAssetDownload'],
            'formType'    => PointActionAssetDownloadType::class,
        ];
        $event = $this->initEvent();

        // add 3 unsorted actions
        $event->addAction('asset.download1', $action);
        $action['label'] = 'autoborna.asset.point.action.download.3';
        $event->addAction('asset.download3', $action);
        $action['label'] = 'autoborna.asset.point.action.download.2';
        $event->addAction('asset.download2', $action);

        $actions = $event->getActions();

        $this->assertCount(3, $actions);

        $shouldBe = 1;
        foreach ($actions as $key => $resultAction) {
            $this->assertSame('asset.download'.$shouldBe, $key);
            ++$shouldBe;
        }
    }

    protected function initEvent()
    {
        $translator = $this->getMockBuilder(Translator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $translator->expects($this->any())
            ->method('trans')
            ->will($this->returnCallback(function () {
                $args = func_get_args();

                return $args[0];
            }));

        return new CampaignBuilderEvent($translator);
    }
}
