<?php

namespace Drupal\rest_without_format;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;

class RestWithoutFormatServiceProvider implements ServiceModifierInterface {

  /**
   * Modifies existing service definitions.
   *
   * @param ContainerBuilder $container
   *   The ContainerBuilder whose service definitions can be altered.
   */
  public function alter(ContainerBuilder $container) {
    $route_filter_defination = $container->getDefinition('request_format_route_filter');
    $route_filter_defination->setClass('Drupal\rest_without_format\Routing\RestWithoutFormatRouteFilter');
  }
}
