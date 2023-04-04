<?php

namespace Drupal\em_news\Plugin\views\filter;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\em_entreprise\EntrepriseServices;
use Drupal\user\Entity\User;
use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Filter Content that related to current connected entreprise.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("filter_content_current_entreprise")
 */
class FilterContentCurrentEntreprise extends FilterPluginBase implements CacheableDependencyInterface {

  const ADMIN_RH_ROLE = 'admin_rh';

  const CONSULTANT_RH_ROLE = 'consultant_rh';

  /**
   * Drupal\Core\Session\AccountProxy definition.
   *
   * @var \Drupal\Core\Session\AccountProxy
   */
  protected $currentUser;

  /**
   * Drupal\em_entreprise\EntrepriseServices definition.
   *
   * @var \Drupal\em_entreprise\EntrepriseServices
   */
  protected $entrepriseServices;

  /**
   * Constructs a Sort Handler object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\em_entreprise\EntrepriseServices $entreprise_service
   *   Custom service.
   * @param \Drupal\Core\Session\AccountProxy $current_user
   *   Current user.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntrepriseServices $entreprise_service, AccountProxy $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entrepriseServices = $entreprise_service;
    $this->currentUser = User::load($current_user->id());
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('em_entreprise.services'),
      $container->get('current_user')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    if (
           in_array(self::ADMIN_RH_ROLE, $this->currentUser->getRoles())
        || in_array(self::CONSULTANT_RH_ROLE, $this->currentUser->getRoles())
    ) {
      $entreprise = $this->entrepriseServices->getEntrepriseForCurrentUser();
      if (!empty($entreprise)) {
        $this->query->addField('node__field_entreprise', 'field_entreprise_target_id');
        $this->query->addWhereExpression($this->options['group'], "(field_entreprise_target_id IS NULL OR field_entreprise_target_id = " . $entreprise->id() . " )");
      }
    }
  }

}
