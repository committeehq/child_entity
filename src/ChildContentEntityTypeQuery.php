<?php

namespace Drupal\child_entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\child_entity\Entity\ChildContentEntityBase;

/**
 * @package Drupal\child_entity
 */
class ChildContentEntityTypeQuery {

  /**
   * @var \Drupal\Core\Entity\EntityTypeInterface|null
   */
  private $entity_type;

  /**
   * @inheritDoc
   */
  public function __construct(EntityTypeInterface $entity_type) {
    $this->entity_type = $entity_type;
    if (!$this->isChildEntity($entity_type)) {
      throw new \InvalidArgumentException(sprintf('"%s" class must use "%s"', $entity_type->getOriginalClass(), ChildEntityTrait::class));
    }

  }

  /**
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   */
  public function getEntityType() {
    return $this->entity_type;
  }

  public function getParentEntityTypeId() {
    if ($this->getEntityType()->hasKey('parent')) {
      return $this->getEntityType()->getKey('parent');
    }
    $this->reportMissingKey('parent');
  }

  /**
   * @return string parent entity key.
   */
  public function getParentColumn() {
    return $this->getParentEntityTypeId();
  }

  /**
   * @return string route key of the parent entity in sub entity urls
   */
  public function getParentKeyInRoute() {
    return $this->getParentEntityTypeId();
  }

  /**
   * @return \Drupal\Core\Entity\EntityTypeInterface|null
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getParentEntityType() {
    return \Drupal::entityTypeManager()
      ->getDefinition($this->getParentEntityTypeId());
  }

  /**
   * @return bool
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function isParentAnotherChildEntity() {
    return $this->isChildEntity($this->getParentEntityType());
  }

  /**
   * @param $key string
   */
  private function reportMissingKey($key) {
    throw new \InvalidArgumentException(sprintf('"%s" key must be set in "entity_keys" of class "%s"', $key, get_class($this)));
  }

  /**
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *
   * @return bool
   */
  private function isChildEntity(EntityTypeInterface $entity_type) {
    $original_class = $entity_type->getOriginalClass();
    if (in_array(ChildEntityTrait::class, class_uses($original_class))) {
      return TRUE;
    }
    return FALSE;
  }
}
