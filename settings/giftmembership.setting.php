<?php

use CRM_Giftmembership_ExtensionUtil as E;

return array(
  'giftmembership_soft_credit_types' => array(
    'group_name' => 'Gift Membership Settings',
    'group' => 'com.ginkgostreet.giftmembership',
    'name' => 'giftmembership_soft_credit_types',
    'title' => E::ts('Soft Credit Types for Gift Memberships'),
    'type' => 'Array',
    'default' => array(),
    'is_domain' => 1,
    'is_contact' => 0,
    'description' => E::ts('A contact soft-credited with one of the selected types will be made the owner of the membership associated with the contribution.'),
  ),
);
