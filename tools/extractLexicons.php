<?php

require_once __DIR__ . '/../lib/Core.php';

const BATCH_SIZE = 10000;
const START_ID = 0;
$offset = 0;
$modified = 0;

do {
  $defs = Model::factory('Definition')
        ->where_in('status', [0, 3])
        ->where_gte('id', START_ID)
        ->order_by_asc('id')
        ->limit(BATCH_SIZE)
        ->offset($offset)
        ->find_many();

  foreach ($defs as $d) {
    $l = $d->lexicon;
    $d->extractLexicon();
    if ($l !== $d->lexicon) {
      printf("%s [%s] [%s]\n", defUrl($d), $l, $d->lexicon);
      $modified++;
      $d->save();
    }
  }

  $offset += count($defs);
  Log::info("$offset definitions reprocessed, $modified modified.");
} while (count($defs));

Log::info("$offset definitions reprocessed, $modified modified.");

/*************************************************************************/

function defUrl($d) {
  return "https://dexonline.ro/editare-definitie?definitionId={$d->id}";
}
