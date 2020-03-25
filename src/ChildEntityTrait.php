<?php

namespace Drupal\child_entity;

use Drupal\child_entity\Entity\ChildEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Provides a trait for parent information.
 */
trait ChildEntityTrait {

  private $query = NULL;

  /**
   * Returns an array of base field definitions for publishing status.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to add the publishing status field to.
   *
   * @return \Drupal\Core\Field\BaseFieldDefinition[]
   *   An array of base field definitions.
   *
   * @throws \Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException
   *   Thrown when the entity type does not implement EntityPublishedInterface
   *   or if it does not have a "published" entity key.
   */
  public static function childBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    if (!is_subclass_of($entity_type->getClass(), ChildEntityInterface::class)) {
      throw new UnsupportedEntityTypeDefinitionException(
        'The entity type ' . $entity_type->id() . ' does not implement \Drupal\child_entity\Entity\ChildEntityInterface.');
    }
    if (!$entity_type->hasKey('parent')) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not have a "parent" entity key.');
    }

    return [
      $entity_type->getKey('parent') => BaseFieldDefinition::create('entity_reference')
        ->setLabel(new TranslatableMarkup('Parent ID'))
        ->setSetting('target_type', $entity_type->getKey('parent'))
        ->setTranslatable($entity_type->isTranslatable())
        ->setRequired(TRUE)
        ->setDisplayOptions('view', [
          'type' => 'entity_reference_label',
          'label' => 'inline',
          'weight' => -3,
        ])
        ->setDisplayConfigurable('form', FALSE)
        ->setDisplayConfigurable('view', TRUE)
    ];
  }

  /**
   * @return string parent entity key.
   */
  public function getParentColumn() {
    return $this->query()->getParentColumn();
  }

  /**
   * @return string route key of the parent entity in sub entity urls
   */
  public function getParentKeyInRoute() {
    return $this->query()->getParentKeyInRoute();
  }

  /**
   * @return \Drupal\child_entity\ChildContentEntityTypeQuery
   */
  private function query() {
    if ($this->query === NULL) {
      $this->query = new ChildContentEntityTypeQuery($this->getEntityType());
    }

    return $this->query;
  }

  /**
   * @inheritDoc
   */
  protected function urlRouteParameters($rel) {
    $params = parent::urlRouteParameters($rel) + [
        $this->getParentKeyInRoute() => $this->getParentEntity()
          ->id(),
      ];
    $params = $this->buildParentParams($params, $this, $this->query());
    return $params;
  }

  public function buildParentParams(array $parameters, ChildEntityInterface $entity, ChildContentEntityTypeQuery $query) {
    if ($query->isParentAnotherChildEntity()) {
      $parentQuery = new ChildContentEntityTypeQuery($query->getParentEntityType());
      $parentQuery->getParentKeyInRoute();
      $parameters[$parentQuery->getParentKeyInRoute()] = $entity->getParentEntity()->id();
      $parameters = $this->buildParentParams($parameters, $entity->getParentEntity(), $parentQuery);
    }

    return $parameters;
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
  public function getParentEntity() {
    $key = $this->getEntityType()->getKey('parent');
    return $this->get($key)->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setParentEntity(EntityInterface $parent) {
    $key = $this->getEntityType()->getKey('parent');
    $this->set($key, $parent);

    return $this;
  }
}
