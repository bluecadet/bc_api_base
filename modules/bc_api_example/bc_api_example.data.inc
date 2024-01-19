<?php

/**
 * @file
 * Api example data.
 */

/**
 * Get Nationalities.
 */
function bc_api_example_get_nationalities_data() {
  return [
    'malta' => [
      'label' => 'Malta',
      'tid' => NULL,
    ],
    'england' => [
      'label' => 'England',
      'tid' => NULL,
    ],
    'wales' => [
      'label' => 'Wales',
      'tid' => NULL,
    ],
    'barbados' => [
      'label' => 'Barbados',
      'tid' => NULL,
    ],
    'scotland' => [
      'label' => 'Scotland',
      'tid' => NULL,
    ],
    'china' => [
      'label' => 'China',
      'tid' => NULL,
    ],
    'united_states' => [
      'label' => 'United States',
      'tid' => NULL,
    ],
    'united_kingdom' => [
      'label' => 'United Kingdom',
      'tid' => NULL,
    ],
    'jamaica' => [
      'label' => 'Jamaica',
      'tid' => NULL,
    ],
    'france' => [
      'label' => 'France',
      'tid' => NULL,
    ],
  ];
}

/**
 * Get Ships.
 */
function bc_api_example_get_ships_data() {
  return [
    'royal_fortune' => [
      'title' => 'Royal Fortune',
      'nid' => NULL,
    ],
    'whydah' => [
      'title' => 'Whydah',
      'nid' => NULL,
    ],
    'queen_anne' => [
      'title' => 'Queen Anne\'s Revenge',
      'nid' => NULL,
    ],
    'revenge' => [
      'title' => 'Revenge',
      'nid' => NULL,
    ],
    'adventure_galley' => [
      'title' => 'Adventure Galley',
      'nid' => NULL,
    ],
    'red_flag_fleet' => [
      'title' => 'Red Flag Fleet',
      'nid' => NULL,
    ],
    'fancy' => [
      'title' => 'Fancy',
      'nid' => NULL,
    ],
    'the_ghost' => [
      'title' => 'The Ghost',
      'nid' => NULL,
    ],
    'whydah_gally' => [
      'title' => 'Whydah Gally',
      'nid' => NULL,
    ],
    'ranger' => [
      'title' => 'Ranger',
      'nid' => NULL,
    ],
    'la_trompeuse' => [
      'title' => 'La Trompeuse',
      'nid' => NULL,
    ],
    'pirate_queen' => [
      'title' => 'pirate_queen',
      'nid' => NULL,
    ],
    'blessed_william' => [
      'title' => 'Blessed William',
      'nid' => NULL,
    ],
  ];
}

/**
 * Get Pirates.
 */
function bc_api_example_get_pirates_data() {
  return [
    'black_beard' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Edward Teach',
      'body' => "",
      'nickname' => ["Blackbeard"],
      'nationality' => ['england'],
      'ships' => ['queen_anne'],
    ],
    'bartholomew_roberts' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Bartholomew Roberts',
      'body' => "",
      'nickname' => ["Black Bart", "The Dread Pirate"],
      'nationality' => ['wales'],
      'ships' => ['royal_fortune'],
    ],
    'sam_bellamy' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Sam Bellamy',
      'body' => "",
      'nickname' => ["Black Sam"],
      'nationality' => ['england'],
      'ships' => ['whydah'],
    ],
    'stede_bonnet' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Stede Bonnet',
      'nickname' => ["The Gentleman Pirate"],
      'body' => "Nicknamed \"The Gentleman Pirate\", Bonnet was born into a wealthy family before turning to piracy.",
      'nationality' => ['barbados'],
      'ships' => ['revenge'],
    ],
    'william_kidd' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'William Kidd',
      'nickname' => ["Captain", "Captain Kidd"],
      'body' => "Although modern historians dispute the legitimacy of his trial and execution, the rumor of Captain Kidd's buried treasure has served only to build a legend around the man as a great pirate. His property was claimed by the crown and given to the Royal Hospital, Greenwich, by Queen Anne.",
      'nationality' => ['scotland'],
      'ships' => ['blessed_william', 'adventure_galley'],
    ],
    'anne_bonny' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Anne Bonny',
      'nickname' => ["The Pirate Queen", "Sea's Fury"],
      'body' => "",
      'nationality' => ['ireland'],
      'ships' => ['revenge'],
    ],
    'zheng yi sao' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Zheng Yi Sao',
      'nickname' => ["The Terror of South China", "Dragon Lady of the Seas"],
      'body' => "",
      'nationality' => ['china'],
      'ships' => ['red_flag_fleet'],
    ],
    'henry_every' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Henry Every',
      'nickname' => ["Long Ben", "King of Pirates"],
      'body' => "",
      'nationality' => ['england'],
      'ships' => ['fancy'],
    ],
    'charlotte_de_berry' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Charlotte de Berry',
      'nickname' => ["The Fierce Mistress"],
      'body' => "",
      'nationality' => ['united_kingdom'],
      'ships' => ['queen_anne'],
    ],
    'mary_read' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Mary Read',
      'nickname' => ["Mark Read"],
      'body' => "",
      'nationality' => ['united_kingdom'],
      'ships' => ['revenge'],
    ],
    'calico_jack' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Calico Jack',
      'nickname' => [],
      'body' => "",
      'nationality' => ['jamaica'],
      'ships' => ['ranger'],
    ],
    'francois_lolonnais' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Francois l\'Olonnais',
      'nickname' => ["The Bane of Spain"],
      'body' => "",
      'nationality' => ['france'],
      'ships' => ['la_trompeuse'],
    ],
    'grace_omalley' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Grace O\'Malley',
      'nickname' => ["Granuaile"],
      'body' => "",
      'nationality' => ['ireland'],
      'ships' => ['pirate_queen'],
    ],
    '' => [
      'nid' => NULL,
      'status' => 1,
      'title' => '',
      'nickname' => ["", ""],
      'body' => "",
      'nationality' => [''],
      'ships' => [''],
    ],
    '' => [
      'nid' => NULL,
      'status' => 1,
      'title' => '',
      'nickname' => ["", ""],
      'body' => "",
      'nationality' => [''],
      'ships' => [''],
    ],
    '' => [
      'nid' => NULL,
      'status' => 1,
      'title' => '',
      'nickname' => ["", ""],
      'body' => "",
      'nationality' => [''],
      'ships' => [''],
    ],
    '' => [
      'nid' => NULL,
      'status' => 1,
      'title' => '',
      'nickname' => ["", ""],
      'body' => "",
      'nationality' => [''],
      'ships' => [''],
    ],
    '' => [
      'nid' => NULL,
      'status' => 1,
      'title' => '',
      'nickname' => ["", ""],
      'body' => "",
      'nationality' => [''],
      'ships' => [''],
    ],
  ];
}
