<?php

namespace Drupal\rest_without_format\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Drupal\Core\Routing\RequestFormatRouteFilter;

/**
 * Overrides the functionality of RequestFormatRouteFilter.
 */
class RestWithoutFormatRouteFilter extends RequestFormatRouteFilter {

  /**
   * {@inheritdoc}
   */
  public function applies(Route $route) {
    return $route->hasRequirement('_format');
  }

  /**
   * {@inheritdoc}
   */
  public function filter(RouteCollection $collection, Request $request) {
    /* @var \Drupal\rest\Plugin\Type\ResourcePluginManager $rest_plugin_manager*/
    $rest_plugin_manager = \Drupal::service('plugin.manager.rest');
    $rest_plugins = $rest_plugin_manager->getDefinitions();

    $endpoints = [];
    foreach ($rest_plugins as $rest_plugin) {
      $endpoints[] = $rest_plugin['uri_paths']['canonical'];
    }

    // If we don't have any rest resource , use original router filter.
    if (empty($endpoints)) {
      return parent::filter($collection, $request);
    }

    $format = $request->getRequestFormat('html');

    $rest_routes = [];

    /** @var \Symfony\Component\Routing\Route $route */
    foreach ($collection as $name => $route) {
      // Get route path.
      $route_path = $route->getPath();

      // If this route is of rest type.
      if (in_array(substr($route_path, 1), $endpoints)) {
        // Get the route format.
        $route_format = $route->getRequirement('_format');

        // If route and request format are not same, means we don't have _format
        // key in endpoint.
        if ($route_format != $format) {
          /* @todo Check if route supports the given format*/
          $route->setRequirement('_format', 'xml');
          $rest_routes[] = $route_path;
        }
      }

    }

    // If there is any route found.
    if (count($rest_routes)) {
      return $collection;
    }

    // If nothing found, process parent for default behavior.
    return parent::filter($collection, $request);
  }

}
