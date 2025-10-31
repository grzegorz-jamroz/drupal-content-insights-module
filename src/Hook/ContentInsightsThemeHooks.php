<?php

declare(strict_types=1);

namespace Drupal\content_insights\Hook;

use Drupal\Core\Hook\Attribute\Hook;

class ContentInsightsThemeHooks
{
  /**
   * Implements hook_theme().
   */
  #[Hook('theme')]
  public function theme(): array
  {
    return [
      'insights_dashboard' => [
        'variables' => [],
        'template' => 'insights-dashboard',
      ],
    ];
  }
}
