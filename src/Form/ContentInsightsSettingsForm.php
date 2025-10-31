<?php

declare(strict_types=1);

namespace Drupal\content_insights\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class ContentInsightsSettingsForm extends ConfigFormBase
{
  protected function getEditableConfigNames(): array
  {
    return ['content_insights.settings'];
  }

  public function getFormId(): string
  {
    return 'content_insights_settings_form';
  }

  public function buildForm(
      array $form,
      FormStateInterface $form_state,
  ): array
  {
    $config = $this->config('content_insights.settings');

    $form['target_word_count'] = [
      '#type' => 'number',
      '#title' => $this->t('Target Word Count'),
      '#description' => $this->t('Set a target word count for content. Set to 0 to disable.'),
      '#default_value' => $config->get('target_word_count') ?? 100,
      '#min' => 0,
    ];

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(
      array &$form,
      FormStateInterface $form_state,
  ): void
  {
    $this->config('content_insights.settings')
      ->set('target_word_count', $form_state->getValue('target_word_count'))
      ->save();
    parent::submitForm($form, $form_state);
  }
}
