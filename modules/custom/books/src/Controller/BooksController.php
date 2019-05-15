<?php

namespace Drupal\books\Controller;

use Drupal\books\Services\CustomService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class BooksController extends ControllerBase{
  protected $books;

  public function __construct(CustomService $books) {
    $this->books = $books;
  }
  public static function create(ContainerInterface $container)  {
    return new static(
      $container->get('books.custom_services')
    );
  }

  public function createBooks(){
    $allBooks = $this->books->getBooksData();
    $booksNode = $this->books->processBookData($allBooks);
    return array(
    '#allBooks' => $allBooks,
    '#bookNode' => $booksNode,
    );
  }

}