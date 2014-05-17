<?php
/**
 * @file
 * Contains \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter.
 */

namespace Drupal\vimeo_field\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'vimeo_default' formatter.
 *
 * @FieldFormatter(
 *   id = "vimeo_default",
 *   label = @Translation("Vimeo Media"),
 *   field_types = {
 *     "text"
 *   }
 * )
 */
class VimeoDefaultFormatter extends FormatterBase
{
	/**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items)
  {
    $elements = array();

    foreach ($items as $delta => $item) {
    	
    	var_dump($item);
      
      preg_match('/vimeo\.com\/(\w+\s*\/?)*([0-9]+)*$/i',$item->processed, $matches);
      $elements[$delta]['video'] = $matches[1];
      $elements[$delta]['height'] = $this->getSetting('vimeo_height');
      $elements[$delta]['width'] = $this->getSetting('vimeo_width');
    }

    $result = [
      '#theme' => 'vimeo_player',
      '#vids' => $elements,
    ];

    //print_r($result);

    return $result;
  }

	/**
   * {@inheritdoc}
   */
  public static function defaultSettings()
  {
    return [
      'vimeo_width'  => 600,
      'vimeo_height' => 400
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, array &$form_state)
  {
  	$element = [];
    $element['vimeo_width'] = [
      '#title' => $this->t('Default width'),
      '#type' => 'number',
      '#default_value' => 600,
      '#empty_option' => $this->t('None'),
    ];

    $element['vimeo_height'] = [
      '#title' => $this->t('Default width'),
      '#type' => 'number',
      '#default_value' => 400,
      '#empty_option' => $this->t('None'),
    ];

    return $element;
  }

  public function settingsSummary() 
  {
    return [
      $this->t('Width: ') . $this->getSetting('vimeo_width') . 'px',
      $this->t('Height: '). $this->getSetting('vimeo_height'). 'px'
    ];
  }
}