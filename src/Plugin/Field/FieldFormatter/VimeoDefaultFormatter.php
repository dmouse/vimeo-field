<?php
/**
 * @file
 * Contains \Drupal\vimeo_field\Plugin\Field\FieldFormatter\VimeoDefaultFormatter.
 */

namespace Drupal\vimeo_field\Plugin\Field\FieldFormatter;

use \Drupal\Core\Field\FieldItemListInterface;
use \Drupal\Core\Field\FormatterBase;
use \InvalidArgumentException;

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
  const VIMEO_HOST = 'vimeo.com';

  /**
   * Gets a Vimeo ID from a Vimeo URL
   *
   * @param  string                   $vimeoUrl A Vimeo URL
   * @return string                             The parsed Vimeo ID
   * @throws InvalidArgumentException
   */
  public function parseVimeoId($vimeoUrl)
  {
    if (!filter_var($vimeoUrl, FILTER_VALIDATE_URL)) {
      throw new InvalidArgumentException("A valid URL was expected, '{$vimeoUrl}' was provided");
    }

    $urlParts = parse_url($vimeoUrl);
    if ($urlParts['scheme'] !== 'http' || $urlParts['host'] !== static::VIMEO_HOST) {
      throw new InvalidArgumentException("The provided URL '{$vimeoUrl}' is not a valid Vimeo URL.");
    }

    $vimeoId = trim($urlParts['path'], '/');
    if (!is_numeric($vimeoId)) {
      throw new InvalidArgumentException("The provided URL '{$vimeoUrl}' does not contain a valid Vimeo ID");
    }

    return $vimeoId;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items)
  {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta]['video'] = $this->parseVimeoId($item->processed);
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
      '#default_value' => $this->getSetting('vimeo_width'),
      '#empty_option' => $this->t('None'),
    ];

    $element['vimeo_height'] = [
      '#title' => $this->t('Default height'),
      '#type' => 'number',
      '#default_value' => $this->getSetting('vimeo_height'),
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
