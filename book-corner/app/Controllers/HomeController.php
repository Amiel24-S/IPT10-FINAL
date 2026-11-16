<?php
namespace App\Controllers;

use App\Models\Book;
use App\Models\Category;
use Spine\Controller;
use Spine\Request;
use Spine\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('home', [
            'title'      => 'Welcome',
            'latest'     => Book::latest(4),
            'categories' => Category::all('name'),
        ]);
    }
}
