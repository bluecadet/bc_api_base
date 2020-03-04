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
      'nickname' => "Blackbeard",
      'nationality' => ['england'],
      'ships' => ['queen_anne'],
    ],
    'bartholomew_roberts' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Bartholomew Roberts',
      'body' => "",
      'nickname' => "Black Bart",
      'nationality' => ['wales'],
      'ships' => ['royal_fortune'],
    ],
    'sam_bellamy' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Sam Bellamy',
      'body' => "",
      'nickname' => "Black Sam",
      'nationality' => ['england'],
      'ships' => ['whydah'],
    ],
    'stede_bonnet' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'Stede Bonnet',
      'nickname' => "The Gentleman Pirate",
      'body' => "Nicknamed \"The Gentleman Pirate\", Bonnet was born into a wealthy family before turning to piracy.",
      'nationality' => ['barbados'],
      'ships' => ['revenge'],
    ],
    'william_kidd' => [
      'nid' => NULL,
      'status' => 1,
      'title' => 'William Kidd',
      'nickname' => "Captain",
      'body' => "Although modern historians dispute the legitimacy of his trial and execution, the rumor of Captain Kidd's buried treasure has served only to build a legend around the man as a great pirate. His property was claimed by the crown and given to the Royal Hospital, Greenwich, by Queen Anne.",
      'nationality' => ['scotland'],
      'ships' => ['revenge'],
    ],
  ];
}
