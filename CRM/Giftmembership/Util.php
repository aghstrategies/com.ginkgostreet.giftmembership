<?php

/**
 * Miscellaneous static functions.
 *
 * Perhaps later some natural organization will reveal itself and these methods
 * will be moved to more appropriate classes. For now, the main purpose of this
 * class is to avoid polluting giftmembership.php and the function namespace.
 */
class CRM_Giftmembership_Util {

  const settingNameSoftCreditTypes = 'giftmembership_soft_credit_types';

  /**
   * Gets the contact IDs for giftees of a membership based on the configured
   * triggering soft credit contribution type(s).
   *
   * @param CRM_Member_DAO_MembershipPayment $payment
   * @return array
   *   Contact IDs.
   */
  public static function getMembershipGiftees(CRM_Member_DAO_MembershipPayment $payment) {
    $qualifyingSoftCreditTypes = Civi::settings()->get(self::settingNameSoftCreditTypes);
    $softContributions = civicrm_api3('ContributionSoft', 'get', array(
      'contribution_id' => $payment->contribution_id,
      'soft_credit_type' => array('IN' => $qualifyingSoftCreditTypes),
    ));
    return array_unique(array_column($softContributions['values'], 'contact_id'));
  }

  /**
   * Changes the ownership of a membership to the specified contact.
   *
   * @param int|string $membershipId
   * @param int}string $contactId
   */
  public static function transferMembershipToGiftee($membershipId, $contactId) {
    civicrm_api3('Membership', 'create', array(
      'contact_id' => $contactId,
      'id' => $membershipId,
    ));
  }

}
