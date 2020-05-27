<?php

namespace Drupal\child_entity\Route;

use Drupal\child_entity\Entity\ChildEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Entity\Routing\AdminHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Child Entities.
 *
 * @see \Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see \Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class ChildContentEntityHtmlRouteProvider extends AdminHtmlRouteProvider {

  /**
   * {@inheritdoc}
   * @throws \Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    if (!$entity_type->entityClassImplements(ChildEntityInterface::class)) {
      throw new UnsupportedEntityTypeDefinitionException(
        'The entity type ' . $entity_type->id() . ' does not implement \Drupal\child_entity\Entity\ChildEntityInterface.');
    }
    if (!$entity_type->hasKey('parent')) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not have a "parent" entity key.');
    }

    foreach ($collection as $key => $route) {
      $option_parameters = $route->getOption('parameters');
      if (!is_array($option_parameters)) {
        $option_parameters = [];
      }
      $option_parameters[$entity_type->getKey('parent')] = [
        'type' => 'entity:' . $entity_type->getKey('parent'),
      ];
      $route->setOption('parameters', $option_parameters);
      $this->prepareWithParentEntities($route, $entity_type);
      $collection->add($key, $route);
    }
    return $collection;
  }

  /**
   * @param \Symfony\Component\Routing\Route $route
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  private function prepareWithParentEntities(Route $route, EntityTypeInterface $entity_type) {
    $parent_type = \Drupal::entityTypeManager()->getDefinition($entity_type->getKey('parent'));

    $link = $parent_type->getLinkTemplate('canonical');
    $route->setPath($link . $route->getPath());

    $option_parameters = $route->getOption('parameters');
    if (!is_array($option_parameters)) {
      $option_parameters = [];
    }
    $option_parameters[$entity_type->getKey('parent')] = [
      'type' => 'entity:' . $entity_type->getKey('parent'),
    ];
    $route->setOption('parameters', $option_parameters);

    if ($parent_type->hasKey('parent')) {
      $this->prepareWithParentEntities($route, $parent_type);
    }
  }

}
