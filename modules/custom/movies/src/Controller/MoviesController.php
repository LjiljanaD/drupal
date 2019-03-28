<?php

namespace Drupal\movies\Controller;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\Query\Sql\QueryFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Drupal\Core\Path\AliasManager;

class MoviesController extends ControllerBase {

  protected $entityQuery;
  protected $entityTypeManager;
  protected $request;
  protected $moviesConfig;

  /**
   * Constructs a DbLogController object.
   *
   */
  public function __construct($entityQuery,
                              $entityTypeManager,
                              RequestStack $request,
                              ConfigFactory $configFactory,
                              AliasManager $aliasManager)
  {
    $this->entityQuery = $entityQuery;
    $this->entityTypeManager = $entityTypeManager;
    $this->request = $request->getCurrentRequest();
    $this->moviesConfig = $configFactory->get('movies.settings');
    $this->aliasManager = $aliasManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container)  {
    return new static(
      $container->get('entity.query'),
      $container->get('entity_type.manager'),
      $container->get('request_stack'),
      $container->get('config.factory'),
      $container->get('path.alias_manager')
    );
  }

  /**
   * @return array
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function movies()  {

    $content_per_page = $this->moviesConfig->get('movies.content_per_page');

    $filterPage = $this->request->get('filter');
    $current_page = $this->request->get('page');
    $offset = $content_per_page * $current_page;

    $movieIds = $this->entityQuery
      ->get('node')
      ->condition('type', 'movies')
      ->range($offset, $content_per_page)
      ->execute();


    $countQuery = $this->entityQuery
      ->get('node')
      ->condition('type', 'movies');

    if (!empty($filterPage)) {
      $countQuery->condition('field_movies_type', $filterPage);

      $movieIds = $this->entityQuery
        ->get('node')
        ->condition('type', 'movies')
        ->condition('field_movies_type', $filterPage)
        ->range($offset, $content_per_page)
        ->execute();
    }

    $total = $countQuery->count()->execute();

    $movieslist = $this->entityTypeManager->getStorage('node')->loadMultiple($movieIds);

    $movies = $this->getMovieData($movieslist);

    return array(
      '#title' => 'World of Movies',
      '#movies' => $movies,
      '#filters' => $this->getTaxonomy(),
      '#pager' => [
        'current' => $current_page,
        'next' => $current_page + 1,
        'previous' => 0,
        'total' => ceil($total / $content_per_page),
        'first' => 0,
        'last' => 10,
        'currentFilter' => !empty($filterPage) ? $filterPage : NULL,
      ],
      '#theme' => 'movies',
    );
  }


  /**
   * @return array
   */
  private function getTaxonomy()  {

    $taxonomyId = 'type_of_movie';

    $terms = $this->entityTypeManager->getStorage('taxonomy_term')->loadTree($taxonomyId);

    $filters = [];

    foreach ($terms as $term) {
      $filters [] = array(
        'name' => $term->name,
        'link' => $this->aliasManager->getAliasByPath('/taxonomy/term/' . $term->tid),
        'id' => $term->tid
      );
    }
    return $filters;
  }

  private function getMovieData($moviesList)  {
    $movies = [];
    foreach ($moviesList as $movie) {
      $actors = [];
      $movieTypes = [];

      foreach ($movie->get('field_actors') as $actor) {
        $actors = $actor->entity->getTitle();
      }

      foreach ($movie->get('field_movies_type') as $type) {
        $movieTypes = $type->entity->getName();
      }

      $movies[] = array(
        'title' => $movie->title->value,
        'description' => $movie->body->value,
        'actors' => $actors,
        'directors' => $movie->field_director->value,
        'movietype' => !empty($movieTypes) ? $movieTypes : NULL,
        'poster' => !empty($movie->field_movie_poster->entity) ? $movie->field_movie_poster->entity->getFileUri() : NULL,
      );
    }
    return $movies;
  }
}
