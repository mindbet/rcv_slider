<?php

namespace Drupal\rcv_slider\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Routing\CurrentRouteMatch;

/**
 * Controller to tally votes and redistribute choices.
 */
class RankChoiceCompile extends ControllerBase {

  public function content() {

    $database = \Drupal::database();


    // Get the id of the current poll.
    $currentNodeID = \Drupal::routeMatch()->getRawParameter('node');
    // Load the poll.
    $node = \Drupal::entityTypeManager()->getStorage('node')->load($currentNodeID);
    // Retrieve the explainer field.
    $explainer_display = $node->get('field_explainer');
    // Retrieve the value of the explainer field to be sent to twig.
    $explainer = $explainer_display->value;


    // Select rankings from database.
    $query = $database->query("SELECT choice_list from {score} where poll_id = $currentNodeID");
    $results = $query->fetchCol();

    // Initialize arrays where rankings will be stored.
    $allvotesarray = [];
    // Array of first choices.
    $firstchoicearray = [];

    // Loop through recorded rankings.
    foreach ($results as $key => $result) {
      // Load values into the arrays.
      $allvotesarray[$key]['ident'] = $key;
      $allvotesarray[$key]['code'] = json_decode($result, TRUE);
      $firstchoicearray[$key] = $allvotesarray[$key]['code'][0]['content'];
    }


    // Count number of ballots.
    $maxsteps = count($firstchoicearray);

    // Calculate total first place votes needed to win.
    $winningvotecount = $maxsteps / 2;

    // This reporting array holds the tallies for each of the step-by-step reassignments.
    $valuesreport = [];

    // Tally first choice votes.
    $values = array_count_values($firstchoicearray);
    // @TODO add candidates with no votes

    // Sort tallly of first choice votes, from most to least.
    arsort($values);

    // Load initial tally of ballots in reporting array.
    $g = 0;
    foreach ($values as $m => $candidaterank) {
      $g++;
      $valuesreport[0][$g] = [
        "candidate" => $m,
        "votecount" => $candidaterank,
      ];
    }

    // Initialize array that will store the ballots to be reassigned.
//    $worklist = [];




    // Set up the main loop to find the winner.
    for ($h = 1; $h <= $maxsteps; $h++) {
//      for ($h = 1; $h <= 7; $h++) {
// TODO FIX HERE






        // Find the vote leader and check how many votes they have.
      $leader = array_slice($values, 0, 1, TRUE);
      $leadervalue = reset($leader);

      // If leader has > 50%, they are the winner, and stop processing.
      if ($leadervalue > $winningvotecount) {
        break;
      }

      // Find the candidate(s) with lowest number of first choice votes.
      // Set a threshold value.
      $threshold = array_slice($values, -1, 1, TRUE);
      $thresholdvalue = reset($threshold);




      foreach (array_reverse($values) as $i => $votetally) {
        if ($votetally <= $thresholdvalue) {


          foreach ($allvotesarray as $j => $voteballot) {
//            if (array_key_exists('0', $voteballot['code'])) {
            $firstchoice = $voteballot['code'][0]['content'];
            if ($firstchoice === $i) {
              // Remove the first choice that is being redistributed.
              array_shift($allvotesarray[$j]['code']);
              // Retrieve the voters new first choice.
              $replacementunit = reset($allvotesarray[$j]['code']);
              // Store the new first choice in the list of all first choices.
              $firstchoicearray[$j] = $replacementunit['content'];
            }
//            } else {
//              unset($firstchoicearray[$j]);
//              unset($allvotesarray[$j]['code']);
//            }
          }
        }
      }




        foreach (array_reverse($values) as $i => $votetally) {
          if ($votetally <= $thresholdvalue) {

            foreach ($allvotesarray as $j => $voteballot) {
//              if ($allvotesarray[$j]['code']) {
                $votestring = array_column($voteballot['code'], 'content');

              $dropmarker = array_search($i, $votestring);
                if (!empty($dropmarker)) {
                array_splice($allvotesarray[$j]['code'], $dropmarker, 1);
              }
            }
          }
        }




        $values = array_count_values($firstchoicearray);

      // Sort vote totals, largest first.
      arsort($values);

      // Load tallies of adjusted ballots in the reporting array.
      $n = 0;
      foreach ($values as $m => $candidaterank) {
        $n++;
        $valuesreport[$h][$n] = [
          "candidate" => $m,
          "votecount" => $candidaterank,
        ];
      }

    }

    // Send results to be output.
    return [
      // Theme hook name.
      '#theme' => 'rcv_slider_page',
      // Variables to pass to twig template.
      '#valuestable' => $valuesreport,
      '#winningthreshold' => floor($winningvotecount),
      '#explanation' => $explainer
    ];

  }

} //end class
