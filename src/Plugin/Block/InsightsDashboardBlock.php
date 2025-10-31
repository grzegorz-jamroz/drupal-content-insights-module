<?php

declare(strict_types=1);

namespace Drupal\content_insights\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Content Insights Dashboard block type.
 */
#[Block(
  id: "content_insights_dashboard_block",
  admin_label: new TranslatableMarkup("Content Insights Dashboard"),
  category: new TranslatableMarkup("Content"),
)]
class InsightsDashboardBlock extends BlockBase implements ContainerFactoryPluginInterface
{
  protected ConfigFactoryInterface $configFactory;

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    ConfigFactoryInterface $config_factory,
  )
  {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->configFactory = $config_factory;
  }

  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition,
  ): static
  {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
    );
  }

  public function build(): array
  {
    $config = $this->configFactory->get('content_insights.settings');
    $targetWordCount = $config->get('target_word_count');

    return [
      '#theme' => 'insights_dashboard',
      '#attached' => [
        'library' => [
          'content_insights/dashboard',
        ],
        'drupalSettings' => [
          'content_insights' => [
            'targetWordCount' => $targetWordCount,
          ],
        ],
      ],
    ];
  }
}
