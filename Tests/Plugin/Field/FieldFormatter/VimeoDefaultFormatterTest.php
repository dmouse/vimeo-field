<?php

/**
 * @file
 * Content Test\Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter
 */
class VimeoDefaultFormatterTest extends \PHPUnit_Framework_TestCase
{
    /** @var $formatter \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter */
    protected $formatter;

    protected function setUp()
    {
        $this->formatter = $this->getMockBuilder('Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formatter->expects($this->any())
            ->method('t')
            ->with($this->isType('string'))
            ->will($this->returnCallback(function () {
                $args = func_get_args();
                return $args[0];
            }));

        $this->formatter->expects($this->any())
            ->method('getSetting')
            ->will($this->returnValue('100'));
    }

    public function testToReturnIframeVideo()
    {
        $id = $this->formatter->vimeoUrlToId('http://vimeo.com/45639028');

        $this->assertEquals('45639028', $id);
    }

    public function testToGetSettingsForm()
    {
        $form_state = [];
        $form = [];
        $form_settings = $this->formatter->settingsForm($form, $form_state);

        $this->assertArrayHasKey('vimeo_width', $form_settings);
        $this->assertArrayHasKey('vimeo_height', $form_settings);
    }

    public function testGetSummary()
    {
        $summary = $this->formatter->settingsSummary();

        $this->assertContains('Width: 100px', $summary[0]);
        $this->assertContains('Height: 100px', $summary[1]);
    }
}
