<?php
/**
 * @file
 * Content Test\Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter
 */

use \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter;
use \PHPUnit_Framework_TestCase;

class VimeoDefaultFormatterTest extends PHPUnit_Framework_TestCase
{
  /** @var $formatter \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter */
  protected $formatter;

  /**
   * @param  array                                                                 $settings
   * @return \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter
   */
  protected function getVimeoFormatter(array $settings = [])
  {
    $pluginDefinition = $this->getMockBuilder('\Drupal\Core\Field\FieldDefinitionInterface')
                             ->getMock();

    return new VimeoDefaultFormatter('vimeo', '', $pluginDefinition, $settings, 'vimeo', '');
  }

  public function testShouldReturnVimeoIdFromValidUrl()
  {
    $formatter = $this->getVimeoFormatter();

    $this->assertEquals('45639028', $formatter->parseVimeoId('http://vimeo.com/45639028'));
  }

  /**
   * @dataProvider invalidUrlProvider
   */
  public function testShouldThrowExceptionIfInvalidUrlIsProvided($url)
  {
    $this->setExpectedException('\InvalidArgumentException');

    $formatter = $this->getVimeoFormatter();

    $formatter->parseVimeoId($url);
  }

  public function invalidUrlProvider()
  {
    return [
      'email' => ['me@gmail.com'],
      'file_path' => ['my_video.jpg'],
    ];
  }

  /**
   * @dataProvider invalidVimeoUrlProvider
   */
  public function testShouldThrowExceptionWithValidUrlThatIsNotAVimeoUrl($url)
  {
    $this->setExpectedException('\InvalidArgumentException');

    $formatter = $this->getVimeoFormatter();

    $formatter->parseVimeoId($url);
  }

  public function invalidVimeoUrlProvider()
  {
    return [
      'ftp_url' => ['ftp://vimeo.com/7890139'],
      'youtube_url' => ['http://youtube.com/3eRksdoias'],
    ];
  }

  /**
   * @dataProvider invalidVimeoUrlProvider
   */
  public function testShouldThrowExceptionWithValidVimeoUrlAndInvalidId($url)
  {
    $this->setExpectedException('\InvalidArgumentException');

    $formatter = $this->getVimeoFormatter();

    $formatter->parseVimeoId('http://vimeo.com/3eRksdoias');
  }

  public function testShouldReturnSettingsFormWithDefaultSettings()
  {
    $form = [];
    $formState = [];
    $translation = $this->getFormTranslation();

    $formatter = $this->getVimeoFormatter();
    $formatter->setStringTranslation($translation);

    $form_settings = $formatter->settingsForm($form, $formState);

    $this->assertArrayHasKey('vimeo_width', $form_settings);
    $this->assertEquals(600, $form_settings['vimeo_width']['#default_value']);
    $this->assertArrayHasKey('vimeo_height', $form_settings);
    $this->assertEquals(400, $form_settings['vimeo_height']['#default_value']);
  }

  public function testShouldReturnSettingsFormWithCustomSettings()
  {
    $form = [];
    $formState = [];
    $translation = $this->getFormTranslation();

    $formatter = $this->getVimeoFormatter(['vimeo_width' => 100, 'vimeo_height' => 100]);
    $formatter->setStringTranslation($translation);

    $formSettings = $formatter->settingsForm($form, $formState);

    $this->assertArrayHasKey('vimeo_width', $formSettings);
    $this->assertEquals(100, $formSettings['vimeo_width']['#default_value']);
    $this->assertArrayHasKey('vimeo_height', $formSettings);
    $this->assertEquals(100, $formSettings['vimeo_height']['#default_value']);
  }

  /**
   * @return \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected function getFormTranslation()
  {
    $translation = $this->getMockBuilder('\Drupal\Core\StringTranslation\TranslationInterface')
                          ->getMock();

    $translation->expects($this->at(0))
                ->method('translate')
                ->with('Default width', [], [])
                ->will($this->returnValue('Default width'));
    $translation->expects($this->at(1))
                ->method('translate')
                ->with('None', [], [])
                ->will($this->returnValue('None'));
    $translation->expects($this->at(2))
                ->method('translate')
                ->with('Default height', [], [])
                ->will($this->returnValue('Default height'));
    $translation->expects($this->at(3))
                ->method('translate')
                ->with('None', [], [])
                ->will($this->returnValue('None'));

    return $translation;
  }

  public function testShouldGetDefaultSettings()
  {
    $this->assertEquals(['vimeo_width'  => 600, 'vimeo_height' => 400], VimeoDefaultFormatter::defaultSettings());
  }

  public function testShouldreturnSummaryWithDefaultSettings()
  {
    $formatter = $this->getVimeoFormatter();
    $translation = $this->getSummaryTranslation();
    $formatter->setStringTranslation($translation);

    $summary = $formatter->settingsSummary();

    $this->assertEquals('Width: 600px', $summary[0]);
    $this->assertEquals('Height: 400px', $summary[1]);
  }

  public function testShouldreturnSummaryWithCustomSettings()
  {
    $formatter = $this->getVimeoFormatter(['vimeo_width' => 100, 'vimeo_height' => 100]);
    $translation = $this->getSummaryTranslation();
    $formatter->setStringTranslation($translation);

    $summary = $formatter->settingsSummary();

    $this->assertEquals('Width: 100px', $summary[0]);
    $this->assertEquals('Height: 100px', $summary[1]);
  }

  /**
   * @return \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected function getSummaryTranslation()
  {
    $translation = $this->getMockBuilder('\Drupal\Core\StringTranslation\TranslationInterface')
                        ->getMock();

    $translation->expects($this->at(0))
                ->method('translate')
                ->with('Width: ', [], [])
                ->will($this->returnValue('Width: '));

    $translation->expects($this->at(1))
                ->method('translate')
                ->with('Height: ', [], [])
                ->will($this->returnValue('Height: '));

    return $translation;
  }

  public function testShouldReturnViewElementsWithDefaultSettings()
  {
    $formatter = $this->getVimeoFormatter();

    $items = $this->getItems();

    $viewElements = [
      '#theme' => 'vimeo_player',
      '#vids' => [
        'vimeo' => [
          'video' => '45639028',
          'height' => 400,
          'width' => 600,
        ],
      ],
    ];

    $this->assertEquals($viewElements, $formatter->viewElements($items));
  }

  public function testShouldReturnViewElementsWithCustomSettings()
  {
    $formatter = $this->getVimeoFormatter(['vimeo_width' => 100, 'vimeo_height' => 100]);

    $items = $this->getItems();

    $viewElements = [
      '#theme' => 'vimeo_player',
      '#vids' => [
        'vimeo' => [
          'video' => '45639028',
          'height' => 100,
          'width' => 100,
        ],
      ],
    ];

    $this->assertEquals($viewElements, $formatter->viewElements($items));
  }

  /**
   * @return \Drupal\Core\TypedData\ListInterface
   */
  protected function getItems()
  {
    $vimeoItem = new ArrayObject(['processed' => 'http://vimeo.com/45639028']);
    $vimeoItem->setFlags(ArrayObject::ARRAY_AS_PROPS);

    $items = $this->getMockBuilder('\Drupal\Core\Field\FieldItemList')
                  ->disableOriginalConstructor()
                  ->getMock();
    $items->expects($this->once())
          ->method('getIterator')
          ->will($this->returnValue(new ArrayObject(['vimeo' => $vimeoItem])));

    return $items;
  }
}
