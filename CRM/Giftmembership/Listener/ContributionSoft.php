<?php

class CRM_Giftmembership_Listener_ContributionSoft {

  /**
   * Handler for ContributionSoft DAO insert or update events.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   * @return void
   */
  public static function upsert(\Symfony\Component\EventDispatcher\Event $event) {
    // This basic qualification could be moved to an intermediary listener/dispatcher.
    if (!isset($event->object) ||!is_a($event->object, 'CRM_Contribute_DAO_ContributionSoft')) {
      return;
    }

    if (CRM_Giftmembership_Util::softContributionTypeQualifies($event->object)) {
      CRM_Giftmembership_Util::transferMembershipToGiftee($event->object);
    }
  }

}
