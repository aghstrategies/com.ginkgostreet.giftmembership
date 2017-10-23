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
   * Determines whether a soft contribution qualifies for additional processing
   * by this extension.
   *
   * @param CRM_Contribute_DAO_ContributionSoft $softContribution
   * @return boolean
   */
  public static function softContributionTypeQualifies(CRM_Contribute_DAO_ContributionSoft $softContribution) {
    $qualifingTypes = Civi::settings()->get(self::settingNameSoftCreditTypes);
    return in_array($softContribution->soft_credit_type_id, $qualifingTypes);
  }

  /**
   * For any memberships associated with the passed soft contribution, changes
   * ownership to match that of the soft contribution.
   *
   * @param CRM_Contribute_DAO_ContributionSoft $softContribution
   */
  public static function transferMembershipToGiftee(CRM_Contribute_DAO_ContributionSoft $softContribution) {
    $payment = civicrm_api3('MembershipPayment', 'get', array(
      'contribution_id' => $softContribution->contribution_id,
      // no point in pulling back memberships already owned by the giftee
      'membership_id.contact_id' => array('!=' => $softContribution->contact_id),
    ));

    // The typical case will be that exactly one membership matches, but it's
    // possible (at least the data schema supports it) that one contribution
    // could be related to multiple memberships, or that the contribution
    // qualifies but is not related to a membership. This approach covers all
    // the bases.
    $membershipIds = array_column($payment['values'], 'membership_id');
    foreach ($membershipIds as $mId) {
      $test = civicrm_api3('Membership', 'create', array(
        'contact_id' => $softContribution->contact_id,
        'id' => $mId,
      ));
    }
  }

}
