<?php

namespace Drupal\userprofiles\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\user\Entity\User;
use Drupal\userprofiles\Entity\PrivateProfile;
use Drupal\userprofiles\Entity\PublicProfile;

/**
 * Form controller for Private Profile edit forms.
 *
 * @ingroup userprofiles
 */
class PrivateProfileForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\userprofiles\Entity\PrivateProfile */
    // This stuff should fill the form out for us, based on the entity.
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Private Profile.', [
          '%label' => $entity->label(),
        ]));

        $form_state->setRedirect('entity.private_profile.canonical', ['private_profile' => $entity->id()]);
        break;

      default:
        $user = $entity->field_userref->entity;

        // Check if the user actually exists
        if ($user) {
          // Load the public profile, so we can copy data
          $public_profile = $user->field_pubref->entity;

          // If the public profile is there, THEN we can copy data
          if ($public_profile) {
            // Grab the states from the private profile form
            $image_state = $entity->field_image_state->value;
            $firstname_state = $entity->field_firstname_state->value;
            $lastname_state = $entity->field_lastname_state->value;
            $contact_email_state = $entity->field_contact_email_state->value;
            $dob_state = $entity->field_dob_state->value;
            $address_state = $entity->field_address_state->value;
            $homephone_state = $entity->field_homephone_state->value;
            $mobile_state = $entity->field_mobile_state->value;
            $role_state = $entity->field_role_state->value;
            $certifications_state = $entity->field_certifications_state->value;

            // Grab the values from the private profile form
            $image = $entity->field_image;
            $firstname = $entity->field_firstname->value;
            $lastname = $entity->field_lastname->value;
            $contact_email = $entity->field_contact_email->value;
            $dob = $entity->field_dob->value;
            $address = $entity->field_address;
            $homephone = $entity->field_homephone->value;
            $mobile = $entity->field_mobile->value;
            $role = $entity->field_role->value;
            $certifications = $entity->field_certifications;

            // First set everything to blank
            $public_profile->field_image = NULL;
            $public_profile->field_firstname = "";
            $public_profile->field_lastname = "";
            $public_profile->field_contact_email = "";
            $public_profile->field_dob = "";
            $public_profile->field_address = "";
            $public_profile->field_homephone = "";
            $public_profile->field_mobile = "";
            $public_profile->field_role = "";
            $public_profile->field_certifications = "";

            // Reset the fields that have been specified
            if ($image_state) $public_profile->field_image = $image;
            if ($firstname_state) $public_profile->field_firstname = $firstname;
            if ($lastname_state) $public_profile->field_lastname = $lastname;
            if ($contact_email_state) $public_profile->field_contact_email = $contact_email;
            if ($dob_state) $public_profile->field_dob = $dob;
            if ($address_state) $public_profile->field_address = $address;
            if ($homephone_state) $public_profile->field_homephone = $homephone;
            if ($mobile_state) $public_profile->field_mobile = $mobile;
            if ($role_state) $public_profile->field_role = $role;
            if ($certifications_state) $public_profile->field_certifications = $certifications;
            
            $public_profile->status->value = $entity->field_published->value;
            $public_profile->save();
          }
        }

        drupal_set_message($this->t('Saved the %label Private Profile.', [
          '%label' => $entity->label(),
        ]));
        $form_state->setRedirect('entity.private_profile.edit_form', ['private_profile' => $entity->id()]);
    }
  }
}
