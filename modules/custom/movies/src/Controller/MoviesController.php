<?php

namespace Drupal\movies\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MoviesController extends ControllerBase {

  protected $entityQuery;
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.query')
    );
  }

  /**
   * Constructs a DbLogController object.
   *
   */
  public function __construct($entityQuery) {
    $this->entityQuery = $entityQuery;

  }

  public function movies() {

  $movieIds = \Drupal::entityQuery('node')
    ->condition('type', 'movies')
    ->execute();

  $movieslist= \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($movieIds);

  $movies = [];
  foreach ($movieslist as $movieid => $movie){
    $a=1;
       $movies[]=array(
      'title'=>$movie->title->value,
      'description'=>$movie->body->value,
      'actors' => $movie->field_actors->value,
      'directors'=>$movie->field_director->value,
      'poster'=>$movie->field_movie_poster->entity->getFileUri(),


    );
  }
  $a=1;
      return array (
      '#title' => 'World of Movies',
      '#movies' => $movies,
      '#theme' => 'movies',
    );


  }
}
