<?php

namespace Autoborna\FormBundle\Tests\Helper;

use Autoborna\FormBundle\Entity\Field;
use Autoborna\FormBundle\Helper\FormFieldHelper;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class FormFieldHelperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormFieldHelper
     */
    protected $fixture;

    protected function setUp(): void
    {
        $translatorMock = $this->getMockBuilder(TranslatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $validatorMock = $this->getMockBuilder(ValidatorInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->fixture = new FormFieldHelper($translatorMock, $validatorMock);
    }

    /**
     * @dataProvider fieldProvider
     */
    public function testPopulateField($field, $value, $formHtml, $expectedValue, $message)
    {
        $this->fixture->populateField($field, $value, 'autoborna', $formHtml);

        $this->assertEquals($expectedValue, $formHtml, $message);
    }

    /**
     * @return array
     */
    public function fieldProvider()
    {
        return [
            [
                $this->getField('First Name', 'text'),
                '%22%2F%3E%3Cscript%3Ealert%280%29%3C%2Fscript%3E',
                '<input id="autobornaform_input_autoborna_firstname" value="" />',
                '<input id="autobornaform_input_autoborna_firstname" value="&quot;/&gt;alert(0)" />',
                'Tags should be stripped from text field values submitted via GET to prevent XSS.',
            ],
            [
                $this->getField('First Name', 'text'),
                '%22%20onfocus=%22alert(123)',
                '<input id="autobornaform_input_autoborna_firstname" value="" />',
                '<input id="autobornaform_input_autoborna_firstname" value="&quot; onfocus=&quot;alert(123)" />',
                'Inline JS values should not be allowed via GET to prevent XSS.',
            ],
            [
                $this->getField('Description', 'textarea'),
                '%22%2F%3E%3Cscript%3Ealert%280%29%3C%2Fscript%3E',
                '<textarea id="autobornaform_input_autoborna_description"></textarea>',
                '<textarea id="autobornaform_input_autoborna_description">&quot;/&gt;alert(0)</textarea>',
                'Tags should be stripped from textarea field values submitted via GET to prevent XSS.',
            ],
            [
                $this->getField('Description', 'textarea'),
                '%22%20onfocus=%22alert(123)',
                '<textarea id="autobornaform_input_autoborna_description"></textarea>',
                '<textarea id="autobornaform_input_autoborna_description">&quot; onfocus=&quot;alert(123)</textarea>',
                'Tags should be stripped from textarea field values submitted via GET to prevent XSS.',
            ],
            [
                $this->getField('Checkbox Single', 'checkboxgrp'),
                'myvalue',
                '<input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Single').'1" value="myvalue"/><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Single').'2" value="notmyvalue"/>',
                '<input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Single').'1" value="myvalue" checked /><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Single').'2" value="notmyvalue"/>',
                'Single value checkbox groups should have their values set appropriately via GET.',
            ],
            [
                $this->getField('Checkbox Multi', 'checkboxgrp'),
                'myvalue|alsomyvalue',
                '<input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'1" value="myvalue"/><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'2" value="alsomyvalue"/><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'3" value="notmyvalue"/>',
                '<input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'1" value="myvalue" checked /><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'2" value="alsomyvalue" checked /><input id="autobornaform_checkboxgrp_checkbox_'.$this->getAliasFromName('Checkbox Multi').'3" value="notmyvalue"/>',
                'Multi-value checkbox groups should have their values set appropriately via GET.',
            ],
            [
                $this->getField('Radio Single', 'radiogrp'),
                'myvalue',
                '<input id="autobornaform_radiogrp_radio_'.$this->getAliasFromName('Radio Single').'1" value="myvalue"/><input id="autobornaform_radiogrp_radio_'.$this->getAliasFromName('Radio Single').'1" value="notmyvalue"/>',
                '<input id="autobornaform_radiogrp_radio_'.$this->getAliasFromName('Radio Single').'1" value="myvalue" checked /><input id="autobornaform_radiogrp_radio_'.$this->getAliasFromName('Radio Single').'1" value="notmyvalue"/>',
                'Single value radio groups should have their values set appropriately via GET.',
            ],
            [
                $this->getField('Select', 'select'),
                'myvalue',
                '<select id="autobornaform_input_autoborna_select"><option value="myvalue">My Value</option></select>',
                '<select id="autobornaform_input_autoborna_select"><option value="myvalue" selected="selected">My Value</option></select>',
                'Select lists should have their values set appropriately via GET.',
            ],
        ];
    }

    /**
     * @param string $name
     * @param string $type
     *
     * @return Field
     */
    protected function getField($name, $type)
    {
        $field = new Field();

        $field->setLabel($name);
        $field->setAlias($this->getAliasFromName($name));
        $field->setType($type);

        return $field;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getAliasFromName($name)
    {
        return strtolower(str_replace(' ', '', $name));
    }
}
