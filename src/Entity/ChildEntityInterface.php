<?php

namespace Drupal\child_entity\Entity;

use Drupal\Core\Entity\EntityInterface;

/**
 * Provides an interface for access to an entity's published state.
 */
interface ChildEntityInterface extends EntityInterface {

  /**
   * Returns the entity parent's entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The parent entity.
   */
  public function getParentEntity();

  /**
   * Sets the entity parent's entity.
   *
   * @param \Drupal\Core\Entity\EntityInterface $parent
   *   The parent entity.
   *
   * @return $this
   */
  public function setParentEntity(EntityInterface $parent);

  /**
   * Returns the entity parent's ID.
   *
   * @return int|null
   *   The parent ID, or NULL in case the parent ID field has not been set on
   *   the entity.
   */
  public function getParentId();

  /**
   * Sets the entity parent's ID.
   *
   * @param int $id
   *   The parent id.
   *
   * @return $this
   */
  public function setParentId($id);

}
