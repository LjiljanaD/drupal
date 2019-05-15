<?php

namespace Drupal\books\Services;

use Drupal\node\Entity\Node;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Entity\Query\Sql\QueryFactory;

class CustomService {
  public function getBooksData() {
       $client= \Drupal::httpClient();
    try {
      $xmlResponse = $client->get('http://www.chilkatsoft.com/xml-samples/bookstore.xml');
      $data = (string) $xmlResponse->getBody();
      $books = simplexml_load_string($data);
    }

    catch(RequestException $e) {
      return FALSE;
    }
    return $books;
  }
  public  function processBookData($books)  {
    foreach ($books as $book) {
      $userComments = [];
      $ISBNs = [];

      foreach ($book->comments as $comment) {
        $userComments = $comment->userComment;
      }
      foreach ($book->attributes() as $isbn) {
        $ISBNs = $isbn;
      }

      $title = $book->title;
      $query = \Drupal::entityQuery('node')
        ->condition('type', 'books')
        ->condition('title', $title);
      $result = $query->execute();
      if (empty($result)) {
        $node = Node::create(array(
          'type' => 'books',
          'title' => $title,
          'field_book_price' => $book->price,
          'field_book_isbn' => $ISBNs,
          'field_book_comments' => $userComments,
          'field_book_title' => $book->title

        ));
        $node->save();
      }
    }
  }


  public function getBookIds() {
    $results = \Drupal::entityQuery('node')
      ->condition('type','books')
      ->execute();
    return $results;
  }

  public function loadBookList($listIDs) {
    $bookList = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($listIDs);
    return $this->getBookData($bookList);
  }

  public function getBookData($bookList) {
    $title = [];
      foreach ($bookList as $book) {
      $title [] = [$book->title->value];
    }
    return $title;
  }
  public function books(){

    $bookIDs = $this->getBookIds();
    $bookList = $this->loadBookList($bookIDs);

    return $bookList;
  }
}