<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBookRequest;
use App\Http\Requests\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

class BooksController extends Controller
{
    public function __construct(private readonly Book $book){
  }

    public function index(): \Illuminate\Http\JsonResponse
    {
      $books = $this->book->all();
      return response()->json($books,200);
    }

    public function show($id): \Illuminate\Http\JsonResponse
    {
        $book = $this->book->find($id);
        return response()->json($book,200);
    }

    public function store(StoreBookRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        $book = $this->book->create($data);
        return response()->json($book, 201);
    }

    public function update($id, UpdateBookRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = $request->all();
        $book = $this->book->find($id);
        $book->update($data);

        return response()->json($book,200);
    }

    public function destroy($id){
        $book = $this->book->find($id);
        $book->delete();
        return response()->json([],204);
    }
}
