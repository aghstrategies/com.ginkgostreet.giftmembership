<?php

class CRM_Giftmembership_Listener_MembershipPayment {

  /**
   * Handler for MembershipPayment DAO insert or update events.
   *
   * @param \Symfony\Component\EventDispatcher\Event $event
   * @return void
   */
  public static function upsert(\Symfony\Component\EventDispatcher\Event $event) {
    // This basic qualification could be moved to an intermediary listener/dispatcher.
    if (!isset($event->object) || !is_a($event->object, 'CRM_Member_DAO_MembershipPayment')) {
      return;
    }

    $gifteeContactIds = CRM_Giftmembership_Util::getMembershipGiftees($event->object);
    if (count($gifteeContactIds) > 1) {
      Civi::log()->warning('More than one qualifying soft credit for membership payment; cannot transfer a single membership to multiple owners.', array(
        'membershipPayment' => $event->object,
        'gifteeContactIds' => $gifteeContactIds,
      ));
      return;
    }

    foreach ($gifteeContactIds as $cid) {
      CRM_Giftmembership_Util::transferMembershipToGiftee($event->object->membership_id, $cid);
    }
  }

}
