<?php

/**
 * @file
 *
 * Provides views data for em_news.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function em_news_views_data_alter(&$data) {
  $data['node_field_data']['current_entreprise'] = [
    'title' => t('Related Content to current entreprise'),
    'help' => t('Filter Content that related to current connected entreprise'),
    'filter' => [
      'id' => 'filter_content_current_entreprise'
    ],
  ];
}


