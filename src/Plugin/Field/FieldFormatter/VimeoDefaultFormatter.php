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
	const URLTOID = '/vimeo\.com\/(\w+\s*\/?)*([0-9]+)*$/i';

	/**
	 * Return id from a Vimeo URL
	 * @param  string $url Vimeo url
	 * @return string      Vimeo id
	 */
	public function vimeoUrlToId($url)
	{
		preg_match(VimeoDefaultFormatter::URLTOID, $url, $matches);

		return $matches[1];
	}

	/**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items)
  {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta]['video'] = $this->vimeoUrlToId($item->processed);
      $elements[$delta]['height'] = $this->getSetting('vimeo_height');
      $elements[$delta]['width'] = $this->getSetting('vimeo_width');
    }

    return [
      '#theme' => 'vimeo_player',
      '#vids' => $elements,
    ];
  }

	/**
   * {@inheritdoc}
   */
  public static function defaultSettings()
  {
    return [
      'vimeo_width'  => 600,
      'vimeo_height' => 400,
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
      '#title' => $this->t('Default height'),
      '#type' => 'number',
      '#default_value' => 400,
      '#empty_option' => $this->t('None'),
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary()
  {
    return [
      $this->t('Width: ') . $this->getSetting('vimeo_width') . 'px',
      $this->t('Height: '). $this->getSetting('vimeo_height'). 'px',
    ];
  }
}
