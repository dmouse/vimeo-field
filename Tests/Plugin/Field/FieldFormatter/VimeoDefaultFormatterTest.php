<?php
/**
 * @file
 * Content 
 */
//use Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter;

class VimeoDefaultFormatterTest extends \PHPUnit_Framework_TestCase
{

	protected function setUp()
	{
		$this->formmaterItemListInterface = $this->getMockBuilder('Drupal\Core\Field\FieldItemListInterface')
			->disableOriginalConstructor()
			->getMock();
		$this->formmaterBase = $this->getMockBuilder('Drupal\Core\Field\FormatterBase')
			->setMethods([])
			->disableOriginalConstructor()
			->getMock();

		$this->formatter = $this->getMockBuilder('Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter')
			->disableOriginalConstructor()
			->setMethods(['t', 'getSetting'])
			->getMock();

		$this->formatter->expects($this->any())
			->method('t')
			->with($this->isType('string'))
			->will($this->returnCallback(function(){
				$args = func_get_args();
				return $args[0];
			}));

		$this->formatter->expects($this->any())
			->method('getSetting')
			->will($this->returnValue('100'));

		$this->items = $this->getMockBuilder('Drupal\text\Plugin\Field\FieldType\TextItem')
			->getMock();

		$this->fieldItemList = $this->getMockBuilder('Drupal\Core\Field\FieldItemListInterface')
			->disableOriginalConstructor()
			->setMethods(['get','getIterator','__get'])
			->getMock();

		$this->fieldItemList->expects($this->any())
			->method('__get')
			->with($this->returnCallback(function(){ return 'snickers'; }))
			->will($this->returnValue([['a'=>'b']]));
	}

	public function testToReturnIframeVideo()
	{
		$markup = $this->formatter->viewElements($this->fieldItemList);
		$this->assertNotEmpty($markup['#vids']);

	}

	public function testToGetSettingsForm(){
		$form_state = [];
		$form = [];
		$form_settings = $this->formatter->settingsForm($form,$form_state);
		$this->assertArrayHasKey('vimeo_width',$form_settings);
		$this->assertArrayHasKey('vimeo_height',$form_settings);
	}

	public function testGetSummary(){
		$summary = $this->formatter->settingsSummary();
		$this->assertContains('Width: 100px', $summary[0]);
		$this->assertContains('Height: 100px',$summary[1]);
	}

}
