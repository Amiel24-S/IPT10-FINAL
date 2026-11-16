<?php
namespace App\Controllers;

use App\Models\Book;
use App\Models\Category;
use Spine\Controller;
use Spine\Request;
use Spine\Response;

class BookController extends Controller
{
    /** GET /books  (supports ?q=search and ?category=id) */
    public function index(Request $request): Response
    {
        $q = (string) $request->input('q', '');
        $categoryId = (int) $request->input('category', 0) ?: null;

        return $this->view('books/index', [
            'title'      => 'Browse books',
            'books'      => Book::search($q, $categoryId),
            'categories' => Category::all('name'),
            'q'          => $q,
            'categoryId' => $categoryId,
        ]);
    }

    /** GET /books/{id} */
    public function show(Request $request): Response
    {
        $book = Book::withCategory((int) $request->param('id'));
        if (!$book) {
            return $this->redirect('/books', 'That book does not exist.', 'error');
        }
        return $this->view('books/show', ['title' => $book['title'], 'book' => $book]);
    }
}
