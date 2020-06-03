<?php

namespace Drupal\child_entity\Form;

use Drupal\child_entity\Entity\ChildEntityInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\child_entity\Context\ChildEntityRouteContextTrait;

class ChildContentEntityForm extends ContentEntityForm {

  use ChildEntityRouteContextTrait;

  /**
   * The entity being used by this form.
   *
   * @var ChildEntityInterface
   */
  protected $entity;

  /**
   * @inheritDoc
   */
  public function save(array $form, FormStateInterface $form_state) {

    $this->entity->setParentEntity($this->getParentEntity());
    return parent::save($form, $form_state);
  }

  /**
   * @return EntityInterface
   */
  protected function getParentEntity() {
    if ($this->entity->isNew()) {
      return $this->getParentEntityFromRoute($this->entity->getParentEntityTypeId());
    }
    else {
      return $this->entity->getParentEntity();
    }
  }

}
