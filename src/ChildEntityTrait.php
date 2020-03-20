<?php

namespace Drupal\child_entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a trait for parent information.
 */
trait ChildEntityTrait {

  /**
   * Returns an array of base field definitions for publishing status.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to add the publishing status field to.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $parent_type
   *   The parent entity type.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   An array of base field definitions.
   *
   * @throws \Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException
   *   Thrown when the entity type does not implement EntityPublishedInterface
   *   or if it does not have a "published" entity key.
   */
  public static function childBaseFieldDefinitions(EntityTypeInterface $entity_type, EntityTypeInterface $parent_type) {
    if (!is_subclass_of($entity_type->getClass(), ChildEntityInterface::class)) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not implement \Drupal\Core\Entity\EntityPublishedInterface.');
    }
    if (!$entity_type->hasKey('parent')) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not have a "parent" entity key.');
    }

    return [
      $entity_type->getKey('parent') => BaseFieldDefinition::create('entity_reference')
        ->setLabel(new TranslatableMarkup('Parent ID'))
        ->setSetting('target_type', $parent_type->id())
        ->setTranslatable($entity_type->isTranslatable())
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getParentId() {
    return $this->getEntityKey('parent');
  }

  /**
   * {@inheritdoc}
   */
  public function setParentId($uid) {
    $key = $this->getEntityType()->getKey('parent');
    $this->set($key, $uid);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getParent() {
    $key = $this->getEntityType()->getKey('parent');
    return $this->get($key)->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent(EntityInterface $parent) {
    $key = $this->getEntityType()->getKey('parent');
    $this->set($key, $parent);

    return $this;
  }
}
