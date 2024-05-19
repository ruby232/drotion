<?php

/**
 * @file
 * Contains \Drupal\drotion_node_workout_plan\Controller\TestAPIController.
 */

namespace Drupal\drotion_node_workout_plan\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\media\Entity\Media;
use Drupal\node\NodeInterface;
use Drupal\paragraphs\Entity\Paragraph;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller routines for api routes.
 */
class APIController extends ControllerBase {

  /**
   * Get sessions for a workout plan.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *  The request.
   * @param \Drupal\node\NodeInterface $workout_plan
   * The workout plan node.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *  The JSON response.
   */
  public function getSessions(Request $request, NodeInterface $workout_plan): JsonResponse {
    if($workout_plan->getType() !== 'workout_plan') {
      return new JsonResponse(['error' => 'Workout plan not found'], 404);
    }

    $sessionsData = $workout_plan->get('field_sessions')->getValue();
    $startDate = $workout_plan->get('field_start')->getString();
    $endDate = $workout_plan->get('field_finish')->getString();
    $sessions = [];
    foreach ($sessionsData as $session) {
      $sessionId = $session["target_id"];
      $sessionParagraph = Paragraph::load($sessionId);
      $date = $sessionParagraph->get('field_day')->getString();
      $done = $sessionParagraph->get('field_done')->getString();
      $videos = $sessionParagraph->get('field_videos')->getValue();
      $mediasTitle = [];
      foreach ($videos as $video) {
        $videoId = $video["target_id"];
        $videoMedia = Media::load($videoId);
        $mediasTitle[] = $videoMedia->get('name')->getString();
      }
      $title = join(' + ', $mediasTitle);
      $sessions[] = [
        'start' => $date,
        'done' => $done,
        'title' => $title,
        'id' => $sessionId,
        'workout_plan_id' => $workout_plan->id(),
      ];
    }

    return new JsonResponse(['sessions' => $sessions, 'start_date' => $startDate, 'end_date' => $endDate]);
  }

}
