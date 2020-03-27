<?php

namespace Drupal\child_entity\Form;

use Drupal\child_entity\Entity\ChildEntityInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

abstract class ChildContentEntityForm extends ContentEntityForm {

  /**
   * @inheritDoc
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var ChildEntityInterface $entity */
    $entity = $this->entity;

    $entity->setParentEntity($this->getParentEntity());
    return parent::save($form, $form_state);
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface
   */
  protected function getParentEntity() {
    if ($this->entity->isNew()) {
      return $this->getParentEntityFromRoute();
    }
    else {
      return $this->entity->getParentEntity();
    }
  }

  /**
   * @return \Drupal\Core\Entity\EntityInterface
   */
  private function getParentEntityFromRoute() {
    /** @var ChildEntityInterface $entity */
    $entity = $this->entity;

    return \Drupal::getContainer()
      ->get('request_stack')
      ->getMasterRequest()
      ->get($entity->getParentEntityTypeId());
  }
}
